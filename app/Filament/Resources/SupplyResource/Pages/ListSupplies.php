<?php

namespace App\Filament\Resources\SupplyResource\Pages;

use App\Filament\Resources\SupplyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupplies extends ListRecords
{
    protected static string $resource = SupplyResource::class;

    protected function getTableActions(): array
    {
        return [
            Actions\Action::make('review')
                ->label('Review Payment')
                ->url(fn ($record): string => SupplyResource::getUrl('review', ['record' => $record]))
                ->icon('heroicon-o-currency-dollar'),
            Actions\EditAction::make(),
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
