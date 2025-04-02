<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Products extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'shop/products';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}
