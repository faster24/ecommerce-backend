<?php

namespace App\Filament\Widgets;

use App\Models\Shop\Order;
use App\Models\Customer;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        // Parse filter dates
        $endDate = !is_null($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])
            : now();
        $startDate = !is_null($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])
            : null;

        // Business customer filter
        $isBusinessCustomersOnly = $this->filters['businessCustomersOnly'] ?? null;
        $businessCustomerMultiplier = match (true) {
            boolval($isBusinessCustomersOnly) => 2 / 3,
            blank($isBusinessCustomersOnly) => 1,
            default => 1 / 3,
        };

        // Define current and previous period
        $diffInDays = $startDate ? $startDate->diffInDays($endDate) : 30; // Default to 30 days if no start date
        $prevEndDate = $startDate ? $startDate->copy()->subDay() : $endDate->copy()->subDays($diffInDays);
        $prevStartDate = $startDate ? $prevEndDate->copy()->subDays($diffInDays) : $endDate->copy()->subDays($diffInDays * 2);

        // Helper function to build order query
        $buildOrderQuery = fn ($start, $end) => Order::query()
            ->when($start, fn ($query) => $query->where('created_at', '>=', $start))
            ->where('created_at', '<=', $end)
            ->when($isBusinessCustomersOnly === '1', fn ($query) => $query->whereHas('customer', fn ($q) => $q->where('is_business', true)))
            ->when($isBusinessCustomersOnly === '0', fn ($query) => $query->whereHas('customer', fn ($q) => $q->where('is_business', false)));

        // Current period data
        $currentOrderQuery = $buildOrderQuery($startDate, $endDate);
        $revenue = (int) ($currentOrderQuery->sum('total_price') * $businessCustomerMultiplier);
        $newOrders = (int) ($currentOrderQuery->count() * $businessCustomerMultiplier);

        $currentCustomerQuery = Customer::query()
            ->whereHas('orders', fn ($query) =>
                $query->when($startDate, fn ($q) => $q->where('created_at', '>=', $startDate))
                      ->where('created_at', '<=', $endDate)
            )
            ->when($isBusinessCustomersOnly === '1', fn ($query) => $query->where('is_business', true))
            ->when($isBusinessCustomersOnly === '0', fn ($query) => $query->where('is_business', false));
        $newCustomers = (int) ($currentCustomerQuery->count() * $businessCustomerMultiplier);

        // Previous period data
        $prevOrderQuery = $buildOrderQuery($prevStartDate, $prevEndDate);
        $prevRevenue = (int) ($prevOrderQuery->sum('total_price') * $businessCustomerMultiplier);
        $prevNewOrders = (int) ($prevOrderQuery->count() * $businessCustomerMultiplier);

        $prevCustomerQuery = Customer::query()
            ->whereHas('orders', fn ($query) =>
                $query->where('created_at', '>=', $prevStartDate)
                      ->where('created_at', '<=', $prevEndDate)
            )
            ->when($isBusinessCustomersOnly === '1', fn ($query) => $query->where('is_business', true))
            ->when($isBusinessCustomersOnly === '0', fn ($query) => $query->where('is_business', false));
        $prevNewCustomers = (int) ($prevCustomerQuery->count() * $businessCustomerMultiplier);

        // Number formatting function
        $formatNumber = function (int $number): string {
            if ($number < 1000) {
                return (string) Number::format($number, 0);
            }
            if ($number < 1000000) {
                return Number::format($number / 1000, 2) . 'k';
            }
            return Number::format($number / 1000000, 2) . 'm';
        };

        // Trend calculation function
        $calculateTrend = function ($current, $previous) use ($formatNumber) {
            $difference = $current - $previous;
            $percentage = $previous > 0 ? ($difference / $previous) * 100 : 0;

            $description = $difference >= 0
                ? "{$formatNumber(abs($difference))} increase"
                : "{$formatNumber(abs($difference))} decrease";
            $description .= " (" . Number::format(abs($percentage), 1) . "%)";

            $icon = $difference >= 0
                ? 'heroicon-m-arrow-trending-up'
                : 'heroicon-m-arrow-trending-down';
            $color = $difference >= 0 ? 'success' : 'danger';

            return [$description, $icon, $color];
        };

        // Calculate trends
        [$revenueTrend, $revenueIcon, $revenueColor] = $calculateTrend($revenue, $prevRevenue);
        [$customersTrend, $customersIcon, $customersColor] = $calculateTrend($newCustomers, $prevNewCustomers);
        [$ordersTrend, $ordersIcon, $ordersColor] = $calculateTrend($newOrders, $prevNewOrders);

        return [
            Stat::make('Revenue', '$' . $formatNumber($revenue))
                ->description($revenueTrend)
                ->descriptionIcon($revenueIcon)
                ->chart([7, 2, 10, 3, 15, 4, 17]) // Replace with real data if available
                ->color($revenueColor),
            Stat::make('New customers', $formatNumber($newCustomers))
                ->description($customersTrend)
                ->descriptionIcon($customersIcon)
                ->chart([17, 16, 14, 15, 14, 13, 12]) // Replace with real data if available
                ->color($customersColor),
            Stat::make('New orders', $formatNumber($newOrders))
                ->description($ordersTrend)
                ->descriptionIcon($ordersIcon)
                ->chart([15, 4, 10, 2, 12, 4, 12]) // Replace with real data if available
                ->color($ordersColor),
        ];
    }
}
