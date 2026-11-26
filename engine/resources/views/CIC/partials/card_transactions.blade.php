<?php
    // API dates come as yyyymmdd => 18 OCT 26
    $tranDate = function ($date) {
        $dt = \DateTime::createFromFormat('!Ymd', substr((string) $date, 0, 8));
        return $dt ? strtoupper($dt->format('d M y')) : $date;
    };
    $money = function ($value) {
        return is_numeric($value) ? number_format($value, 2) : '—';
    };
?>
@foreach($statements as $item)
    <?php
        $amount = function ($row) {
            return is_numeric($row['billingAmount'] ?? null) ? (float) $row['billingAmount'] : (is_numeric($row['tranAmt'] ?? null) ? (float) $row['tranAmt'] : 0);
        };
        $info = $item['info'] ?? [];
        // Running balance only when the API gives the balance at period start
        $openingBalance = $item['statement']['openingBalance'] ?? null;
        $balance = is_numeric($openingBalance) ? (float) $openingBalance : null;
        $totalDebit = collect($item['rows'])->filter(function ($row) {
            return strtoupper($row['transSign'] ?? '') != 'C';
        })->sum($amount);
        $totalCredit = collect($item['rows'])->filter(function ($row) {
            return strtoupper($row['transSign'] ?? '') == 'C';
        })->sum($amount);
    ?>
    <div class="cic-stmt-card">
        <div class="cic-stmt-head">
            <span><i class="fa fa-building"></i> Branch <b>{{ ($info['branch'] ?? '') ?: '—' }}</b></span>
            <span><i class="fa fa-university"></i> A/C Number <b class="cic-mono">{{ ($info['accountNumber'] ?? '') ?: ($accountNumber ?: '—') }}</b></span>
            <span><i class="fa fa-id-card-o"></i> Customer ID <b class="cic-mono">{{ ($info['customerId'] ?? '') ?: '—' }}</b></span>
            <span><i class="fa fa-user"></i> A/C Title <b>{{ ($info['title'] ?? '') ?: '—' }}</b></span>
            <span><i class="fa fa-tag"></i> A/C Type <b>{{ ($info['accountType'] ?? '') ?: '—' }}</b></span>
            @if(!empty($item['card']))
                <span><i class="fa fa-credit-card"></i> Card <b class="cic-mono">{{ $item['statement']['cardNumber'] ?? ($item['card']['cardNumber'] ?? '—') }}</b></span>
            @endif
            <span><i class="fa fa-calendar"></i> Statement from <b>{{ $fromDate->format('d/m/Y') }}</b> to <b>{{ $toDate->format('d/m/Y') }}</b></span>
            @if(!$item['error'])
                <span class="count"><b>{{ count($item['rows']) }}</b> transactions</span>
            @endif
        </div>

        @if($item['error'])
            <div class="cic-alert" style="margin:14px">{{ $item['error'] }}</div>
        @else
            <div class="table-responsive">
                <table class="cic-stmt-table cic-txn-table">
                    <thead>
                    <tr>
                        <td colspan="4" style="text-align:right">Balance at Period Start</td>
                        <td class="amount">{{ $money($balance) }}</td>
                    </tr>
                    <tr>
                        <th style="width:130px">Date</th>
                        <th>Description</th>
                        <th class="amount" style="width:150px">Debit</th>
                        <th class="amount" style="width:150px">Credit</th>
                        <th class="amount" style="width:150px">Balance</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($item['rows'] as $i => $row)
                        <?php
                            $isCredit = strtoupper($row['transSign'] ?? '') == 'C';
                            $value = $amount($row);
                            if ($balance !== null) {
                                $balance += $isCredit ? $value : -$value;
                            }
                        ?>
                        <tr>
                            <td><span class="cic-date-badge">{{ $tranDate($row['tranDate'] ?? '') }}</span></td>
                            <td>{{ $row['tranDesc'] ?? '' }}</td>
                            <td class="amount cic-dr">{{ $isCredit ? '0' : number_format($value, 2) }}</td>
                            <td class="amount cic-cr">{{ $isCredit ? number_format($value, 2) : '0' }}</td>
                            <td class="amount">{{ $money($balance) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="cic-empty"><i class="fa fa-inbox"></i> No transaction found</td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                    @if(count($item['rows']))
                        <tr>
                            <td colspan="2" style="text-align:right">Total</td>
                            <td class="amount cic-dr">{{ number_format($totalDebit, 2) }}</td>
                            <td class="amount cic-cr">{{ number_format($totalCredit, 2) }}</td>
                            <td></td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="4" style="text-align:right">Current Working Balance</td>
                        <td class="amount">{{ $money($info['workingBalance'] ?? null) }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
@endforeach
