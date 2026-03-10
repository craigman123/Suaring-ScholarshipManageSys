<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/logReg.css') }}" rel="stylesheet">
    <script src="{{ asset('js/logReg.js') }}"></script>
    <title>Document</title>
</head>
<body>

<div class="bg bg1"></div>
<div class="bg bg2"></div>
<div class="bg bg3"></div>
<div class="bg bg4"></div>
<div class="bg bg5"></div>

<nav class="logo-container">
    <img class="logo-img" src="assets/logo.png" alt="LUMA Logo">
</nav>

<section class="login-section">
    @if(session('success'))
    <div class="alert alert-success show">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error show">{{ session('error') }}</div>
    @endif

    @if(session('message'))
        <div class="alert alert-info show">{{ session('message') }}</div>
    @endif

    <div class="register-container hidden">
        <form action="{{ route('register.submit') }}" method="post">
            @csrf
            <h2>HELLO NEWUSER</h2>

            <div class="input-box name">
                <input class="first" type="text" name="first_name" placeholder="Firstname" required>
                <input class="middle" type="text" name="middle_name" placeholder="Middlename">
                <input class="last" type="text" name="last_name" placeholder="Lastname" required>
            </div>

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="input-box">
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
            </div>

            <button type="submit" class="btn">REGISTER</button>

            <div class="create-account">
                Already have an account?<br>
                <button type="button" class="create-btn" id="showLogin">
                    Login
                </button>
            </div>

            <div class="social-register">
                <img class="img1" src="assets/google.png">
                <strong>Continue with Google</strong>
            </div>
        </form>
    </div>

    <div class="login-container">
        <form action="{{ route('login.submit') }}" method="post">
            @csrf
            <h2>WELCOME BACK</h2>

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="forgot">Forgot Password?</div>

            <button type="submit" class="btn">LOG IN</button>

            <div class="create-account">
                Don't have an account?<br>
                <button type="button" class="create-btn" id="showRegister">
                    Create Account
                </button>
            </div>

            <div class="social-login">
                <img class="img1" src="assets/google.png">
                <strong>Continue with Google</strong>
            </div>
        </form>
    </div>
</section>

</body>
</html>