<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="resources/css/logReg.css" rel="stylesheet">
    <title>Document</title>
</head>
<body style="display: flex; justify-content: center; align-items: center; height: 100vh;" >
    <div style=" display: inline-block; gap: 20px; border: 1px solid black; padding: 20px;">
        <h2>Register</h2>
        <form action="/register" method="POST">
            @csrf
            <input type="text" name="first_name" placeholder="First name" required>
            <input type="text" name="middle_name" placeholder="Middle name">
            <input type="text" name="last_name" placeholder="Last name" required>
            <input type="text" name="email" placeholder="email" required>
            <input type="password" name="password" placeholder="password" required>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>
    </div>
    <div style=" display: inline-block; gap: 20px; border: 1px solid black; padding: 20px;">
        <h2>Login</h2>
        <form action="/login" method="POST">
            @csrf
            <input type="text" name="email" placeholder="email" required>
            <input type="password" name="password" placeholder="password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>