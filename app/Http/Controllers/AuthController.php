<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // Redirect berdasarkan role
            if (Auth::check() && Auth::user()->role == "admin") {
                return redirect('/admin/dashboard');
            }
            if (Auth::check() && Auth::user()->role == "petugas") {
                return redirect('/petugas/dashboard');
            }
            if (Auth::attempt($credentials)) {
                ActivityLog::record('Login', 'Pengguna melakukan login');
                $request->session()->regenerate();
            }
            return redirect('/peminjam/dashboard');
        }
        return back()->withErrors(['email' => 'Login gagal.']);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        ActivityLog::record('Logout', 'Pengguna melakukan logout');

        return redirect('/login');
    }
}
