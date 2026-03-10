<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/adminDash.css') }}" rel="stylesheet">
    {{-- <script src="{{ asset('js/adminDash.js') }}"></script> --}}
    <title>Document</title>
</head>
<body>
    <div id="alert-container"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="assets/logo.png" alt="Logo">
            <h2>Admin Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a></li>
                <li><a href="{{ route('admin.users') }}">Users</a></li>
                <li><a href="{{ route('admin.scholarships') }}">Scholarships</a></li>
                <li><a href="{{ route('admin.reports') }}">Reports</a></li>
                <li><a href="{{ route('admin.settings') }}">Settings</a></li>
                <li><a href="{{ route('admin.logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main content -->
    <main class="main-content">
        <header>
            <h1>Welcome, Admin</h1>
            <p>Here’s an overview of the system.</p>
        </header>

        <section class="dashboard-cards">
            <div class="card">
                <h3>Total Users</h3>
                <p>{{ $totalUsers }}</p>
            </div>
            <div class="card">
                <h3>Total Scholarships</h3>
                <p>{{ $totalScholarships }}</p>
            </div>
            <div class="card">
                <h3>Pending Approvals</h3>
                <p>{{ $pendingApprovals }}</p>
            </div>
            <div class="card">
                <h3>Rejected Applications</h3>
                <p>5</p>
            </div>
        </section>

        <section class="recent-activity">
            <h2>Recent User Registrations</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered At</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->first_name }} {{ $user->middle_name ?? '' }} {{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @switch($user->role_id)
                                        @case('1')
                                            <div style="color: #A61B1B; font-weight: 700;">Admin</div>
                                            @break
                                        @case('2')
                                            <div style="color: #50A61B;"><i>Scholarship Provider</i></div>
                                            @break
                                        @case('3')
                                            <div style="color: #1b8da6;">Scholar / Student</div>
                                            @break
                                        @default
                                            <div style="color: gray;">Unknown</div>
                                    @endswitch
                                </td>
                                <td>{{ $user->created_at->format('M j, Y') }}</td>
                                <td>
                                    @if($user->user_status_id == '1')
                                        <div style="color: #50A61B;">Active</div>
                                    @elseif($user->user_status_id == '2')
                                        <div style="color: red;">Inactive</div>
                                    @else
                                        <div style="color: gray;">Unknown</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    
</body>
</html>