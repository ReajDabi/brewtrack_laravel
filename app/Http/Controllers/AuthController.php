<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Show the login page
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle the login form submission
    public function login(Request $request)
    {
        // Validate inputs
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Try to log in
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if account is active
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Your account has been deactivated.',
                ]);
            }

            // Update last login time
            $user->update(['last_login' => now()]);

            $request->session()->regenerate();

            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('cashier.pos');
            }
        }

        // Login failed
        return back()->withErrors([
            'username' => 'Invalid username or password.',
        ])->onlyInput('username');
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}