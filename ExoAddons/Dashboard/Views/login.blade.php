<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExoAddons — Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('ExoAddons/Dashboard/css/dashboard.css') }}">
</head>
<body class="login-page">

<div class="exo-login-bg">
    <div class="exo-login-orb orb-1"></div>
    <div class="exo-login-orb orb-2"></div>
</div>

<div class="exo-login-wrap">
    <div class="exo-login-card">

        <div class="exo-login-brand">
            <div class="exo-login-icon">
                <i class="fas fa-puzzle-piece"></i>
            </div>
            <h1 class="exo-login-title">ExoAddons</h1>
            <p class="exo-login-sub">Admin Dashboard</p>
        </div>

        @if($errors->any())
            <div class="exo-alert exo-alert-error" style="margin-bottom:1.25rem">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('exodash.authenticate') }}" class="exo-login-form">
            @csrf

            <div class="exo-form-group">
                <label class="exo-label">Username</label>
                <div class="exo-input-wrap">
                    <i class="fas fa-user exo-input-icon"></i>
                    <input type="text" name="username" class="exo-input" placeholder="Enter admin username"
                           value="{{ old('username') }}" required autofocus>
                </div>
            </div>

            <div class="exo-form-group">
                <label class="exo-label">Password</label>
                <div class="exo-input-wrap">
                    <i class="fas fa-lock exo-input-icon"></i>
                    <input type="password" name="password" class="exo-input" placeholder="Enter password" required>
                </div>
            </div>

            <button type="submit" class="exo-btn exo-btn-primary w-full">
                <i class="fas fa-arrow-right-to-bracket"></i>
                Sign In
            </button>
        </form>

        <p class="exo-login-hint">
            <i class="fas fa-shield-halved"></i>
            Admin role required to access this panel.
        </p>
    </div>
</div>

</body>
</html>
