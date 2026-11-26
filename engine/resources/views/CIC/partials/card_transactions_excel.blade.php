<?php
    // API dates come as yyyymmdd
    $tranDate = function ($date) {
        $dt = \DateTime::createFromFormat('Ymd', substr((string) $date, 0, 8));
        return $dt ? $dt->format('d M Y') : $date;
    };
    $amount = function ($row) {
        return is_numeric($row['billingAmount'] ?? null) ? (float) $row['billingAmount'] : (is_numeric($row['tranAmt'] ?? null) ? (float) $row['tranAmt'] : 0);
    };
?>
<table>
    <tr><td colspan="8">{{ collect($statements)->contains(function ($item) { return !empty($item['card']); }) ? 'Card Transaction Statement' : 'Account Statement' }}</td></tr>
    <tr><td><b>Customer Name</b></td><td colspan="7">{{ $customerName }}</td></tr>
    <tr><td><b>Account Number</b></td><td colspan="7" data-type="s">{{ $accountNumber }}</td></tr>
    <tr><td><b>Period</b></td><td colspan="7">{{ $fromDate->format('d M Y') }} to {{ $toDate->format('d M Y') }}</td></tr>
    <tr><td><b>Generated At</b></td><td colspan="7">{{ date('d M Y h:i A') }}</td></tr>

    @foreach($statements as $item)
        <?php
            $totalDebit = 0;
            $totalCredit = 0;
        ?>
        <tr><td colspan="8"></td></tr>
        <tr>
            @if(!empty($item['card']))
                <td colspan="8"><b>Card: {{ $item['statement']['cardNumber'] ?? ($item['card']['aliasPan'] ?? '') }} ({{ trim(($item['card']['cardType'] ?? '') . ' ' . ($item['card']['cardProductName'] ?? '')) }})</b></td>
            @else
                <td colspan="8"><b>Balance at Period Start: {{ is_numeric($item['statement']['openingBalance'] ?? null) ? number_format($item['statement']['openingBalance'], 2) : '—' }}</b></td>
            @endif
        </tr>
        @if($item['error'])
            <tr><td colspan="8">{{ $item['error'] }}</td></tr>
        @else
            <tr>
                <th><b>SL.</b></th>
                <th><b>Tran. Date</b></th>
                <th><b>Post Date</b></th>
                <th><b>Description</b></th>
                <th><b>Currency</b></th>
                <th><b>Dr/Cr</b></th>
                <th><b>Amount</b></th>
                <th><b>Transaction ID</b></th>
            </tr>
            @forelse($item['rows'] as $i => $row)
                <?php
                    $isCredit = strtoupper($row['transSign'] ?? '') == 'C';
                    if ($isCredit) {
                        $totalCredit += $amount($row);
                    } else {
                        $totalDebit += $amount($row);
                    }
                ?>
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $tranDate($row['tranDate'] ?? '') }}</td>
                    <td>{{ $tranDate($row['postDate'] ?? '') }}</td>
                    <td>{{ $row['tranDesc'] ?? '' }}</td>
                    <td data-type="s">{{ $row['billingCcy'] ?? ($row['tranCcy'] ?? '') }}</td>
                    <td>{{ $isCredit ? 'Cr' : 'Dr' }}</td>
                    <td data-format="#,##0.00">{{ $amount($row) }}</td>
                    <td data-type="s">{{ $row['transId'] ?? '' }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No transaction found</td></tr>
            @endforelse
            @if(count($item['rows']))
                <tr>
                    <td colspan="6"><b>Total Debit</b></td>
                    <td data-format="#,##0.00"><b>{{ $totalDebit }}</b></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="6"><b>Total Credit</b></td>
                    <td data-format="#,##0.00"><b>{{ $totalCredit }}</b></td>
                    <td></td>
                </tr>
            @endif
        @endif
    @endforeach
</table>
