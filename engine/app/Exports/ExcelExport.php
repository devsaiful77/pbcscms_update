<?php

namespace App\Exports;

// use App\Excel;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelExport implements FromView, ShouldAutoSize, WithStyles
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($getData = array(),$searchDataForView = array(),$viewName = "")
    {
        $this->getData = $getData;
        $this->searchDataForView = $searchDataForView;
        $this->viewName = $viewName;
    }

    /*public function query()
    {
        // return $this->getData->getQuery();
    }*/

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
        return view($this->viewName, [
            'dataForView' => $this->getData->get(),
            'searchDataForView'=>$this->searchDataForView
        ]);
    }

}
