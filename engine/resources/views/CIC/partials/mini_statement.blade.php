<?php
    // API date comes as DD-MM-YYYY or YYYYMMDD => 30 September 2025
    $miniDate = function ($date) {
        $dt = \DateTime::createFromFormat('!d-m-Y', (string) $date) ?: \DateTime::createFromFormat('!Ymd', substr((string) $date, 0, 8));
        return $dt ? $dt->format('d F Y') : $date;
    };
    // Dr/Cr from the API sign field when sent, otherwise from the amount sign
    $isDebit = function ($row) {
        $sign = strtoupper(trim($row['transSign'] ?? ($row['drCrIndicator'] ?? '')));
        if ($sign !== '') {
            return $sign == 'D';
        }
        return is_numeric($row['transactionAmount'] ?? null) && $row['transactionAmount'] < 0;
    };
?>
@foreach($statements as $item)
    <?php $info = $item['info'] ?? []; ?>
    <div class="cic-stmt-card">
        <div class="cic-stmt-head">
            <span><i class="fa fa-university"></i> Mini A/C Statement of <b class="cic-mono">{{ ($info['accountNumber'] ?? '') ?: ($accountNumber ?: '—') }}</b></span>
            <span><i class="fa fa-credit-card"></i> Card <b class="cic-mono">{{ $item['statement']['cardNumber'] ?? ($item['card']['cardNumber'] ?? '—') }}</b></span>
            <span><i class="fa fa-calendar"></i> <b>{{ $fromDate->format('d M Y') }}</b> to <b>{{ $toDate->format('d M Y') }}</b></span>
            @if(!$item['error'])
                <span class="count"><b>{{ count($item['rows']) }}</b> transactions</span>
            @endif
        </div>

        @if($item['error'])
            <div class="cic-alert" style="margin:14px">{{ $item['error'] }}</div>
        @else
            <div class="table-responsive">
                <table class="cic-stmt-table">
                    <thead>
                    <tr>
                        <th style="width:150px">Date</th>
                        <th>Description</th>
                        <th style="width:120px">Debit/Credit</th>
                        <th class="amount" style="width:150px">Txn Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($item['rows'] as $row)
                        <?php $debit = $isDebit($row); ?>
                        <tr>
                            <td><span class="cic-date-badge">{{ $miniDate($row['transactionDate'] ?? '') }}</span></td>
                            <td>{{ $row['descriptionOfTransaction'] ?? '' }}</td>
                            <td><span class="cic-pill {{ $debit ? 'bad' : 'good' }}">{{ $debit ? 'D-' : 'C+' }}</span></td>
                            <td class="amount">{{ is_numeric($row['transactionAmount'] ?? null) ? number_format(abs($row['transactionAmount']), 2) : '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="cic-empty"><i class="fa fa-inbox"></i> No transaction found</td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right">Current Working Balance :</td>
                        <td class="amount">{{ is_numeric($info['workingBalance'] ?? null) ? number_format($info['workingBalance'], 2) : '—' }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
@endforeach
