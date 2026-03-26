<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use Illuminate\Http\Request;

class ApplicationsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get all applications with scholarship
        $applications = $user->applications()->with('scholarship')->latest()->get();

        // Counts
        $totalApplications = $applications->count();
        $approvedApplications = $applications->where('status', 'approved')->count();
        $pendingApplications = $applications->where('status', 'pending')->count();
        $rejectedApplications = $applications->where('status', 'rejected')->count();

        return view('student_applications', compact(
            'applications',
            'totalApplications',
            'approvedApplications',
            'pendingApplications',
            'rejectedApplications'
        ));
    }

    public function dashboard()
    {
        return view('student_dashboard');
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

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'Logged out successfully!');
    }
}
