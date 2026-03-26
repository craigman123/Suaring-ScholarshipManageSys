<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{

    public function WebLogs()
    {
        $logs = \App\Models\Log::with('user')->latest()->get(); 
        return view('reports', compact('logs'));
    }

    public function index()
    {
        $logs = Log::with('user')->latest()->paginate(10);
        return response()->json($logs);
    }

    public static function logAction($action, $description = null)
    {
        Log::create([
            'user_id' => Auth::id() ?? null,
            'action' => $action,
            'description' => $description,
        ]);
    }

    public function getLogSearch(Request $request)
    {
        // Optional: get search query from request
        $search = $request->input('q');

        // Fetch logs from your Log model, optionally filter by search
        $logs = \App\Models\Log::when($search, function ($query, $search) {
            return $query->where('message', 'like', "%{$search}%");
        })->get();

        // Return JSON response
        return response()->json([
            'success' => true,
            'count' => $logs->count(),
            'data' => $logs
        ]);
    }
}
