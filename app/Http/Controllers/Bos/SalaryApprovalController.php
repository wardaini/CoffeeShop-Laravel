<?php

namespace App\Http\Controllers\Bos;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class SalaryApprovalController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $salaries = Salary::with('user.employeeProfile')
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('status')
            ->get();

        $totalAmount = $salaries->sum('total_salary');

        return view('bos.salary', compact('salaries', 'month', 'year', 'totalAmount'));
    }

    public function approveAll(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $salaries = Salary::where('month', $month)
            ->where('year', $year)
            ->where('status', 'draft')
            ->get();

        foreach ($salaries as $salary) {
            $salary->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);

            // Notifikasi ke masing-masing karyawan
            UserNotification::send(
                $salary->user_id,
                '💰 Gaji Kamu Sudah Dibayar!',
                "Gaji kamu untuk periode " . $salary->period_label . " sebesar " . $salary->formatted_total . " telah dibayarkan.",
                '💰',
                '/karyawan/gaji'
            );
        }

        return back()->with('success', 'Semua gaji berhasil disetujui dan karyawan telah diberitahu.');
    }

    public function approveSingle(Salary $salary)
    {
        $salary->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        UserNotification::send(
            $salary->user_id,
            '💰 Gaji Kamu Sudah Dibayar!',
            "Gaji kamu untuk periode " . $salary->period_label . " sebesar " . $salary->formatted_total . " telah dibayarkan.",
            '💰',
            '/karyawan/gaji'
        );

        return back()->with('success', 'Gaji berhasil disetujui.');
    }

    public function downloadSlipPdf(Salary $salary)
    {
        $salary->load('user.employeeProfile');

        $pdf = \PDF::loadView('bos.salary-slip-pdf', compact('salary'));

        return $pdf->download('slip-gaji-' . $salary->user->name . '-' . $salary->period_label . '.pdf');
    }
}