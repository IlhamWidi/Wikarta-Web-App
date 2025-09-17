<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceMonitorController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $query = User::where('user_type', 'ADMIN');
        if ($request->filled('nama')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nama . '%')
                    ->orWhere('phone_number', 'like', '%' . $request->nama . '%');
            });
        }
        $users = $query->orderBy('id')->paginate(10)->withQueryString();
        $attendances = Attendance::where('date', $date)->whereIn('user_id', $users->pluck('id'))->get()->keyBy('user_id');
        return view('pages.attendance_monitor.index', compact('users', 'attendances', 'date', 'request'));
    }

    public function show($id, Request $request)
    {
        $user = User::findOrFail($id);
        $date = $request->input('date', null);
        $attendanceQuery = Attendance::where('user_id', $user->id);
        if ($date) {
            $attendanceQuery->where('date', $date);
        }
        $attendances = $attendanceQuery->orderBy('date', 'desc')->paginate(10)->withQueryString();
        return view('pages.attendance_monitor.show', compact('user', 'attendances', 'date', 'request'));
    }
}
