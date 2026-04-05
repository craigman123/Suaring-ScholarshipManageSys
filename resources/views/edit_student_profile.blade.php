<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/editstudProfile.css') }}" rel="stylesheet">
    <script src="{{ asset('js/studProfile.js') }}"></script>
    <title>Document</title>
</head>
<body>
    <div class="profile-edit-container">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h2>Edit Profile</h2>

    <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Profile Image --}}
        <div class="form-group">
            <label for="image">Profile Image</label>
            <input type="file" name="image" id="image" accept="image/*">
            @error('image') <span class="text-danger">{{ $message }}</span> @enderror
            <br>
            <img id="preview" 
                 src="{{ $profile && $profile->image ? asset('storage/' . $profile->image) : asset('assets/default.png') }}" 
                 alt="Profile Image" 
                 class="profile-preview" style="width:150px; margin-top:10px;">
        </div>

        {{-- Name --}}
        <div class="form-group">
            <label for="name">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" value="{{ old('name', $profile->name ?? '') }}" required>
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="email">Enter Other Accounts <span class="text-danger">*</span></label>
            <input type="email" name="email" value="{{ old('email', $profile->email ?? '') }}" required>
            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        {{-- Phone --}}
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $profile->phone ?? '') }}">
        </div>

        {{-- Bio --}}
        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea name="bio">{{ old('bio', $profile->bio ?? '') }}</textarea>
        </div>

        {{-- Course --}}
        <div class="form-group">
            <label for="course">Course</label>
            <input type="text" name="course" value="{{ old('course', $profile->course ?? '') }}">
        </div>

        {{-- Institution --}}
        <div class="form-group">
            <label for="institution">Institution</label>
            <input type="text" name="institution" value="{{ old('institution', $profile->institution ?? '') }}">
        </div>

        {{-- Address --}}
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="address" value="{{ old('address', $profile->address ?? '') }}">
        </div>

        {{-- City, State, Country, Zip --}}
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" name="city" value="{{ old('city', $profile->city ?? '') }}">
        </div>

        <div class="form-group">
            <label for="state">State</label>
            <input type="text" name="state" value="{{ old('state', $profile->state ?? '') }}">
        </div>

        <div class="form-group">
            <label for="country">Country</label>
            <input type="text" name="country" value="{{ old('country', $profile->country ?? '') }}">
        </div>

        <div class="form-group">
            <label for="zip">Zip</label>
            <input type="text" name="zip" value="{{ old('zip', $profile->zip ?? '') }}">
        </div>

        {{-- Gender --}}
        <div class="form-group">
            <label for="gender">Gender</label>
            <select name="gender">
                <option value="">Select Gender</option>
                <option value="Male" {{ old('gender', $profile->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender', $profile->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ old('gender', $profile->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        {{-- DOB --}}
        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" name="dob" value="{{ old('dob', $profile->dob ?? '') }}">
        </div>

        {{-- Marital Status --}}
        <div class="form-group">
            <label for="marital_status">Marital Status</label>
            <input type="text" name="marital_status" value="{{ old('marital_status', $profile->marital_status ?? '') }}">
        </div>

        {{-- Religion --}}
        <div class="form-group">
            <label for="religion">Religion</label>
            <input type="text" name="religion" value="{{ old('religion', $profile->religion ?? '') }}">
        </div>

        {{-- Nationality --}}
        <div class="form-group">
            <label for="nationality">Nationality</label>
            <input type="text" name="nationality" value="{{ old('nationality', $profile->nationality ?? '') }}">
        </div>

        {{-- Achievements --}}
        <div class="form-group">
            <label for="achievements">Achievements</label>
            <textarea name="achievements">{{ old('achievements', $profile->achievements ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Profile</button>
        <button type="button" class="btn btn-secondary" onclick="window.location='{{ route('student.profile') }}'">Cancel</button>
    </form>
</div>
</body>
</html>