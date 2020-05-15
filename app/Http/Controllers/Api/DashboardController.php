<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Http\ApiResponse;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Review;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'from' => 'nullable|date',
            'to' => 'nullable|required_with:from|date|after:' . Carbon::parse($request->get('from'))->toDateString()
        ]);

        $from = Carbon::parse($request->get('from'));
        $to = Carbon::parse($request->get('to'));

        $dates = $this->generateDatesFromRange($request->filled('from') ? $from : $from = Carbon::now()->startOfWeek(),
            $request->filled('to') ? $to : $to = Carbon::now()->endOfWeek());
        $labels = [];
        $usersData = [];
        $clientsData = [];
        $subscriptionsData = [];
        $earningsData = [];

        foreach ($dates as $key => $date) {
            $labels[] = $key;
            $usersData[] = User::query()->whereDate('created_at', $date)->count();
            $clientsData[] = Client::query()->whereDate('created_at', $date)->count();
            $subscriptionsData[] = $subs = User::query()->whereDate('created_at', $date)->subscribed()->count();
            $earningsData[] = Invoice::getTotalEarningsBetween(Carbon::parse($date), Carbon::parse($date));
        }

        return ApiResponse::success([
            'data' => [
                'labels' => $labels,
                'current_week' => Carbon::now()->weekOfYear,
                'users' => [
                    'count' => User::query()->count(),
                    'data' => $usersData
                ],
                'clients' => [
                    'count' => Client::query()->count(),
                    'data' => $clientsData,
                ],
                'reviews' => [
                    'count' => Review::query()->count(),
                    'data' => $subscriptionsData
                ],
                'earnings' => [
                    'total' => collect($earningsData)->sum(),
                    'data' => $earningsData
                ]
            ]
        ]);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return array
     */
    private function generateDatesFromRange(Carbon $start_date, Carbon $end_date): array
    {
        $dates = [];

        for ($date = $start_date; $date->lte($end_date); $date->addDay()) {

            $dates[$date->format('Y-m-d')] = $date->toDateString();

        }

        return $dates;
    }
}
