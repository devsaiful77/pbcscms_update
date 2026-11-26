<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// CIC card transaction history (CICController@cardTransactionsExport)
class CardTransactionsExport implements FromView, ShouldAutoSize, WithStyles
{
    private $data;

    // ['customerName', 'accountNumber', 'statements', 'fromDate', 'toDate']
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function styles(Worksheet $sheet): ?array
    {
        $sheet->getStyle('1')->getFont()->setBold(true)->setSize(14);

        return null;
    }

    public function view(): View
    {
        return view('CIC.partials.card_transactions_excel', $this->data);
    }
}
