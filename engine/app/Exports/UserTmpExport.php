<?php

namespace App\Exports;

use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserTmpExport implements FromCollection, WithHeadings, WithStyles
{
    protected $tmpData;

    public function __construct($tmpData)
    {
        $this->tmpData = $tmpData;
    }

    public function collection(): Enumerable
    {
        return collect($this->tmpData->map(function ($user, $index) {
            return [
                'SL' => $index + 1,
                'User ID' => $user->user_id,
                'Name' => $user->name,
                'Designation' => $user->designation,
                'Email' => $user->email,
                'Mobile No' => $user->mobile_no,
                'Emp ID' => $user->emp_id,
                'Unit Name' =>$user->unit_name,
                'Role Name' => $user->role_name,
                'Status' => $user->ustatus == 1 ? 'Active' : 'Inactive',
                'Created At' => $user->uCreated_by,
                'Comments' => $user->ucomments

            ];
        }));
    }

    public function headings(): array
    {
        return [
            'SL',
            'User ID',
            'Name',
            'Designation',
            'Email',
            'Mobile No',
            'Emp ID',
            'Unit Name',
            'Role Name',
            'Status',
            'Created At',
            'Comments'
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1    => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FF008000'], // Green font color
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => [
                        'argb' => 'FFFFE699', // Light yellow background
                    ]
                ]
            ],
        ];
    }
}
