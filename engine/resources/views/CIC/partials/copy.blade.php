@if(!empty(trim((string) $text)))
    <button type="button" class="cic-copy" data-copy="{{ trim((string) $text) }}" onclick="cicCopy(this)" title="Copy"><i class="fa fa-clone"></i></button>
@endif
