<?php

namespace App\Filament\Exports\Blog;

use App\Models\Ticket;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class YearlyTicketExport extends ExcelExport
{
    public function __construct(string $name = 'yearly')
    {
        parent::__construct($name);
    }

    public function query()
    {
        return Ticket::selectRaw('YEAR(created_at) as year, status, COUNT(*) as count')
            ->groupBy('year', 'status')
            ->orderBy('year', 'desc');
    }

    public function getColumns(): array
    {
        return [
            Column::make('year')->heading('Year'),
            Column::make('status')->heading('Status'),
            Column::make('count')->heading('Ticket Count'),
        ];
    }
}
