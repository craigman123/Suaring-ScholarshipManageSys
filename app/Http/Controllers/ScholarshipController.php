<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Scholarship;
use App\Models\Requirements;

class ScholarshipController extends Controller
{   
    public function getScholarship($id)
    {
        $user = Scholarship::find($id);

        if (!$user) {
            return response()->json(['message' => 'Scholarship not found'], 404);
        }

        return response()->json($user);
    }

    public function getScholarships()
    {
        $scholarships = Scholarship::all(); 
        return response()->json($scholarships);
    }



    
    public function AdminwebIndex()
    {
        $scholarships = Scholarship::with('requirement')->get();
        $totalScholarships = $scholarships->count();
        $pendingScholarships = Scholarship::where('status', 'pending')->count();
        $approvedScholarships = Scholarship::where('status', 'approved')->count();
        $rejectedScholarships = Scholarship::where('status', 'rejected')->count();
        $holdScholarships = Scholarship::where('status', 'hold')->count();

        return view('scholarships', compact(
            'scholarships',
            'totalScholarships',
            'approvedScholarships',
            'pendingScholarships',
            'rejectedScholarships',
            'holdScholarships'
        ));
    }

    public function dashboard()
    {
        $totalScholarships = Scholarship::count();
        $pendingApprovals = Scholarship::where('status', 'pending')->count();
        $approvedApplications = Scholarship::where('status', 'approved')->count();
        $rejectedApplications = Scholarship::where('status', 'rejected')->count();
        $holdApplications = Scholarship::where('status', 'hold')->count();

        return view('scholarships', compact(
            'totalScholarships',
            'pendingScholarships',
            'approvedScholarships',
            'rejectedScholarships',
            'holdScholarships'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'poster' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'status' => 'required|string',
            'requirement' => 'nullable|string', 
        ]);

        if ($request->has('requirements')) {
            return response()->json([
                'message' => "Field 'requirements' does not exist. Do you mean 'requirement' instead?"
            ], 422);
        }

        $posterPath = $request->hasFile('poster') 
            ? $request->file('poster')->store('posters', 'public') 
            : null;

        $scholarship = \App\Models\Scholarship::create([
            'image_path' => $posterPath,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'status' => $request->status,
        ]);

        if ($request->requirement) {
                $requirementsArray = array_filter(array_map('trim', explode("\n", $request->requirement)));
            } else {
                $requirementsArray = ["none"];
            }

            Requirements::create([
                'scholarship_id' => $scholarship->id,
                'requirements' => json_encode($requirementsArray),
            ]);

        return response()->json([
            'message' => 'Scholarship added successfully!',
            'data' => $scholarship
        ], 201);
    }

    public function AdminwebStore(Request $request)
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
            'status' => $request->status ?? 'Active',
        ]);

        $requirementsArray = $request->requirement
            ? array_filter(array_map('trim', explode("\n", $request->requirement)))
            : ['none']; 

        \App\Models\Requirements::create([
            'scholarship_id' => $scholarship->id,
            'requirements' => json_encode($requirementsArray),
        ]);

        return redirect()->back()->with('success', 'Scholarship added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'deadline' => 'sometimes|date',
            'status' => 'sometimes|string',
            'requirement' => 'nullable|string',
        ]);

        try {
            $scholarship = \App\Models\Scholarship::findOrFail($id);

            // Update scholarship fields even if empty
            $scholarship->title = $request->input('title', $scholarship->title);
            $scholarship->description = $request->input('description', $scholarship->description);
            $scholarship->deadline = $request->input('deadline', $scholarship->deadline);
            $scholarship->status = $request->input('status', $scholarship->status);

            // Handle poster upload
            if ($request->hasFile('poster')) {
                $posterPath = $request->file('poster')->store('posters', 'public');
                $scholarship->image_path = $posterPath;
            }

            $scholarship->save();

            // Handle requirements
            $requirementsArray = $request->filled('requirement')
                ? array_filter(array_map('trim', explode("\n", $request->requirement)))
                : ["none"];

            if ($scholarship->requirement) {
                $scholarship->requirement->requirements = json_encode($requirementsArray);
                $scholarship->requirement->save();
            } else {
                $scholarship->requirement()->create([
                    'requirements' => json_encode($requirementsArray)
                ]);
            }

            return response()->json([
                'message' => 'Scholarship updated successfully!',
                'data' => [
                    'scholarship' => $scholarship,
                    'requirements' => $requirementsArray
                ]
            ]);

        } catch (\Exception $e) {
            // Return error for debugging
            return response()->json([
                'message' => 'Update failed',
                'error' => $e->getMessage(),
                'trace' => $e->getTrace()
            ], 500);
        }
    }

    public function webUpdate(Request $request, Scholarship $id)
    {
        
        $request->validate([
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'requirement' => 'nullable|string',
            'status' => 'required|string',
        ]);

        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('posters', 'public');
            $id->image_path = $posterPath;
        }

        $id->update([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'status' => $request->status,
        ]);

        if ($id->requirement) {
            $id->requirement->update([
                'requirements' => $request->requirement ? json_encode(array_filter(array_map('trim', explode("\n", $request->requirement)))) : json_encode([]),
            ]);
        } else {
            $id->requirement()->create([
                'requirements' => $request->requirement ? json_encode(array_filter(array_map('trim', explode("\n", $request->requirement)))) : json_encode([]),
            ]);
        }

        return redirect()->back()->with('success', 'Scholarship updated successfully!');
    }

    public function destroy($id)
    {
        $scholarship = \App\Models\Scholarship::find($id);

        if($scholarship){
            $requirement = Requirements::where('scholarship_id', $scholarship->id)->first();
            if ($requirement) {
                $requirement->delete();
            }

            if($scholarship->image_path) {
                Storage::delete('public/' . $scholarship->image_path);
            }

            $scholarship->delete();

            return response()->json([
                'message' => 'Scholarship deleted successfully!'
            ], 200);

        } else {
            return response()->json([
                'message' => 'Scholarship not found!'
            ], 404);
        }
    }
}
