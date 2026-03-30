<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentScholarshipController extends Controller
{
    public function index()
    {
        $scholarships = Scholarship::where('status', 'approved')->get();
        $totalScholarships = $scholarships->count(); 
        $appliedScholarships = Application::where('user_id', Auth::id())->pluck('scholarship_id');

        return view('student_scholarships', compact('scholarships', 'totalScholarships', 'appliedScholarships'));
    }

    public function ScholarshipApply(Scholarship $scholarship)
    {
        $alreadyApplied = $scholarship->applications->contains('user_id', Auth::id());
        if ($alreadyApplied) {
            return redirect()->back()->with('error', 'You have already applied for this scholarship.');
        }

        Application::create([
            'user_id' => Auth::id(),
            'scholarship_id' => $scholarship->id,
            'status' => 'pending',
        ]);


        return redirect()->back()->with('success', 'Application submitted!');
    }

    public function dashboard()
    {
        return view('studentdash');
    }

    public function applications()
    {
        $scholarships = Scholarship::all();
        return view('student_applications', compact('scholarships'));
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

    public function apply(Request $request, Scholarship $scholarship)
    {
    try {
        $validated = $request->validate([
            'essay' => 'required|string',
            'requirements.*' => 'required|file|mimes:jpg,jpeg,png,pdf',
        ]);

        $filePaths = [];

        if ($request->hasFile('requirements')) {
            foreach ($request->file('requirements') as $file) {
                $path = $file->store('requirements', 'public');
                $filePaths[] = $path;
            }
        }

        // Create application
        $application = Application::create([
            'user_id' => Auth::id(),
            'scholarship_id' => $scholarship->id,
            'essay' => $validated['essay'],
            'status' => 'pending',
        ]);

        // OPTIONAL: store file paths in another table
        foreach ($filePaths as $path) {
            $application->files()->create([
                'file_path' => $path
            ]);
        }

        return response()->json([
            'message' => 'Application submitted successfully',
            'data' => $application
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
