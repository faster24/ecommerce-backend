<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplyResource\Pages;
use App\Filament\Resources\SupplyResource\RelationManagers;
use App\Models\Shop\Supply;
use App\Models\Shop\Product;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class SupplyResource extends Resource
{
    protected static ?string $model = Supply::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Supplies';

    protected static ?string $navigationGroup = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->relationship('product' , 'name')
                    ->preload()
                    ->searchable()
                    ->required(),

                Hidden::make('supplier_id')->default(auth()->id()),

                TextInput::make('quantity')->numeric(),
                TextInput::make('price')->numeric(),

                Hidden::make('payment_status')
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name'),
                TextColumn::make('supplier.name'),
                TextColumn::make('quantity'),
                TextColumn::make('price'),

                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'rejected' => 'danger',
                        'reviewing' => 'gray',
    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('review')
                    ->label('Review')
                    ->visible(fn () => auth()->user()->hasRole('super_admin'))
                    ->url(fn ($record): string => static::getUrl('review', ['record' => $record]))
                    ->icon('heroicon-o-currency-dollar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupplies::route('/'),
            'create' => Pages\CreateSupply::route('/create'),
            'edit' => Pages\EditSupply::route('/{record}/edit'),
            'review' => Pages\Supply\ReviewPayment::route('/{record}/review'),
        ];
    }
}
