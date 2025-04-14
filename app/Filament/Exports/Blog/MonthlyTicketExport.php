<?php

namespace App\Filament\Exports\Blog;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MonthlyTicketExport implements FromCollection, WithHeadings
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function collection()
    {
        return Ticket::selectRaw('MONTH(created_at) as month, status, COUNT(*) as count')
            ->whereYear('created_at', $this->year)
            ->groupBy('month', 'status')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($row) {
                $row->month = date('F', mktime(0, 0, 0, $row->month, 1));
                return $row;
            });
    }

    public function headings(): array
    {
        return ['Month', 'Status', 'Ticket Count'];
    }
}
