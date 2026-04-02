<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/studDash.css') }}" rel="stylesheet">
    <link href="{{ asset('css/studScholarship.css') }}" rel="stylesheet">
    <script src="{{ asset('js/studScholarship.js') }}"></script>
    <title>Scholarships</title>
</head>
<body>
    <div id="alert-container"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('assets/logo.png') }}" alt="Logo">
            <h2>Student Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('student.applications') }}">Applications</a></li>
                <li><a href="{{ route('student.scholarships') }}" class="active">Scholarships</a></li>
                <li><a href="{{ route('student.profile') }}">Profile</a></li>
                <li><a href="{{ route('student.logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>

    @if(session('success'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 10px; z-index: 100; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background-color: #fee2e2; color: #991b1b; padding: 10px; z-index: 100; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif
    

    <!-- Main content -->
    <main class="main-content">
        <div class="back-to-top" onclick="scrollToTop()"><svg  xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" viewBox="0 0 24 24" ><path d="M13 18v-6h4l-5-6-5 6h4v6z"></path></svg></div>

        <section href="up" class="dashboard-cards">
            <div class="card">
                <h3>Total Scholarships</h3>
                <p>{{ $totalScholarships }}</p>
            </div>
        </section>

        <header>
            <h1>Available Scholarships</h1>
            <p>Browse scholarships and apply directly from here.</p>
        </header>

        <div class="search" id="search">
            <input class="search-input" type="text" placeholder="Search by name or email...">
            <button class="search-btn-scholar" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18 10c0-4.41-3.59-8-8-8s-8 3.59-8 8 3.59 8 8 8c1.85 0 3.54-.63 4.9-1.69l5.1 5.1L21.41 20l-5.1-5.1A8 8 0 0 0 18 10M4 10c0-3.31 2.69-6 6-6s6 2.69 6 6-2.69 6-6 6-6-2.69-6-6"></path>
                </svg>
            </button>
        </div>

        <section class="scholarship-grid">
            @forelse($scholarships as $scholarship)
                <div class="scholarship-card">
                    <div class="image-wrapper">
                        <img src="{{ $scholarship->image_path ? asset('storage/'.$scholarship->image_path) : asset('assets/default-scholarship.png') }}" alt="{{ $scholarship->title }}">

                        @php
                            $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($scholarship->deadline), false);
                        @endphp

                        @if($appliedScholarships->contains($scholarship->id))
                            <span class="badge applied">👑 Applied</span>
                        @elseif($daysLeft < 0)
                            <span class="badge closed">❌ Closed</span>
                        @elseif($daysLeft <= 3)
                            <span class="badge urgent">🔥 Soon</span>
                        @elseif($daysLeft <= 7)
                            <span class="badge warning">⚠️ Few Days</span>
                        @else
                            <span class="badge open">✅ Open</span>
                        @endif

                    </div>
                    <h3>{{ $scholarship->title }}</h3>
                    <p>{{ Str::limit($scholarship->description, 100) }}</p>
                    <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($scholarship->deadline)->format('M d, Y') }}</p>

                   @php
                        $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($scholarship->deadline), false);
                        $isClosed = $daysLeft < 0;
                        $hasApplied = $appliedScholarships->contains($scholarship->id);
                    @endphp

                    @if($isClosed)
                        <span class="apply-btn disabled" title="Application deadline has passed">
                            Closed
                        </span>

                    @elseif($hasApplied)
                        <span class="apply-btn apply" title="You have already applied">
                            Already Applied
                        </span>
                    @else
                        <a href="{{ route('student.scholarships.view', $scholarship->id) }}"
                            class="apply-btn">
                            Apply Now
                            </a>
                    @endif
                </div>
            @empty
                <p>No scholarships available at the moment.</p>
            @endforelse
        </section>
    </main>
</body>
</html>