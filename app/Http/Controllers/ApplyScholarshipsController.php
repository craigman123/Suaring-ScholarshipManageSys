<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use Illuminate\Http\Request;

class ApplyScholarshipsController extends Controller
{
    public function create($scholarshipId)
    {
        $scholarship = Scholarship::findOrFail($scholarshipId);

        if (\Carbon\Carbon::parse($scholarship->deadline)->isPast()) {
            return redirect()->back()->with('error', 'Application deadline has passed.');
        }

        return view('application_page', compact('scholarship'));
    }

    public function ScholarshipApply(Request $request, Scholarship $scholarship)
    {
        // Check if already applied
        $alreadyApplied = $scholarship->applications()->where('user_id', auth()->id())->exists();
        if ($alreadyApplied) {
            return redirect()->route('student.scholarships.view', $scholarship->id)
                ->with('error', 'You have already applied for this scholarship.');
        }

        // Validate essay (optional)
        $request->validate([
            'essay' => 'nullable|string|max:2000',
            'requirements.*' => 'required|file|mimes:pdf,jpg,png|max:5120', // max 5MB per file
        ]);

        // Store application
        $application = $scholarship->applications()->create([
            'user_id' => auth()->id(),
            'essay' => $request->essay,
            'status' => 'pending',
        ]);

        // Store each requirement file
        if($request->hasFile('requirements')){
            foreach($request->file('requirements') as $index => $file){
                $filename = time().'_'.$file->getClientOriginalName();
                $file->storeAs('applications/'.$application->id, $filename, 'public');

                // Save file record if you have a related table
                $application->files()->create([
                    'requirement_name' => $scholarship->requirements[$index],
                    'filename' => $filename,
                    'path' => 'applications/'.$application->id.'/'.$filename,
                ]);
            }
        }

        return redirect()->route('student.scholarships.view', $scholarship->id)
            ->with('success', 'Application submitted successfully!');
    }
}
