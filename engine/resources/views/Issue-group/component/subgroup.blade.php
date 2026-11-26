@php
    $nextLevelGroups = $subgroups->where('is_touch_point', 0);
    $touchGroups     = $subgroups->where('is_touch_point', 1);
@endphp

<div class="row">
    {{-- ================= NEXT LEVEL GROUPS ================= --}}
    <div class="col-md-6 group-section">
        <h4 class="mb-3" style="font-size: 18px">
            Next Level Group
            @if($nextLevelGroups->isNotEmpty())
                <button type="button" class="btn btn-outline-success btn-sm ms-2 select-all-section" data-role="all">Select All</button>
                <button type="button" class="btn btn-outline-success btn-sm ms-1 select-all-section" data-role="maker">Select All Maker</button>
                <button type="button" class="btn btn-outline-success btn-sm ms-1 select-all-section" data-role="checker">Select All Checker</button>
            @endif
        </h4>
        @forelse($nextLevelGroups as $subgroup)
            @include('Issue-group.component.subgroup-card', ['subgroup' => $subgroup, 'isTouch' => false])
        @empty
            <p class="text-muted">Not Found!</p>
        @endforelse
    </div>

    {{-- ================= TOUCH GROUPS ================= --}}
    <div class="col-md-6 group-section">
        <h4 class="mb-3" style="font-size: 18px">
            Touch Group
            @if($touchGroups->isNotEmpty())
                <button type="button" class="btn btn-outline-primary btn-sm ms-2 select-all-section" data-role="all">Select All</button>
                <button type="button" class="btn btn-outline-primary btn-sm ms-1 select-all-section" data-role="maker">Select All Maker</button>
                <button type="button" class="btn btn-outline-primary btn-sm ms-1 select-all-section" data-role="checker">Select All Checker</button>
            @endif
        </h4>
        @forelse($touchGroups as $subgroup)
            @include('Issue-group.component.subgroup-card', ['subgroup' => $subgroup, 'isTouch' => true])
        @empty
            <p class="text-muted">Not Found!</p>
        @endforelse
    </div>
</div>
