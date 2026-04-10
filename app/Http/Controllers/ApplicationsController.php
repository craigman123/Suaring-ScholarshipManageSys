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

    public function approveReject(Request $request, $application_id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $application = Application::with('user', 'scholarship', 'requirements')->find($application_id);

        if (!$application) {
            return response()->json(['error' => 'Application not found'], 404);
        }
        if (!$application->scholarship) {
            return response()->json(['error' => 'Scholarship not found'], 404);
        }

        if ($user->role_id != 1 && $application->scholarship->provider_id != $user->id) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You can only manage applications for your own scholarships'
            ], 403);
        }

        // ✅ Validate the status input
        $validated = $request->validate([
            'status' => 'nullable|in:Approved,Rejected'
        ]);

        // Prevent double updates
        if (strtolower($application->status) !== 'pending') {
            return response()->json([
                'message' => 'Application already processed',
                'error' => 'Already processed',
                'id' => $application->id,
                'status' => $application->status,
                'application' => $application
            ], 400);
        }

        // Update using update()
        $status = $request->input('status'); // returns null if missing
            if (!$status) {
                return response()->json(['message' => 'Status is required'], 400);
            }

            $application->update([
                'status' => ucfirst($status),
            ]);

        if (!$updated) {
            return response()->json(['error' => 'Failed to update status'], 500);
        }

        return response()->json([
            'message' => 'Application status updated successfully',
            'data' => [
                'id' => $application->id,
                'status' => $application->status
            ]
        ], 200);
    }

    // public function approveReject(Request $request, $application_id)
    // {
    //     $request->validate([
    //         'status' => 'nullable|in:approved,rejected'
    //     ]);

    //     $application = Application::findOrFail($application_id);

    //     $application->status = $request->status;
    //     $application->save();

    //     return response()->json([
    //         'message' => 'Application status updated successfully',
    //         'data' => $application
    //     ]);
    // }

    public function getAllApplicant()
    {
        $user = auth()->user();

        $applications = Application::with('user', 'scholarship')
            ->whereHas('scholarship', function ($query) use ($user) {
                $query->where('provider_id', $user->id);
            })
            ->latest()
            ->get();

        if($applications->isEmpty()) {
            return response()->json([
                'status' => 'Empty',
                'message' => 'No applications found'
            ], 200);
        }

        return response()->json([
            'message' => 'Applications retrieved successfully',
            'data' => $applications
            ], 200);
    }

    public function getApplicant($id)
    {
        $user = auth()->user(); 

        $application = Application::with('user', 'scholarship')->find($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        if ($user->role_id == 1) {
            return response()->json($application);
        }

        if ($user->role_id == 2 && $application->scholarship->provider_id == $user->id) {
            return response()->json($application);
        }

        return response()->json(['message' => 'Unauthorized access'], 403);
    }

    public function getApplicantOnScholarship($scholarship_id)
    {
        try {
            $applications = Application::with('user')
                ->where('scholarship_id', $scholarship_id)
                ->get();

            $requirements = Application::with('requirements')
                ->where('scholarship_id', $scholarship_id)
                ->get()
                ->pluck('requirements')
                ->flatten();

            if($applications->isEmpty()) {
                $result = '(' . count($applications) . ') No applicants found for this scholarship';
            } else {
                $result = '(' . count($applications) . ') Applicants found for this scholarship';
            }

            return response()->json([
                'success' => true,
                'message' => $result,
                'data' => $applications,
                'requirements' => $requirements
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch applicants.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getApplicantOnOwnScholarship($scholarship_id)
    {
        $user = auth()->user();

        $applications = Application::with('user')
            ->where('scholarship_id', $scholarship_id)
            ->whereHas('scholarship', function ($query) use ($user) {
                $query->where('provider_id', $user->id);
            })
            ->get();

        $requirements = Application::with('requirements')
            ->where('scholarship_id', $scholarship_id)
            ->get()
            ->pluck('requirements')
            ->flatten();

        return response()->json([
            'message' => 'Applications retrieved successfully',
            'data' => $applications
        ], 200);
    }

    public function getApprovedApplications(){
        $applications = Application::where('status', 'approved')->get();

        if($applications->isEmpty()) {
            $result = '(' . count($applications) . ') No approved applications found';
        } else {
            $result = '(' . count($applications) . ') Approved applications found';
        }

        return response()->json([
            'message' => $result,
            'data' => $applications
        ], 200);
    }

    public function getRejectedApplications()
    {
        $applications = Application::where('status', 'rejected')->get();

        if($applications->isEmpty()) {
            $result = '(' . count($applications) . ') No Rejected applications found';
        } else {
            $result = '(' . count($applications) . ') Rejected applications found';
        }

        return response()->json([
            'message' => $result,
            'data' => $applications
        ], 200);
    }

    public function getPendingApplications(){
        $applications = Application::where('status', 'pending')->get();

        if($applications->isEmpty()) {
            $result = '(' . count($applications) . ') No Pending applications found';
        } else {
            $result = '(' . count($applications) . ') Pending applications found';
        }

        return response()->json([
            'message' => $result,
            'data' => $applications
        ], 200);
    }
}
