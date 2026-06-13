<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.customer.login');
    }

    /**
     * Step 1: input no HP, kirim OTP (simulasi)
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
        ]);

        $otp = rand(1000, 9999);

        // Simulasi kirim OTP - simpan di session (production: kirim via SMS gateway)
        Session::put('otp_phone', $request->phone);
        Session::put('otp_code', $otp);
        Session::put('otp_expires', now()->addMinutes(5));

        // TODO: integrasikan SMS Gateway (Twilio/Vonage/Zenziva) di sini
        session()->flash('otp_debug', "Kode OTP kamu: $otp (simulasi)");

        return redirect()->route('customer.otp.form');
    }

    public function showOtpForm()
    {
        if (!Session::has('otp_phone')) {
            return redirect()->route('customer.login');
        }

        return view('auth.customer.otp');
    }

    /**
     * Step 2: verifikasi OTP, login atau register otomatis
     */
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:4']);

        if (now()->greaterThan(Session::get('otp_expires'))) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa.']);
        }

        if ($request->otp != Session::get('otp_code')) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        $phone = Session::get('otp_phone');

        $user = User::firstOrCreate(
            ['phone' => $phone],
            [
                'name' => 'Pelanggan ' . substr($phone, -4),
                'email' => $phone . '@coffeeshop.local',
                'password' => Hash::make(Str::random(16)),
                'role' => 'pelanggan',
            ]
        );

        Auth::login($user, true);
        Session::forget(['otp_phone', 'otp_code', 'otp_expires']);

        return redirect()->route('home')->with('success', 'Login berhasil! Selamat datang, ' . $user->name);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}