<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function absenIn(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);
        $user = Auth::user();
        $today = Carbon::today();
        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['in_time' => now()->format('H:i:s'), 'in_lat' => $request->lat, 'in_lng' => $request->lng]
        );
        if ($attendance->in_time) {
            return back()->with('absen_in', $attendance->in_time)->with('absen_out', $attendance->out_time);
        }
        $attendance->update([
            'in_time' => now()->format('H:i:s'),
            'in_lat' => $request->lat,
            'in_lng' => $request->lng
        ]);
        return back()->with('absen_in', $attendance->in_time)->with('absen_out', $attendance->out_time);
    }

    public function absenOut(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);
        $user = Auth::user();
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        if (!$attendance || $attendance->out_time) {
            return back()->with('absen_in', $attendance->in_time ?? null)->with('absen_out', $attendance->out_time ?? null);
        }
        $attendance->update([
            'out_time' => now()->format('H:i:s'),
            'out_lat' => $request->lat,
            'out_lng' => $request->lng
        ]);
        return back()->with('absen_in', $attendance->in_time)->with('absen_out', $attendance->out_time);
    }
}
