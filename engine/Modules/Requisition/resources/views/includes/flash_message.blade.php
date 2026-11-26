<div class="form-element-area">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if (flash()->message)
                    <div class="alert alert-{{ flash()->class }} alert-dismissible fade show text-center fw-bold" style="font-size: 14px;">
                        {!! flash()->message !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
