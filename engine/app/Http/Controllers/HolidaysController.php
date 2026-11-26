<?php

namespace App\Http\Controllers;

use Excel;
use Exception;
use App\Holiday;
use App\HolidayTmp;
use App\WorkingDay;
use App\HolidaysTmp;
use App\Http\Requests;
use App\WorkingHourTmp;
use App\WorkingHoursTmp;
use Illuminate\Http\Request;
use App\Imports\HolidaysImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\HolidayRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class HolidaysController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $checker = false;

    public function __construct()
    {
        // $this->middleware('ability:superadmin|admin,adminActivity|true');
        $this->middleware(['role_or_permission:superadmin|adminActivity|true|accessHoliday|accessHolidayChecker|accessWorkingDay|accessWorkingHours|accessWorkingDay']);
        parent::__construct();
    }

    public function accessHolidayChecker()
    {
        if (Auth::check() && Auth::user()->hasPermissionTo('accessHolidayChecker')) {
            return $this->checker = true;
        } else {
            return $this->checker;
        }
    }

    public function accessWorkingHourChecker()
    {
        if (Auth::check() && Auth::user()->hasPermissionTo('accessWorkingHoursChecker')) {
            return $this->checker = true;
        } else {
            return $this->checker;
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        $dataForView = array();
        $searchDataForView = $request->all();
        $tblData = array();

        $holidayModelName = new Holiday;
        $dataObj = $holidayModelName
            ->orderBy("dates", "DESC")
            ->get();
        //->paginate(PAGINATION_NUMBER);
        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }

        $title = "Holiday List";
        $title_for_layout = "Holiday List";
        $checker = $this->accessHolidayChecker();
        return view('Holidays.index', compact('title', 'title_for_layout', 'tblData', 'searchDataForView', 'dataObj', 'checker'));
    }
    public function add()
    {
        $checker = $this->accessHolidayChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $id = 0;
        $dataForView = array();
        $tmpId = 0;
        $title = "Add Holiday";
        $title_for_layout = "Add Holiday";

        return view('Holidays.add', compact('title', 'title_for_layout', 'id', 'dataForView', 'tmpId'));
    }

    public function store(HolidayRequest $request)
    {
        if ($request->isMethod('post')) {
            $dates = $request->dates;
            $day = date('d', strtotime($dates));
            $months = date('m', strtotime($dates));
            $year = date('Y', strtotime($dates));

            $holidayTmp = new HolidayTmp();
            $holidayTmp->dates = date('Y-m-d', strtotime($request->dates));
            $holidayTmp->day = $day;
            $holidayTmp->months = $months;
            $holidayTmp->year = $year;
            $holidayTmp->type = $request->type;
            $holidayTmp->remarks = $request->remarks;
            $holidayTmp->year = $year;
            $holidayTmp->action = "Add";
            $holidayTmp->status = 2;
            $holidayTmp->created_by = Auth::user()->id;
            $holidayTmp->ip = $this->getClientIp();

            if ($holidayTmp->save()) {
                flash('Holidays info has been inserted For Review in Checker', 'success');
                return redirect('Holidays');
            } else {
                flash('Failed to insert data', 'danger');
                return redirect('Holidays/add');
            }
        }
    }



    public function approveHoliday($id)
    {
        $checker = $this->accessHolidayChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }
        DB::beginTransaction();

        try {
            // $holidayTmp = DB::table('holiday_tmps')->where('status', '!=', 3)->first();
            $holidayTmp = HolidayTmp::findOrFail($id);

            if (!$holidayTmp) {
                flash('Holiday info not found or not approved yet.', 'danger');
                return redirect()->back();
            }

            if ($holidayTmp->action == "Add" || $holidayTmp->action == "Edit") {
                $holidayData = [
                    'day'           => $holidayTmp->day,
                    'months'    => $holidayTmp->months,
                    'year'  => $holidayTmp->year,
                    'dates' => $holidayTmp->dates,
                    'type' => $holidayTmp->type,
                    'remarks' => $holidayTmp->remarks,
                    'status'      => $holidayTmp->status == 2 ? 1 : $holidayTmp->status,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }

            if ($holidayTmp->master_id != null && $holidayTmp->action == "Edit") {
                DB::table('holidays')->where('id', $holidayTmp->master_id)->update($holidayData);
            } elseif ($holidayTmp->action == "Import") {
                if ($holidayTmp->importable_file) {
                    $this->import($holidayTmp);
                } else {
                    flash('Import file not found or invalid.', 'danger');
                    return redirect()->back();
                }
            } elseif ($holidayTmp->action == "Delete") {
                if ($holidayTmp->master_id) {
                    Holiday::find($holidayTmp->master_id)->delete();
                } else {
                    flash('Holiday master ID not found for deletion.', 'danger');
                    return redirect()->back();
                }
            } elseif ($holidayTmp->action == "Add") {
                DB::table('holidays')->insert($holidayData);
            }

            DB::table('holiday_tmps')->where('id', $holidayTmp->id)->delete();
            DB::commit();

            if ($holidayTmp->action == "Add") {
                flash('Holiday create info has been approved.', 'success');
            } elseif ($holidayTmp->action == "Edit") {
                flash('Holiday update info has been approved.', 'success');
            } elseif ($holidayTmp->action == "Delete") {
                flash('Holiday delete request has been approved.', 'success');
            } elseif ($holidayTmp->action == "Import") {
                flash('Holiday import request has been approved.', 'success');
            }

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash('An error occurred during the approval process: ' . $e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    private function import($data)
    {
        $fileContent = $data->importable_file;

        if (empty($fileContent)) {
            dd('File empty');
        }

        $tempFilePath = tempnam(sys_get_temp_dir(), 'excel_');

        file_put_contents($tempFilePath, $fileContent);

        if (!file_exists($tempFilePath)) {
            abort(403, 'You do not add any file.');
        }

        $file = new \Illuminate\Http\UploadedFile(
            $tempFilePath,
            $data->file_name,
            $data->file_mime,
            null,
            true
        );

        Excel::import(new HolidaysImport(), $file);
    }


    public function edit($id = null, Request $req)
    {
        $checker = $this->accessHolidayChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $holidayModelName = new Holiday;
        $title = " Edit Holiday";
        $title_for_layout = 'Edit Holiday';
        $searchDataForView = $req->all();
        $tmpId = 0;
        $dataForViewObj = $holidayModelName->where([['id', $id]])->first();

        $dataForView = array();
        if (!empty($dataForViewObj)) {
            $dataForView = $dataForViewObj->toArray();
        }
        if ($dataForView['status'] == 0) {
            abort(403, 'Edit Not Allowed !!!');
        }
        return view('Holidays.add', compact('title', 'title_for_layout', 'dataForView', 'searchDataForView', 'id', 'tmpId'));
    }

    public function holidayTmpEdit(Request $request, $id = null)
    {
        $checker = $this->accessHolidayChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $holidayModelName = new HolidayTmp();
        $title = " Edit Holiday";
        $title_for_layout = 'Edit Holiday';
        $searchDataForView = $request->all();
        $tmpId = $id;
        $dataForViewObj = $holidayModelName->where([['id', $id]])->first();

        $dataForView = array();
        if (!empty($dataForViewObj)) {
            $dataForView = $dataForViewObj->toArray();
        }

        return view('Holidays.add', compact('title', 'title_for_layout', 'dataForView', 'searchDataForView', 'id', 'tmpId'));
    }

    public function update(Request $request, $id = null)
    {

        $isTemp = !empty($request->tmpId) && $request->tmpId != 0;

        $tableName = $isTemp ? 'holiday_tmps' : 'holidays';

        $this->validate($request, [
            'dates' => 'required|unique:' . $tableName . ',dates,' . $id,
        ]);

        $existingRecords = $isTemp ? HolidayTmp::where('id', $id)->get() : HolidayTmp::where('master_id', $id)->get();

        $hasInvalidRecord = $existingRecords->contains(function ($record) {
            return $record->form_status != 7 && $record->form_status != -1;
        });

        if ($hasInvalidRecord) {
            flash('An entry already exists, please wait for Checker Approval', 'danger');
            return Redirect::back();
        }

        $data = $request->all();
        $dates = $data['dates'];
        $day = date('d', strtotime($dates));
        $months = date('m', strtotime($dates));
        $year = date('Y', strtotime($dates));

        $data['dates'] = date('Y-m-d', strtotime($data['dates']));
        $data['day'] = $day;
        $data['months'] = $months;
        $data['year'] = $year;

        $additionalParams = (!empty($data['additionalParams'])) ? $data['additionalParams'] : "";
        unset($data['additionalParams']);

        $data['modified_by'] = Auth::user()->id;

        if ($isTemp) {
            $holidayTmp = HolidayTmp::findOrFail($id);
        } else {
            $holidayTmp = new HolidayTmp();
            $update = Holiday::findOrFail($id);
        }

        $holidayTmp->day = $data['day'];
        $holidayTmp->months = $data['months'];
        $holidayTmp->year = $data['year'];
        $holidayTmp->dates = $data['dates'];
        $holidayTmp->type = $request->type;
        $holidayTmp->remarks = $request->remarks;
        $holidayTmp->status = 2;
        $holidayTmp->form_status = 0;
        $holidayTmp->action = "Edit";
        $holidayTmp->master_id = $isTemp ? $holidayTmp->master_id : $update->id;
        $holidayTmp->created_by = Auth::user()->id;
        $holidayTmp->ip = $this->getClientIp();

        if ($holidayTmp->save()) {
            flash('Holiday info has been updated For Review in Checker', 'success');
            if ($isTemp) {
                flash("Holidays temporary data updated successfully.", "success");
                return redirect('Holidays/action-queue-list');
            }
            return redirect('/Holidays' . $additionalParams);
        } else {
            flash('Failed to update group info, Please try again', 'danger');
        }
    }

    public function uploadHolidays(Request $request)
    {
        $checker = $this->accessHolidayChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'upload_holidays' => 'required|mimes:xls,xlsx'
        ], [
            'upload_holidays.required' => 'The holiday file is required.',
            'upload_holidays.mimes' => 'The holiday file must be an Excel file with .xls or .xlsx extension.',
        ]);

        if (!empty($request->file('upload_holidays'))) {
            $file = $request->file('upload_holidays');

            $fileContent = file_get_contents($file->getRealPath());
            $holidayTmp = new HolidayTmp();
            $holidayTmp->importable_file = $fileContent;
            $holidayTmp->file_name = $file->getClientOriginalName();
            $holidayTmp->file_mime = $file->getMimeType();
            $holidayTmp->action = "Import";
            $holidayTmp->status = 1;
            $holidayTmp->created_by = Auth::user()->id;
            $holidayTmp->ip = $this->getClientIp();
            $holidayTmp->save();

            flash('Uploaded successfully for review in checker.', 'success');

            // try {
            //     //laravel-excel New version code (Zihad):
            //     Excel::import(new HolidaysImport(), $file);


            //laravel-excel package old version code:
            /*Excel::selectSheetsByIndex(0)->load($request->file('upload_holidays'), function ($reader) {
                    $dataFromExcel = $reader->toArray();
                    if (!empty($dataFromExcel)) {
                        $lastYear = 0;
                        foreach ($dataFromExcel as $key=>$row) {
                            if(empty($row['holiday_date'])|| empty($row['type_of_holiday'])) {
                                continue;
                            } else {

                                $dates = $row['holiday_date'];
                                $day = date('d',strtotime($dates));
                                $months = date('m',strtotime($dates));
                                $year = date('Y',strtotime($dates));

                                if ($lastYear != $year ) {
                                    $existingHolidayModel = new Holiday();
                                    $existingHolidayModel->where("year",$year)->delete();
                                }

                                $holidayModelName = new Holiday;
                                $holidayModelName->dates = date('Y-m-d',strtotime($row['holiday_date']));
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
                    flash('Uploaded successfully.', 'success');
                });*/
            // } catch (Exception $e) {
            //    flash('Failed to Insert Data', 'error');
            // }
        } else {
            flash('Please upload file to add holidays.', 'error');
        }
        return redirect('Holidays');
    }

    public function delete($id = null, $status = "")
    {
        // $holidayModelName = new Holiday;
        // $holidayModelName->where('id', $id)->delete();
        // flash('Holiday has been deleted', 'danger');


        // return Redirect::back();
        $checker = $this->accessHolidayChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $existingTmpRecords = HolidayTmp::where('master_id', $id)->get();

        $hasInvalidRecord = $existingTmpRecords->contains(function ($record) {
            return $record->form_status != 7 && $record->form_status != -1;
        });

        if ($hasInvalidRecord) {
            flash('An entry already exists, please wait for Checker Approval', 'danger');
            return Redirect::back();
        }

        $holiday = Holiday::findOrFail($id);
        if ($holiday) {
            $holidayTmp = new HolidayTmp();
            $holidayTmp->dates = $holiday->dates;
            $holidayTmp->type = $holiday->type;
            $holidayTmp->remarks = $holiday->remarks;
            $holidayTmp->status = 0;
            $holidayTmp->master_id = $id;
            $holidayTmp->created_by = Auth::user()->id;
            $holidayTmp->action = "Delete";
            $holidayTmp->ip = $this->getClientIp();
            $holidayTmp->save();
            flash('Holiday delete request sent to checker', 'success');
            return Redirect::back();
        } else {
            flash('Something went wrong', 'danger');
            return Redirect::back();
        }
    }

    public function workingDays(Request $request)
    {
        $dataForView = array();
        $searchDataForView = $request->all();
        if (!empty($searchDataForView)) {
            $year = (!empty($searchDataForView['year'])) ? $searchDataForView['year'] : "";
            $type = (!empty($searchDataForView['type'])) ? $searchDataForView['type'] : "";
            if ($type == "generate" && !empty($year)) {
                $dateOfYear = date($year . '-01-01');
                $lastDateOfYear = date($year . '-12-31');
                $dateSequence = array();

                $holidayModelName = new Holiday;
                $hoidayDataObj = $holidayModelName->select('dates')->where('year', $year)->pluck('dates', 'dates');
                $holidayData = array();
                if (!empty($hoidayDataObj)) {
                    $holidayData = $hoidayDataObj->toArray();
                }
                $workingDayDelete = new WorkingDay;
                $workingDayDelete->where('year', $year)->delete();
                while ($dateOfYear <= $lastDateOfYear) {
                    $flag = 0;
                    if (date('w', strtotime($dateOfYear)) == 5 || date('w', strtotime($dateOfYear)) == 6) {
                        $flag = 1;
                    }
                    if (!empty($holidayData[$dateOfYear])) {
                        $flag = 1;
                    }

                    if ($flag == 0) {
                        $workingDayModel = new WorkingDay;
                        $workingDayModel->dates = $dateOfYear;
                        $workingDayModel->year = $year;
                        $workingDayModel->status = 1;
                        $workingDayModel->save();
                    }
                    $dateOfYear = date('Y-m-d', strtotime($dateOfYear . ' +1 day'));
                }
                flash('Working days have been generated successfully', 'success');
                $_GET['type'] = 'search';
                return redirect('WorkingDays?' . http_build_query($_GET));
            }
        }
        $tblData = array();

        $workingDayModelName = new WorkingDay;
        $dataObj = $workingDayModelName;
        if (!empty($searchDataForView['year'])) {
            $dataObj = $dataObj->where("year", $searchDataForView['year']);
        }
        $dataObj = $dataObj
            ->orderBy("year", "DESC")
            ->orderBy("dates", "ASC")
            ->get();
        //->paginate(PAGINATION_NUMBER);
        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }

        $title = "Working Day List";
        $title_for_layout = "Working Day List";
        return view('Holidays.workingDays', compact('title', 'title_for_layout', 'tblData', 'searchDataForView', 'dataObj'));
    }
    public function workingHours(Request $request)
    {
        $data = $request->all();
        if ($data != [] && count($data) != 0) {
            $data['start_hour'] = ltrim($data['start_hour'], '0') ?: '0';
            $data['start_minute'] = ltrim($data['start_minute'], '0') ?: '0';
            $data['start_second'] = ltrim($data['start_second'], '0') ?: '0';
            $data['end_hour'] = ltrim($data['end_hour'], '0') ?: '0';
            $data['end_minute'] = ltrim($data['end_minute'], '0') ?: '0';
            $data['end_second'] = ltrim($data['end_second'], '0') ?: '0';

            $request->replace($data);

            $request->validate([
                'start_hour' => 'required|integer|between:0,23',
                'start_minute' => 'required|integer|between:0,59',
                'start_second' => 'required|integer|between:0,59',
                'end_hour' => 'required|integer|between:0,23',
                'end_minute' => 'required|integer|between:0,59',
                'end_second' => 'required|integer|between:0,59',
            ]);

            $request->merge([
                'start_hour'   => str_pad($request->input('start_hour'), 2, '0', STR_PAD_LEFT),
                'start_minute' => str_pad($request->input('start_minute'), 2, '0', STR_PAD_LEFT),
                'start_second' => str_pad($request->input('start_second'), 2, '0', STR_PAD_LEFT),
                'end_hour'     => str_pad($request->input('end_hour'), 2, '0', STR_PAD_LEFT),
                'end_minute'   => str_pad($request->input('end_minute'), 2, '0', STR_PAD_LEFT),
                'end_second'   => str_pad($request->input('end_second'), 2, '0', STR_PAD_LEFT),
            ]);

            $startTime = sprintf('%02d:%02d:%02d', $request->start_hour, $request->start_minute, $request->start_second);
            $endTime = sprintf('%02d:%02d:%02d', $request->end_hour, $request->end_minute, $request->end_second);

            if ($startTime >= $endTime) {
                flash('The start time must be earlier than the end time.', 'warning');
                return redirect()->back();
            }
        }

        $checker = $this->accessWorkingHourChecker();
        $dataForView = DB::table('working_hours')->first();
        $dataForView = json_decode(json_encode($dataForView), true);

        if (!empty($dataForView['office_from'])) {
            $dataForView['start_hour'] = substr($dataForView['office_from'], 0, 2);
            $dataForView['start_minute'] = substr($dataForView['office_from'], 2, 2);
            $dataForView['start_second'] = substr($dataForView['office_from'], 4, 2);
        } else {
            $dataForView['start_hour'] = '10';
            $dataForView['start_minute'] = '00';
            $dataForView['start_second'] = '00';
        }
        if (!empty($dataForView['office_to'])) {
            $dataForView['end_hour'] = substr($dataForView['office_to'], 0, 2);
            $dataForView['end_minute'] = substr($dataForView['office_to'], 2, 2);
            $dataForView['end_second'] = substr($dataForView['office_to'], 4, 2);
        } else {
            $dataForView['start_hour'] = '18';
            $dataForView['start_minute'] = '00';
            $dataForView['start_second'] = '00';
        }

        $reqData = $request->all();
        if (!empty($reqData)) {
            // DB::table('working_hours')->delete();
            // $office_from = $reqData['start_hour'].$reqData['start_minute'].$reqData['start_second'];
            // $office_to = $reqData['end_hour'].$reqData['end_minute'].$reqData['end_second'];
            // DB::table('working_hours')->insert(['office_from'=>$office_from,'office_to'=>$office_to]);
            //  flash('Working hours have been updated successfully','success');
            DB::table('working_hour_tmps')->delete();
            $office_from = $reqData['start_hour'] . $reqData['start_minute'] . $reqData['start_second'];
            $office_to = $reqData['end_hour'] . $reqData['end_minute'] . $reqData['end_second'];
            DB::table('working_hour_tmps')->insert([
                'office_from' => $office_from,
                'office_to' => $office_to,
                'created_by' => Auth::user()->id,
                'action' => 'Edit',
                'status' => 2,
                'ip' => $this->getClientIp()
            ]);
            flash('Working hours update request sent to checker for approval.', 'success');
            return redirect('WorkingHours');
        }

        $title = "Working Hour";
        $title_for_layout = "Working Hour";
        $checker = $this->accessHolidayChecker();
        return view('Holidays.workingHours', compact('title', 'title_for_layout', 'dataForView', 'checker'));
    }


    public function approveWorkingHours($id)
    {
        $checker = $this->accessWorkingHourChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $tempData = WorkingHourTmp::findOrFail($id);
        if ($tempData) {
            DB::table('working_hours')->delete();
            $newData = DB::table('working_hours')->insert([
                'office_from' => $tempData->office_from,
                'office_to' => $tempData->office_to
            ]);

            if ($newData) {
                DB::table('working_hour_tmps')->delete();
                flash('Working hours have been approved successfully', 'success');
                return Redirect::back();
            }
        } else {
            flash('No new working hours is requested!', 'warning');
            return Redirect::back();
        }
    }

    public function holidayDownloadFile($id)
    {
        $tmpData = HolidayTmp::findOrFail($id);

        $filename = $tmpData->file_name;
        $mimeType = $tmpData->file_mime;

        $file = $tmpData->importable_file;

        return response($file)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->header('Content-Length', strlen($file));
    }

    public function holidayQueueList()
    {
        $checker = $this->accessHolidayChecker();

        if ($checker) {
            $isChecker = true;
            $holidayTmp = HolidayTmp::whereNotIn('form_status', [7, -1])->get();
        } else {
            $isChecker = false;
            $holidayTmp = HolidayTmp::whereIn('form_status', [7, -1])->get();
        }

        return view('Holidays.index_tmp', compact('holidayTmp', 'isChecker'));
    }

    public function holidayAssign($id)
    {
        $tmpData = HolidayTmp::findOrFail($id);

        $tmpData->form_status = 1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->save();

        $data = [
            'message' => 'Assigned Successfully from your End!',
            'id' => $id,
        ];

        return response()->json($data, 200);
    }

    public function checkerTableData($id)
    {
        $tmpData = HolidayTmp::findOrFail($id);

        if (!empty($tmpData->master_id)) {
            $oldData = Holiday::findOrFail($tmpData->master_id);
        }

        $tmpData->holiday_date = $tmpData->dates ?? null;
        $tmpData->type_of_holiday = $tmpData->type ?? null;
        $tmpData->file = $tmpData->file_name ?? null;

        if (!empty($tmpData->file_name)) {
            $fileUrl = url('Holidays/download', ['id' => $id]);
            $tmpData->file = '<a href="' . $fileUrl . '">' . $tmpData->file_name . '</a>';
        } else {
            $tmpData->file = null;
        }


        unset($tmpData->dates, $tmpData->type, $tmpData->file_name);

        if (isset($oldData)) {
            $oldData->holiday_date = $oldData->dates ?? null;
            $oldData->type_of_holiday = $oldData->type ?? null;
            unset($oldData->dates, $oldData->type);
        }


        $columnsToSend = ['holiday_date', 'type_of_holiday', 'remarks', 'file', 'action'];

        $filteredTmpData = $tmpData->only($columnsToSend);

        $filteredOldData = isset($oldData) ? $oldData->only($columnsToSend) : null;

        $response = [
            'old_data' => $filteredOldData,
            'new_data' => $filteredTmpData
        ];

        return response()->json($response, 200);
    }

    public function buttonControl($id)
    {
        $tmpData = HolidayTmp::findOrFail($id);

        if ($tmpData->form_status == 0) {
            $assign_btn = true;
        } else {
            $assign_btn = false;
        }

        if ($tmpData->modified_by == Auth::user()->user_id) {
            $all_buttons = true;
        } else {
            $all_buttons = false;
        }

        $response = [
            'assign_btn' => $assign_btn,
            'all_buttons' => $all_buttons,
            'modified_by' => $tmpData->modified_by,
        ];

        return response()->json($response, 200);
    }

    public function holidaySendback(Request $request, $id)
    {
        $checker = $this->accessHolidayChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);

        $tmpData = HolidayTmp::findOrFail($id);
        $tmpData->form_status = 7;
        $tmpData->modified_by = null;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Send back Successfully!', 'success');
        return redirect()->back();
    }

    public function holidayReject(Request $request, $id)
    {
        $checker = $this->accessHolidayChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);

        $tmpData = HolidayTmp::findOrFail($id);
        $tmpData->form_status = -1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Request rejected!!', 'danger');
        return Redirect::back();
    }

    public function workingHourQueueList()
    {
        $checker = $this->accessWorkingHourChecker();
        if ($checker) {
            $isChecker = true;
            $dataForView = DB::table('working_hour_tmps')->whereNotIn('form_status', [7, -1])->first();
        } else {
            $isChecker = false;
            $dataForView = DB::table('working_hour_tmps')->whereIn('form_status', [7, -1])->first();
        }
        $dataForView = json_decode(json_encode($dataForView), true);

        if (!empty($dataForView['office_from'])) {
            $dataForView['start_hour'] = substr($dataForView['office_from'], 0, 2);
            $dataForView['start_minute'] = substr($dataForView['office_from'], 2, 2);
            $dataForView['start_second'] = substr($dataForView['office_from'], 4, 2);
        } else {
            $dataForView['start_hour'] = '';
            $dataForView['start_minute'] = '';
            $dataForView['start_second'] = '';
        }
        if (!empty($dataForView['office_to'])) {
            $dataForView['end_hour'] = substr($dataForView['office_to'], 0, 2);
            $dataForView['end_minute'] = substr($dataForView['office_to'], 2, 2);
            $dataForView['end_second'] = substr($dataForView['office_to'], 4, 2);
        } else {
            $dataForView['start_hour'] = '';
            $dataForView['start_minute'] = '';
            $dataForView['start_second'] = '';
        }

        return view('Holidays.working_hour_tmp', compact('dataForView', 'isChecker'));
    }

    public function workingHourAssign($id)
    {
        $tmpData = WorkingHourTmp::findOrFail($id);
        $tmpData->form_status = 1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->save();

        $data = [
            'message' => 'Assigned Successfully from your End!',
            'id' => $id,
        ];

        return response()->json($data, 200);
    }

    public function buttonControlWH($id)
    {
        $tmpData = WorkingHourTmp::findOrFail($id);

        if ($tmpData->form_status == 0) {
            $assign_btn = true;
        } else {
            $assign_btn = false;
        }

        if ($tmpData->modified_by == Auth::user()->user_id) {
            $all_buttons = true;
        } else {
            $all_buttons = false;
        }

        $response = [
            'assign_btn' => $assign_btn,
            'all_buttons' => $all_buttons,
            'modified_by' => $tmpData->modified_by,
        ];

        return response()->json($response, 200);
    }

    public function checkerTableDataWH($id)
    {
        $tmpData = WorkingHourTmp::findOrFail($id);
        $tmpData = $tmpData->toArray();

        $oldData = DB::table('working_hours')->first();
        $oldData = json_decode(json_encode($oldData), true);

        if ($tmpData) {
            $tmpData['start_hour'] = substr($tmpData['office_from'], 0, 2);
            $tmpData['start_minute'] = substr($tmpData['office_from'], 2, 2);
            $tmpData['start_second'] = substr($tmpData['office_from'], 4, 2);

            $tmpData['end_hour'] = substr($tmpData['office_to'], 0, 2);
            $tmpData['end_minute'] = substr($tmpData['office_to'], 2, 2);
            $tmpData['end_second'] = substr($tmpData['office_to'], 4, 2);
        }

        if ($oldData) {
            $oldData['start_hour'] = substr($oldData['office_from'], 0, 2);
            $oldData['start_minute'] = substr($oldData['office_from'], 2, 2);
            $oldData['start_second'] = substr($oldData['office_from'], 4, 2);

            $oldData['end_hour'] = substr($oldData['office_to'], 0, 2);
            $oldData['end_minute'] = substr($oldData['office_to'], 2, 2);
            $oldData['end_second'] = substr($oldData['office_to'], 4, 2);
        }

        $columnsToSend = ['start_hour', 'start_minute', 'start_second', 'end_hour', 'end_minute', 'end_second', 'action'];

        $filteredTmpData = array_intersect_key($tmpData, array_flip($columnsToSend));
        $filteredOldData = $oldData ? array_intersect_key($oldData, array_flip($columnsToSend)) : null;

        $response = [
            'old_data' => $filteredOldData,
            'new_data' => $filteredTmpData
        ];

        return response()->json($response, 200);
    }

    public function workingHourSendback(Request $request, $id)
    {
        $checker = $this->accessWorkingHourChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);

        $tmpData = WorkingHourTmp::findOrFail($id);
        $tmpData->form_status = 7;
        $tmpData->modified_by = null;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Send back Successfully!', 'success');
        return redirect()->back();
    }

    public function workingHourReject(Request $request, $id)
    {
        $checker = $this->accessWorkingHourChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);

        $tmpData = WorkingHourTmp::findOrFail($id);
        $tmpData->form_status = -1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Request rejected!!', 'danger');
        return Redirect::back();
    }
}
