<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/applicationsFiles.css') }}" rel="stylesheet">
    <script src="{{ asset('js/application.js') }}"></script>
    <title>Document</title>
</head>
<body>
    <div class="container">
    <h2>Application Files for {{ $application->user->name }}</h2>

    <!-- Essay Section -->
    <div class="section">
        <h4>Intention:</h4>
        @if($application->essay)
            <div class="essay-box">
                {{ $application->essay }}
            </div>
        @else
            <p>No essay submitted.</p>
        @endif
    </div>

    <!-- Files Section -->
    <div class="section">
        <h4>Uploaded Files</h4>
        @if($application->files->count() > 0)
            <div class="requirements-grid">
                @foreach($application->files as $file)
                    @php
                        $ext = pathinfo($file->file_path, PATHINFO_EXTENSION);
                        $fileUrl = asset('storage/' . $file->file_path);
                    @endphp

                    <div class="requirement-card">
                        @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif']))
                            <img src="{{ $file->file_path }}" style="max-width:200px; max-height:200px;">
                            {{ $file->file_path }}
                        @else
                            <div class="file-box">
                                {{ basename($file->file_path) }}
                            </div>
                        @endif
                        <div class="actions">
                            <a href="{{ $file->file_path }}" target="_blank" class="btn">Open</a>
                            <a href="{{ $fileUrl }}" download class="btn download-btn">Download</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>No files uploaded yet.</p>
        @endif
    </div>

    <a href="{{ url()->previous() }}" class="back-btn">⬅ Back</a>

</div>
</body>
</html>