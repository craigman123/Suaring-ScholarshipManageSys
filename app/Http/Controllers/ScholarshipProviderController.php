<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use Illuminate\Http\Request;

class ScholarshipProviderController extends Controller
{
    public function webStore(Request $request)
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
            'status' => 'Pending',
            'provider_id' => auth()->id(),
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
}
