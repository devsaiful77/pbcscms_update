<?php
    $amount = function ($value) {
        return is_numeric($value) ? number_format((float) $value, 2) : '—';
    };
    $txDate = function ($value) {
        $date = !empty($value) ? \DateTime::createFromFormat('!Ymd', substr((string) $value, 0, 8)) : false;
        return $date ? $date->format('d-m-Y') : $value;
    };
    $isMatch = function ($tx) use ($trAmount, $tnxNumber) {
        if (!empty($trAmount) && is_numeric($tx['tranAmt'] ?? null) && (float) $tx['tranAmt'] == (float) $trAmount) {
            return true;
        }
        if (!empty($tnxNumber) && !empty($tx['transId']) && strtolower(trim((string) $tx['transId'])) === strtolower(trim((string) $tnxNumber))) {
            return true;
        }
        return false;
    };
?>

<div class="card border-0 shadow-sm" style="border-radius: 10px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center py-2 px-3" style="border-bottom: 1px solid #eef2f6;">
        <div class="d-flex align-items-center gap-2">
            <span style="font-size: 15px; font-weight: 700; color: #1e293b;">
                <i class="fa fa-list-alt text-primary"></i> Dispute Transaction Statement
            </span>
            <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 600; font-size: 12px;">
                Ticket TR Date: <b>{{ $trDate ?: 'N/A' }}</b>
            </span>
            @if(!empty($trAmount))
                <span class="badge" style="background: #fef3c7; color: #b45309; font-weight: 600; font-size: 12px;">
                    Disputed Amt: <b>{{ is_numeric($trAmount) ? number_format((float)$trAmount, 2) : $trAmount }}</b>
                </span>
            @endif
            @if(!empty($cardNumber))
                <span class="badge" style="background: #f1f5f9; color: #475569; font-family: monospace; font-size: 12px;">
                    <i class="fa fa-credit-card"></i> {{ $cardNumber }}
                </span>
            @endif
        </div>
        <div>
            <span class="badge bg-secondary" style="font-size: 12px;">
                Showing <b>{{ count($rows) }}</b> transaction(s)
            </span>
        </div>
    </div>

    <div class="card-body p-3">
        {{-- Date Filter Form --}}
        <form class="d-flex flex-wrap align-items-end gap-2 mb-3 p-2" 
              style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;"
              onsubmit="filterTicketStatement(event); return false;">
            <div>
                <label style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 2px; display: block;">From Date</label>
                <input type="date" id="tktStmtFrom" class="form-control form-control-sm" value="{{ $fromDate }}" style="height: 32px; width: 140px; font-size: 13px;">
            </div>
            <div>
                <label style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 2px; display: block;">To Date</label>
                <input type="date" id="tktStmtTo" class="form-control form-control-sm" value="{{ $toDate }}" style="height: 32px; width: 140px; font-size: 13px;">
            </div>
            <div>
                <button type="submit" class="btn btn-sm btn-primary" style="height: 32px; padding: 0 14px; font-weight: 600;">
                    <i class="fa fa-search"></i> Filter Statement
                </button>
            </div>
            @if($fromDate !== $defaultDateYmd || $toDate !== $defaultDateYmd)
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetTicketStatementToDate()" style="height: 32px; padding: 0 12px; font-weight: 600;">
                        <i class="fa fa-undo"></i> Reset to Ticket Date ({{ $trDate }})
                    </button>
                </div>
            @endif
            <div class="ms-auto text-end">
                <small class="text-muted" style="font-size: 11.5px;">
                    Default view loads only transactions for <b>{{ $trDate ?: $defaultDateYmd }}</b>.
                </small>
            </div>
        </form>

        @if($error)
            <div class="alert alert-warning mb-0 py-2 px-3" style="font-size: 13.5px;">
                <i class="fa fa-exclamation-triangle"></i> {{ $error }}
            </div>
        @elseif(empty($rows))
            <div class="text-center py-4 px-3" style="background: #fafafa; border: 1px dashed #cbd5e1; border-radius: 8px;">
                <i class="fa fa-calendar-times-o fa-2x text-muted mb-2" style="opacity: 0.6;"></i>
                <div style="font-weight: 600; color: #475569; font-size: 14px;">
                    No transactions found on {{ $fromDate == $toDate ? $txDate(str_replace('-', '', $fromDate)) : ($fromDate . ' to ' . $toDate) }}.
                </div>
                @if(!empty($rawList) && count($rawList) > 0)
                    <div class="text-muted mt-1" style="font-size: 12.5px;">
                        ({{ count($rawList) }} total transactions found in the broader window. Use the date range above to inspect adjacent dates.)
                    </div>
                @endif
            </div>
        @else
            <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                <table class="table table-hover table-sm align-middle mb-0" style="font-size: 13px;">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 2; background: #f1f5f9;">
                        <tr>
                            <th style="width: 45px;">SL.</th>
                            <th>Card Number</th>
                            <th style="width: 105px;">Tran Date</th>
                            <th style="width: 105px;">Post Date</th>
                            <th>Description</th>
                            <th>Trans ID</th>
                            <th class="text-end" style="width: 110px;">Amount</th>
                            <th style="width: 60px;" class="text-center">D/C</th>
                            <th style="width: 65px;">Currency</th>
                            <th style="width: 130px;" class="text-center">Verification</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $i => $row)
                            @php
                                $matched = $isMatch($row);
                                $isDebit = strtoupper($row['transSign'] ?? 'D') === 'D';
                            @endphp
                            <tr style="{{ $matched ? 'background-color: #fef2f2 !important; font-weight: 600;' : '' }}">
                                <td>{{ $i + 1 }}</td>
                                <td style="font-family: monospace; font-size: 12px;">{{ $row['tranCrdNo'] ?? '' }}</td>
                                <td>{{ $txDate($row['tranDate'] ?? null) }}</td>
                                <td>{{ $txDate($row['postDate'] ?? null) }}</td>
                                <td>{{ $row['tranDesc'] ?? '—' }}</td>
                                <td style="font-family: monospace; font-size: 12px;">{{ $row['transId'] ?? '—' }}</td>
                                <td class="text-end font-monospace" style="font-weight: 700; color: {{ $isDebit ? '#dc2626' : '#16a34a' }};">
                                    {{ $amount($row['tranAmt'] ?? null) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $isDebit ? 'bg-danger' : 'bg-success' }}" style="font-size: 11px;">
                                        {{ $row['transSign'] ?? 'D' }}
                                    </span>
                                </td>
                                <td>{{ $row['tranCcy'] ?? 'BDT' }}</td>
                                <td class="text-center">
                                    @if($matched)
                                        <span class="badge bg-danger text-white py-1 px-2" style="font-size: 11px;">
                                            <i class="fa fa-exclamation-circle"></i> Disputed Match
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted" style="font-size: 11px; border: 1px solid #cbd5e1;">
                                            Normal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
