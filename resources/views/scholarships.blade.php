<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/scholarship.css') }}" rel="stylesheet">
    <script src="{{ asset('js/scholarships.js') }}"></script>
    <title>Add Scholarship</title>
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
                <li><a href="{{ route('admin.users') }}">Users</a></li>
                <li><a href="{{ route('admin.scholarships') }}" class="active">Scholarships</a></li>
                <li><a href="{{ route('admin.reports') }}">Reports</a></li>
                <li><a href="{{ route('admin.settings') }}">Settings</a></li>
                <li><a href="{{ route('admin.logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>
 
<main class="main-content">    

<button class="add-btn" onclick="openModal()">+ Add Scholarship</button>

        <section class="dashboard-cards">
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


    <table border="1" cellpadding="10" width="100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Dealine</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scholarships as $scholarship)
            <tr>
                <td>{{ $scholarship->id }}</td>
                <td>{{ $scholarship->title }}</td>
                <td>{{ $scholarship->description }}</td>
                <td>{{ $scholarship->deadline }}</td>
                <td>{{ $scholarship->status }}</td>
                <td>
                    <button onclick="openEditModal({{ $scholarship->id }})" type="button" class="edit-btn"
                        data-id="{{ $scholarship->id }}"
                        data-poster="{{ $scholarship->image_path }}"
                        data-title="{{ $scholarship->title }}"
                        data-description="{{ $scholarship->description }}"
                        data-deadline="{{ $scholarship->deadline }}"
                        data-requirement="{{ $scholarship->requirement->requirement ?? '' }}"
                    >Edit</button>

                    <button type="button" class="delete-btn" data-id="{{ $scholarship->id }}">Delete</button>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal Form -->
    <div id="scholarshipModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>

            <form action="{{ route('admin.scholarships.store') }}" method="POST" enctype="multipart/form-data">

                @csrf
                <div class="modal-title">
                    <h2>Add Scholarship</h2>
                </div>

                <div class="scholarship-image">
                    <div class="file-container"> 
                        <div class="file-header"> 
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> 
                            <path d="M7 10V9C7 6.23858 9.23858 4 12 4C14.7614 4 17 6.23858 17 9V10C19.2091 10 21 11.7909 21 14C21 15.4806 20.1956 16.8084 19 17.5M7 10C4.79086 10 3 11.7909 3 14C3 15.4806 3.8044 16.8084 5 17.5M7 10C7.43285 10 7.84965 10.0688 8.24006 10.1959M12 12V21M12 12L15 15M12 12L9 15" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg> <p>UPLOAD POSTER</p>
                        </div> 
                        <label for="fileposter" class="file-footer"> 
                            <svg fill="#000000" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M15.331 6H8.5v20h15V14.154h-8.169z"></path><path d="M18.153 6h-.009v5.342H23.5v-.002z"></path></g></svg> 
                            <p>Select file</p> 
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M5.16565 10.1534C5.07629 8.99181 5.99473 8 7.15975 8H16.8402C18.0053 8 18.9237 8.9918 18.8344 10.1534L18.142 19.1534C18.0619 20.1954 17.193 21 16.1479 21H7.85206C6.80699 21 5.93811 20.1954 5.85795 19.1534L5.16565 10.1534Z" stroke="#000000" stroke-width="2"></path> <path d="M19.5 5H4.5" stroke="#000000" stroke-width="2" stroke-linecap="round"></path> <path d="M10 3C10 2.44772 10.4477 2 11 2H13C13.5523 2 14 2.44772 14 3V5H10V3Z" stroke="#000000" stroke-width="2"></path> </g></svg>
                        </label> 
                        <input id="fileposter" name="poster" type="file" accept="image/*" required> 
                    </div>
                </div>

                <div class="scholarship-info">
                    <label>Title</label><br>
                    <input type="text" name="title" required><br><br>

                    <label>Description</label><br>
                    <textarea name="description" required></textarea><br><br>

                    <label>Deadline</label><br>
                    <input type="date" name="deadline" required><br><br>
                    
                </div>
                <div class="scholarship-requirements">
                    <textarea class="requirements" name="requirements" placeholder="Requirements"></textarea>
                </div>

                <div class="create-btn">
                    <button type="submit">Create Scholarship</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editScholarshipModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <h2>Edit Scholarship</h2>
            <form id="editScholarshipForm" method="POST" action="">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" id="scholarshipId">

                <div class="scholarship-info">
                    <label>Poster:</label>
                    <input type="file" name="poster" id="scholarshipPoster">

                    <label>Title:</label>
                    <input type="text" name="title" id="scholarshipTitle">

                    <label>Description:</label>
                    <textarea name="description" id="scholarshipDescription"></textarea>

                    <label>Deadline:</label>
                    <input type="date" name="deadline" id="scholarshipDeadline">
                </div>

                <div class="scholarship-requirements">
                    <textarea class="requirements" name="requirement" id="scholarshipRequirement" placeholder="Requirements"></textarea>
                </div>

                <div class="create-btn">
                    <button type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</main>
</body>
</html>