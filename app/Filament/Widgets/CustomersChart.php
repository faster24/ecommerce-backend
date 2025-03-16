<?php

namespace App\Filament\Widgets;

use App\Models\Customer; // Adjust namespace based on your Customer model location
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CustomersChart extends ChartWidget
{
    protected static ?string $heading = 'Total Customers';

    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        // Get the current year as the default range
        $year = Carbon::now()->year;

        // Query to count customers cumulatively by month
        $customers = Customer::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();

        // Prepare cumulative data for all 12 months
        $data = [];
        $cumulativeTotal = 0;

        // Get total customers before this year to start the cumulative count
        $priorTotal = Customer::where('created_at', '<', Carbon::create($year, 1, 1)->startOfYear())->count();

        for ($month = 1; $month <= 12; $month++) {
            $cumulativeTotal += $customers[$month] ?? 0;
            $data[] = $priorTotal + $cumulativeTotal;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Customers',
                    'data' => $data,
                    'fill' => 'start',
                    'borderColor' => '#2196F3', // Optional: Customize line color (blue)
                    'tension' => 0.1,           // Optional: Smooth the line
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
}
