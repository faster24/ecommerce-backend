<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Textarea;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Tickets';

    protected static ?string $navigationGroup = 'Support';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime('F j, Y, g:i A'),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->colors([
                        'warning' => 'pending', // Yellow for pending
                        'success' => 'resolved', // Green for resolved
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check')
                    ->form([
                        Textarea::make('ticket_message')
                            ->label('Ticket Message')
                            ->default(fn (Ticket $record) => $record->message)
                            ->disabled()
                            ->rows(4),
                        Textarea::make('message')
                            ->label('Reply Message')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (Ticket $record, array $data) {
                        $reply = $record->replies()->create([
                            'message' => $data['message'],
                        ]);

                        $record->user->notify(new \App\Notifications\TicketResolvedNotification($record, $data['message']));

                        $record->update(['status' => 'resolved']);
                    })
                    ->requiresConfirmation()
                    ->color('success'),
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if ($user->hasRole('supplier')) {
            return false; // Hide for supplier role
        }
        return true; // Show for others with permission
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
