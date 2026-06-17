<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeVerificationController extends Controller
{
    public function index()
    {
        $pending  = EmployeeProfile::with('user')->where('verification_status', 'pending')->get();
        $verified = EmployeeProfile::with('user')->where('verification_status', 'verified')->get();
        $rejected = EmployeeProfile::with('user')->where('verification_status', 'rejected')->get();

        return view('admin.employees.index', compact('pending', 'verified', 'rejected'));
    }

    public function show(EmployeeProfile $profile)
    {
        $profile->load('user');
        return view('admin.employees.show', compact('profile'));
    }

    public function verify(EmployeeProfile $profile)
    {
        $profile->update(['verification_status' => 'verified']);
        $profile->user->update(['is_active' => true]);

        return back()->with('success', 'Karyawan ' . $profile->user->name . ' berhasil diverifikasi.');
    }

    public function reject(Request $request, EmployeeProfile $profile)
    {
        $profile->update(['verification_status' => 'rejected']);
        $profile->user->update(['is_active' => false]);

        return back()->with('success', 'Karyawan ' . $profile->user->name . ' ditolak.');
    }

    public function updateSalary(Request $request, EmployeeProfile $profile)
    {
        $request->validate(['base_salary' => 'required|numeric|min:0']);
        $profile->update(['base_salary' => $request->base_salary]);

        return back()->with('success', 'Gaji pokok berhasil diperbarui.');
    }
}