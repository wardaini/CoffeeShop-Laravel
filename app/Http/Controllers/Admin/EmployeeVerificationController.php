<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use Illuminate\Http\Request;

class EmployeeVerificationController extends Controller
{
    /**
     * Hanya menampilkan karyawan yang SUDAH diverifikasi oleh IT.
     * Verifikasi murni jadi tugas IT.
     */
    public function index()
    {
        $verified = EmployeeProfile::with('user')->where('verification_status', 'verified')->get();

        return view('admin.employees.index', compact('verified'));
    }

    public function updateSalary(Request $request, EmployeeProfile $profile)
    {
        $request->validate(['base_salary' => 'required|numeric|min:0']);
        $profile->update(['base_salary' => $request->base_salary]);

        return back()->with('success', 'Gaji pokok berhasil diperbarui.');
    }
}