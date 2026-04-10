<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function getUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'user' => $user,
            'profile' => $user->profile()->get() ?? 'Profile Not Set',
            'applications' => $user->applications()->get() ?? 'No Applications Found',
        ], 200);
    }

    public function getUserSearch(Request $request)
    {
        $email = $request->query('email');

        if (!$email) {
            return response()->json(['message' => 'Email query param required'], 400);
        }

        $user = User::withoutGlobalScopes()
            ->whereRaw('LOWER(email) = ?', [strtolower(trim($email))])
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'requested_email' => $email,
            ], 404);
        }

        return response()->json([
            'user' => $user,
            'profile' => $user->profile()->get() ?? 'Profile Not Set',
        ], 200);

    }

    protected static function booted() {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('user_status_id', 1);
        });
    }




    public function AdminwebUsers()
    {
        $totalUsers = User::count();
        $users = User::all();
        $activeUsers = User::where('user_status_id', '1')->count();
        $inactiveUsers = User::where('user_status_id', '2')->count();
        $Students = User::where('role_id', '3')->count();
        $ScholarshipProviders = User::where('role_id', '2')->count();

        return view('users', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'Students',
            'ScholarshipProviders'
        ));
    }

    public function AdminwebUserStore(Request $request)
    {
        $request->validate([
            'first_name'  => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'   => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6',
            'role'        => 'required|integer|in:1,2,3',
            'status'      => 'required|integer|in:1,2',  
        ]);

        $user = User::create([
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name, 
            'last_name'      => $request->last_name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'role_id'        => $request->role,
            'user_status_id' => $request->status,
        ]);

        LogHelper::log("CREATED USER", "Successfully created user", auth()->user()); 
        return redirect()->route('admin.users')->with('success', 'User created successfully!');
    }

    public function AdminwebUserUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'role'       => 'required|integer|in:1,2,3',
            'status'     => 'required|integer|in:1,2',
            'password'   => 'nullable|string|min:6', 
        ]);

        $user->first_name     = $request->first_name;
        $user->middle_name    = $request->middle_name;
        $user->last_name      = $request->last_name;
        $user->email          = $request->email;
        $user->role_id        = $request->role;
        $user->user_status_id = $request->status;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        LogHelper::log("UPDATED USER", "Successfully updated user", auth()->user()); 
        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    public function AdminwebUserDestroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        LogHelper::log("DELETED USER", "Successfully deleted user", auth()->user()); 
        return redirect()->back()->with('success', 'User deleted successfully!');
    }

    public function userStore(Request $request)
    {
        try{
            $request->validate([
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'role' => 'nullable|integer|in:1,2,3',     
                'status' => 'nullable|integer|in:1,2',    
            ]);
        }catch(\Illuminate\Validation\ValidationException $e){
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }

        $role = $request->input('role', 3);      
        $status = $request->input('status', 1); 

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role,
            'user_status_id' => $status,
        ]);

        LogHelper::log("CREATED USER", "Successfully created user", auth()->user()); 
        return response()->json([
            'message' => 'User created successfully!',
            'user' => $user
        ], 201);
    }

    public function userDestroy($id)
    {
        $user = User::find($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'You cannot delete a logged own account.'
            ], 403);
        }elseif (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        LogHelper::log("DELETED USER", "Successfully deleted user", auth()->user()); 
        return response()->json([
            'message' => 'User deleted successfully!'
        ]);
    }

    public function userUpdate(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'middle_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|integer|in:1,2,3',
            'status' => 'sometimes|integer|in:1,2',
            'password' => 'sometimes|string|min:6',
        ]);

        // Handle password separately
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Map fields (since request keys differ from DB columns)
        if (isset($validated['role'])) {
            $validated['role_id'] = $validated['role'];
            unset($validated['role']);
        }

        if (isset($validated['status'])) {
            $validated['user_status_id'] = $validated['status'];
            unset($validated['status']);
        }

        $user->update($validated);

        LogHelper::log("UPDATED USER", "Successfully updated user", auth()->user());

        return response()->json([
            'message' => 'User updated successfully!',
            'user' => $user
        ]);
    }


    public function InquireUser($user_id)
    {
        // Fetch user with profile
        $user = User::with('profile')->find($user_id);

        // Check if user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if account is confidential
        if ($user->role_id == 1) {
            return response()->json(['message' => 'This Account is Confidential'], 403);
        }

        $profile = $user->profile ?? null;

        // Return user info along with profile
        return response()->json([
            'message' => 'User found',
            'user' => $user,
            'profile' => $profile ?? null, 
        ]);
    }

    public function deactivateUser($id, Request $request)
    {
        $request->validate([
            'status' => 'required|integer|in:1,2', // 1 = active, 2 = deactivated
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Prevent self-deactivation
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot change the status of your own account.'], 403);
        }

        $status = $request->input('status');

        if ($user->user_status_id === $status) {
            $msg = $status === 2 ? 'User is already deactivated.' : 'User is already active.';
            return response()->json(['message' => $msg], 400);
        }

        $user->user_status_id = $status;
        $user->save();

        $logMessage = $status === 2 ? "DEACTIVATED USER" : "ACTIVATED USER";
        LogHelper::log($logMessage, "Successfully updated user status", auth()->user());

        $successMsg = $status === 2 ? 'User deactivated successfully' : 'User activated successfully';
        return response()->json(['message' => $successMsg]);
    }

    public function searchByCategory(Request $request)
    {
        $query = User::query();

        // Apply filters only if present
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        if ($request->filled('country')) {
            $query->where('country', 'like', '%' . $request->input('country') . '%');
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        if ($request->filled('nationality')) {
            $query->where('nationality', 'like', '%' . $request->input('nationality') . '%');
        }

        if ($request->filled('status')) {
            $query->where('user_status_id', $request->input('status'));
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->input('role_id'));
        }

        $users = $query->get(); 
        
        if(!$users->isEmpty()){
            LogHelper::log("SEARCHED USERS", "Successfully searched users with filters", auth()->user());
            return response()->json($users);
        } else {
            LogHelper::log("SEARCHED USERS", "No users found with the given filters", auth()->user());
            return response()->json([
                'message' => 'No users found matching the given criteria.'
            ], 200);
        }

        
    }


}
