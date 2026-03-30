<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Application;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        // 3️⃣ Create main application
        $application = $scholarship->applications()->create([
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

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

    public function ScholarshipDestroy($id)
    {
        $application = Application::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$application) {
            return response()->json([
                'message' => 'Application not found'
            ], 404);
        }

        $application->delete();

        return response()->json([
            'message' => 'Application deleted successfully'
        ]);
    }


    public function getAllApplications()
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'No authenticated user found'
            ], 401);
        }

        $applications = Application::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        LogHelper::log("READ", "Applications retrieved for user_id: $userId, count: " . $applications->count(), $applications);

        if ($applications->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No applications found',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $applications
        ]);
    }

    public function getApplication($id)
    {
        $application = Application::with(['scholarship', 'files'])
            ->where('user_id', Auth::id())
            ->find($id);

        if (!$application) {
            LogHelper::log("ERROR", "Application not found", ['application_id' => $id, 'user_id' => Auth::id()]);
            return response()->json(['status' => 'error', 'message' => 'Application not found'], 404);
        }

        LogHelper::log("READ", "Application retrieved", $application);
        return response()->json(['status' => 'success', 'data' => $application], 200);
    }

    public function storeApplication(Request $request)
    {
        $request->validate([
            'scholarship_id' => 'required|exists:scholarships,id',
            'essay' => 'nullable|string',
            'requirements.*' => 'required|file|mimes:jpg,jpeg,png,pdf',
        ]);

        $filePaths = [];
        if ($request->hasFile('requirements')) {
            foreach ($request->file('requirements') as $file) {
                $filePaths[] = $file->store('requirements', 'public');
            }
        }

        $application = Application::create([
            'user_id' => Auth::id(),
            'scholarship_id' => $request->scholarship_id,
            'essay' => $request->essay,
            'status' => 'pending',
        ]);

        foreach ($filePaths as $path) {
            $application->files()->create(['file_path' => $path]);
        }

        LogHelper::log("CREATED", "Application created", $application);
        return response()->json([
            'status' => 'success',
            'message' => 'Application submitted successfully',
            'data' => $application
        ], 201);
    }

    public function updateApplication(Request $request, $id)
    {
        $application = Application::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$application) {
            LogHelper::log("ERROR", "Application not found for update", ['application_id' => $id, 'user_id' => Auth::id()]);
            return response()->json(['status' => 'error', 'message' => 'Application not found'], 404);
        }

        $application->update($request->only(['essay']));

        LogHelper::log("UPDATED", "Application updated", $application);

        return response()->json([
            'status' => 'success',
            'message' => 'Application updated successfully',
            'data' => $application
        ], 200);
    }

    public function destroyApplication($id)
    {
        $application = Application::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$application) {
            LogHelper::log("ERROR", "Application not found for deletion", ['application_id' => $id, 'user_id' => Auth::id()]);
            return response()->json(['status' => 'error', 'message' => 'Application not found'], 404);
        }

        foreach ($application->files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();
        }

        $application->delete();

        LogHelper::log("DELETED", "Application deleted", $application);
        return response()->json([
            'status' => 'success',
            'message' => 'Application deleted successfully'
        ], 200);
    }
}
