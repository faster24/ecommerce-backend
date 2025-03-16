<?php

namespace App\Filament\Widgets;

use App\Models\Shop\Order; // Adjust namespace based on your Order model location
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Orders per Month';

    protected static ?int $sort = 1;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        // Get the current year as the default range
        $year = Carbon::now()->year;

        // Query to count orders per month
        $orders = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();

        // Prepare data for all 12 months (fill missing months with 0)
        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            $data[] = $orders[$month] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data,
                    'fill' => 'start',
                    'borderColor' => '#4CAF50', // Optional: Customize line color
                    'tension' => 0.1,           // Optional: Smooth the line
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
}
