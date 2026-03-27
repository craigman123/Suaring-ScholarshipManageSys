
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/studDash.css') }}" rel="stylesheet">
    <script src="{{ asset('js/studDash.js') }}"></script>
    <title>Student Applications</title>
</head>
<body>
    <div id="alert-container"></div>

    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('assets/logo.png') }}" alt="Logo">
            <h2>Student Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('student.applications') }}" class="active">Applications</a></li>
                <li><a href="{{ route('student.scholarships') }}">Scholarships</a></li>
                <li><a href="{{ route('student.profile') }}">Profile</a></li>
                <li><a href="{{ route('student.logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main content -->
    <main class="main-content">

        <!-- Dashboard Cards -->
        <section class="dashboard-cards">
            <div class="card">
                <h3>Total Applications</h3>
                <p>{{ $totalApplications }}</p>
            </div>
            <div class="card">
                <h3>Approved Applications</h3>
                <p>{{ $approvedApplications }}</p>
            </div>
            <div class="card">
                <h3>Pending Applications</h3>
                <p>{{ $pendingApplications }}</p>
            </div>
            <div class="card">
                <h3>Rejected Applications</h3>
                <p>{{ $rejectedApplications }}</p>
            </div>
        </section>

        <!-- Scholarships as Cards -->
        <section class="scholarship-cards" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px,1fr)); gap:1.5rem; margin-top:1.5rem;">
            @forelse($applications as $app)
                <div class="scholarship-card" style="background:#fff; border-radius:8px; padding:1rem; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
                    <!-- Scholarship Picture -->
                    @if($app->scholarship->image_path)
                        <img src="{{ asset('storage/' . $app->scholarship->image_path) }}" alt="{{ $app->scholarship->title }}" style="width:100%; height:150px; object-fit:cover; border-radius:6px;">
                    @else
                        <img src="{{ asset('assets/default-scholarship.png') }}" alt="No Image" style="width:100%; height:150px; object-fit:cover; border-radius:6px;">
                    @endif

                    <!-- Scholarship Info -->
                    <h3 style="margin-top:0.5rem;">{{ $app->scholarship->title }}</h3>
                    <p class="scholarship-status" style="font-weight:bold; color:{{ $app->status === 'approved' ? 'green' : ($app->status === 'pending' ? 'orange' : 'red') }}">
                        {{ ucfirst($app->status) }}
                    </p>
                    <p>Submitted: {{ $app->created_at->format('M d, Y') }}</p>
                    
                    <a href="{{ route('student.application.view', $app->id) }}" style="display:inline-block; padding:0.3rem 0.7rem; background:#007bff; color:white; border-radius:4px; text-decoration:none; font-size:0.9rem; margin-top:0.5rem;">
                        View Details
                    </a>
                </div>
            @empty
                <p>You have not applied for any scholarships yet.</p>
            @endforelse
        </section>
    </main>
</body>
</html>