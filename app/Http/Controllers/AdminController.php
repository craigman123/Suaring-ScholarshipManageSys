<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{

    public function dashboard()
    {
        $totalUsers = User::count();
        $users = User::where('created_at', '>=', Carbon::now()->subDays(2))->get();
        $totalScholarships = Scholarship::count();
        $pendingScholarships = Scholarship::where('status', 'Pending')->count();
        $approvedScholarships = Scholarship::where('status', 'Approved')->count();
        $rejectedScholarships = Scholarship::where('status', 'Rejected')->count();
        $holdScholarships = Scholarship::where('status', 'Hold')->count();
        
        return view('admindashboard', compact(
            'totalUsers',
            'totalScholarships',
            'users',
            'pendingScholarships',
            'rejectedScholarships',
            'approvedScholarships',
            'holdScholarships'
        ));
    }

    public function users()
    {
        $users = User::all();
        return view('users', compact('users'));
        
    }

    public function scholarships(){
        return view('scholarships');
    }

    public function reports(){
        return view('reports');
    }

    public function settings(){
        return view('settings');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'Logged out successfully!');
    }
}
