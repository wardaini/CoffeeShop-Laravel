<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        $pending  = LeaveRequest::with('user')
            ->where('status', 'pending')
            ->whereHas('user', fn($q) => $q->whereIn('role', ['karyawan']))
            ->orderByDesc('created_at')
            ->get();

        $history = LeaveRequest::with('user')
            ->whereIn('status', ['approved', 'rejected'])
            ->whereHas('user', fn($q) => $q->whereIn('role', ['karyawan']))
            ->orderByDesc('updated_at')
            ->take(20)
            ->get();

        return view('admin.leave.index', compact('pending', 'history'));
    }

    public function approve(LeaveRequest $leave)
    {
        $leave->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
        ]);

        // Buat record absensi otomatis untuk hari-hari cuti
        $start = $leave->start_date->copy();
        $end   = $leave->end_date->copy();

        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                Attendance::updateOrCreate(
                    ['user_id' => $leave->user_id, 'date' => $start->format('Y-m-d')],
                    ['status' => 'izin', 'notes' => ucfirst($leave->type) . ': ' . $leave->reason]
                );
            }
            $start->addDay();
        }

        // Notifikasi ke karyawan
        UserNotification::send(
            $leave->user_id,
            '✅ Pengajuan Cuti Disetujui',
            "Pengajuan " . $leave->type_label . " kamu dari " .
            $leave->start_date->format('d M Y') . " hingga " .
            $leave->end_date->format('d M Y') . " telah disetujui.",
            '✅',
            '/karyawan/cuti'
        );

        return back()->with('success', 'Pengajuan cuti disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        $request->validate(['rejection_reason' => 'required|string|max:255']);

        $leave->update([
            'status'           => 'rejected',
            'approved_by'      => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Notifikasi ke karyawan
        UserNotification::send(
            $leave->user_id,
            '❌ Pengajuan Cuti Ditolak',
            "Pengajuan " . $leave->type_label . " kamu ditolak. Alasan: " . $request->rejection_reason,
            '❌',
            '/karyawan/cuti'
        );

        return back()->with('success', 'Pengajuan cuti ditolak.');
    }
}