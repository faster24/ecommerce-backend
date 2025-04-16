<?php

namespace App\Filament\Resources\Shop\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Shop\OrderResource;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected $originalStatus;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        // Store the original status before saving
        $this->originalStatus = Order::find($this->record->id)->status;
    }

    protected function afterSave(): void
    {
        if ($this->record->status === OrderStatus::Delivered) {
            DB::transaction(function () {
                foreach ($this->record->items as $item) {
                    $product = Product::find($item->shop_product_id);
                    if (!$product) {
                        Notification::make()
                            ->title('Error')
                            ->body("Product ID {$item->shop_product_id} not found.")
                            ->danger()
                            ->send();
                        throw new \Exception("Product not found.");
                    }

                    // Check if sufficient stock
                    if ($product->stock_qty < $item->qty) {
                        Notification::make()
                            ->title('Error')
                            ->body("Insufficient stock for {$product->name}. Required: {$item->qty}, Available: {$product->stock_qty}.")
                            ->danger()
                            ->send();
                        throw new \Exception("Insufficient stock.");
                    }

                    // Reduce stock_qty
                    $product->decrement('stock_qty', $item->qty);
                }

                Notification::make()
                    ->title('Stock Updated')
                    ->body('Product stock quantities reduced for delivered order.')
                    ->success()
                    ->send();
            });
        }
    }
}
