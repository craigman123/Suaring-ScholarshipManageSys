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
                <h3>Pending Scholarships</h3>
                <p>{{ $pendingScholarships }}</p>
            </div>
            <div class="card">
                <h3>Approved Scholarships</h3>
                <p>{{ $approvedScholarships }}</p>
            </div>
            <div class="card">
                <h3>Rejected Scholarships</h3>
                <p>{{ $rejectedScholarships }}</p>
            </div>
            <div class="card">
                <h3>Hold Scholarships</h3>
                <p>{{ $holdScholarships }}</p>
            </div>
        </section>

        <div class="search" id="search">
            <input class="search-input" type="text" placeholder="Search by title...">
            <button class="search-btn" type="button"><svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  fill="currentColor" viewBox="0 0 24 24" ><path d="M18 10c0-4.41-3.59-8-8-8s-8 3.59-8 8 3.59 8 8 8c1.85 0 3.54-.63 4.9-1.69l5.1 5.1L21.41 20l-5.1-5.1A8 8 0 0 0 18 10M4 10c0-3.31 2.69-6 6-6s6 2.69 6 6-2.69 6-6 6-6-2.69-6-6"></path></svg></button>
        </div>

    <div class="table-container">
        <table>
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
                    <td class="
                        {{ $scholarship->status == 'Pending' ? 'status-pending' : '' }}
                        {{ $scholarship->status == 'Approved' ? 'status-approved' : '' }}
                        {{ $scholarship->status == 'Rejected' ? 'status-rejected' : '' }}
                        {{ $scholarship->status == 'Hold' ? 'status-hold' : '' }}
                    ">
                        {{ $scholarship->status }}
                    </td>
                    <td>
                        <button type="button" class="edit-scholarship-btn"
                                data-id="{{ $scholarship->id }}"
                                data-title="{{ $scholarship->title }}"
                                data-description="{{ $scholarship->description }}"
                                data-deadline="{{ $scholarship->deadline }}"
                                data-status="{{ $scholarship->status }}"
                                data-requirement="{{ $scholarship->requirement ? implode("\n", json_decode($scholarship->requirement->requirements)) : '' }}"
                                data-poster="{{ $scholarship->image_path ? asset('storage/' . $scholarship->image_path) : '' }}">
                            <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24" ><path d="M5 21h14c1.1 0 2-.9 2-2v-7h-2v7H5V5h7V3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2"></path><path d="M7 13v3c0 .55.45 1 1 1h3c.27 0 .52-.11.71-.29l9-9a.996.996 0 0 0 0-1.41l-3-3a.996.996 0 0 0-1.41 0l-9.01 8.99A1 1 0 0 0 7 13m10-7.59L18.59 7 17.5 8.09 15.91 6.5zm-8 8 5.5-5.5 1.59 1.59-5.5 5.5H9z"></path></svg>
                        </button>

                        <button type="button" class="delete-btn" data-id="{{ $scholarship->id }}"><svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24" ><path d="M20 4H8.51c-.64 0-1.25.31-1.63.84l-4.7 6.58a.99.99 0 0 0 0 1.16l4.7 6.58c.37.52.98.84 1.63.84H20c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2m0 14H8.51l-4.29-6 4.29-6H20z"></path><path d="m9.79 9.21 2.8 2.79-2.8 2.79 1.42 1.42 2.79-2.8 2.79 2.8 1.42-1.42-2.8-2.79 2.8-2.79-1.42-1.42-2.79 2.8-2.79-2.8z"></path></svg></button>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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

                    <label>Status</label><br>
                    <select name="status" required>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Hold">Hold</option>
                    </select><br><br>
                    
                </div>
                <div class="scholarship-requirements">
                    <textarea class="requirements" name="requirement" placeholder="Requirements"></textarea>
                </div>

                <div class="create-btn">
                    <button type="submit">Create Scholarship</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editScholarshipModal" class="modal-edit">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <form id="editScholarshipForm" method="POST" action="">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" id="scholarshipId">

                <div class="modal-title">
                    <h2>Edit Scholarship</h2>
                </div>

                <div class="scholarship-image">
                    <div class="file-container-edit"> 
                        <div class="file-header-edit">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> 
                            <path d="M7 10V9C7 6.23858 9.23858 4 12 4C14.7614 4 17 6.23858 17 9V10C19.2091 10 21 11.7909 21 14C21 15.4806 20.1956 16.8084 19 17.5M7 10C4.79086 10 3 11.7909 3 14C3 15.4806 3.8044 16.8084 5 17.5M7 10C7.43285 10 7.84965 10.0688 8.24006 10.1959M12 12V21M12 12L15 15M12 12L9 15" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg> <p>UPLOAD POSTER</p>
                        </div> 
                        <label for="editFilePoster" class="file-footer">
                            <svg fill="#000000" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M15.331 6H8.5v20h15V14.154h-8.169z"></path><path d="M18.153 6h-.009v5.342H23.5v-.002z"></path></g></svg> 
                            <p>Select file</p> 
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M5.16565 10.1534C5.07629 8.99181 5.99473 8 7.15975 8H16.8402C18.0053 8 18.9237 8.9918 18.8344 10.1534L18.142 19.1534C18.0619 20.1954 17.193 21 16.1479 21H7.85206C6.80699 21 5.93811 20.1954 5.85795 19.1534L5.16565 10.1534Z" stroke="#000000" stroke-width="2"></path> <path d="M19.5 5H4.5" stroke="#000000" stroke-width="2" stroke-linecap="round"></path> <path d="M10 3C10 2.44772 10.4477 2 11 2H13C13.5523 2 14 2.44772 14 3V5H10V3Z" stroke="#000000" stroke-width="2"></path> </g></svg>
                        </label> 
                        <input id="editFilePoster" name="poster" type="file" accept="image/*">
                    </div>
                </div>

                <div class="scholarship-info">
                    <label>Title:</label>
                    <input type="text" name="title" id="scholarshipTitle">

                    <label>Description:</label>
                    <textarea name="description" id="scholarshipDescription"></textarea>

                    <label>Deadline:</label>
                    <input type="date" name="deadline" id="scholarshipDeadline">

                    <label>Status:</label>
                    <select name="status" id="scholarshipStatus">
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Hold">Hold</option>
                    </select>
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