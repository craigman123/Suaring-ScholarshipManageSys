<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Requirements;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScholarshipController extends Controller
{   
    public function getScholarship($id)
    {
        $scholarship = Scholarship::with('requirement')->find($id);

        if (!$scholarship) {
            return response()->json([
                'message' => 'Scholarship not found'
            ], 404);
        }

        return response()->json($scholarship);
    }

    public function getScholarships()
    {
        $scholarships = Scholarship::all(); 
        return response()->json($scholarships);
    }

    public function show($id)
    {
        $scholarship = Scholarship::with('requirement')->find($id);

        if (!$scholarship) {
            abort(404, 'Scholarship not found');
        }

        return view('scholarships', compact('scholarship'));
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

        // Prevent wrong field
        if ($request->has('requirements')) {

            LogHelper::log("ERROR", "Field 'requirements' does not exist. Use 'requirement'.", auth()->user());

            return response()->json([
                'message' => "Field 'requirements' does not exist. Use 'requirement'."
            ], 422);
        }

        // Upload image
        $posterPath = $request->hasFile('poster') 
            ? $request->file('poster')->store('posters', 'public') 
            : null;

        // Create scholarship
        $scholarship = \App\Models\Scholarship::create([
            'image_path' => $posterPath,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'status' => $request->status,
        ]);

        $requirementsArray = [];

        if ($request->filled('requirement')) {
            // Split by comma OR newline
            $raw = preg_split('/[\n,]+/', $request->requirement); 
            $requirementsArray = array_map('trim', $raw);    
            $requirementsArray = array_filter($requirementsArray); 
        }

        // Store as array (Laravel handles JSON)
        Requirements::create([
            'scholarship_id' => $scholarship->id,
            'requirements' => $requirementsArray,
        ]);

        LogHelper::log("INFO", "Scholarship added successfully!", auth()->user());

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

        $requirementsArray = [];

        if ($request->filled('requirement')) {
            // Split by comma and/or newline
            $raw = preg_split('/[\n,]+/', $request->requirement);

            // Trim each element and remove empty strings
            $requirementsArray = array_filter(array_map('trim', $raw));
        } else {
            $requirementsArray = ['none'];
        }

        // Store as array (Laravel will cast to JSON automatically if your model has $casts)
        \App\Models\Requirements::create([
            'scholarship_id' => $scholarship->id,
            'requirements' => $requirementsArray,
        ]);

        try {
            LogHelper::log("CREATED SCHOLARSHIP", "Successfully created scholarship", auth()->user());
        } catch (\Exception $e) {
            LogHelper::error("ERROR LOGGING SCHOLARSHIP", "Error logging scholarship", auth()->user());
            return redirect()->back()->with('error', 'Something went wrong while creating scholarship.');
        }

        LogHelper::log("CREATED SCHOLARSHIP", "Successfully created scholarship", auth()->user());
        return redirect()->back()->with('success', 'Scholarship added successfully!');
    }

    public function update(Request $request, $id)
    {
        // dd([   >> debugging only
        //     'title' => $request->input('title'),
        //     'description' => $request->input('description'),
        //     'deadline' => $request->input('deadline'),
        //     'status' => $request->input('status'),
        //     'requirement' => $request->input('requirement'),
        //     'hasPoster' => $request->hasFile('poster')
        // ]);

        $request->validate([
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'deadline' => 'sometimes|date',
            'status' => 'sometimes|string',
            'requirement' => 'nullable|string',
        ]);

        if ($request->has('requirements')) {
            LogHelper::log("ERROR", "Field 'requirements' does not exist. Do you mean 'requirement' instead?", auth()->user());
            return response()->json([
                'message' => "Field 'requirements' does not exist. Do you mean 'requirement' instead?"
            ], 422);
        }

        $scholarship = \App\Models\Scholarship::findOrFail($id);

        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('posters', 'public');
            $scholarship->image_path = $posterPath;
        }

        if ($request->has('title')) {
            $scholarship->title = $request->title;
        }

        if ($request->has('description')) {
            $scholarship->description = $request->description;
        }

        if ($request->has('deadline')) {
            $scholarship->deadline = $request->deadline;
        }

        if ($request->has('status')) {
            $scholarship->status = $request->status;
        }

        $scholarship->save();

        if ($request->has('requirement')) {
            $requirementsArray = array_filter(
                array_map('trim', explode("\n", $request->requirement))
            );

            $requirement = Requirements::where('scholarship_id', $scholarship->id)->first();

            if ($requirement) {
                $requirement->requirements = json_encode($requirementsArray);
                $requirement->save();
            } else {
                Requirements::create([
                    'scholarship_id' => $scholarship->id,
                    'requirements' => json_encode($requirementsArray),
                ]);
            }

        }
        
        try {
            $scholarship->save();
        } catch (\Exception $e) {
            LogHelper::error("ERROR UPDATING SCHOLARSHIP", "Error updating scholarship", auth()->user()); 
            return response()->json([
                'message' => 'Something went wrong while updating.'
            ], 500);
        }

        LogHelper::log("UPDATED SCHOLARSHIP", "Successfully updated scholarship", auth()->user());
        return response()->json([
            'message' => 'Scholarship updated successfully!',
            'data' => $scholarship
        ]);
    }

   public function webUpdate(Request $request, Scholarship $scholarship)
{
    // 1️⃣ Validate the request
    $request->validate([
        'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'deadline' => 'required|date',
        'requirement' => 'nullable|string',
        'status' => 'required|string',
    ]);

    // 2️⃣ Handle poster upload
    if ($request->hasFile('poster')) {
        $posterPath = $request->file('poster')->store('posters', 'public');
        $scholarship->image_path = $posterPath;
    }

    // 3️⃣ Update scholarship fields
    $scholarship->update([
        'title' => $request->title,
        'description' => $request->description,
        'deadline' => $request->deadline,
        'status' => $request->status,
    ]);

    // 4️⃣ Prepare requirements array
    $requirementsArray = $request->requirement
    ? array_filter(array_map('trim', explode("\n", $request->requirement)))
    : [];

    // 5️⃣ Update existing requirement or create new
    if ($scholarship->requirement) {
        // Update existing requirement row
        $scholarship->requirement->update([
            'requirements' => $requirementsArray
        ]);
    } else {
        // Create new requirement row with explicit scholarship_id
        $requirement = new Requirements();
        $requirement->scholarship_id = $scholarships->id;
        $requirement->requirements = $requirementsArray;
        $requirement->save();
    }

    // 6️⃣ Optional: log the update
    LogHelper::log("UPDATED SCHOLARSHIP", "Successfully updated scholarship", auth()->user());

    // 7️⃣ Redirect back with success message
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

            LogHelper::log("DELETED SCHOLARSHIP", "Successfully deleted scholarship", auth()->user());  
            return response()->json([
                'message' => 'Scholarship deleted successfully!'
            ], 200);

        } else {
            LogHelper::error("ERROR DELETING SCHOLARSHIP", "Error deleting scholarship", auth()->user()); 
            return response()->json([
                'message' => 'Scholarship not found!'
            ], 404);
        }
    }

    public function webDestroy($id)
    {
        $scholarship = Scholarship::find($id);

        if (!$scholarship) {
            return redirect()->back()->with('error', 'Scholarship not found.');
        }

        try {
            $scholarship->delete();

            LogHelper::log("DELETED SCHOLARSHIP", "Successfully deleted scholarship", auth()->user()); 

            return redirect()->back()->with('success', 'Scholarship deleted successfully.');
        } catch (\Exception $e) {
            LogHelper::error("ERROR DELETING SCHOLARSHIP", "Error deleting scholarship", auth()->user()); 
            return redirect()->back()->with('error', 'Something went wrong while deleting.');
        }
    }

    
}
