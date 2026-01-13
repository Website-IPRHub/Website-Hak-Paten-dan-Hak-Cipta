<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>

    @vite(['resources/js/app.js'])
</head>
<body>

    <header class="admin-header">
        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>

        <a href="#" class="user-icon">
            <img src="{{ asset('images/user.png') }}" alt="User">
        </a>
    </header>

    <main class="login-body">
        <div class="login-wrap">
            <h1 class="login-title">Selamat Datang</h1>

            <div class="login-card">

                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf

                    <label>Username</label>
                    <input type="text" name="username" placeholder="Username" required>

                    <label>Password</label>
                    <div class="password-wrap">
                        <input id="password" type="password" name="password" placeholder="Password" required>
                    </div>

                    <button type="submit">Log in</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
