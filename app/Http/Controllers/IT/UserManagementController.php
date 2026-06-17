<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmployeeProfile;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('employeeProfile')->orderBy('role')->paginate(20);
        return view('it.users.index', compact('users'));
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', 'User ' . $user->name . ' berhasil ' . $status . '.');
    }

    public function verifyEmployee(EmployeeProfile $profile)
    {
        $profile->update(['verification_status' => 'verified']);
        $profile->user->update(['is_active' => true]);

        return back()->with('success', 'Karyawan ' . $profile->user->name . ' berhasil diverifikasi.');
    }
}