<?php

namespace App\Http\Controllers\Stats\Visit;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->subDay()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->toDateString());

        $totalVisits = Visit::whereBetween('created_at', [$startDate, $endDate])->count();
        $uniqueVisitors = Visit::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('ip_address')
            ->count('ip_address');

        return response()->json([
            'total_visits' => $totalVisits,
            'unique_visitors' => $uniqueVisitors,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
}
