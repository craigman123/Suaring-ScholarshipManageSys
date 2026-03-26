<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Application Page</title>
</head>
<body>
<div class="application-page max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg">

    <!-- Scholarship Header -->
    <h1 class="text-2xl font-bold mb-2">{{ $scholarship->title }}</h1>
    <p class="text-gray-600 mb-4">Deadline: {{ \Carbon\Carbon::parse($scholarship->deadline)->format('F j, Y') }}</p>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <!-- Dynamic Application Form -->
    <form action="{{ route('student.scholarships.apply', $scholarship->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <!-- Optional Essay -->
        <div>
            <label for="essay" class="block font-medium mb-1">Why should you get this scholarship?</label>
            <textarea name="essay" id="essay" rows="5" class="w-full border rounded p-2"></textarea>
        </div>

        <!-- Loop through dynamic requirements -->
        <div>
            <h2 class="text-xl font-semibold mb-2">Upload Scholarship Requirements:</h2>
            @foreach($scholarship->requirements as $index => $requirement)
                <div class="mb-3">
                    <label class="block font-medium mb-1">{{ $requirement }}:</label>
                    <input type="file" name="requirements[{{ $index }}]" accept=".pdf,.jpg,.png" required class="w-full">
                    <small class="text-gray-500">Upload the {{ $requirement }} as a PDF or image.</small>
                </div>
            @endforeach
        </div>

        <!-- Submit -->
        <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded hover:bg-blue-700">
            Submit Application
        </button>
    </form>
</div>
</body>
</html>