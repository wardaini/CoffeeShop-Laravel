<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CleaningSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class CleaningManagementController extends Controller
{
    public function index()
    {
        $schedules = CleaningSchedule::with('assignedTo')
            ->whereDate('scheduled_date', today())
            ->orderBy('status')
            ->get();

        $cleaners = User::where('role', 'karyawan')
            ->whereHas('employeeProfile', fn($q) => $q->where('position', 'Cleaning Service'))
            ->where('is_active', true)
            ->get();

        return view('admin.cleaning.index', compact('schedules', 'cleaners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'area'           => 'required|string|max:100',
            'assigned_to'    => 'required|exists:users,id',
            'frequency'      => 'required|in:daily,per_shift,weekly',
            'scheduled_date' => 'required|date',
        ]);

        CleaningSchedule::create($request->only(['area', 'assigned_to', 'frequency', 'scheduled_date']));

        return back()->with('success', 'Jadwal kebersihan berhasil ditambahkan.');
    }

    public function destroy(CleaningSchedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal dihapus.');
    }
}