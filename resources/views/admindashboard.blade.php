<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Provider Dashboard</title>
    <link href="{{ asset('css/adminDash.css') }}" rel="stylesheet">
</head>
<body>

<div id="alert-container"></div>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="assets/logo.png" alt="Logo">
        <h2>Provider Panel</h2>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="{{ route('provider.dashboard') }}" class="active">Dashboard</a></li>
            <li><a href="{{ route('provider.scholarships') }}">My Scholarships</a></li>
            <li><a href="{{ route('provider.applications') }}">Applications</a></li>
            <li><a href="{{ route('provider.reports') }}">Reports</a></li>
            <li><a href="{{ route('provider.settings') }}">Settings</a></li>
            <li><a href="{{ route('provider.logout') }}">Logout</a></li>
        </ul>
    </nav>
</aside>

<!-- Main content -->
<main class="main-content">
    <header>
        <h1>Welcome, Provider</h1>
        <p>Manage your scholarships and applicants.</p>
    </header>

    <!-- Dashboard Cards -->
    <section class="dashboard-cards">
        <div class="card">
            <h3>Total Scholarships</h3>
            <p>{{ $totalScholarships }}</p>
        </div>
        <div class="card">
            <h3>Pending Applications</h3>
            <p>{{ $pendingApplications }}</p>
        </div>
        <div class="card">
            <h3>Approved Applications</h3>
            <p>{{ $approvedApplications }}</p>
        </div>
        <div class="card">
            <h3>Rejected Applications</h3>
            <p>{{ $rejectedApplications }}</p>
        </div>
    </section>

    <!-- Recent Applications -->
    <section class="recent-activity">
        <h2>Recent Applications</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Applicant Name</th>
                        <th>Email</th>
                        <th>Scholarship</th>
                        <th>Applied At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $app)
                        <tr>
                            <td>{{ $app->user->first_name }} {{ $app->user->last_name }}</td>
                            <td>{{ $app->user->email }}</td>
                            <td>{{ $app->scholarship->title }}</td>
                            <td>{{ $app->created_at->format('M j, Y') }}</td>
                            <td>
                                @switch($app->status)
                                    @case('pending')
                                        <div style="color: orange; font-weight: 700;">Pending</div>
                                        @break
                                    @case('approved')
                                        <div style="color: #50A61B; font-weight: 700;">Approved</div>
                                        @break
                                    @case('rejected')
                                        <div style="color: red; font-weight: 700;">Rejected</div>
                                        @break
                                    @default
                                        <div style="color: gray;">Unknown</div>
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: gray;">
                                No recent applications
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</main>

</body>
</html>