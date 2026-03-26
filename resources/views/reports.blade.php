<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reports - Admin Panel</title>
    <link href="{{ asset('css/adminDash.css') }}" rel="stylesheet" />
</head>
<body>
    <div id="alert-container"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="assets/logo.png" alt="Logo" />
            <h2>Admin Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.users') }}">Users</a></li>
                <li><a href="{{ route('admin.scholarships') }}">Scholarships</a></li>
                <li><a href="{{ route('admin.reports') }}" class="active">Reports</a></li>
                <li><a href="{{ route('admin.settings') }}">Settings</a></li>
                <li><a href="{{ route('admin.logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main content -->
    <main class="main-content">
        <header>
            <h1>System Reports</h1>
            <p>Here’s the activity log of all users and actions.</p>
        </header>

        <section class="recent-activity">
            <h2>Activity Logs</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->user->first_name ?? 'Unknown' }} {{ $log->user->last_name ?? '' }}</td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->created_at->format('M j, Y h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: gray;">
                                No logs found
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