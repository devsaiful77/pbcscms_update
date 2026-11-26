<?php

namespace App\Imports;

use App\SegmentCode;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
class SegmentImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows): void
    {
        try {
            if (!empty($rows)) {
                foreach ($rows as $key => $row) {
                    $holidayModelName = new SegmentCode;
                    $holidayModelName->name = $row['name'];
                    $holidayModelName->code =  $row['code'];
                    $holidayModelName->status = 1;
                    $holidayModelName->save();
                }
            }
            flash('Imported successfully.', 'success');
        } catch (Exception $e) {
            flash('Failed to Insert Data', 'error');
        }
    }
}
