<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderController extends Controller
{
    public function index()
    {
        $providerId = Auth::id();
        $totalUploadedScholarships = Scholarship::where('id', $providerId)->count();
        $scholarshipIds = Scholarship::where('id', $providerId)->pluck('id');
        $totalApplicants = Application::whereIn('scholarship_id', $scholarshipIds)->count();

        $pendingApplications = Application::whereIn('scholarship_id', $scholarshipIds)
            ->where('status', 'pending')
            ->count();

        $applications = Application::whereIn('scholarship_id', $scholarshipIds)
            ->latest()->take(5)->get();

        $approvedApplications = Application::whereIn('scholarship_id', $scholarshipIds)
            ->where('status', 'approved')
            ->count();

        $rejectedApplications = Application::whereIn('scholarship_id', $scholarshipIds)
            ->where('status', 'rejected')
            ->count();

        return view('providerdash', compact(
            'totalUploadedScholarships',
            'totalApplicants',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications',
            'applications'
        ));
    }

    public function dashboard(){
        return view('providerdash');
    }

    public function profile(){
        return view('provider_profile');
    }

    public function applications(){
        return view('provider_applications');
    }

    public function scholarships(){
        $providerId = Auth::id();
        $totalUploadedScholarships = Scholarship::where('id', $providerId)->count();
        $scholarshipIds = Scholarship::where('id', $providerId)->pluck('id');
        $totalApplicants = Application::whereIn('scholarship_id', $scholarshipIds)->count();

        $pendingApplications = Application::whereIn('scholarship_id', $scholarshipIds)
            ->where('status', 'pending')
            ->count();

        $applications = Application::whereIn('scholarship_id', $scholarshipIds)
            ->latest()->take(5)->get();

        $approvedApplications = Application::whereIn('scholarship_id', $scholarshipIds)
            ->where('status', 'approved')
            ->count();

        $rejectedApplications = Application::whereIn('scholarship_id', $scholarshipIds)
            ->where('status', 'rejected')
            ->count();

        $scholarships = Scholarship::where('id', auth()->id())->get();

        return view('provider_scholarship', compact(
            'totalUploadedScholarships',
            'totalApplicants',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications',
            'applications',
            'scholarships'
        ));
    }   

    public function settings(){
        return view('provider_settings');
    }

    public function reports(){
        return view('provider_reports');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'Logged out successfully!');
    }
}
