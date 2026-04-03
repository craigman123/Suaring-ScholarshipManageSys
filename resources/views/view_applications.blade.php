<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applicants</title>

    <link href="{{ asset('css/viewApplicants.css') }}" rel="stylesheet">
</head>
<body>

<div class="main-container">

    <!-- LEFT PANEL (Scholarship Info) -->
    <div class="left-panel card">
        <div class="back-button">
            <a href="{{ route('provider.applications') }}" class="btn-back">← Back to Scholarships</a>
        </div>

        <div class="image-container">
            <img src="{{ asset('storage/' . $scholarship->image_path) }}" class="scholarship-img" alt="Scholarship Image">
        </div>

        <h2>{{ $scholarship->title }}</h2>

        <p class="description">{{ $scholarship->description }}</p>

        <div class="info">
            <p><strong>📅 Deadline:</strong> {{ $scholarship->deadline }}</p>
            <p><strong>👥 Total Applicants:</strong> {{ $applications->count() }}</p>
        </div>
    </div>

    <!-- RIGHT PANEL (Applicants) -->
    <div class="right-panel card">
        <h3>Applicants</h3>
        {{-- <div class="search">
            <input class="search-input" type="text" placeholder="Search by name...">
        </div> --}}

        <div class="table-wrapper">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Files</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $app)
                    <tr>
                        <td class="id-col">#{{ $app->id }}</td>
                        <td>
                            <div class="user-info">
                                <div class="avatar">{{ strtoupper(substr($app->user->name,0,1)) }}</div>
                                <div>
                                    <strong>{{ $app->user->name }}</strong>
                                    <small>{{ $app->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="status 
                                {{ $app->status == 'pending' ? 'pending' : '' }}
                                {{ $app->status == 'approved' ? 'approved' : '' }}
                                {{ $app->status == 'rejected' ? 'rejected' : '' }}">
                                {{ $app->status }}
                            </span>
                        </td>
                        <td>
                            <div class="files">
                                <a href="{{ route('provider.applications.files', $app->id) }}" class="btn view">📎 View Files</a>
                            </div>
                        </td>
                        <td class="actions">
                            <!-- Approve Button -->
                            <form action="{{ route('provider.applications.approve', $app->id) }}" method="POST">
                                @csrf
                                <button class="btn approve"
                                    @if($app->status === 'Pending')
                                        disabled
                                        title="Cannot approve – current status: {{ $app->status }}"
                                        style="cursor: not-allowed; opacity: 0.5;"
                                    @endif
                                >
                                    ✔
                                </button>
                            </form>

                            <!-- Reject Button -->
                            <form action="{{ route('provider.applications.reject', $app->id) }}" method="POST">
                                @csrf
                                <button class="btn reject"
                                    @if($app->status === 'Pending')
                                        disabled
                                        title="Cannot reject – current status: {{ $app->status }}"
                                        style="cursor: not-allowed; opacity: 0.5;"
                                    @endif
                                >
                                    ✖
                                </button>
                            </form>
                       </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="multi-actions">
            @php
                $allPending = $applications->every(fn($app) => $app->status === 'Pending');
            @endphp

            <button class="btn approve-all" {{ $allPending ? '' : 'disabled' }}>Approve All</button>
            <button class="btn reject-all" {{ $allPending ? '' : 'disabled' }}>Reject All</button>
        </div>
    </div>

</div>

</body>
</html>