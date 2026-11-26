<?php

namespace App\Imports;

use App\Holiday;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HolidaysImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows): void
    {
        try {
            if (!empty($rows)) {
                $lastYear = 0;
                foreach ($rows as $key => $row) {
                    if(empty($row['holiday_date'])|| empty($row['type_of_holiday'])) {
                        continue;
                    } else {
                        if(!is_int($row['holiday_date'])){
                            continue;
                        }

                        $dates = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['holiday_date'])->format('d/m/Y');
                        
                        $day = date('d',strtotime($dates));
                        $months = date('m',strtotime($dates));
                        $year = date('Y',strtotime($dates));

                        if ($lastYear != $year ) {
                            $existingHolidayModel = new Holiday();
                            $existingHolidayModel->where("year",$year)->delete();
                        }

                        $holidayModelName = new Holiday;
                        $holidayModelName->dates = date('Y-m-d', strtotime($dates));
                        $holidayModelName->day = $day;
                        $holidayModelName->months = $months;
                        $holidayModelName->year = $year;
                        $holidayModelName->type = strtolower($row['type_of_holiday']);
                        $holidayModelName->remarks =  $row['remarks'];
                        $holidayModelName->status = 1;
                        $holidayModelName->save();

                        $lastYear = $year;
                    }
                }
            }
            flash('Imported successfully.', 'success');
        }catch (Exception $e) {
            flash('Failed to Insert Data', 'error');
        }
    }
}
