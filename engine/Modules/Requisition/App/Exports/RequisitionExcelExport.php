<?php

namespace Modules\Requisition\App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RequisitionExcelExport implements FromView, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $getData;
    protected $searchDataForView;
    protected $viewName;
    public function __construct($getData, $searchDataForView = array(), $viewName = "")
    {
        $this->getData = $getData;
        $this->searchDataForView = $searchDataForView;
        $this->viewName = $viewName;
    }

    public function styles(Worksheet $sheet): ?array
    {
        $columnCount = $sheet->getHighestColumn();
        $rowNumber = $sheet->getHighestRow();
        $sheet->getStyle('1')->getFont()->setBold(true);

        $alignment = [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ];

        $sheet->getStyle('A1:' . $columnCount . $rowNumber)->getAlignment()->applyFromArray($alignment);

        return null;
    }

    public function view(): View
    {
        return view($this->viewName, [
            'dataForView' => $this->getData->get(),
            'searchDataForView' => $this->searchDataForView
        ]);
    }
}
