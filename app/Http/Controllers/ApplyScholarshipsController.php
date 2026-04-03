<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Application;
use App\Models\ApplicationRequirement;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApplyScholarshipsController extends Controller
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

    public function webEdit($id)
    {
        $application = Application::with('scholarship')->where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending applications can be edited.');
        }

        return view('edit_application_page', compact('application'));
    }

    public function Webcreate($scholarshipId)
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

        if($scholarship->deadline && \Carbon\Carbon::parse($scholarship->deadline)->isPast()) {
            return redirect()->route('student.scholarships.view', $scholarship->id)
                ->with('error', 'The application deadline for this scholarship has passed.');
        }

        if($scholarship->status !== 'Approved') {
            return redirect()->route('student.scholarships.view', $scholarship->id)
                ->with('error', 'This scholarship is not currently accepting applications.');
        }

        // 2️⃣ Validate
        $request->validate([
            'essay' => 'nullable|string',
            'requirements' => 'required|array',
            'requirements.*' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        // 3️⃣ Create main application (✅ essay goes here now)
        $application = $scholarship->applications()->create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'essay' => $request->essay, // 🔥 FIX HERE
        ]);

        // 4️⃣ Store files ONLY (no essay here anymore)
        if ($request->hasFile('requirements')) {
            foreach ($request->file('requirements') as $index => $file) {

                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('applications/' . $application->id, $filename, 'public');

                \App\Models\ApplicationRequirement::create([
                    'application_id' => $application->id,
                    'file_path' => $path,
                    'passed' => false,
                ]);
            }
        }

        // 5️⃣ Redirect
        return redirect()->route('student.scholarships', $scholarship->id)
            ->with('success', 'Application submitted successfully!');
    }

    public function ApplicationDestroy($id)
    {
        $application = Application::with('requirements')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        // 🔥 Delete all files from storage
        foreach ($application->requirements as $req) {
            if ($req->file_path && \Storage::disk('public')->exists($req->file_path)) {
                \Storage::disk('public')->delete($req->file_path);
            }
        }

        $application->requirements()->delete();

        $application->delete();

        return redirect()->back()->with('success', 'Application deleted successfully!');
    }

    public function ApplicationUpdate(Request $request, $id)
    {
        $application = Application::with('requirements')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        // ✅ Validate (optional but recommended)
        $request->validate([
            'essay' => 'nullable|string',
            'requirements.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // ✅ Update essay
        $application->update([
            'essay' => $request->essay
        ]);

        // ✅ Update files (ONLY if new file uploaded)
        if ($request->hasFile('requirements')) {

            foreach ($request->file('requirements') as $index => $file) {

                if (!$file) continue;

                $existing = $application->requirements->get($index);

                // 🔥 Delete old file
                if ($existing && \Storage::disk('public')->exists($existing->file_path)) {
                    \Storage::disk('public')->delete($existing->file_path);
                }

                // Store new file
                $filename = time().'_'.$file->getClientOriginalName();
                $path = $file->storeAs('applications/'.$application->id, $filename, 'public');

                if ($existing) {
                    $existing->update([
                        'file_path' => $path
                    ]);
                } else {
                    // fallback (in case missing index)
                    ApplicationRequirement::create([
                        'application_id' => $application->id,
                        'file_path' => $path
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Application updated successfully!');
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
        ], 200);
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

        LogHelper::log("READ", "Applications retrieved for user_id: $userId, count: " . $applications->count());

        if ($applications->isEmpty()) {
            return response()->json([
                'status' => 'data retrieved success',
                'message' => 'No applications found',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $applications
        ],200);
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

    public function storeApplication(Request $request, $scholarship_id)
    {
        try {
            $alreadyApplied = Application::where('user_id', Auth::id())
                ->where('scholarship_id', $scholarship_id)
                ->exists();

            $scholarship = Scholarship::with('requirementsForAPI')->find($scholarship_id);

            if ($alreadyApplied) {
                return response()->json([
                    'status' => 'Invalid Request',
                    'message' => 'You have already applied to this scholarship. You can only apply once.'
                ], 422);
            }

            if(!Scholarship::find($scholarship_id)){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Scholarship not found'
                ], 404);
            }

            if($scholarship->deadline && \Carbon\Carbon::parse($scholarship->deadline)->isPast()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The application deadline for this scholarship has passed.'
                ], 422);
            }

            if($scholarship->status !== 'Approved') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This scholarship is not currently accepting applications.'
                ], 422);
            }

            // 2️⃣ Wrap uploaded files
            $uploadedFiles = Arr::wrap($request->file('requirements'));

            $scholarship->load('requirementsForAPI');

            $requiredCount = collect($scholarship->requirementsForAPI)
                ->flatMap(fn($req) => $req->requirements ?? [])
                ->values()->filter(fn($r) => !empty($r) && $r !== 'none')
                ->count();

            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Files wrapped successfully',
            //     'uploaded_files_count' => count($uploadedFiles),
            //     'required_files_count' => $requiredCount,
            //     "id" => $scholarship->id,
            //     "requirements" => $scholarship->requirementsForAPI,
            // ], 200);

            if (count($uploadedFiles) !== $requiredCount) {
                return response()->json([
                    'status' => 'error',
                    'message' => "This scholarship requires exactly $requiredCount file(s). You uploaded " . count($uploadedFiles),
                    'data' => $scholarship,
                ], 422);
            } else if (count($uploadedFiles) > $requiredCount) {
                return response()->json([
                    'status' => 'error',
                    'message' => "You uploaded more files than required. This scholarship requires exactly $requiredCount file(s). You uploaded " . count($uploadedFiles),
                    'data' => $scholarship,
                ], 422);
            } else if (count($uploadedFiles) < $requiredCount) {
                return response()->json([
                    'status' => 'error',
                    'message' => "You uploaded fewer files than required. This scholarship requires exactly $requiredCount file(s). You uploaded " . count($uploadedFiles),
                    'data' => $scholarship,
                ], 422);
            }

            // 4️⃣ Create main application row
            $application = Application::create([
                'user_id' => Auth::id(),
                'scholarship_id' => $scholarship->id,
                'essay' => $request->essay,
                'status' => 'pending',
            ]);

            foreach ($request->file('requirements') as $index => $file) {
                $filename = time().'_'.$file->getClientOriginalName();
                $file->storeAs('applications/'.$application->id, $filename, 'public');

                $application->requirements()->create([
                    'file_path' => 'applications/'.$filename,
                    'requirement_name' => $requirementNames[$index] ?? 'Requirement '.($index+1),
                ]);
            }

            $application = Application::with(['scholarship', 'files'])
            ->where('user_id', Auth::id())
            ->find($application->id);

            LogHelper::log("CREATED", "Application created" . $application);

            $application->load('scholarship', 'files');
            return response()->json([
                'status' => 'success',
                'message' => 'Application submitted successfully',
                'data' => $application
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateApplication(Request $request, $application_id)
    {
        $application = Application::where('id', $application_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$application) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found'
            ], 404);
        }

        if ($application->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only pending applications can be updated'
            ], 422);
        }

        // Update essay if present
        if ($request->has('essay')) {
            $application->update([
                'essay' => $request->essay
            ]);
        }

        $updatedFiles = [];
        $invalidID = [];
        $filesUploadedNames = [];

        if ($request->hasFile('requirements')) {
            foreach ($request->file('requirements') as $id => $file) {
                $requirementId = (int)$id;

                $existing = DB::table('application_requirements')
                    ->where('id', $requirementId)
                    ->where('application_id', $application->id)
                    ->first();

                if (!$existing) {
                    $invalidID[] = $requirementId;
                    continue;
                }

                if ($existing->file_path) {
                    Storage::disk('public')->delete($existing->file_path);
                }

                $path = $file->store('applications', 'public');

                DB::table('application_requirements')
                    ->where('id', $requirementId)
                    ->where('application_id', $application->id)
                    ->update([
                        'file_path' => $path,
                        'updated_at' => now()
                    ]);

                $updatedFiles[] = [
                    'id' => $requirementId,
                    'file_path' => $path,
                ];

                $filesUploadedNames[$requirementId] = $file->getClientOriginalName();
            }
        }

        $application->refresh();

        // Logging
        LogHelper::log("UPDATED", "Application updated" . json_encode($request->all()));

        $message = (!$request->has('essay') && empty($updatedFiles))
            ? 'No changes were made to the application.'
            : 'Application updated successfully.';

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $application,
            'files' => $application->files()->get(),
            'updated_files' => $updatedFiles,
            'invalid_file_ids' => $invalidID ?: null,
            'files_uploaded' => $filesUploadedNames ?: null,
        ], 200);
    }

    public function destroyApplication($id)
    {
        $application = Application::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$application) {
            LogHelper::log("ERROR", "Application not found for deletion" . $id);
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found'
            ], 404);
        }

        // ✅ Only allow delete if pending (same rule as update)
        if ($application->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only pending applications can be deleted'
            ], 422);
        }

        $deletedFiles = [];

    foreach ($application->files as $file) {
        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // Track both id and file_path
        $deletedFiles[] = [
            'id' => $file->id,
            'file_path' => $file->file_path,
        ];

        $file->delete();
    }

        LogHelper::log("DELETED", "Application deleted" . $id);

        $application->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Application deleted successfully',
            'deleted_file_ids' => $deletedFiles
        ], 200);
    }
}
