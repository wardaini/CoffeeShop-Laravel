<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Attendance;
use App\Models\Salary;
use App\Models\ActivityLog;

class ExportController extends Controller
{
    public function index()
    {
        return view('it.export');
    }

    public function exportUsers()
    {
        $users = User::with('employeeProfile')->get();

        ActivityLog::record('EXPORT', 'it', 'IT export data semua user');

        return $this->streamCsv('data-users.csv', function ($file) use ($users) {
            fputcsv($file, ['ID', 'Nama', 'Email', 'No HP', 'Role', 'Posisi', 'Status', 'Bergabung']);
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id, $user->name, $user->email, $user->phone ?? '-',
                    ucfirst($user->role),
                    $user->employeeProfile->position ?? '-',
                    $user->is_active ? 'Aktif' : 'Nonaktif',
                    $user->created_at->format('d/m/Y'),
                ]);
            }
        });
    }

    public function exportOrders()
    {
        $orders = Order::with('items.product')->latest()->get();

        ActivityLog::record('EXPORT', 'it', 'IT export data semua order');

        return $this->streamCsv('data-orders.csv', function ($file) use ($orders) {
            fputcsv($file, ['Kode', 'Nama', 'Tipe', 'Pembayaran', 'Status Bayar', 'Total', 'Ongkir', 'Grand Total', 'Tanggal']);
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_code, $order->customer_name,
                    $order->order_type_label, $order->payment_method_label,
                    $order->payment_status === 'paid' ? 'Lunas' : 'Belum',
                    $order->total_price, $order->delivery_fee, $order->grand_total,
                    $order->created_at->format('d/m/Y H:i'),
                ]);
            }
        });
    }

    public function exportAttendances()
    {
        $attendances = Attendance::with('user')->orderBy('date')->get();

        ActivityLog::record('EXPORT', 'it', 'IT export data absensi');

        return $this->streamCsv('data-absensi.csv', function ($file) use ($attendances) {
            fputcsv($file, ['Tanggal', 'Nama', 'Jam Masuk', 'Jam Keluar', 'Durasi', 'Status']);
            foreach ($attendances as $att) {
                fputcsv($file, [
                    $att->date->format('d/m/Y'),
                    $att->user->name,
                    $att->clock_in?->format('H:i') ?? '-',
                    $att->clock_out?->format('H:i') ?? '-',
                    $att->work_duration ?? '-',
                    ucfirst($att->status),
                ]);
            }
        });
    }

    public function exportSalaries()
    {
        $salaries = Salary::with('user.employeeProfile')->orderByDesc('year')->orderByDesc('month')->get();

        ActivityLog::record('EXPORT', 'it', 'IT export data gaji');

        return $this->streamCsv('data-gaji.csv', function ($file) use ($salaries) {
            fputcsv($file, ['Nama', 'Posisi', 'Periode', 'Gaji Pokok', 'Bonus', 'Potongan', 'Total', 'Status']);
            foreach ($salaries as $salary) {
                fputcsv($file, [
                    $salary->user->name,
                    $salary->user->employeeProfile->position ?? '-',
                    $salary->period_label,
                    $salary->base_salary, $salary->bonus,
                    $salary->deduction, $salary->total_salary,
                    $salary->status === 'paid' ? 'Lunas' : 'Draft',
                ]);
            }
        });
    }

    private function streamCsv(string $filename, callable $callback)
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($callback) {
            $file = fopen('php://output', 'w');
            $callback($file);
            fclose($file);
        }, 200, $headers);
    }
}