<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
    <link href="{{ asset('css/user.css') }}" rel="stylesheet">
    <script src="{{ asset('js/user.js') }}"></script>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('assets/logo.png') }}" alt="Logo">
        <h2>Admin Panel</h2>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.users') }}" class="active">Users</a></li>
            <li><a href="{{ route('admin.scholarships') }}">Scholarships</a></li>
            <li><a href="{{ route('admin.reports') }}">Reports</a></li>
            <li><a href="{{ route('admin.settings') }}">Settings</a></li>
            <li><a href="{{ route('admin.logout') }}">Logout</a></li>
        </ul>
    </nav>
</aside>

<main class="main-content">

<button class="add-btn" onclick="openUserModal()">+ Add User</button>

<section class="dashboard-cards">
    <div class="card">
        <h3>Total Users</h3>
        <p>{{ $totalUsers }}</p>
    </div>
    <div class="card">
        <h3>Active Users</h3>
        <p>{{ $activeUsers }}</p>
    </div>
    <div class="card">
        <h3>Inactive Users</h3>
        <p>{{ $inactiveUsers }}</p>
    </div>
    <div class="card">
        <h3>Scholarship Providers</h3>
        <p>{{ $ScholarshipProviders }}</p>
    </div>
    <div class="card">
        <h3>Students</h3>
        <p>{{ $Students }}</p>
    </div>
</section>

<div class="search" id="search">
    <input class="search-input" type="text" placeholder="Search by name or email...">
    <button class="search-btn" type="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
            <path d="M18 10c0-4.41-3.59-8-8-8s-8 3.59-8 8 3.59 8 8 8c1.85 0 3.54-.63 4.9-1.69l5.1 5.1L21.41 20l-5.1-5.1A8 8 0 0 0 18 10M4 10c0-3.31 2.69-6 6-6s6 2.69 6 6-2.69 6-6 6-6-2.69-6-6"></path>
        </svg>
    </button>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Current Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
           @php
                $statusMap = [
                    1 => 'active',
                    2 => 'inactive',
                ];

                $roleMap = [
                    1 => 'admin',
                    2 => 'scholarship provider',
                    3 => 'student', 
                ];

                $roleColorClass = [
                    1 => 'role-admin', 
                    2 => 'role-provider',
                    3 => 'role-user',  
                ];
            @endphp

            @foreach($users as $user)
            <tr id="user-row-{{ $user->id }}">
                <td>{{ $user->id }}</td>
                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                <td>{{ $user->email }}</td>
                
                <td class="{{ $roleColorClass[$user->role_id] ?? '' }}">
                    {{ ucfirst($roleMap[$user->role_id] ?? 'Unknown') }}
                </td>
                
                <td class="{{ isset($statusMap[$user->user_status_id]) && $statusMap[$user->user_status_id] == 'active' ? 'status-active' : 'status-inactive' }}">
                    {{ ucfirst($statusMap[$user->user_status_id] ?? 'Unknown') }}
                </td>

                <td>
                    @if($user->id === auth()->id())
                        <span title="Online" style="color:green; font-size:20px; font-weight:bold;"><svg  xmlns="http://www.w3.org/2000/svg" width="40" height="40"  fill="currentColor" viewBox="0 0 24 24" ><path d="M12 5c-3.86 0-7 3.14-7 7s3.14 7 7 7 7-3.14 7-7-3.14-7-7-7m0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5"></path><path d="M12 9a3 3 0 1 0 0 6 3 3 0 1 0 0-6"></path></svg>
                        </span>
                    @else
                        <span title="Offline" style="color:gray; font-size:20px; font-weight:bold;"><svg  xmlns="http://www.w3.org/2000/svg" width="40" height="40"  fill="currentColor" viewBox="0 0 24 24" ><path d="M12 5c-3.86 0-7 3.14-7 7s3.14 7 7 7 7-3.14 7-7-3.14-7-7-7m0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5"></path><path d="M12 9a3 3 0 1 0 0 6 3 3 0 1 0 0-6"></path></svg>
                        </span>
                    @endif
                </td>

                <td>
                    <button type="button" class="edit-user-btn"
                        data-id="{{ $user->id }}"
                        data-first_name="{{ $user->first_name }}"
                        data-last_name="{{ $user->last_name }}"
                        data-email="{{ $user->email }}"
                        data-role="{{ $user->role_id }}" 
                        data-status="{{ $user->user_status_id }}"> 
                    Edit
                </button>

                    @if(auth()->id() !== $user->id)
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    @else
                        <button type="button" class="disabledBtn" disabled title="Cannot delete logged in account">Delete</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add User Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeUserModal()">&times;</span>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <h2>Add User</h2>

            <label>Name:</label>
            <input type="text" name="first_name" placeholder="First Name" required><br><br>
            <input type="text" name="middle_name" placeholder="Middle Name" required><br><br>
            <input type="text" name="last_name" placeholder="Last Name" required><br><br>

            <label>Email:</label>
            <input type="email" name="email" required><br><br>

            <label>Password:</label>
            <input type="password" name="password" required><br><br>

            <label >Role:</label>
            <select name="role" required>
                <option value="1">Admin</option>
                <option value="2">Scholarship Provider</option>
                <option value="3">Student</option>
            </select><br><br>

            <label>Status:</label>
            <select name="status" required>
                <option value="1">Active</option>
                <option value="2">Inactive</option>
            </select>

            <button type="submit">Create User</button>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal-edit">
    <div class="modal-content">
        <span class="close-btn" onclick="closeEditUserModal()">&times;</span>
        <form id="editUserForm" method="POST" action="">
            <h2>Edit User</h2>
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="userId">

            <label>First Name:</label>
            <input type="text" name="first_name" id="editUserFirstName"><br><br>

            <label>Last Name:</label>
            <input type="text" name="last_name" id="editUserLastName"><br><br>

            <label>Email:</label>
            <input type="email" name="email" id="editUserEmail"><br><br>

            <label>Role:</label>
            <select name="role" id="editUserRole">
                <option value="1">Admin</option>
                <option value="2">Scholarship Provider</option>
                <option value="3">Student</option>
            </select><br><br>

            <label>Status:</label>
            <select name="status" id="editUserStatus">
                <option value="1">Active</option>
                <option value="2">Inactive</option>
            </select><br><br>

            <button type="submit">Save</button>
        </form>
    </div>
</div>

</main>
</body>
</html>