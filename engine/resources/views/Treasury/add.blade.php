@extends('layouts.admin')

@section('content')

@IF ($id != null)
  {!!
      Form::open([
        'method'=>'post',
        'action' => ['TreasuryBillController@update',$id] ,

        'id'=>'formId',
        'class'=>'form-horizontal form-label-left',
        'enctype' => 'multipart/form-data'
      ]);
  !!}
@ELSE
  {!!
      Form::open([
        'method'=>'post',
        'action' => ['TreasuryBillController@store'] ,
        'id'=>'formId',
        'class'=>'form-horizontal form-label-left',
        'enctype' => 'multipart/form-data'
      ]);
  !!}
@ENDIF
  {!! Form::token(); !!}

  <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span class="required">*</span>
    </label>

      <div class="col-md-6 col-sm-6 col-xs-12">
        {!!
          Form::text('name',(!empty($dataForView["name"])) ? $dataForView["name"] : '' ,[
            'class' => 'form-control',
            'label'=>false,
            'autocomplete'=>'off',
            'type'=>'text',
            'autofocus'=>'true',
            'placeholder'=>'Name'
          ]);
        !!}
        {{ Form::hidden('tmpId',(!empty($tmpId)) ? $tmpId : '' ) }}
      </div>
      <div class="error">{{ $errors->first('name') }}</div>
  </div><!-- Name -->
  <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drow_day">Week
    </label>
    <div class="col-md-6 col-sm-6 col-xs-12">
      {!!
        Form::text('week',(!empty($dataForView["week"])) ? $dataForView["week"] : ''  ,[
          'rows'=>3,
          'class' => 'form-control',
          'label'=>false,
          'autocomplete'=>'off',
          'placeholder'=>''
        ]);
      !!}
    </div>
    <div class="error">{{ $errors->first('week') }}</div>
  </div><!-- drow_day -->
  <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drow_day">count
    </label>
    <div class="col-md-6 col-sm-6 col-xs-12">
      {!!
        Form::text('count',(!empty($dataForView["count"])) ? $dataForView["count"] : ''  ,[
          'rows'=>3,
          'class' => 'form-control',
          'label'=>false,
          'autocomplete'=>'off',
          'placeholder'=>''
        ]);
      !!}
    </div>
    <div class="error">{{ $errors->first('count') }}</div>
  </div><!-- drow_count -->

  <div class="ln_solid">&nbsp;</div>
  <div class="form-group">
      <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        <?php $additionalParams = (!empty($searchDataForView)) ? '?'.http_build_query($searchDataForView) : ""; ?>
        {{ Form::hidden('additionalParams',$additionalParams) }}

        {!!
          Form::submit((!empty($id)) ? 'Update':'Submit',array(
            'class'=>'btn btn-primary gradient',
            'title'=>'Add',
            'escape'=>false
          ));
        !!}
        <button type="button" class="btn btn-info gradient" onclick="cancel('/treasury-bill{{ $additionalParams}}')">Back</button>
      </div>
  </div>

{!! Form::close(); !!}
@endsection
