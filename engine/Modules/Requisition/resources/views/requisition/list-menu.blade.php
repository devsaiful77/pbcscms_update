<style>
    .btn-flat {
        border-radius: 0 !important;
        width: 100%;
    }

    .btn-group .btn {
        flex: 1 1 0;
        text-align: center;
    }
</style>

<div class="mt-3 mb-3">
    <div class="d-flex justify-content-center gap-2" role="group" aria-label="Service Type">
        <div>
            <input type="radio" class="btn-check" name="serviceType" id="generalSupport" autocomplete="off"
                   onclick="window.location='{{ route('requisition.general-service-list') }}'"
                   @if (request()->routeIs('requisition.general-service-list')) checked @endif>
            <label class="btn btn-outline-primary btn-flat" for="generalSupport">
                <i class="fa fa-cog"></i> General Support Service
            </label>
        </div>

        <div>
            <input type="radio" class="btn-check" name="serviceType" id="printingStationery" autocomplete="off"
                   onclick="window.location='{{ route('requisition.printing-item-list') }}'"
                   @if (request()->routeIs('requisition.printing-item-list')) checked @endif>
            <label class="btn btn-outline-primary btn-flat" for="printingStationery">
                <i class="fa fa-print"></i> Printing Stationery
            </label>
        </div>

       <div>
           <input type="radio" class="btn-check" name="serviceType" id="tableStationery" autocomplete="off"
                  onclick="window.location='{{ route('requisition.table-stationery-list') }}'"
                  @if (request()->routeIs('requisition.table-stationery-list')) checked @endif>
           <label class="btn btn-outline-primary btn-flat" for="tableStationery">
               <i class="fa fa-file"></i> Table Stationery
           </label>
       </div>

    </div>
</div>


{{-- Auto trigger general service route only on first visit --}}
@if (
    !request()->routeIs('requisition.general-service-list') &&
    !request()->routeIs('requisition.printing-item-list') &&
    !request()->routeIs('requisition.table-stationery-list')
)
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            document.getElementById('generalSupport').click();
        });
    </script>
@endif


