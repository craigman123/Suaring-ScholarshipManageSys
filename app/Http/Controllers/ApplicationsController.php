<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function show($id)
    {
        $application = Application::with('scholarship.requirement', 'requirements')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $scholarship = $application->scholarship;

        return view('edit_application_page', compact('application', 'scholarship'));
    }

    public function dashboard()
    {
        return view('studentdash');
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





    public function showApplication(Application $application)
    {
        return view('applications_show', compact('application'));
    }

    public function showFiles($id)
    {
        // Fetch the application
        $application = Application::with('user')->findOrFail($id);
        // if($application->provider_id !== auth()->id()) abort(403);

        // The uploaded files (essay + requirements) should be stored paths in DB
        $essayFile = $application->essay; // assuming 'essay' column
        $requirements = json_decode($application->requirements, true) ?? []; // array of file paths

        return view('applications_files', compact('application', 'essayFile', 'requirements'));
    }

    public function approveReject(Application $id, Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthenticated'
            ], 401);
        }

        // ✅ Eager load scholarship (no need for find)
        $application->load('scholarship');

        // ✅ If no status, just return details
        if (!$request->has('status')) {
            return response()->json([
                'message' => 'Application details fetched',
                'data' => $application
            ], 200);
        }

        // ✅ Validate status
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $statusCheck = strtolower($request->status);

        // ✅ Authorization (owner or admin)
        if ($user->role_id != 1 && $application->scholarship->provider_id != $user->id) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You are not allowed to update this application'
            ], 403);
        }

        // ✅ Prevent double updates
        if ($application->status !== 'Pending') {
            return response()->json([
                'error' => 'Already processed'
            ], 400);
        }

        // ✅ Update status
        $application->status = ucfirst($statusCheck);
        $application->save();

        return response()->json([
            'message' => 'Application status updated successfully',
            'data' => [
                'id' => $application->id,
                'status' => $application->status
            ]
        ], 200);
    }
}
