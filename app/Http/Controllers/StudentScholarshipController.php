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
        
        $scholarship = $scholarships->first();
        if ($scholarship->deadline < now()) {
            abort(403, 'Application closed');
        }

        return view('student_scholarships', compact('scholarships', 'totalScholarships'));
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

    public function apply(Request $request, Scholarship $scholarship)
    {
        $request->validate([
            'essay' => 'required|string',
            'requirements.*' => 'required|file|mimes:jpg,jpeg,png,pdf',
        ]);

        // Handle saving files
        foreach ($request->file('requirements', []) as $file) {
            $path = $file->store('requirements', 'public');
            // Save $path in DB if needed
        }

        Application::create([
            'user_id' => auth()->id(),
            'scholarship_id' => $scholarship->id,
            'essay' => $request->essay,
            'status' => 'pending',
        ]);

        return redirect()->route('student.scholarships.index')
                        ->with('success', 'Application submitted successfully!');
    }
}
