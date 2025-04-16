<?php

namespace App\Filament\Resources\SupplyResource\Pages\Supply;

use App\Filament\Resources\SupplyResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action; // Correct namespace for header actions
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Models\Shop\Product;
use App\Models\Shop\Supply;

class ReviewPayment extends EditRecord
{
    protected static string $resource = SupplyResource::class;

    protected static ?string $title = 'Review Payment Status';

    /**
     * Define the form schema for Filament v3 compatibility.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Supply Details')
                    ->schema([
                        Forms\Components\Placeholder::make('product.name')
                            ->label('Product Name')
                            ->content(fn ($record) => $record->product->name ?? 'N/A'),
                        Forms\Components\Placeholder::make('quantity')
                            ->label('Quantity')
                            ->content(fn ($record) => $record->quantity ?? 'N/A'),
                        Forms\Components\Placeholder::make('supplier')
                            ->label('Supplier Name')
                            ->content(fn ($record) => $record->supplier->name ?? 'N/A'),
                        Forms\Components\Placeholder::make('supplier')
                            ->label('Email')
                            ->content(fn ($record) => $record->supplier->email ?? 'N/A'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make('Payment Status')
                    ->schema([
                        Forms\Components\Select::make('payment_status')
                            ->label('Payment Status')
                            ->default('pending')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'rejected' => 'Rejected',
                                'reviewing' => 'reviewing',
                            ])
                            ->required()
                            ->default(fn ($record) => $record->payment_status),
                    ]),
            ]);
    }

    /**
     * Filter data before filling the form.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return [
            'payment_status' => $data['payment_status'],
        ];
    }

    /**
     * Filter data before saving to the database.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return [
            'payment_status' => $data['payment_status'],
        ];
    }

    /**
     * Define page-level header actions using the correct Action class.
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('back') // Use Filament\Actions\Action
                ->label('Back to List')
                ->url(fn () => $this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }

    /**
     * Redirect after saving.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Notify after saving.
     */
    protected function afterSave(): void
    {
        $record = $this->record;

        $product = Product::find($record->product_id);

        if (!$product) {
            Notification::make()
                ->title('Error')
                ->body('Product not found.')
                ->danger()
                ->send();
            return;
        }

        if ($record->payment_status === 'paid') {
            $product->increment('stock_qty', $record->quantity);
            Notification::make()
                ->title('Stock Updated')
                ->body("Added {$record->quantity} to {$product->name}'s stock.")
                ->success()
                ->send();
        }

        Notification::make()
            ->title('Payment Status Updated')
            ->body("Payment status set to {$record->payment_status}.")
            ->success()
            ->send();
    }
}
