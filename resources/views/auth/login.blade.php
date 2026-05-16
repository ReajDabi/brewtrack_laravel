<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — BrewTrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f0e8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* The white card that holds both panels */
        .login-card {
            display: flex;
            width: 720px;
            max-width: 95vw;
            min-height: 480px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        /* LEFT panel — brown branded area */
        .login-left {
            background: #6F4E37;
            flex: 1;
            padding: 40px 32px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .logo-circle {
            width: 80px; height: 80px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; color: white;
            margin-bottom: 20px;
        }
        .login-left h1 { color: white; font-size: 28px; font-weight: 700; }
        .login-left .tagline { color: rgba(255,255,255,0.75); font-size: 13px; margin-top: 6px; }
        .feature-list { margin-top: 32px; width: 100%; }
        .feature-item {
            display: flex; align-items: center; gap: 12px;
            background: rgba(255,255,255,0.12);
            border-radius: 10px; padding: 12px 16px;
            margin-bottom: 10px;
            color: white; font-size: 13px; font-weight: 500;
            text-align: left;
        }
        .feature-item i { font-size: 15px; opacity: 0.9; }

        /* RIGHT panel — white form area */
        .login-right {
            background: white;
            flex: 1;
            padding: 40px 36px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-right h2 { font-size: 24px; font-weight: 700; color: #1a1a2e; }
        .login-right .subtitle { color: #6b7280; font-size: 13px; margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: flex; align-items: center; gap: 6px;
            font-size: 13px; font-weight: 500;
            color: #374151; margin-bottom: 7px;
        }
        .form-group input {
            width: 100%; padding: 12px 14px;
            border: 2px solid #e5e7eb; border-radius: 8px;
            font-size: 14px; font-family: 'Poppins', sans-serif;
            transition: border-color 0.2s;
        }
        .form-group input:focus { outline: none; border-color: #6F4E37; }
        /* Password field with show/hide button */
        .password-wrap { position: relative; }
        .password-wrap input { padding-right: 44px; }
        .toggle-pw {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #9ca3af; cursor: pointer; font-size: 14px;
        }
        .btn-login {
            width: 100%; padding: 13px;
            background: #6F4E37; color: white;
            border: none; border-radius: 8px;
            font-size: 15px; font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer; transition: background 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover { background: #5a3d2b; }
        .alert-error {
            background: #fee2e2; color: #991b1b;
            border: 1px solid #fecaca;
            border-radius: 8px; padding: 12px 14px;
            font-size: 13px; margin-bottom: 16px;
            display: flex; align-items: center; gap: 8px;
        }

        /* --- MOBILE RESPONSIVENESS --- */
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column; /* Stacks the panels vertically */
                height: auto;
                min-height: auto;
            }
            
            .login-left {
                padding: 30px 20px;
                /* Optional: Hide features on mobile to save space */
                /* .feature-list { display: none; } */
            }

            .logo-circle {
    width: 90px; /* Slightly increased for a better look, adjust as needed */
    height: 90px;
    background: rgba(255,255,255,0.15);
    border-radius: 50%;
    display: flex; 
    align-items: center; 
    justify-content: center;
    margin-bottom: 20px;
    overflow: hidden; /* IMPORTANT: This stops the square image corners from poking out */
}

/* Add this new rule specifically for the image */
.logo-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* This makes sure the image fills the circle without stretching */
    object-position: center; /* This guarantees the logo is dead-center */
}

            .login-left h1 { font-size: 24px; }
            
            .login-right {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">

        <!-- Left panel -->
        <div class="login-left">
            <div class="logo-circle">
                <img src="{{ asset('icons/Logo.png') }}" alt="BrewTrack Logo" style="width: 100%; height: 100%;">
            </div>
            <h1>BrewTrack</h1>
            <p class="tagline">Coffee Shop Management System</p>

            <div class="feature-list">
                <div class="feature-item">
                    <i class="fas fa-utensils"></i> Order Management
                </div>
                <div class="feature-item">
                    <i class="fas fa-boxes"></i> Inventory Tracking
                </div>
                <div class="feature-item">
                    <i class="fas fa-chart-line"></i> Sales Analytics
                </div>
            </div>
        </div>

        <!-- Right panel: Login form -->
        <div class="login-right">
            <h2>Welcome Back</h2>
            <p class="subtitle">Please sign in to continue</p>

            {{-- Show error if login failed --}}
            @if($errors->any())
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- The login form. @csrf adds a hidden security token --}}
            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="form-group">
                    <label>
                        <i class="fas fa-user"></i> Username
                    </label>
                    {{-- old('username') re-fills the field if form was submitted with errors --}}
                    <input type="text" name="username" value="{{ old('username') }}"
                           placeholder="Enter your username" required autofocus>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="password-wrap">
                        <input type="password" id="pw" name="password"
                               placeholder="Enter your password" required>
                        <button type="button" class="toggle-pw" onclick="togglePw()">
                            <i class="fas fa-eye" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePw() {
            const input = document.getElementById('pw');
            const icon  = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
    </script>
</body>
</html>