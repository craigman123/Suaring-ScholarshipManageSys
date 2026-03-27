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
        // 1️⃣ Check if already applied
        $alreadyApplied = $scholarship->applications()
            ->where('user_id', auth()->id())
            ->exists();

        if ($alreadyApplied) {
            return redirect()->route('student.scholarships.view', $scholarship->id)
                ->with('error', 'You have already applied for this scholarship.');
        }

        // 2️⃣ Validate
        $request->validate([
            'essay' => 'nullable|string',
            'requirements' => 'required|array',
            'requirements.*' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        dd($request->all());

        // 3️⃣ Create main application
        $application = $scholarship->applications()->create([
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        dd($application);

        // 4️⃣ Store files + essays in application_requirements
        if ($request->hasFile('requirements')) {
            foreach ($request->file('requirements') as $index => $file) {

                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('applications/' . $application->id, $filename, 'public');

                // Save to application_requirements table
                \App\Models\ApplicationRequirement::create([
                    'application_id' => $application->id,
                    'essay' => $request->essay ?? null,  // if you want same essay for all, or you can use $request->essay[$index] for per-file essays
                    'file_path' => $path,
                    'passed' => false,
                ]);
            }
        }

        // 5️⃣ Redirect
        return redirect()->route('student.scholarships.view', $scholarship->id)
            ->with('success', 'Application submitted successfully!');
    }
}
