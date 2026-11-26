@extends('layouts.admin')
@section('content')

<div class="bs-example mb-4 mt-3">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ $title_for_layout }}</li>
        </ol>
    </nav>
</div>

<div class="mt-3">
    <div class="card">
        <div class="card-header bg-light">
            <h4 class="card-title mb-0">{{ $title_for_layout }}</h4>
            <small class="text-danger d-block mt-2 font-weight-bold">
                <i class="fa fa-exclamation-triangle"></i> Make sure to add
                <code>{requisition_number}</code> for Ticket Number
            </small>
        </div>

        <div class="card-body">
            {!!
               Form::open([
                 'method' => 'post',
                 'route' => ['requisition.sms-emails.store', encrypt($id)],
                 'enctype' => 'multipart/form-data'
               ]);
            !!}
            {!! Form::token(); !!}

            <div class="table-responsive">
                <table class="table table-bordered table-condensed table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 25%;" class="text-white requ-th-color">Template Type</th>
                            <th style="width: 60%;" class="text-white requ-th-color">Template Content</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold align-middle" rowspan="1">Requisition Opening SMS</td>
                            <td>
                               {{
                                   Form::textarea('issue_opening_sms', (!empty($dataForView["issue_opening_sms"])) ? $dataForView["issue_opening_sms"] : '', [
                                     'rows' => 3,
                                     'class' => 'form-control',
                                     'placeholder' => 'Requisition Opening SMS'
                                   ])
                               }}
                            </td>
                        </tr>

                        <tr>
                            <td class="font-weight-bold align-middle" rowspan="1">Requisition Opening EMAIL</td>
                            <td>
                               {{
                                   Form::textarea('issue_opening_email', (!empty($dataForView["issue_opening_email"])) ? $dataForView["issue_opening_email"] : '', [
                                     'rows' => 4,
                                     'class' => 'form-control',
                                     'placeholder' => 'Requisition Opening EMAIL'
                                   ])
                               }}
                            </td>
                        </tr>

                        <tr>
                            <td class="font-weight-bold align-middle" rowspan="1">Requisition Closing SMS</td>
                            <td>
                               {{
                                   Form::textarea('issue_closing_sms', (!empty($dataForView["issue_closing_sms"])) ? $dataForView["issue_closing_sms"] : '', [
                                     'rows' => 3,
                                     'class' => 'form-control',
                                     'placeholder' => 'Requisition Closing SMS'
                                   ])
                               }}
                            </td>
                        </tr>

                        <tr>
                            <td class="font-weight-bold align-middle" rowspan="1">Requisition Closing EMAIL</td>
                            <td>
                               {{
                                  Form::textarea('issue_closing_email', (!empty($dataForView["issue_closing_email"])) ? $dataForView["issue_closing_email"] : '', [
                                     'rows' => 4,
                                     'class' => 'form-control',
                                     'placeholder' => 'Requisition Closing EMAIL'
                                  ])
                               }}
                            </td>
                        </tr>

                        <tr>
                            <td class="font-weight-bold align-middle" rowspan="1">Requisition Approve SMS</td>
                            <td>
                               {{
                                   Form::textarea('requisition_approve_sms', (!empty($dataForView["requisition_approve_sms"])) ? $dataForView["requisition_approve_sms"] : '', [
                                     'rows' => 3,
                                     'class' => 'form-control',
                                     'placeholder' => 'Requisition Approve SMS'
                                   ])
                               }}
                            </td>
                        </tr>

                        <tr>
                            <td class="font-weight-bold align-middle" rowspan="1">Requisition Approve EMAIL</td>
                            <td>
                               {{
                                   Form::textarea('requisition_approve_email', (!empty($dataForView["requisition_approve_email"])) ? $dataForView["requisition_approve_email"] : '', [
                                     'rows' => 4,
                                     'class' => 'form-control',
                                     'placeholder' => 'Requisition Approve EMAIL'
                                   ])
                               }}
                            </td>
                        </tr>

                        <tr>
                            <td class="font-weight-bold align-middle" rowspan="1">Requisition Cancel SMS</td>
                            <td>
                               {{
                                   Form::textarea('requisition_cancel_sms', (!empty($dataForView["requisition_cancel_sms"])) ? $dataForView["requisition_cancel_sms"] : '', [
                                     'rows' => 3,
                                     'class' => 'form-control',
                                     'placeholder' => 'Requisition Cancel SMS'
                                   ])
                               }}
                            </td>
                        </tr>

                        <tr>
                            <td class="font-weight-bold align-middle" rowspan="1">Requisition Cancel EMAIL</td>
                            <td>
                               {{
                                   Form::textarea('requisition_cancel_email', (!empty($dataForView["requisition_cancel_email"])) ? $dataForView["requisition_cancel_email"] : '', [
                                     'rows' => 4,
                                     'class' => 'form-control',
                                     'placeholder' => 'Requisition Cancel EMAIL'
                                   ])
                               }}
                            </td>
                        </tr>

                        <tr>
                            <td class="font-weight-bold align-middle" rowspan="1">Requisition Sendback SMS</td>
                            <td>
                               {{
                                   Form::textarea('requisition_sendback_sms', (!empty($dataForView["requisition_sendback_sms"])) ? $dataForView["requisition_sendback_sms"] : '', [
                                     'rows' => 3,
                                     'class' => 'form-control',
                                     'placeholder' => 'Requisition Sendback SMS'
                                   ])
                               }}
                            </td>
                        </tr>

                        <tr>
                            <td class="font-weight-bold align-middle" rowspan="1">Requisition Sendback EMAIL</td>
                            <td>
                               {{
                                   Form::textarea('requisition_sendback_email', (!empty($dataForView["requisition_sendback_email"])) ? $dataForView["requisition_sendback_email"] : '', [
                                     'rows' => 4,
                                     'class' => 'form-control',
                                     'placeholder' => 'Requisition Sendback EMAIL'
                                   ])
                               }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 text-right">
                <button class="btn btn-primary gradient px-4" type="submit">
                    <i class="fa fa-check"></i> Save Templates
                </button>
            </div>

            {!! Form::close(); !!}
        </div>
    </div>
</div>

@endsection