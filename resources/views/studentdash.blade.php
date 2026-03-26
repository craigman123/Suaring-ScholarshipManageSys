<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/studDash.css') }}" rel="stylesheet">
    <script src="{{ asset('js/studDash.js') }}"></script>
    <title>Student Dashboard</title>
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
                <li><a href="{{ route('student.dashboard') }}" class="active">Dashboard</a></li>
                <li><a href="{{ route ('student.applications')}}">Applications</a></li>
                <li><a href="{{ route('student.scholarships') }}">Scholarships</a></li>
                <li><a href="{{ route('student.profile') }}">Profile</a></li>
                <li><a href="{{ route('student.logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header>
            <h1>Welcome, {{ auth()->user()->name }}</h1>
            <p>Here’s an overview of your student account.</p>
        </header>

        <section class="dashboard-cards">
            <div class="card">
                <h3>Total Applications</h3>
                <p>{{ $totalScholarships }}</p>
            </div>
            <div class="card">
                <h3>Approved Applications</h3>
                <p>{{ $approved }}</p>
            </div>
            <div class="card">
                <h3>Pending Applications</h3>
                <p>{{ $pending }}</p>
            </div>
            <div class="card">
                <h3>Rejected Applications</h3>
                <p>{{ $rejected }}</p>
            </div>
        </section>

        <section class="recent-activity">
            <h2>Recent Applications</h2>
            <table>
                <thead>
                    <tr>
                        <th>Scholarship</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td>{{ $app->scholarship->title }}</td>
                            <td>{{ ucfirst($app->status) }}</td>
                            <td>{{ $app->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>