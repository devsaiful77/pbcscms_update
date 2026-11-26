<?php
namespace Modules\Requisition\App\Exports;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RequisitionExport implements FromView, ShouldAutoSize, WithStyles
{
    use Exportable;
    
    protected $tblData;
    protected $activeTab;

    public function __construct(array $tblData, string $activeTab)
    {
        $this->tblData = $tblData;
        $this->activeTab = $activeTab;
    }

    public function styles(Worksheet $sheet): ?array
    {
        $columnCount = $sheet->getHighestColumn();
        $rowNumber = $sheet->getHighestRow();
        $sheet->getStyle('1')->getFont()->setBold(true);
        $alignment = [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_CENTER,
        ];

        $sheet->getStyle('A1:' . $columnCount . $rowNumber)
            ->getAlignment()
            ->applyFromArray($alignment);

        return null;
    }

    public function view(): View
    {
        return view('requisition::requisition.exports.requisition-export', [
            'tblData' => $this->tblData,  
            'activeTab' => $this->activeTab,
        ]);
    }
}

