<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/studProfile.css') }}" rel="stylesheet">
    <script src="{{ asset('js/studProfile.js') }}"></script>
    <title>Document</title>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('assets/logo.png') }}" alt="Logo">
            <h2>Student Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('student.applications') }}">Applications</a></li>
                <li><a href="{{ route('student.scholarships') }}">Scholarships</a></li>
                <li><a href="{{ route('student.profile') }}" class="active">Profile</a></li>
                <li><a href="{{ route('student.logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <div class="profile-container">

        <div class="profile-details">
            <div class="profile-header">
            <img 
                src="{{ $profile && $profile->image 
                        ? asset('storage/' . $profile->image) 
                        : asset('assets/default.png') }}" 
                class="profile-img"
            >

            <div class="profile-info">
                <h2>{{ $profile->name ?? 'No Name' }}</h2>
                <p>{{ $profile->email ?? 'No Email' }}</p>
            </div>
        </div>

    <!-- Details -->
        <div class="info">
            <div>
                <span class="label">Student ID:</span>
                {{ auth()->user()->id ?? 'No detail' }}
            </div>

            <div>
                <span class="label">Course:</span>
                {{ $profile->course ?? 'No detail' }}
            </div>

            <div>
                <span class="label">Institution:</span>
                {{ $profile->institution ?? 'No detail' }}
            </div>

            <div>
                <span class="label">Phone:</span>
                {{ $profile->phone ?? 'No detail' }}
            </div>

            <div>
                <span class="label">Address:</span>
                {{ $profile->address ?? 'No detail' }}
            </div>

            <div>
                <span class="label">City:</span>
                {{ $profile->city ?? 'No detail' }}
            </div>

            <div>
                <span class="label">State:</span>
                {{ $profile->state ?? 'No detail' }}
            </div>

            <div>
                <span class="label">Country:</span>
                {{ $profile->country ?? 'No detail' }}
            </div>

            <div>
                <span class="label">Zip:</span>
                {{ $profile->zip ?? 'No detail' }}
            </div>

            <div>
                <span class="label">Gender:</span>
                {{ $profile->gender ?? 'No detail' }}
            </div>

            <div>
                <span class="label">Date of Birth:</span>
                {{ $profile->dob ?? 'No detail' }}
            </div>

            <div>
                <span class="label">Marital Status:</span>
                {{ $profile->marital_status ?? 'No detail' }}
            </div>

            <div>
                <span class="label">Religion:</span>
                {{ $profile->religion ?? 'No detail' }}
            </div>

            <div>
                <span class="label">Nationality:</span>
                {{ $profile->nationality ?? 'No detail' }}
            </div>

            <div>
                <span class="label">Bio:</span>
                {{ $profile->bio ?? 'No detail' }}
            </div>
        </div>

        <!-- Edit Button -->
        <a href="{{ route('student.profile.show') }}" class="edit-btn">
            Edit Profile
        </a>
    </div>

    <div class="achievements-section">
        <span class="label">Achievements:</span>
        {{ $profile->achievements ?? 'No detail' }}
    </div>

</div>

    
</body>
</html>