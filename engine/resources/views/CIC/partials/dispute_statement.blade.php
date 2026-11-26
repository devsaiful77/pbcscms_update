<?php
    $dash = function ($value) {
        return ($value === null || trim((string) $value) === '') ? '—' : trim((string) $value);
    };
    // API dates come as YYYYMMDD => 01-NOV-26
    $txnDate = function ($value) {
        $date = !empty($value) ? \DateTime::createFromFormat('!Ymd', substr((string) $value, 0, 8)) : false;
        return $date ? strtoupper($date->format('d-M-y')) : '—';
    };
    $canCreateTicket = (function_exists('user_touch_permission') && user_touch_permission())
        || (auth()->check() && (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin')));
?>
<div class="cic-stmt-card">
    <div class="cic-stmt-head">
        <span><i class="fa fa-university"></i> Card Txn Log for <b class="cic-mono">{{ $accountNumber ?: '—' }}</b></span>
        <span><i class="fa fa-credit-card"></i> <b class="cic-mono">{{ $dash($card['cardNumber'] ?? null) }}</b> · CIF <b class="cic-mono">{{ $dash($card['clientCode'] ?? null) }}</b></span>
        <span><i class="fa fa-calendar"></i> <b>{{ $fromDate->format('d M Y') }}</b> to <b>{{ $toDate->format('d M Y') }}</b></span>
        @if(!$error)
            <span class="count"><b>{{ count($rows) }}</b> transactions</span>
        @endif
    </div>

    @if($error)
        <div class="cic-alert" style="margin:14px">{{ $error }}</div>
    @else
        <div class="table-responsive">
            <table class="cic-stmt-table">
                <thead>
                <tr>
                    <th style="width:50px">SL No</th>
                    <th>PAN</th>
                    <th>A/C No</th>
                    <th>MSG Type</th>
                    <th>RES Code</th>
                    <th class="amount">Amount</th>
                    <th>Tran Date Time</th>
                    <th>Trace</th>
                    <th>Term ID</th>
                    <th>Location</th>
                    <th>Acquirer</th>
                    <th>Acquirer Bank</th>
                    <th>POS Entry Mode</th>
                    <th>Refnum</th>
                    <th>Auth Num</th>
                    <th>End Expiry</th>
                    <th>ACQ Settlement Date</th>
                    <th>Wording</th>
                    @if(!empty($disputeIssues))
                        <th style="width:110px">Dispute</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="cic-mono">{{ $dash($row['tranCrdNo'] ?? ($row['cardNumber'] ?? null)) }}</td>
                        <td class="cic-mono">{{ $dash($row['accountNumber'] ?? null) }}</td>
                        <td>{{ $dash($row['trnType'] ?? null) }}</td>
                        <td>{{ $dash($row['responseCode'] ?? null) }}</td>
                        <td class="amount">{{ is_numeric($row['tranAmt'] ?? null) ? number_format($row['tranAmt'], 2) : '—' }}</td>
                        <td>{{ $txnDate($row['tranDate'] ?? null) }} {{ $row['transactionTime'] ?? '' }}</td>
                        <td>{{ $dash($row['externalStan'] ?? ($row['internalStan'] ?? null)) }}</td>
                        <td>{{ $dash($row['terminalId'] ?? null) }}</td>
                        <td>{{ $dash($row['location'] ?? null) }}</td>
                        <td>{{ $dash($row['acquirer'] ?? null) }}</td>
                        <td>{{ $dash($row['acquirerBank'] ?? null) }}</td>
                        <td>{{ $dash($row['posEntryMode'] ?? null) }}</td>
                        <td class="cic-mono">{{ $dash($row['transId'] ?? null) }}</td>
                        <td>{{ $dash($row['tranApprCode'] ?? null) }}</td>
                        <td>{{ $dash($row['expiryDate'] ?? null) }}</td>
                        <td>{{ $txnDate($row['postDate'] ?? null) }}</td>
                        <td>{{ $dash($row['tranDesc'] ?? null) }}</td>
                        @if(!empty($disputeIssues))
                            <td>
                                <div class="dropdown">
                                @if($canCreateTicket)
                                    @php
                                        $typeOfCard = strtoupper(trim($row['typeOfCard'] ?? ''));
                                        $rowIssues = [];
                                        foreach ($disputeIssues as $issueId => $issueLabel) {
                                            if ($typeOfCard === 'DC' && $issueId == getId('DISPUTE_CREDIT_CARD')) {
                                                continue;
                                            }
                                            $rowIssues[$issueId] = $issueLabel;
                                        }
                                    @endphp
                                    <button type="button" class="cic-btn cic-btn-primary cic-btn-sm dropdown-toggle" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false">
                                        <i class="fa fa-ticket"></i> Dispute
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end cic-dropdown">
                                        @foreach($rowIssues as $issueId => $issueLabel)
                                            <li>
                                                <a class="dropdown-item" href="{{ route('cic.disputeTicket', [$type, $key, $i, $issueId]) }}">
                                                    <i class="fa {{ $issueId == getId('DISPUTE_CREDIT_CARD') ? 'fa-credit-card' : 'fa-university' }}"></i> {{ $issueLabel }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="cic-pill warn" title="Only touch point users can create tickets">
                                        <i class="fa fa-lock"></i> Restricted
                                    </span>
                                @endif
                                </div></td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ empty($disputeIssues) ? 18 : 19 }}" class="cic-empty"><i class="fa fa-inbox"></i> No transaction found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
