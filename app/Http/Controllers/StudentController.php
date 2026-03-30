<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $applications = $user->applications()->with('scholarship')->latest()->get();

        return view('studentdash', [
            'applications' => $applications,
            'totalScholarships' => $applications->count(),
            'approved' => $applications->where('status', 'approved')->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
        ]);
    }

    public function applications()
    {
        $scholarships = Scholarship::all();
        return view('student_applications', compact('scholarships'));
    }

    public function scholarships()
    {
        $scholarships = Scholarship::all();
        return view('student_scholarships', compact('scholarships'));
    }

    public function StudentProfile(Request $request)
    {
        $user = $request->user();
        return view('student_profile', compact('user'));
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'Logged out successfully!');
    }
}