<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmployeeProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EmployeeAuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.employee.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'required|string|unique:users,phone|min:10|max:15',
            'password'    => 'required|string|min:8|confirmed',
            'position'    => 'required|string|max:50',
            'ktp_number'  => 'required|string|max:20',
            'ktp_photo'   => 'required|image|max:2048',
            'face_photo'  => 'required|image|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $ktpPath  = $request->file('ktp_photo')->store('employees/ktp', 'public');
            $facePath = $request->file('face_photo')->store('employees/face', 'public');

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'password' => Hash::make($request->password),
                'role'     => 'karyawan',
                'photo'    => $facePath,
            ]);

            EmployeeProfile::create([
                'user_id'             => $user->id,
                'position'            => $request->position,
                'ktp_number'          => $request->ktp_number,
                'ktp_photo'           => $ktpPath,
                'face_photo'          => $facePath,
                'verification_status' => 'pending',
                'joined_at'           => now(),
            ]);
        });

        return redirect()->route('employee.login')
            ->with('success', 'Pendaftaran berhasil! Akun kamu menunggu verifikasi dari Admin/IT sebelum bisa login.');
    }

    public function showLogin()
    {
        return view('auth.employee.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string', // email atau no hp
            'password' => 'required|string',
        ]);

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::where($field, $request->login)
            ->where('role', 'karyawan')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Email/No HP atau password salah.']);
        }

        $profile = $user->employeeProfile;

        if ($profile && $profile->verification_status !== 'verified') {
            return back()->withErrors(['login' => 'Akun kamu belum diverifikasi. Status: ' . $profile->verification_status]);
        }

        if (!$user->is_active) {
            return back()->withErrors(['login' => 'Akun kamu dinonaktifkan. Hubungi admin.']);
        }

        Auth::login($user, $request->boolean('remember'));

        return redirect()->route('employee.dashboard')->with('success', 'Selamat datang, ' . $user->name);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}