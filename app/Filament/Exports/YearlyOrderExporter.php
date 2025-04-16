<?php

namespace App\Filament\Exports;

use App\Models\Shop\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class YearlyOrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('number')->label('Order Number'),
            ExportColumn::make('customer.name')->label('Customer Name'),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\OrderStatus ? $state->value : $state),
            ExportColumn::make('total_price')->label('Total Price'),
            ExportColumn::make('shipping_price')->label('Shipping Price'),
            ExportColumn::make('created_at')->label('Order Date'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your yearly order export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
