<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function dashboard()
    {
        $totalUsers = User::count();
        $users = User::all(); 
        $totalScholarships = Scholarship::count();
        $pendingApprovals = Scholarship::where('status', 'pending')->count();
        // $rejectedApplications = Application::where('status', 'rejected')->count();

        return view('admindashboard', compact(
            'totalUsers',
            'totalScholarships',
            'users',
            'pendingApprovals',
            // 'rejectedApplications'
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
