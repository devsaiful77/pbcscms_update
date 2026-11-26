@php
    $bpid = $bpId->bp_id;
@endphp

<td colspan="4" width="100%" class="none-bottom-border">
    <table class="table">
        <tr>
            <td class="vcenter">
                <div style="display: flex; gap: 10px">
                    <strong>Treasury Type <span class="required">*</span></strong>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="treasury_type" value="treasury_bill"
                                id="flexRadioDefault1">
                            <label style="padding: 0" class="form-check-label" for="flexRadioDefault1">
                                Treasury Bill
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="treasury_type" value="treasury_bound"
                                id="flexRadioDefault2">
                            <label style="padding: 0" class="form-check-label" for="flexRadioDefault2">
                                Treasury Bound
                            </label>
                        </div>
                    </div>
                </div>
            </td>
        </tr>


        <tr>
            <td class="vcenter">
                <div style="display: flex; flex-direction: column; gap: 6px;" id="treasury_bill">

                    <strong>Treasury Bill <span class="required">*</span></strong>

                    <div style="display: flex; align-items: center; gap: 6px; margin-left: 20px">
                        @foreach ($treasury_bills as $bill)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="treasury_bill[]"
                                    value="{{ $bill->id }}" id="treasury_bill_{{ $bill->id }}">

                                <label style="padding: 0" class="form-check-label"
                                    for="treasury_bill_{{ $bill->id }}">
                                    {{ $bill->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                </div>

                {{-- treasury bound --}}
                <div style="display: flex; flex-direction: column; gap: 6px;" id="treasury_bound">

                    <strong>Treasury Bound <span class="required">*</span></strong>

                    <div style="display: flex; align-items: center; gap: 6px; margin-left: 20px">
                        @foreach ($treasury_bounds as $bound)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="treasury_bound[]"
                                    value="{{ $bound->id }}" id="treasury_bound_{{ $bound->id }}">

                                <label style="padding: 0" class="form-check-label"
                                    for="treasury_bound_{{ $bound->id }}">
                                    {{ $bound->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                </div>
            </td>

        </tr>

    </table>
</td>


<script>
    $(document).ready(function() {

        // Hide both initially
        $('input[name="bp_id"]').val('{{ $bpid }}');
        $('#treasury_bill').hide();
        $('#treasury_bound').hide();

        // Listen for radio selection
        $('input[name="treasury_type"]').on('change', function() {
            let type = $(this).val();



            if (type === 'treasury_bill') {
                $('#treasury_bill').show();
                $('#treasury_bound').hide();
            }

            if (type === 'treasury_bound') {
                $('#treasury_bill').hide();
                $('#treasury_bound').show();
            }
        });

    });
</script>
