<?php

namespace App\Filament\Resources;

use App\Filament\Exports\Blog\YearlyTicketExport;
use App\Filament\Exports\Blog\MonthlyTicketExport;
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
use Filament\Forms\Components\Select;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;

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
                //
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
                        'warning' => 'pending',
                        'success' => 'resolved',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Year')
                    ->options(
                        Ticket::selectRaw('YEAR(created_at) as year')
                            ->distinct()
                            ->pluck('year')
                            ->mapWithKeys(fn ($year) => [$year => $year])
                            ->toArray()
                    )
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'],
                        fn (Builder $query, $year) => $query->whereYear('created_at', $year)
                    )),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')
                            ->label('From Date')
                            ->displayFormat('M d, Y'),
                        DatePicker::make('to')
                            ->label('To Date')
                            ->displayFormat('M d, Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From ' . Carbon::parse($data['from'])->toFormattedDateString();
                        }

                        if ($data['to'] ?? null) {
                            $indicators['to'] = 'To ' . Carbon::parse($data['to'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check')
                    ->form([
                        Textarea::make('order_message')
                            ->label('Order Message')
                            ->default(fn (Ticket $record) => $record->message)
                            ->disabled()
                            ->rows(4),
                        Textarea::make('message')
                            ->label('Reply Message')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (Ticket $record, array $data) {
                        $record->replies()->create([
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
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Export Yearly Report')
                    ->exporter(\App\Filament\Exports\Blog\YearlyTicketExport::class)
                    ->modifyQueryUsing(function (Builder $query) {
                        return $query->when(
                            request()->has('tableFilters.year.value'),
                            fn (Builder $query) => $query->whereYear('created_at', request()->get('tableFilters.year.value'))
                        );
                    })
                    ->color('primary'),
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
            return false;
        }
        return true;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
