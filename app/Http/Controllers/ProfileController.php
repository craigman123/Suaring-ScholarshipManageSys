<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $profile = Auth::user()->profile;

        return view('student_profile', compact('profile'));
    }

    public function storeProfile(Request $request)
    {
        $data = $request->validate([
            'name'        => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'bio'         => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'course'      => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'address'     => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:100',
            'state'       => 'nullable|string|max:100',
            'country'     => 'nullable|string|max:100',
            'zip'         => 'nullable|string|max:20',
            'gender'      => 'nullable|string|in:Male,Female,Other',
            'dob'         => 'nullable|date',
            'marital_status' => 'nullable|string|max:50',
            'religion'    => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'achievements' => 'nullable|string',
        ]);

        // Handle image upload if present
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('profiles', 'public');
        } else {
            $data['image'] = $data['image'] ?? 'default.png'; // default image
        }

        // Update or create profile
        $profile = Auth::user()->profile()->updateOrCreate(
            ['user_id' => Auth::id()],
            $data
        );

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Alias method if you want a separate update route
     */
    public function updateProfile(Request $request)
    {
        return $this->storeProfile($request);
    }

    public function editProfile()
    {
        $profile = Auth::user()->profile;

        return view('edit_student_profile', compact('profile'));
    }





    public function getProfile()
    {
        $profile = Auth::user()->profile;

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json(['profile' => $profile]);
    }

    // POST /api/profile
    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name'        => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'bio'         => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'course'      => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'address'     => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:100',
            'state'       => 'nullable|string|max:100',
            'country'     => 'nullable|string|max:100',
            'zip'         => 'nullable|string|max:20',
            'gender'      => 'nullable|string|in:Male,Female,Other',
            'dob'         => 'nullable|date',
            'marital_status' => 'nullable|string|max:50',
            'religion'    => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'achievements' => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('profiles', 'public');
        }

        

        // Fill email from authenticated user
        $data['email'] = Auth::user()->email;

        // Default "No detail" for string fields
        $stringFields = ['name','phone','bio','course','institution','address','city','state','country','zip','marital_status','religion','nationality','achievements'];
        foreach ($stringFields as $field) {
            if (empty($data[$field])) {
                $data[$field] = 'No detail';
            }
        }

        // Keep DOB null if empty
        if (empty($data['dob'])) $data['dob'] = null;

        // Create or update profile
        $profile = Auth::user()->profile()->updateOrCreate(
            ['user_id' => Auth::id()],
            $data
        );

        return response()->json([
            'message' => 'Profile saved successfully',
            'profile' => $profile
        ]);
    }

    // PATCH /api/profile
    public function update(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated'
            ], 401);
        }

        $data = $request->validate([
            'name'        => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'bio'         => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'course'      => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'address'     => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:100',
            'state'       => 'nullable|string|max:100',
            'country'     => 'nullable|string|max:100',
            'zip'         => 'nullable|string|max:20',
            'gender'      => 'nullable|string|in:Male,Female,Other',
            'dob'         => 'nullable|date',
            'marital_status' => 'nullable|string|max:50',
            'religion'    => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'achievements' => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('profiles', 'public');
        }

        // Keep email from authenticated user
        $data['email'] = $user->email;

        // Default values (same as your store)
        $stringFields = [
            'name','phone','bio','course','institution',
            'address','city','state','country','zip',
            'marital_status','religion','nationality','achievements'
        ];

        foreach ($stringFields as $field) {
            if (empty($data[$field])) {
                $data[$field] = 'No detail';
            }
        }

        // Keep DOB null if empty
        if (empty($data['dob'])) {
            $data['dob'] = null;
        }

        // Update profile
        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $profile
        ]);
    }
}
