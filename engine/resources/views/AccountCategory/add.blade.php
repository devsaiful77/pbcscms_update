@extends('layouts.admin')

@section('content')

@if ($id != null)
  {!!
      Form::open([
        'method' => 'post',
        'action' => ['AccountCategoryController@update', $id],
        'id'     => 'formId',
        'class'  => 'form-horizontal form-label-left',
        'enctype'=> 'multipart/form-data'
      ]);
  !!}
@else
  {!!
      Form::open([
        'method' => 'post',
        'action' => ['AccountCategoryController@store'],
        'id'     => 'formId',
        'class'  => 'form-horizontal form-label-left',
        'enctype'=> 'multipart/form-data'
      ]);
  !!}
@endif
  {!! Form::token(); !!}

  <!-- Category Code -->
  <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="categ_code">Category Code <span class="required text-danger">*</span>
    </label>
    <div class="col-md-6 col-sm-6 col-xs-12">
      {!!
        Form::text('categ_code', (!empty($dataForView["categ_code"])) ? $dataForView["categ_code"] : '', [
          'class' => 'form-control',
          'label' => false,
          'autocomplete' => 'off',
          'type' => 'text',
          'autofocus' => 'true',
          'placeholder' => 'Category Code (e.g. 6001)'
        ]);
      !!}
      {{ Form::hidden('tmpId', (!empty($tmpId)) ? $tmpId : '') }}
    </div>
    <div class="error text-danger">{{ $errors->first('categ_code') }}</div>
  </div>

  <!-- Title -->
  <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Title <span class="required text-danger">*</span>
    </label>
    <div class="col-md-6 col-sm-6 col-xs-12">
      {!!
        Form::text('title', (!empty($dataForView["title"])) ? $dataForView["title"] : '', [
          'class' => 'form-control',
          'label' => false,
          'autocomplete' => 'off',
          'type' => 'text',
          'placeholder' => 'Account Category Title'
        ]);
      !!}
    </div>
    <div class="error text-danger">{{ $errors->first('title') }}</div>
  </div>

  <!-- Short ID -->
  <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="short_id">Short ID
    </label>
    <div class="col-md-6 col-sm-6 col-xs-12">
      {!!
        Form::number('short_id', (!empty($dataForView["short_id"])) ? $dataForView["short_id"] : '', [
          'class' => 'form-control',
          'label' => false,
          'autocomplete' => 'off',
          'placeholder' => 'Like 21, 23, 24'
        ]);
      !!}
    </div>
    <div class="error text-danger">{{ $errors->first('short_id') }}</div>
  </div>

  <!-- Account Type -->
  <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ac_type">Account Type
    </label>
    <div class="col-md-6 col-sm-6 col-xs-12">
      {!!
        Form::text('ac_type', (!empty($dataForView["ac_type"])) ? $dataForView["ac_type"] : '', [
          'class' => 'form-control',
          'label' => false,
          'autocomplete' => 'off',
          'placeholder' => 'Account Type (e.g. Savings, Current)'
        ]);
      !!}
    </div>
    <div class="error text-danger">{{ $errors->first('ac_type') }}</div>
  </div>

  <!-- Description -->
  <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="description">Description
    </label>
    <div class="col-md-6 col-sm-6 col-xs-12">
      {!!
        Form::textarea('description', (!empty($dataForView["description"])) ? $dataForView["description"] : '', [
          'rows' => 3,
          'class' => 'form-control',
          'label' => false,
          'autocomplete' => 'off',
          'placeholder' => 'Description (max 100 characters)'
        ]);
      !!}
    </div>
    <div class="error text-danger">{{ $errors->first('description') }}</div>
  </div>

  <div class="ln_solid">&nbsp;</div>
  <div class="form-group">
      <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        <?php $additionalParams = (!empty($searchDataForView)) ? '?' . http_build_query($searchDataForView) : ""; ?>
        {{ Form::hidden('additionalParams', $additionalParams) }}

        {!!
          Form::submit((!empty($id)) ? 'Update' : 'Submit', [
            'class' => 'btn btn-primary gradient',
            'title' => (!empty($id)) ? 'Update' : 'Submit',
            'escape' => false
          ]);
        !!}
        <button type="button" class="btn btn-info gradient" onclick="cancel('/AccountCategory{{ $additionalParams }}')">Back</button>
      </div>
  </div>

{!! Form::close(); !!}
@endsection
