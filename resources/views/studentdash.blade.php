<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/studDash.css') }}" rel="stylesheet">
    <script src="{{ asset('js/studDash.js') }}"></script>
    <title>Document</title>
</head>
<body>
    <div id="alert-container"></div>

    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="assets/logo.png" alt="Logo">
            <h2>Student Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="#" class="active">Dashboard</a></li>
                <li><a href="#">Scholarships</a></li>
                <li><a href="#">Profile</a></li>
                <li><a href="{{ route('student.logout') }}">Logout</a>

            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header>
            <h1>Welcome, John</h1>
            <p>Here’s an overview of your student account.</p>
        </header>

        <section class="dashboard-cards">
            <div class="card">
                <h3>Total Scholarships</h3>
                <p>12</p>
            </div>
            <div class="card">
                <h3>Approved</h3>
                <p>5</p>
            </div>
            <div class="card">
                <h3>Pending</h3>
                <p>4</p>
            </div>
            <div class="card">
                <h3>Rejected</h3>
                <p>3</p>
            </div>
        </section>

        <section class="recent-activity">
            <h2>Recent Applications</h2>
            <table>
                <thead>
                    <tr>
                        <th>Scholarship</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Academic Excellence</td>
                        <td>Approved</td>
                        <td>Mar 1, 2026</td>
                    </tr>
                    <tr>
                        <td>Sports Scholarship</td>
                        <td>Pending</td>
                        <td>Mar 5, 2026</td>
                    </tr>
                    <tr>
                        <td>Arts Grant</td>
                        <td>Rejected</td>
                        <td>Feb 20, 2026</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>