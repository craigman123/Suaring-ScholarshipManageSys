<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->profile;
        return view('student_profile', compact('profile'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'bio' => 'nullable|string',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('profiles', 'public');
        }

        $profile = auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return response()->json([
            'message' => 'Profile saved',
            'data' => $profile
        ]);
    }
}
