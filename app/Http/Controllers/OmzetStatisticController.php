<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Branch;
use Carbon\Carbon;

class OmzetStatisticController extends Controller
{
    /**
     * Menampilkan halaman statistik omzet
     */
    public function index()
    {
        $branches = Branch::all();
        return view('omzet-statistic.index', compact('branches'));
    }

    /**
     * Mengambil data statistik omzet untuk chart
     */
    public function getChartData(Request $request)
    {
        $startDate = Carbon::parse($request->start_date)->startOfMonth();
        $endDate = Carbon::parse($request->end_date)->endOfMonth();

        $branches = Branch::all();
        $months = [];
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            $months[] = $currentDate->format('M Y');
            $currentDate->addMonth();
        }

        $series = [];
        foreach ($branches as $branch) {
            $data = [];
            $currentDate = clone $startDate;

            while ($currentDate <= $endDate) {
                $omzet = Invoice::where('branch_id', $branch->id)
                    ->where('invoice_status', 'SETTLEMENT')
                    ->whereYear('paid_off_date', $currentDate->year)
                    ->whereMonth('paid_off_date', $currentDate->month)
                    ->sum('amount');

                $data[] = (float)$omzet;
                $currentDate->addMonth();
            }

            $series[] = [
                'name' => $branch->name,
                'data' => $data
            ];
        }

        return response()->json([
            'categories' => $months,
            'series' => $series
        ]);
    }
}
