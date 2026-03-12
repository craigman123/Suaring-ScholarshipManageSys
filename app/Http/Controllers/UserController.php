<?php

namespace App\Http\Controllers;

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
        return response()->json($user);
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
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
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

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    public function AdminwebUserDestroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

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

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|integer|in:1,2,3',
            'status' => 'required|integer|in:1,2',
            'password' => 'nullable|string|min:6',
        ]);

        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->role_id = $request->role;
        $user->user_status_id = $request->status;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully!',
            'user' => $user
        ]);
    }


}
