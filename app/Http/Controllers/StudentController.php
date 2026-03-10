<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard()
    {
        $scholarships = Scholarship::all();

        return view('studentdash', compact('scholarships'));
    }

    public function scholarships()
    {
        $scholarships = Scholarship::all();
        return view('student_scholarships', compact('scholarships'));
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        return view('student_profile', compact('user'));
    }

    public function settings(Request $request)
    {
        $user = $request->user();
        return view('student_settings', compact('user'));
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'Logged out successfully!');
    }
}