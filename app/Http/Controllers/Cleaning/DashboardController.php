<?php

namespace App\Http\Controllers\Cleaning;

use App\Http\Controllers\Controller;
use App\Models\CleaningSchedule;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySchedules = CleaningSchedule::where('assigned_to', auth()->id())
            ->where('scheduled_date', today())
            ->orderBy('status')
            ->get();

        $pendingCount = $todaySchedules->where('status', 'pending')->count();
        $doneCount    = $todaySchedules->where('status', 'done')->count();

        return view('cleaning.dashboard', compact('todaySchedules', 'pendingCount', 'doneCount'));
    }

    public function markDone(CleaningSchedule $schedule)
    {
        if ($schedule->assigned_to !== auth()->id()) abort(403);

        $schedule->update([
            'status'       => 'done',
            'completed_at' => now(),
        ]);

        ActivityLog::record('CLEANING_DONE', 'cleaning',
            "Cleaning selesai membersihkan: {$schedule->area}");

        return back()->with('success', "{$schedule->area} berhasil ditandai selesai dibersihkan.");
    }
}