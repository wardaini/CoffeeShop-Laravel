<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('user')->orderByDesc('date');

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $attendances = $query->paginate(20)->withQueryString();
        $employees = User::where('role', 'karyawan')->orderBy('name')->get();

        return view('it.attendance.index', compact('attendances', 'employees'));
    }

    public function create()
    {
        $employees = User::where('role', 'karyawan')
            ->whereHas('employeeProfile', fn($q) => $q->where('verification_status', 'verified'))
            ->orderBy('name')
            ->get();

        return view('it.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'date'      => 'required|date',
            'clock_in'  => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'status'    => 'required|in:hadir,telat,izin,alpha',
            'notes'     => 'nullable|string|max:255',
        ]);

        $attendance = Attendance::updateOrCreate(
            ['user_id' => $request->user_id, 'date' => $request->date],
            [
                'clock_in'  => $request->clock_in ? $request->date . ' ' . $request->clock_in : null,
                'clock_out' => $request->clock_out ? $request->date . ' ' . $request->clock_out : null,
                'status'    => $request->status,
                'notes'     => $request->notes,
            ]
        );

        return redirect()->route('it.attendance.index')->with('success', 'Absensi berhasil disimpan.');
    }

    public function edit(Attendance $attendance)
    {
        $employees = User::where('role', 'karyawan')->orderBy('name')->get();
        return view('it.attendance.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'clock_in'  => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'status'    => 'required|in:hadir,telat,izin,alpha',
            'notes'     => 'nullable|string|max:255',
        ]);

        $attendance->update([
            'clock_in'  => $request->clock_in ? $attendance->date->format('Y-m-d') . ' ' . $request->clock_in : null,
            'clock_out' => $request->clock_out ? $attendance->date->format('Y-m-d') . ' ' . $request->clock_out : null,
            'status'    => $request->status,
            'notes'     => $request->notes,
        ]);

        return redirect()->route('it.attendance.index')->with('success', 'Absensi berhasil diperbarui.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return back()->with('success', 'Data absensi dihapus.');
    }
}