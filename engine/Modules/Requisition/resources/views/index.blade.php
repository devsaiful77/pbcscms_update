@extends('requisition::layouts.master')

@section('content')
<div class="border mx-auto my-5" style="width: 600px; height: 250px;">
    <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center">
      <h3 class="h3">Welcome To</h3>
      <h4 class="h4"><strong>Requisition Management Module</strong></h4>
      <div class="my-2">
            <img src="{{ asset('public/img/favicon.ico') }}" alt="Prime Bank Logo" style="height: 22px;">
      </div>
      <h4 class="h6">Prime Bank PLC</h4>
    </div>
</div>
@endsection
