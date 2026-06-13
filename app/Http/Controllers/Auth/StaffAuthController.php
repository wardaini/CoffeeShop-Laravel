<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.staff.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->whereIn('role', ['admin', 'bos', 'it'])
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Email atau password salah.']);
        }

        if (!$user->is_active) {
            return back()->withErrors(['email' => 'Akun dinonaktifkan.']);
        }

        Auth::login($user, $request->boolean('remember'));

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'bos'   => redirect()->route('bos.dashboard'),
            'it'    => redirect()->route('it.dashboard'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}