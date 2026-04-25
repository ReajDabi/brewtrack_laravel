<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Access Denied — BrewTrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f0e8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .box {
            background: white;
            border-radius: 20px;
            padding: 60px;
            max-width: 450px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .box i { font-size: 80px; color: #ef4444; margin-bottom: 24px; }
        .box h1 { font-size: 30px; margin-bottom: 12px; }
        .box p  { color: #6b7280; margin-bottom: 28px; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 24px; background: #6F4E37; color: white;
            border-radius: 8px; text-decoration: none; font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="box">
        <i class="fas fa-lock"></i>
        <h1>Access Denied</h1>
        <p>You don't have permission to view this page.</p>
        @auth
            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('cashier.pos') }}"
               class="btn">
                <i class="fas fa-arrow-left"></i> Go Back
            </a>
        @else
            <a href="{{ route('login') }}" class="btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        @endauth
    </div>
</body>
</html>