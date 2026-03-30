<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/application.css') }}">
    <script src="{{ asset('js/application.js') }}"></script>
    <title>Application Page</title>
</head>
<body>
    <div class="poster-page">
    <a href="{{ route('student.scholarships')}}" class="back-btn">
        <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  
        fill="currentColor" viewBox="0 0 24 24" >
        <path d="M11.79 6.29 6.09 12l5.7 5.71 1.42-1.42L9.91 13H18v-2H9.91l3.3-3.29z"></path>
        </svg>
    </a>

        <div class="image-container" style="position: relative;">
            <img src="{{ asset('storage/' . $scholarship->image_path) }}" 
                alt="Scholarship Image" 
                class="scholarship-image" 
                style="width:100%; border-radius:8px;">

            @php
                $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($scholarship->deadline), false);
            @endphp


            @if($daysLeft < 0)
                <span class="badge closed">Closed</span>
            @elseif($daysLeft <= 3)
                <span class="badge urgent">🔥 Soon</span>
            @elseif($daysLeft <= 7)
                <span class="badge warning">⚠️ Few Days</span>
            @else
                <span class="badge open">Open</span>
            @endif
        </div>

            <h1>{{ $scholarship->title }}</h1>
            <p style="font-weight: bold;">Deadline: {{ \Carbon\Carbon::parse($scholarship->deadline)->format('F j, Y') }}</p>
        </div>

    <div class="description">
        <h2>Scholarship Overview:</h2>
        <p style="font-weight: bold;">{{ $scholarship->description }}</p>
    </div>

    <div class="requirements">
        <h2>Requirements:</h2>
        <ul>
            @forelse($scholarship->requirement->requirements ?? [] as $req)
                <li>{{ $req }}</li>
            @empty
                <li>No requirements listed.</li>
            @endforelse
        </ul>
    </div>

    <div class="application-page">
    
        <div class="application-form">

            @if(session('success'))
                <div style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="background-color: #fee2e2; color: #991b1b; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('student.scholarships.apply', $scholarship->id) }}" 
                method="POST" 
                enctype="multipart/form-data">

                @csrf

                <div style="margin-bottom: 10px;">
                    <label style="font-weight: bold;" for="essay" placeholder="Intentions . . .">What is your intention to apply for this scholarship?</label>
                    <textarea name="essay" id="essay" rows="5" style="width:100%; padding:5px;"></textarea>
                </div>

                <div style="margin-bottom: 10px;">
                    <h2>Upload Requirements:</h2>

                    @foreach($scholarship->requirement->requirements ?? [] as $index => $requirement)
                        <div class="requirement-wrapper" style="margin-bottom:15px;">
                            <label>{{ $requirement }}</label><br>

                            <!-- Custom file button -->
                            <label class="file-btn" for="requirement_{{ $index }}">Choose Image</label>
                            <input type="file"
                                id="requirement_{{ $index }}"
                                name="requirements[{{ $index }}]"
                                accept="image/*"
                                class="requirement-input"
                                required
                                style="display:none;">

                            <span class="file-name" id="fileName_{{ $index }}">No file chosen</span>
                            <span class="error" id="error_{{ $index }}" style="display:none; color:red;">Image is required!</span>

                            <!-- Preview container for this input -->
                            <div class="preview-container" id="preview_{{ $index }}" style="margin-top:10px;"></div>
                        </div>
                    @endforeach
                </div>

                <!-- ✅ Submit Button -->
                <button class="submit-btn" type="submit">
                    Submit Application
                </button>

                <div class="notify-container">
                </div>
            </form>

        </div>
    </div>
</body>
</html>