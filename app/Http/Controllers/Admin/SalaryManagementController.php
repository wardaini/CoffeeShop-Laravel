<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Salary;
use App\Models\Attendance;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryManagementController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $employees = User::where('role', 'karyawan')
            ->whereHas('employeeProfile', fn($q) => $q->where('verification_status', 'verified'))
            ->with(['employeeProfile', 'salaries' => fn($q) => $q->where('month', $month)->where('year', $year)])
            ->get();

        return view('admin.salary.index', compact('employees', 'month', 'year'));
    }

    /**
     * Generate gaji otomatis untuk semua karyawan bulan ini
     */
    public function generateAll(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $employees = User::where('role', 'karyawan')
            ->whereHas('employeeProfile', fn($q) => $q->where('verification_status', 'verified'))
            ->with('employeeProfile')
            ->get();

        $generated = 0;

        foreach ($employees as $employee) {
            // Hitung total hari hadir bulan ini
            $totalPresent = Attendance::where('user_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->whereIn('status', ['hadir', 'telat'])
                ->count();

            $totalAlpha = Attendance::where('user_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('status', 'alpha')
                ->count();

            $totalTelat = Attendance::where('user_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('status', 'telat')
                ->count();

            $baseSalary = $employee->employeeProfile->base_salary ?? 0;

            // Potongan: alpha = 2% gaji pokok per hari, telat = 0.5%
            $deduction = ($totalAlpha * ($baseSalary * 0.02)) + ($totalTelat * ($baseSalary * 0.005));

            $totalSalary = $baseSalary - $deduction;
            $totalSalary = max(0, $totalSalary); // tidak boleh minus

            Salary::updateOrCreate(
                ['user_id' => $employee->id, 'month' => $month, 'year' => $year],
                [
                    'base_salary'   => $baseSalary,
                    'total_present' => $totalPresent,
                    'deduction'     => $deduction,
                    'total_salary'  => $totalSalary,
                    'status'        => 'draft',
                ]
            );

            $generated++;
        }

        return back()->with('success', "Gaji berhasil digenerate untuk $generated karyawan.");
    }

    /**
     * Update bonus/potongan manual per karyawan
     */
    public function updateBonus(Request $request, Salary $salary)
    {
        $request->validate([
            'bonus'     => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'notes'     => 'nullable|string|max:255',
        ]);

        $bonus     = $request->input('bonus', 0);
        $deduction = $request->input('deduction', 0);

        $salary->update([
            'bonus'        => $bonus,
            'deduction'    => $deduction,
            'total_salary' => max(0, $salary->base_salary + $bonus - $deduction),
            'notes'        => $request->notes,
        ]);

        return back()->with('success', 'Bonus/potongan berhasil diperbarui.');
    }

    /**
     * Submit slip gaji ke Bos untuk diapprove
     */
    public function submitToBos(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $count = Salary::where('month', $month)->where('year', $year)->where('status', 'draft')->count();

        if ($count === 0) {
            return back()->with('error', 'Tidak ada gaji draft untuk disubmit. Generate gaji dulu.');
        }

        // Kirim notifikasi ke Bos
        UserNotification::sendToRole(
            'bos',
            '💰 Gaji Karyawan Menunggu Persetujuan',
            "Admin telah menyiapkan slip gaji untuk " . $this->getBulan($month) . " $year. Silakan review dan approve.",
            '💰',
            '/bos/gaji'
        );

        return back()->with('success', "Slip gaji $count karyawan telah dikirim ke Bos untuk disetujui.");
    }

    private function getBulan($month): string
    {
        $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                  7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        return $bulan[$month] ?? $month;
    }
}