<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmployeeProfile;
use App\Models\ActivityLog;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserEditController extends Controller
{
    public function create()
    {
        return view('it.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|unique:users,phone',
            'role'     => 'required|in:admin,kasir,barista,dapur,kurir,cleaning,it,bos',
            'password' => 'required|string|min:8',
            'position' => 'nullable|string',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => in_array($request->role, ['admin', 'it', 'bos']) ? $request->role : 'karyawan',
            'password' => Hash::make($request->password),
            'is_active'=> true,
        ]);

        // Kalau bukan staff, buat employee profile
        if (!in_array($request->role, ['admin', 'it', 'bos'])) {
            EmployeeProfile::create([
                'user_id'             => $user->id,
                'position'            => $request->position ?? $request->role,
                'verification_status' => 'verified',
                'joined_at'           => now(),
            ]);
        }

        ActivityLog::record('CREATE_USER', 'it', "IT membuat akun baru: {$user->name} ({$user->role})");

        return redirect()->route('it.dashboard')->with('success', "Akun {$user->name} berhasil dibuat.");
    }

    public function edit(User $user)
    {
        return view('it.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|unique:users,phone,' . $user->id,
            'position' => 'nullable|string',
        ]);

        $oldName = $user->name;
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Update posisi di employee profile
        if ($user->employeeProfile && $request->position) {
            $user->employeeProfile->update(['position' => $request->position]);
        }

        ActivityLog::record('UPDATE_USER', 'it', "IT mengupdate data user: {$oldName} → {$user->name}");

        return redirect()->route('it.dashboard')->with('success', "Data {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::record('DELETE_USER', 'it', "IT menghapus akun: {$name}");

        return redirect()->route('it.dashboard')->with('success', "Akun {$name} berhasil dihapus.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $newPassword = Str::random(10);
        $user->update(['password' => Hash::make($newPassword)]);

        ActivityLog::record('RESET_PASSWORD', 'it', "IT mereset password: {$user->name}");

        // Kirim notifikasi ke user
        UserNotification::send(
            $user->id,
            '🔑 Password Direset',
            "Password akun kamu telah direset oleh IT. Password baru: {$newPassword}. Segera ganti password setelah login.",
            '🔑'
        );

        return back()->with('success', "Password {$user->name} direset. Password baru: {$newPassword}");
    }
}