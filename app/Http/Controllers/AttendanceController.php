<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\EmployeeProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Halaman scan QR (statis, sama untuk semua karyawan).
     * QR ini cukup berisi URL halaman pilih-karyawan, jadi
     * "scan" di sini sebenarnya cukup tombol langsung,
     * tapi kita tetap pakai scanner untuk konsistensi & bisa diprint.
     */
    public function scanPage()
    {
        return view('attendance.scan');
    }

    /**
     * Setelah QR statis di-scan, tampilkan daftar karyawan aktif untuk dipilih.
     */
    public function selectEmployee()
    {
        $employees = EmployeeProfile::with('user')
            ->where('verification_status', 'verified')
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->get();

        return view('attendance.select-employee', compact('employees'));
    }

    /**
     * Halaman verifikasi wajah setelah karyawan memilih namanya.
     */
    public function facePage($employeeCode)
    {
        $profile = EmployeeProfile::where('employee_code', $employeeCode)
            ->where('verification_status', 'verified')
            ->firstOrFail();

        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $profile->user_id)
            ->where('date', $today)
            ->first();

        $mode = (!$attendance || !$attendance->clock_in) ? 'in' : 'out';

        return view('attendance.face', compact('profile', 'mode', 'attendance'));
    }

    /**
     * Proses simpan absensi (clock in / out) dengan foto wajah.
     */
    public function process(Request $request)
    {
        $request->validate([
            'employee_code' => 'required|string',
            'photo'         => 'required|string',
            'mode'          => 'required|in:in,out',
        ]);

        $profile = EmployeeProfile::where('employee_code', $request->employee_code)
            ->where('verification_status', 'verified')
            ->firstOrFail();

        $imageData = $request->photo;
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageBinary = base64_decode($imageData);

        $filename = 'attendance/' . $profile->employee_code . '_' . now()->format('YmdHis') . '.png';
        Storage::disk('public')->put($filename, $imageBinary);

        $today = Carbon::today();
        $now   = Carbon::now();

        $attendance = Attendance::firstOrNew([
            'user_id' => $profile->user_id,
            'date'    => $today,
        ]);

        if ($request->mode === 'in') {
            if ($attendance->exists && $attendance->clock_in) {
                return response()->json(['success' => false, 'message' => 'Kamu sudah absen masuk hari ini.']);
            }

            $attendance->clock_in = $now;
            $attendance->clock_in_photo = $filename;
            $attendance->status = $now->format('H:i') > '08:30' ? 'telat' : 'hadir';
        } else {
            if (!$attendance->exists || !$attendance->clock_in) {
                return response()->json(['success' => false, 'message' => 'Kamu belum absen masuk hari ini.']);
            }
            if ($attendance->clock_out) {
                return response()->json(['success' => false, 'message' => 'Kamu sudah absen keluar hari ini.']);
            }

            $attendance->clock_out = $now;
            $attendance->clock_out_photo = $filename;
        }

        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => $request->mode === 'in'
                ? 'Absen masuk berhasil! Selamat bekerja, ' . $profile->user->name . '.'
                : 'Absen keluar berhasil! Sampai jumpa, ' . $profile->user->name . '.',
        ]);
    }
}