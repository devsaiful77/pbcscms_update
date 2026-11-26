{{-- touch-card e validation shithil (user select optional) --}}
<div class="card mb-3 {{ !empty($isTouch) ? 'touch-card' : '' }}">
    <div class="card-header fw-bold">
        {{ $subgroup->name }}
    </div>

    @php
        $makerUsers = $subgroup->userUnits->filter(function ($unit) {
            $roles = $unit->user?->roles->pluck('display_name')->toArray() ?? [];
            return in_array('Maker', $roles);
        });

        $checkerUsers = $subgroup->userUnits->filter(function ($unit) {
            $roles = $unit->user?->roles->pluck('display_name')->toArray() ?? [];
            return in_array('Checker', $roles);
        });
    @endphp


    <div class="card-body">
        <div class="row">
            {{-- ================= Maker ================= --}}
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
                        <div class="row align-items-center mb-2">
                            <div class="col-md-6">
                                <label class="form-check d-block">
                                    <input type="checkbox"
                                        name="users[{{ $unit->user->id }}][1][id]"
                                        value="{{ $unit->user->id }}"
                                        class="form-check-input maker-checkbox">
                                    {{ $unit->user->name }} [{{ $unit->user->user_id }}]
                                </label>
                            </div>

                            <div class="col-md-6" style="padding-top: 0.75rem">
                                <input type="number"
                                    name="users[{{ $unit->user->id }}][1][position]"
                                    class="form-control form-control-sm position-input"
                                    placeholder="Position">
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
            {{-- ================= Checker ================= --}}
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
                    <span class="text-warning">Checker skipped for this subgroup</span>
                @elseif($checkerUsers->isEmpty())
                    <span class="text-danger no-checker">No Checker user found</span>
                @else
                    @foreach($checkerUsers as $unit)
                        <div class="row align-items-center mb-2">
                            <div class="col-md-6">
                                <label class="form-check d-block">
                                    <input type="checkbox"
                                        name="users[{{ $unit->user->id }}][2][id]"
                                        value="{{ $unit->user->id }}"
                                        class="form-check-input checker-checkbox">
                                    {{ $unit->user->name }} [{{ $unit->user->user_id }}]
                                </label>
                            </div>

                            <div class="col-md-6" style="padding-top: 0.75rem">
                                <input type="number"
                                    name="users[{{ $unit->user->id }}][2][position]"
                                    class="form-control form-control-sm position-input"
                                    placeholder="Position">
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
        </div>
    </div>
</div>
