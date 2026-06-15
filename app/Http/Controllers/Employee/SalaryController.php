<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\Attendance;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index()
    {
        $salaries = Salary::where('user_id', auth()->id())
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('employee.salary.index', compact('salaries'));
    }

    public function attendanceHistory()
    {
        $attendances = Attendance::where('user_id', auth()->id())
            ->orderByDesc('date')
            ->paginate(15);

        return view('employee.attendance.index', compact('attendances'));
    }
}