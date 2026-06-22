<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = LeaveRequest::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('employee.leave.index', compact('leaves'));
    }

    public function create()
    {
        return view('employee.leave.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:cuti,izin,sakit',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|max:500',
        ]);

        $startDate  = Carbon::parse($request->start_date);
        $endDate    = Carbon::parse($request->end_date);
        $totalDays  = $startDate->diffInWeekdays($endDate) + 1;

        $leave = LeaveRequest::create([
            'user_id'    => auth()->id(),
            'type'       => $request->type,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'total_days' => $totalDays,
            'reason'     => $request->reason,
        ]);

        // Notifikasi ke Admin
        UserNotification::sendToRole(
            'admin',
            '📋 Pengajuan Cuti Baru',
            auth()->user()->name . " mengajukan " . $leave->type_label . " dari " .
            $startDate->format('d M Y') . " hingga " . $endDate->format('d M Y') . ".",
            '📋',
            '/admin/cuti'
        );

        return redirect()->route('employee.leave.index')
            ->with('success', 'Pengajuan cuti berhasil dikirim. Menunggu persetujuan Admin.');
    }

    public function cancel(LeaveRequest $leave)
    {
        if ($leave->user_id !== auth()->id() || $leave->status !== 'pending') {
            abort(403);
        }

        $leave->delete();

        return back()->with('success', 'Pengajuan cuti dibatalkan.');
    }
}