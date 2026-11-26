@extends('layouts.admin')

@section('content')

<div class="row">
    <div class="col-md-12">

        <div class="mb-3">
            <h4>{{ $issueGroup->issueName->name }}</h4>
        </div>

        @php
            $touchGroups     = $subgroups->where('is_touch_point', 1);
            $nextLevelGroups = $subgroups->where('is_touch_point', 0);
        @endphp

        <div class="row">
            {{-- ================= NEXT LEVEL GROUPS ================= --}}
            <div class="col-md-6 group-section">
                @if($nextLevelGroups->isNotEmpty())
                    {!! Form::open([
                        'method' => 'post',
                        'action' => ['IssueGroupController@update', $issueGroup->id],
                        'class' => 'form-horizontal form-label-left',
                        'id' => 'nextLevelForm'
                    ]) !!}
                    <input type="hidden" name="section" value="next_level">

                    <h5 class="text-success border-bottom pb-1 mb-3">
                        Next Level Groups
                        <button type="button" class="btn btn-outline-success btn-sm ms-2 select-all-section" data-role="all">Select All</button>
                        <button type="button" class="btn btn-outline-success btn-sm ms-1 select-all-section" data-role="maker">Select All Maker</button>
                        <button type="button" class="btn btn-outline-success btn-sm ms-1 select-all-section" data-role="checker">Select All Checker</button>
                    </h5>

                    <div id="subgroupUserContainer">
                        @foreach($nextLevelGroups as $subgroup)

                            @php
                                // unit_id wise alada: 1 = Maker, 2 = Checker
                                $makerMembers   = collect($subgroup->issueGroupMembers)->where('unit_id', 1)->keyBy('user_id');
                                $checkerMembers = collect($subgroup->issueGroupMembers)->where('unit_id', 2)->keyBy('user_id');
                            @endphp

                            <div class="card mb-3">
                                <div class="card-header fw-bold">
                                    {{ $subgroup->name }}
                                </div>

                                <div class="card-body">
                                    <div class="row">

                                        {{-- MAKER --}}
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-primary">
                                                Maker
                                                <label class="form-check-label fw-normal ms-2" style="font-size: 12px">
                                                    <input type="checkbox" class="form-check-input select-all-role" data-role="maker"> All
                                                </label>
                                            </h6>

                                            @foreach($subgroup->userUnits as $unit)
                                                @php
                                                    $roles = $unit->user?->roles->pluck('display_name')->toArray() ?? [];
                                                    $issueUser = $makerMembers[$unit->user->id] ?? null;
                                                @endphp

                                                @if(in_array('Maker', $roles))
                                                    <div class="row align-items-center mb-2">
                                                        <div class="col-md-6">
                                                            <label class="form-check d-block">
                                                                <input type="checkbox"
                                                                    class="form-check-input maker-checkbox"
                                                                    name="users[{{ $unit->user->id }}][1][id]"
                                                                    value="{{ $unit->user->id }}"
                                                                    {{ $issueUser ? 'checked' : '' }}>
                                                                {{ $unit->user->name }} [{{ $unit->user->user_id }}]
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number"
                                                                class="form-control form-control-sm position-input"
                                                                name="users[{{ $unit->user->id }}][1][position]"
                                                                value="{{ $issueUser->ordering ?? '' }}"
                                                                placeholder="Position">
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        {{-- CHECKER --}}
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-success">
                                                Checker
                                                <label class="form-check-label fw-normal ms-2" style="font-size: 12px">
                                                    <input type="checkbox" class="form-check-input select-all-role" data-role="checker"> All
                                                </label>
                                            </h6>

                                            @foreach($subgroup->userUnits as $unit)
                                                @php
                                                    $roles = $unit->user?->roles->pluck('display_name')->toArray() ?? [];
                                                    $issueUser = $checkerMembers[$unit->user->id] ?? null;
                                                @endphp

                                                @if(in_array('Checker', $roles))
                                                    <div class="row align-items-center mb-2">
                                                        <div class="col-md-6">
                                                            <label class="form-check d-block">
                                                                <input type="checkbox"
                                                                    class="form-check-input checker-checkbox"
                                                                    name="users[{{ $unit->user->id }}][2][id]"
                                                                    value="{{ $unit->user->id }}"
                                                                    {{ $issueUser ? 'checked' : '' }}>
                                                                {{ $unit->user->name }} [{{ $unit->user->user_id }}]
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number"
                                                                class="form-control form-control-sm position-input"
                                                                name="users[{{ $unit->user->id }}][2][position]"
                                                                value="{{ $issueUser->ordering ?? '' }}"
                                                                placeholder="Position">
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-success">Update Next Level Group</button>
                    {!! Form::close() !!}
                @endif
            </div>

            {{-- ================= TOUCH GROUPS ================= --}}
            <div class="col-md-6 group-section">
                @if($touchGroups->isNotEmpty())
                    {!! Form::open([
                        'method' => 'post',
                        'action' => ['IssueGroupController@update', $issueGroup->id],
                        'class' => 'form-horizontal form-label-left',
                        'id' => 'touchForm'
                    ]) !!}
                    <input type="hidden" name="section" value="touch">

                    <h5 class="text-primary border-bottom pb-1 mb-3">
                        Touch Groups
                        <button type="button" class="btn btn-outline-primary btn-sm ms-2 select-all-section" data-role="all">Select All</button>
                        <button type="button" class="btn btn-outline-primary btn-sm ms-1 select-all-section" data-role="maker">Select All Maker</button>
                        <button type="button" class="btn btn-outline-primary btn-sm ms-1 select-all-section" data-role="checker">Select All Checker</button>
                    </h5>

                    <div id="touchGroupContainer">
                        @foreach($touchGroups as $subgroup)

                            @php
                                // unit_id wise alada: 1 = Maker, 2 = Checker
                                $makerMembers   = collect($subgroup->issueGroupMembers)->where('unit_id', 1)->keyBy('user_id');
                                $checkerMembers = collect($subgroup->issueGroupMembers)->where('unit_id', 2)->keyBy('user_id');

                                $makerUsers = $subgroup->userUnits->filter(fn ($u) =>
                                    in_array('Maker', $u->user?->roles->pluck('display_name')->toArray() ?? [])
                                );

                                $checkerUsers = $subgroup->userUnits->filter(fn ($u) =>
                                    in_array('Checker', $u->user?->roles->pluck('display_name')->toArray() ?? [])
                                );
                            @endphp

                            <div class="card mb-3">
                                <div class="card-header fw-bold">
                                    {{ $subgroup->name }}
                                </div>

                                <div class="card-body">
                                    <div class="row">

                                        {{-- MAKER --}}
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-primary">
                                                Maker
                                                @if($makerUsers->isNotEmpty())
                                                    <label class="form-check-label fw-normal ms-2" style="font-size: 12px">
                                                        <input type="checkbox" class="form-check-input select-all-role" data-role="maker"> All
                                                    </label>
                                                @endif
                                            </h6>

                                            @if($makerUsers->isEmpty())
                                                <span class="text-danger no-maker">No Maker user found</span>
                                            @else
                                                @foreach($makerUsers as $unit)
                                                    @php $issueUser = $makerMembers[$unit->user->id] ?? null; @endphp
                                                    <div class="row align-items-center mb-2">
                                                        <div class="col-md-6">
                                                            <label class="form-check d-block">
                                                                <input type="checkbox"
                                                                    class="form-check-input maker-checkbox"
                                                                    name="users[{{ $unit->user->id }}][1][id]"
                                                                    value="{{ $unit->user->id }}"
                                                                    {{ $issueUser ? 'checked' : '' }}>
                                                                {{ $unit->user->name }} [{{ $unit->user->user_id }}]
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number"
                                                                class="form-control form-control-sm position-input"
                                                                name="users[{{ $unit->user->id }}][1][position]"
                                                                value="{{ $issueUser->ordering ?? '' }}"
                                                                placeholder="Position">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                        {{-- CHECKER --}}
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-success">
                                                Checker
                                                @if($subgroup->touch_checker == 1 && $checkerUsers->isNotEmpty())
                                                    <label class="form-check-label fw-normal ms-2" style="font-size: 12px">
                                                        <input type="checkbox" class="form-check-input select-all-role" data-role="checker"> All
                                                    </label>
                                                @endif
                                            </h6>

                                            @if($subgroup->touch_checker != 1)
                                                <span class="text-warning">Checker skipped</span>
                                            @elseif($checkerUsers->isEmpty())
                                                <span class="text-danger no-checker">No Checker user found</span>
                                            @else
                                                @foreach($checkerUsers as $unit)
                                                    @php $issueUser = $checkerMembers[$unit->user->id] ?? null; @endphp
                                                    <div class="row align-items-center mb-2">
                                                        <div class="col-md-6">
                                                            <label class="form-check d-block">
                                                                <input type="checkbox"
                                                                    class="form-check-input checker-checkbox"
                                                                    name="users[{{ $unit->user->id }}][2][id]"
                                                                    value="{{ $unit->user->id }}"
                                                                    {{ $issueUser ? 'checked' : '' }}>
                                                                {{ $unit->user->name }} [{{ $unit->user->user_id }}]
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number"
                                                                class="form-control form-control-sm position-input"
                                                                name="users[{{ $unit->user->id }}][2][position]"
                                                                value="{{ $issueUser->ordering ?? '' }}"
                                                                placeholder="Position">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-primary">Update Touch Group</button>
                    {!! Form::close() !!}
                @endif
            </div>
        </div>

    </div>
</div>

<div class="ln_solid"></div>
<div class="form-group mt-2">
    <button type="button" class="btn btn-info" onclick="cancel('/issue/group')">Back</button>
</div>
@endsection

@section('script')
    @include('Issue-group.component.member-select-script', ['container' => '#subgroupUserContainer, #touchGroupContainer'])
@endsection
