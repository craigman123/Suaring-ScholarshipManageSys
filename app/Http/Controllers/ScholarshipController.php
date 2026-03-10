<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index()
    {
        $scholarships = Scholarship::all();
        $totalScholarships = $scholarships->count();
        $pendingApprovals = Scholarship::where('status', 'pending')->count();

        return view('scholarships', compact(
            'scholarships', 
            'totalScholarships',
            'pendingApprovals'
            ));
    }

    public function dashboard()
    {
        $totalScholarships = Scholarship::count();
        $pendingApprovals = Scholarship::where('status', 'pending')->count();
        // $rejectedApplications = Application::where('status', 'rejected')->count();

        return view('scholarships', compact(
            'totalScholarships',
            'pendingApprovals',
            // 'rejectedApplications'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'poster' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'requirement' => 'nullable|string', 
        ]);

        $posterPath = $request->hasFile('poster') 
            ? $request->file('poster')->store('posters', 'public') 
            : null;

        $scholarship = \App\Models\Scholarship::create([
            'image_path' => $posterPath,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'status' => $request->status ?? 'pending',
        ]);

        if ($request->requirement) {
            \App\Models\Requirement::create([
                'scholarship_id' => $scholarship->id,
                'requirement' => $request->requirement,
            ]);
        }

        return redirect()->back()->with('success', 'Scholarship added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'requirement' => 'nullable|string',
        ]);

        $scholarship = \App\Models\Scholarship::findOrFail($id);

        $scholarship->update([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
        ]);

        if ($scholarship->requirement) {
            $scholarship->requirement->update([
                'requirement' => $request->requirement,
            ]);
        } else {
            $scholarship->requirement()->create([
                'requirement' => $request->requirement,
            ]);
        }

        return redirect()->back()->with('success', 'Scholarship updated successfully!');
    }
}
