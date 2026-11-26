@extends('layouts.admin')
@section('content')
    <?php
        // API dates come as yyyymmdd (same as formatDate() in Supports/index)
        $formatDate = function ($date) {
            if (empty($date)) {
                return '';
            }
            $dt = \DateTime::createFromFormat('Ymd', substr($date, 0, 8));
            return $dt ? $dt->format('d M Y') : $date;
        };
        $val = function ($value) {
            return ($value === null || trim((string) $value) === '') ? '—' : trim((string) $value);
        };
        $money = function ($value) {
            if ($value === null || $value === '') {
                return '—';
            }
            return is_numeric($value) ? number_format((float) $value, 2) : $value;
        };

        $type = $type ?? 'ac';
        $key = $key ?? 0;
        $activeKey = $key;
        $isCard = ($type == 'cc' || $type == 'dc');

        $customerName = $isCard ? ($card['cardHolderName'] ?? '') : ($account['accountTitle'] ?? ($tblData['customer_name'] ?? ''));
        $customerName = trim(rtrim(trim((string) $customerName), '.'));
        $initials = collect(preg_split('/\s+/', $customerName))->filter()->take(2)->map(function ($w) {
            return mb_substr($w, 0, 1);
        })->implode('');

        $customerId = $isCard ? ($card['clientCode'] ?? '') : ($tblData['CIF_number'] ?? '');
        $mobile = $isCard ? ($card['mobileNumber'] ?? '') : ($tblData['mobile_number'] ?? '');
        $email = $isCard ? ($card['email'] ?? '') : ($tblData['def_email_addr'] ?? '');
        $dob = $isCard ? ($card['dob'] ?? '') : ((!empty($tblData['date_of_birth'])) ? date('d M Y', strtotime($tblData['date_of_birth'])) : '');
        $typeLabels = ['cc' => 'Credit Card', 'dc' => 'Debit Card', 'ac' => 'Account', 'loan' => 'Loan'];
        $typeIcons = ['cc' => 'fa-credit-card', 'dc' => 'fa-credit-card-alt', 'ac' => 'fa-university', 'loan' => 'fa-line-chart'];

        // Status => tone (good / warn / bad)
        $statusTone = function ($status) {
            $status = strtoupper(trim((string) $status));
            if (in_array($status, ['', 'NORMAL', 'ACTIVE', 'OPEN', 'N'])) {
                return 'good';
            }
            if (in_array($status, ['CANCELLED', 'CLOSED', 'BLOCKED', 'LOST', 'STOLEN'])) {
                return 'bad';
            }
            return 'warn';
        };

        if ($isCard) {
            $statusText = $val($card['cardStatus'] ?? '');
            $tone = $statusTone($card['cardStatus'] ?? '');
            $brand = strtolower(trim($card['cardType'] ?? ''));
            $brandClass = str_contains($brand, 'visa') ? 'visa' : (str_contains($brand, 'master') ? 'master' : (str_contains($brand, 'jcb') ? 'jcb' : 'other'));
        } else {
            $dormant = $account['dormant'] ?? '';
            $statusText = strtoupper((string) $dormant) == 'Y' ? 'Dormant' : (empty($account) ? '—' : 'Active');
            $tone = strtoupper((string) $dormant) == 'Y' ? 'warn' : 'good';
        }

        // Loan repayment progress
        $repaidPercent = null;
        if ($type == 'loan' && is_numeric($loan['disbursementAmount'] ?? null) && $loan['disbursementAmount'] > 0 && is_numeric($loan['outstandingAmount'] ?? null)) {
            $repaidPercent = max(0, min(100, round((1 - $loan['outstandingAmount'] / $loan['disbursementAmount']) * 100)));
        }
    ?>

    <style>
        .cic-page {
            --cic-ink: #0f172a;
            --cic-muted: #64748b;
            --cic-line: #e8ecf3;
            --cic-surface: #ffffff;
            --cic-canvas: #f4f6fb;
            /* Prime Bank logo palette */
            --cic-accent: #d71f26;
            --cic-accent-soft: #fdecec;
            --cic-blue: #1f4fa3;
            --cic-blue-soft: #e8eef9;
            --cic-green: #1a7a3c;
            --cic-navy: #0e2a5c;
            --cic-good: #1a7a3c;
            --cic-good-soft: #e6f4ea;
            --cic-warn: #b45309;
            --cic-warn-soft: #fff6e0;
            --cic-bad: #dc2626;
            --cic-bad-soft: #fdeceb;
            color: var(--cic-ink);
            background: var(--cic-canvas);
            border-radius: 20px;
            padding: 18px;
            margin-top: 10px;
            font-family: "Segoe UI", system-ui, -apple-system, Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* ---------- Hero ---------- */
        .cic-hero {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            padding: 28px;
            color: #fff;
            background:
                radial-gradient(circle at 85% 15%, rgba(215, 31, 38, .55), transparent 42%),
                radial-gradient(circle at 10% 110%, rgba(26, 122, 60, .6), transparent 45%),
                linear-gradient(135deg, #0a1d42 0%, #133a7f 55%, #1f4fa3 100%);
            box-shadow: 0 20px 45px -20px rgba(14, 42, 92, .65);
            display: flex;
            flex-wrap: wrap;
            gap: 28px;
            align-items: center;
            justify-content: space-between;
            animation: cicRise .5s ease both;
        }
        .cic-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, .05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .05) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: linear-gradient(90deg, #000 0%, transparent 75%);
            -webkit-mask-image: linear-gradient(90deg, #000 0%, transparent 75%);
            pointer-events: none;
        }
        .cic-hero > * { position: relative; }
        .cic-profile { display: flex; gap: 20px; align-items: center; min-width: 0; flex: 1 1 300px; }
        .cic-profile-details { min-width: 0; flex: 1; }
        .cic-avatar {
            flex: 0 0 auto;
            width: 84px;
            height: 84px;
            border-radius: 24px;
            display: grid;
            place-items: center;
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #ef4b51, var(--cic-accent));
            box-shadow: 0 0 0 4px rgba(255, 255, 255, .12), 0 14px 30px -10px rgba(215, 31, 38, .8);
        }
        .cic-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .7);
        }
        .cic-identity-line {
            display: flex;
            align-items: center;
            gap: 14px;
            width: max-content;
            max-width: 100%;
            overflow-x: auto;
            padding-bottom: 2px;
        }
        .cic-name { flex: 0 0 auto; font-size: 26px; font-weight: 700; margin: 0; line-height: 1.2; color: #fff; white-space: nowrap; }
        .cic-chips { display: flex; flex-wrap: nowrap; align-items: center; gap: 8px; }
        .cic-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12.5px;
            white-space: nowrap;
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .16);
            color: #fff;
            backdrop-filter: blur(6px);
        }
        .cic-chip i { opacity: .75; }
        .cic-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 18px; }
        .cic-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 0;
            border-radius: 12px;
            padding: 10px 18px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }
        .cic-btn:hover { transform: translateY(-2px); text-decoration: none; }
        .cic-btn:disabled { opacity: .65; cursor: wait; transform: none; }
        .cic-btn-primary { background: var(--cic-accent); color: #fff; box-shadow: 0 10px 22px -10px rgba(215, 31, 38, .9); }
        .cic-btn-primary:hover { color: #fff; box-shadow: 0 14px 26px -10px rgba(215, 31, 38, 1); }
        .cic-btn-ghost { background: rgba(255, 255, 255, .12); color: #fff; border: 1px solid rgba(255, 255, 255, .22); }
        .cic-btn-ghost:hover { background: rgba(255, 255, 255, .2); color: #fff; }
        .cic-btn-blue { background: var(--cic-blue); color: #fff; box-shadow: 0 10px 22px -10px rgba(31, 79, 163, .9); }
        .cic-btn-blue:hover { color: #fff; box-shadow: 0 14px 26px -10px rgba(31, 79, 163, 1); }
        .cic-btn-green { background: var(--cic-green); color: #fff; box-shadow: 0 10px 22px -10px rgba(26, 122, 60, .9); }
        .cic-btn-green:hover { color: #fff; box-shadow: 0 14px 26px -10px rgba(26, 122, 60, 1); }
        .cic-btn-sm { padding: 6px 12px; font-size: 12px; white-space: nowrap; }
        .cic-dropdown { border: 0; border-radius: 10px; box-shadow: 0 14px 30px -12px rgba(15, 23, 42, .45); padding: 6px; font-size: 13px; }
        .cic-dropdown .dropdown-item { border-radius: 7px; padding: 8px 12px; display: flex; align-items: center; gap: 8px; }
        .cic-dropdown .dropdown-item:hover { background: var(--cic-accent); color: #fff; }

        /* ---------- Virtual card ---------- */
        .cic-vcard {
            flex: 0 0 auto;
            width: 340px;
            max-width: 100%;
            aspect-ratio: 1.586;
            border-radius: 18px;
            padding: 20px 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            color: #fff;
            box-shadow: 0 25px 40px -18px rgba(0, 0, 0, .7), inset 0 1px 0 rgba(255, 255, 255, .25);
            transform: perspective(900px) rotateY(-8deg) rotateX(4deg);
            transition: transform .35s ease;
        }
        .cic-vcard:hover { transform: perspective(900px) rotateY(0) rotateX(0) translateY(-4px); }
        .cic-vcard::after {
            content: '';
            position: absolute;
            top: -60%;
            left: -60%;
            width: 60%;
            height: 220%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .28), transparent);
            transform: rotate(25deg);
            animation: cicShine 4.5s ease-in-out infinite;
        }
        .cic-vcard.visa { background: linear-gradient(135deg, #1a3fa8 0%, #0e1f5b 60%, #0a1440 100%); }
        .cic-vcard.master { background: linear-gradient(135deg, #2b2b2b 0%, #141414 55%, #3a1a0a 100%); }
        .cic-vcard.jcb { background: linear-gradient(135deg, #0f766e 0%, #064e3b 60%, #052e2b 100%); }
        .cic-vcard.other { background: linear-gradient(135deg, #6d28d9 0%, #3b0764 100%); }
        .cic-vcard-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .cic-vcard-kind { font-size: 11px; letter-spacing: .16em; text-transform: uppercase; opacity: .8; }
        .cic-vcard-product { font-size: 12px; font-weight: 600; margin-top: 3px; max-width: 200px; line-height: 1.3; }
        .cic-chip-gold {
            width: 44px;
            height: 33px;
            border-radius: 7px;
            background: linear-gradient(135deg, #f7e19c, #c9a227 55%, #f5d67a);
            position: relative;
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, .15);
        }
        .cic-chip-gold::before, .cic-chip-gold::after {
            content: '';
            position: absolute;
            inset: 7px 0;
            border-top: 1px solid rgba(0, 0, 0, .25);
            border-bottom: 1px solid rgba(0, 0, 0, .25);
        }
        .cic-chip-gold::after { inset: 0 14px; border: 0; border-left: 1px solid rgba(0, 0, 0, .25); border-right: 1px solid rgba(0, 0, 0, .25); }
        .cic-vcard-number {
            font-family: "Consolas", "SFMono-Regular", Menlo, monospace;
            font-size: 15px;
            letter-spacing: .12em;
            word-break: break-all;
            text-shadow: 0 1px 2px rgba(0, 0, 0, .4);
        }
        .cic-vcard-bottom { display: flex; justify-content: space-between; align-items: flex-end; gap: 10px; }
        .cic-vcard-label { font-size: 9px; letter-spacing: .14em; text-transform: uppercase; opacity: .65; }
        .cic-vcard-holder { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
        .cic-vcard-brand { font-size: 20px; font-weight: 800; font-style: italic; letter-spacing: .02em; }
        .cic-vcard-brand .m1, .cic-vcard-brand .m2 { display: inline-block; width: 26px; height: 26px; border-radius: 50%; vertical-align: middle; }
        .cic-vcard-brand .m1 { background: #eb001b; }
        .cic-vcard-brand .m2 { background: #f79e1b; margin-left: -10px; mix-blend-mode: screen; }

        /* ---------- Stat tiles ---------- */
        .cic-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(200px, 100%), 1fr));
            gap: 14px;
            margin: 18px 0;
        }
        .cic-stat {
            background: var(--cic-surface);
            border: 1px solid var(--cic-line);
            border-radius: 16px;
            padding: 16px 18px;
            display: flex;
            gap: 14px;
            align-items: center;
            box-shadow: 0 6px 18px -14px rgba(15, 23, 42, .35);
            animation: cicRise .5s ease both;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .cic-stat:hover { transform: translateY(-3px); box-shadow: 0 14px 28px -16px rgba(15, 23, 42, .45); }
        .cic-stat:nth-child(2) { animation-delay: .06s; }
        .cic-stat:nth-child(3) { animation-delay: .12s; }
        .cic-stat:nth-child(4) { animation-delay: .18s; }
        .cic-stat-icon {
            flex: 0 0 auto;
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 18px;
            color: var(--cic-accent);
            background: var(--cic-accent-soft);
        }
        .cic-stat-label { font-size: 11.5px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: var(--cic-muted); }
        .cic-stat-value { font-size: 19px; font-weight: 700; margin-top: 2px; word-break: break-word; }
        .cic-stat-value small { font-size: 12px; font-weight: 600; color: var(--cic-muted); }

        .cic-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 11px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 700;
            white-space: nowrap;
        }
        .cic-pill::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
        .cic-pill.good { color: var(--cic-good); background: var(--cic-good-soft); }
        .cic-pill.warn { color: var(--cic-warn); background: var(--cic-warn-soft); }
        .cic-pill.bad { color: var(--cic-bad); background: var(--cic-bad-soft); }
        .cic-pill.good::before { animation: cicPulse 1.8s ease-in-out infinite; }

        /* ---------- Panels ---------- */
        .cic-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(320px, 100%), 1fr));
            gap: 16px;
        }
        .cic-panel {
            background: var(--cic-surface);
            border: 1px solid var(--cic-line);
            border-radius: 18px;
            padding: 6px 20px 14px;
            box-shadow: 0 6px 18px -14px rgba(15, 23, 42, .35);
            animation: cicRise .55s ease both;
        }
        .cic-panel-head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 0 12px;
            border-bottom: 1px dashed var(--cic-line);
            margin-bottom: 4px;
        }
        .cic-panel-head i {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            color: #fff;
            background: linear-gradient(135deg, var(--cic-navy), var(--cic-blue));
            font-size: 14px;
        }
        .cic-panel-head h5 { margin: 0; font-size: 15px; font-weight: 700; color: var(--cic-ink); }
        .cic-panel-head span { margin-left: auto; font-size: 11.5px; color: var(--cic-muted); }
        .cic-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 10px 0;
            border-bottom: 1px solid #f1f4f9;
        }
        .cic-row:last-child { border-bottom: 0; }
        .cic-row dt { font-size: 12.5px; font-weight: 500; color: var(--cic-muted); white-space: nowrap; margin: 0; }
        .cic-row dd { font-size: 13.5px; font-weight: 600; text-align: right; margin: 0; word-break: break-word; }
        .cic-mono { font-family: "Consolas", "SFMono-Regular", Menlo, monospace; letter-spacing: .03em; }
        .cic-copy {
            border: 0;
            background: transparent;
            color: #94a3b8;
            padding: 0 0 0 6px;
            cursor: pointer;
            font-size: 12px;
            transition: color .15s ease;
        }
        .cic-copy:hover { color: var(--cic-accent); }
        .cic-copy.done { color: var(--cic-good); }
        .cic-full { grid-column: 1 / -1; }

        /* ---------- Loan ---------- */
        .cic-loan-figures {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(160px, 100%), 1fr));
            gap: 12px;
            margin: 12px 0 16px;
        }
        .cic-figure { border-radius: 14px; padding: 14px 16px; background: #f8fafc; border: 1px solid var(--cic-line); }
        .cic-figure .label { font-size: 11px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: var(--cic-muted); }
        .cic-figure .value { font-size: 18px; font-weight: 700; margin-top: 2px; }
        .cic-figure.alert-tone { background: var(--cic-bad-soft); border-color: #f8d0cd; }
        .cic-figure.alert-tone .value { color: var(--cic-bad); }
        .cic-progress { height: 10px; border-radius: 999px; background: #eef1f7; overflow: hidden; }
        .cic-progress > div {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--cic-blue), var(--cic-green));
            width: 0;
            transition: width 1.2s cubic-bezier(.2, .8, .2, 1);
        }
        .cic-progress-meta { display: flex; justify-content: space-between; font-size: 12px; color: var(--cic-muted); margin: 8px 0 6px; }

        /* ---------- Mini statement modal ---------- */
        .cic-modal .modal-content {
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 60px -20px rgba(10, 29, 66, .6);
        }
        .cic-modal .modal-header {
            border: 0;
            padding: 18px 22px;
            color: #fff;
            background:
                radial-gradient(circle at 90% 0%, rgba(215, 31, 38, .55), transparent 45%),
                linear-gradient(135deg, #0a1d42 0%, #133a7f 60%, #1f4fa3 100%);
        }
        .cic-modal .modal-title { display: flex; align-items: center; gap: 12px; font-size: 17px; font-weight: 700; color: #fff; }
        .cic-modal .modal-title i {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: var(--cic-accent);
            box-shadow: 0 8px 18px -8px rgba(215, 31, 38, .9);
        }
        .cic-modal .modal-title small { display: block; font-size: 11.5px; font-weight: 500; color: rgba(255, 255, 255, .7); }
        .cic-modal .cic-modal-close {
            margin-left: auto;
            width: 36px;
            height: 36px;
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 10px;
            background: rgba(255, 255, 255, .1);
            color: #fff;
            transition: background .15s ease, transform .15s ease;
        }
        .cic-modal .cic-modal-close:hover { background: rgba(255, 255, 255, .22); transform: rotate(90deg); }
        .cic-modal .modal-body {
            position: relative;
            padding: 18px 22px 22px;
            min-height: 260px;
            background: #fffdf8;
            isolation: isolate;
        }
        /* Taka note watermark */
        .cic-modal .modal-body::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: -1;
            background: url('{{ URL::asset('public/cic/taka2.gif') }}') center / min(85%, 700px) no-repeat;
            opacity: .12;
            pointer-events: none;
        }
        .cic-modal .cic-stmt-card { background: rgba(255, 255, 255, .55); backdrop-filter: blur(1px); }
        .cic-modal .cic-stmt-head { background: linear-gradient(90deg, rgba(248, 250, 252, .85), rgba(255, 255, 255, .6)); }
        .cic-modal .cic-stmt-table th,
        .cic-modal .cic-stmt-table tfoot td { background: rgba(251, 252, 254, .8); }
        .cic-modal .cic-stmt-table tbody tr:hover { background: rgba(232, 238, 249, .75); }
        .cic-modal .cic-stmt-card:first-child { margin-top: 0; }
        .cic-loading { display: flex; gap: 10px; align-items: center; justify-content: center; padding: 30px; color: var(--cic-muted); }
        .cic-skeleton { height: 14px; border-radius: 6px; background: linear-gradient(90deg, #eef1f7 25%, #f8fafc 50%, #eef1f7 75%); background-size: 200% 100%; animation: cicSkeleton 1.2s linear infinite; margin: 10px 0; }
        .cic-alert { border-radius: 14px; padding: 14px 16px; background: var(--cic-bad-soft); color: var(--cic-bad); font-weight: 600; text-align: center; margin: 12px 0; }
        .cic-stmt-card { border: 1px solid var(--cic-line); border-radius: 16px; overflow: hidden; margin: 14px 0; }
        .cic-stmt-head {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 22px;
            align-items: center;
            padding: 12px 16px;
            background: linear-gradient(90deg, #f8fafc, #fff);
            border-bottom: 1px solid var(--cic-line);
            font-size: 12.5px;
            color: var(--cic-muted);
        }
        .cic-stmt-head b { color: var(--cic-ink); font-weight: 700; }
        .cic-stmt-head .count { margin-left: auto; }
        .cic-stmt-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .cic-stmt-table th {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--cic-muted);
            padding: 10px 16px;
            background: #fbfcfe;
            border-bottom: 1px solid var(--cic-line);
            text-align: left;
        }
        .cic-stmt-table td { padding: 11px 16px; border-bottom: 1px solid #f1f4f9; vertical-align: middle; }
        .cic-stmt-table tbody tr { transition: background .15s ease; }
        .cic-stmt-table tbody tr:hover { background: var(--cic-blue-soft); }
        .cic-stmt-table .amount { text-align: right; font-weight: 700; font-family: "Consolas", "SFMono-Regular", Menlo, monospace; }
        .cic-stmt-table tfoot td { font-weight: 700; background: #fbfcfe; border-bottom: 0; }
        .cic-date-badge { display: inline-block; min-width: 92px; padding: 3px 9px; border-radius: 8px; background: var(--cic-blue-soft); color: var(--cic-blue); font-weight: 600; font-size: 12px; text-align: center; }
        .cic-empty { text-align: center; color: var(--cic-muted); padding: 22px; }
        .cic-stmt-table .cic-dr { color: var(--cic-accent); }
        .cic-stmt-table .cic-cr { color: var(--cic-green); }
        .cic-stmt-table .cic-pill { font-size: 11px; padding: 2px 9px; }

        /* ---------- Statement (card transactions) filter ---------- */
        .cic-filter {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(170px, 100%), 1fr));
            gap: 12px;
            align-items: end;
            padding: 14px 16px;
            border: 1px solid var(--cic-line);
            border-radius: 16px;
            background: rgba(255, 255, 255, .85);
            margin-bottom: 4px;
        }
        .cic-filter label { display: block; font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--cic-muted); margin-bottom: 5px; }
        .cic-filter select, .cic-filter input {
            width: 100%;
            height: 40px;
            border: 1px solid #d5dce8;
            border-radius: 10px;
            padding: 0 12px;
            font-size: 13.5px;
            color: var(--cic-ink);
            background: #fff;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .cic-filter select:focus, .cic-filter input:focus { outline: 0; border-color: var(--cic-blue); box-shadow: 0 0 0 3px rgba(31, 79, 163, .15); }
        .cic-filter .cic-btn { height: 40px; justify-content: center; width: 100%; }
        .cic-filter-note { grid-column: 1 / -1; font-size: 11.5px; color: var(--cic-muted); margin-top: -4px; }
        .cic-filter-note.error { color: var(--cic-bad); font-weight: 600; }

        @keyframes cicRise { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
        @keyframes cicShine { 0%, 60% { left: -60%; } 100% { left: 140%; } }
        @keyframes cicPulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(26, 122, 60, .5); } 50% { box-shadow: 0 0 0 5px rgba(26, 122, 60, 0); } }
        @keyframes cicSkeleton { from { background-position: 200% 0; } to { background-position: -200% 0; } }

        @media (prefers-reduced-motion: reduce) {
            .cic-page *, .cic-page *::before, .cic-page *::after { animation: none !important; transition: none !important; }
        }
        @media (max-width: 767px) {
            .cic-page { padding: 10px; border-radius: 14px; }
            .cic-hero { padding: 20px; }
            .cic-name { font-size: 21px; }
            .cic-avatar { width: 64px; height: 64px; font-size: 22px; border-radius: 18px; }
            .cic-vcard { transform: none; width: 100%; }
            .cic-grid { grid-template-columns: 1fr; }
            .cic-profile { flex-direction: column; align-items: flex-start; }
            .cic-profile-details { width: 100%; }
            .cic-row dt { white-space: normal; }
            .cic-stats { grid-template-columns: 1fr 1fr; gap: 10px; }
            .cic-stat { padding: 12px; gap: 10px; }
            .cic-stat-icon { width: 38px; height: 38px; font-size: 15px; }
            .cic-stat-value { font-size: 16px; }
            .cic-stmt-table th:nth-child(4), .cic-stmt-table td:nth-child(4) { display: none; }
            /* Transaction table: keep Description, hide Post Date & Currency */
            .cic-txn-table th:nth-child(4), .cic-txn-table td:nth-child(4) { display: table-cell; }
            .cic-txn-table th:nth-child(3), .cic-txn-table td:nth-child(3),
            .cic-txn-table th:nth-child(5), .cic-txn-table td:nth-child(5) { display: none; }
        }
        @media print {
            .cic-actions, .cic-copy { display: none !important; }
            .cic-page { background: #fff; padding: 0; }
            .cic-hero { box-shadow: none; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .cic-vcard { transform: none; }
        }
    </style>

    {{-- ================= Top Search Bar ================= --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; background: #ffffff; padding: 12px 18px;">
        <form class="d-flex flex-wrap align-items-center" id="cic-account-search" action="{{ url('/Supports/home') }}" method="GET">
            <input type="hidden" name="from_cic" value="1">
            <div class="me-3 my-1">
                <div class="d-flex flex-wrap align-items-center">
                    @inject('products','App\Services\ProductTypeService')
                    @foreach($products->productTypes() as $product)
                        @continue($product->id == 4) {{-- Loan hidden on CIC search --}}
                        <label class="form-check me-3 mb-0" style="cursor: pointer; display: flex; align-items: center;">
                            <input type="radio" name="account_type" class="form-check-input cic-account-type me-1"
                                   value="{{ $product->id }}" {{ ($searchDataForView['account_type'] ?? 2) == $product->id ? 'checked' : '' }}>
                            <strong style="font-size: 13.5px;">{{ $product->name }}</strong>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="input-container my-1 d-flex flex-wrap align-items-center gap-2">
                <?php 
                    $currAccType = $searchDataForView['account_type'] ?? 2;
                    $accNoPlaceholder = ($currAccType == 1 || $currAccType == 3) ? 'Card Number' : 'Enter Account Number'; 
                ?>

                <input type="text" style="width:190px;" name="account_number" 
                       id="cic_account_number"
                       class="form-control" 
                       placeholder="{{ $accNoPlaceholder }}" 
                       value="{{ $searchDataForView['account_number'] ?? '' }}" />

                <div id="cic_cardSearchingWrap" style="position: relative; width: 140px; display: {{ ($currAccType == 1 || $currAccType == 3) ? 'block' : 'none' }};">
                    <select name="cardSearchingType" id="cic_cardSearchingTag" class="form-control" style="width: 100%;">
                        <option value="Cif" {{ ($searchDataForView['cardSearchingType'] ?? '') == 'Cif' ? 'selected' : '' }}>CIF Number</option>
                        <option value="Mobile" {{ ($searchDataForView['cardSearchingType'] ?? 'Mobile') == 'Mobile' ? 'selected' : '' }}>Mobile Number</option>
                    </select>
                </div>

                <input type="text"
                       name="cif_mobile"
                       id="cic_cardSearchingInputTag"
                       class="form-control"
                       placeholder="Enter Mobile Number"
                       style="width:180px; display: {{ ($currAccType == 1 || $currAccType == 3) ? 'block' : 'none' }};"
                       value="{{ old('cif_mobile', $searchDataForView['cif_mobile'] ?? '') }}" />
            </div>

            <div class="my-1 ms-2">
                <button type="submit" class="btn btn-success" id="cicSearchBtn" style="font-weight: 600; padding: 6px 20px;">
                    <i class="fa fa-search"></i> Find
                </button>
            </div>
        </form>
    </div>

    @if(empty($row))
        <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body text-center py-5">
                <div style="width: 70px; height: 70px; margin: 0 auto 16px; border-radius: 50%; background: #eaf0fb; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-id-card-o" style="font-size: 32px; color: #375eb1;"></i>
                </div>
                <h4 style="font-weight: 600; color: #1e293b;">Customer Interaction Center (CIC)</h4>
                <p class="text-muted mb-0" style="max-width: 520px; margin: 0 auto; font-size: 14px;">
                    Please select an account type and enter an Account Number, Card Number, Mobile Number or CIF above, then click <strong>Find</strong> to view customer profile, statements, and dispute options.
                </p>
            </div>
        </div>
    @else
    <div class="cic-page">

        {{-- ============ Hero ============ --}}
        <section class="cic-hero">
            <div class="cic-profile">
                <div class="cic-avatar">{{ $initials ?: '?' }}</div>
                <div class="cic-profile-details">
                    <div class="cic-eyebrow"><i class="fa {{ $typeIcons[$type] }}"></i> {{ $typeLabels[$type] }} Customer Profile</div>
                    <div class="cic-identity-line">
                        <h2 class="cic-name">{{ $val($customerName) }}</h2>
                        <div class="cic-chips">
                            <span class="cic-chip"><i class="fa fa-hashtag"></i> CIF {{ $val($customerId) }}</span>
                            <span class="cic-chip"><i class="fa fa-phone"></i> {{ $val($mobile) }}</span>
                            <span class="cic-chip"><i class="fa fa-envelope"></i> {{ $val($email) }}</span>
                            @if(!$isCard && !empty($tblData['customer_type']))
                                <span class="cic-chip"><i class="fa fa-star"></i> {{ $tblData['customer_type'] }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="cic-actions">
                        <button type="button" class="cic-btn cic-btn-sm cic-btn-primary" id="miniStatementBtn" onclick="openMiniStatementModal()">
                            <i class="fa fa-list-alt"></i> Mini Statement View
                        </button>
                        <button type="button" class="cic-btn cic-btn-sm cic-btn-blue" onclick="openStatementModal()">
                            <i class="fa fa-file-text-o"></i> Statement
                        </button>
                        <button type="button" class="cic-btn cic-btn-sm cic-btn-green" id="disputeStatementBtn" onclick="openDisputeStatementModal()">
                            <i class="fa fa-exclamation-circle"></i> Dispute Statement
                        </button>
                    </div>
                </div>
            </div>

            @if($isCard)
                <div class="cic-vcard {{ $brandClass }}">
                    <div class="cic-vcard-top">
                        <div>
                            <div class="cic-vcard-kind">{{ $typeLabels[$type] }}</div>
                            <div class="cic-vcard-product">{{ $val($card['cardProductName'] ?? '') }}</div>
                        </div>
                        <div class="cic-chip-gold"></div>
                    </div>
                    <div class="cic-vcard-number">{{ $val($card['aliasPan'] ?? ($card['maskedCardNumber'] ?? '')) }}</div>
                    <div class="cic-vcard-bottom">
                        <div style="min-width:0">
                            <div class="cic-vcard-label">Card Holder</div>
                            <div class="cic-vcard-holder">{{ $val($customerName) }}</div>
                        </div>
                        <div class="cic-vcard-brand">
                            @if($brandClass == 'master')
                                <span class="m1"></span><span class="m2"></span>
                            @else
                                {{ strtoupper($card['cardType'] ?? '') }}
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </section>

        {{-- ============ Stat tiles ============ --}}
        <section class="cic-stats">
            @if($isCard)
                <div class="cic-stat">
                    <div class="cic-stat-icon"><i class="fa fa-money"></i></div>
                    <div>
                        <div class="cic-stat-label">Card Balance</div>
                        <div class="cic-stat-value">{{ $money($card['availableBalance'] ?? '') }}</div>
                    </div>
                </div>
                <div class="cic-stat">
                    <div class="cic-stat-icon"><i class="fa fa-shield"></i></div>
                    <div>
                        <div class="cic-stat-label">Card Status</div>
                        <div class="cic-stat-value"><span class="cic-pill {{ $tone }}">{{ $statusText }}</span></div>
                    </div>
                </div>
                <div class="cic-stat">
                    <div class="cic-stat-icon"><i class="fa fa-tags"></i></div>
                    <div>
                        <div class="cic-stat-label">Type Of Card</div>
                        <div class="cic-stat-value">{{ $val($card['typeOfCard'] ?? '') }} <small>{{ $card['cardType'] ?? '' }}</small></div>
                    </div>
                </div>
                <div class="cic-stat">
                    <div class="cic-stat-icon"><i class="fa fa-building"></i></div>
                    <div>
                        <div class="cic-stat-label">Branch Code</div>
                        <div class="cic-stat-value cic-mono">{{ $val($card['branchCode'] ?? '') }}</div>
                    </div>
                </div>
            @else
                <div class="cic-stat">
                    <div class="cic-stat-icon"><i class="fa fa-money"></i></div>
                    <div>
                        <div class="cic-stat-label">A/C Balance</div>
                        <div class="cic-stat-value">{{ $money($account['balance'] ?? '') }} <small>{{ $account['currency'] ?? '' }}</small></div>
                    </div>
                </div>
                <div class="cic-stat">
                    <div class="cic-stat-icon"><i class="fa fa-shield"></i></div>
                    <div>
                        <div class="cic-stat-label">A/C Status</div>
                        <div class="cic-stat-value"><span class="cic-pill {{ $tone }}">{{ $statusText }}</span></div>
                    </div>
                </div>
                <div class="cic-stat">
                    <div class="cic-stat-icon"><i class="fa fa-calendar"></i></div>
                    <div>
                        <div class="cic-stat-label">A/C Open Date</div>
                        <div class="cic-stat-value">{{ $val($formatDate($account['accountOpenDate'] ?? '')) }}</div>
                    </div>
                </div>
                <div class="cic-stat">
                    <div class="cic-stat-icon"><i class="fa fa-building"></i></div>
                    <div>
                        <div class="cic-stat-label">Branch</div>
                        <div class="cic-stat-value" style="font-size:15px">{{ $val($account['branchName'] ?? '') }}</div>
                    </div>
                </div>
            @endif
        </section>

        {{-- ============ Details ============ --}}
        <div class="cic-grid">
            <section class="cic-panel">
                <div class="cic-panel-head"><i class="fa fa-user"></i><h5>Personal Information</h5></div>
                <dl class="mb-0">
                    <div class="cic-row"><dt>Customer Name</dt><dd>{{ $val($customerName) }}</dd></div>
                    <div class="cic-row"><dt>Date Of Birth</dt><dd>{{ $val($dob) }}</dd></div>
                    <div class="cic-row"><dt>Gender</dt><dd>{{ $val($tblData['customer_gender']) }}</dd></div>
                    <div class="cic-row"><dt>Father's Name</dt><dd>{{ $val($tblData['father_name']) }}</dd></div>
                    <div class="cic-row"><dt>Mother's Name</dt><dd>{{ $val($tblData['mother_name']) }}</dd></div>
                    <div class="cic-row"><dt>Spouse's Name</dt><dd>{{ $val($tblData['spouse_name']) }}</dd></div>
                    @if(!$isCard)
                        <div class="cic-row"><dt>Customer Type</dt><dd>{{ $val($tblData['customer_type']) }}</dd></div>
                    @endif
                </dl>
            </section>

            <section class="cic-panel">
                <div class="cic-panel-head"><i class="fa fa-user-secret"></i><h5>Identity &amp; Contact</h5></div>
                <dl class="mb-0">
                    <div class="cic-row"><dt>Customer ID</dt><dd class="cic-mono">{{ $val($customerId) }}@include('CIC.partials.copy', ['text' => $customerId])</dd></div>
                    <div class="cic-row"><dt>NID</dt><dd class="cic-mono">{{ $val($tblData['NID']) }}@include('CIC.partials.copy', ['text' => $tblData['NID']])</dd></div>
                    <div class="cic-row"><dt>TIN</dt><dd class="cic-mono">{{ $val($tblData['TIN']) }}</dd></div>
                    <div class="cic-row"><dt>Passport Number</dt><dd class="cic-mono">{{ $val($tblData['passport_number']) }}</dd></div>
                    <div class="cic-row"><dt>Mobile Number</dt><dd>{{ $val($mobile) }}@include('CIC.partials.copy', ['text' => $mobile])</dd></div>
                    <div class="cic-row"><dt>E-mail Address</dt><dd>{{ $val($email) }}</dd></div>
                    <div class="cic-row"><dt>Communication Address</dt><dd>{{ $val(str_replace('--', ', ', $tblData['communication'])) }}</dd></div>
                </dl>
            </section>

            <section class="cic-panel">
                @if($isCard)
                    <div class="cic-panel-head"><i class="fa fa-credit-card"></i><h5>Card Details</h5><span>{{ $typeLabels[$type] }}</span></div>
                    <dl class="mb-0">
                        <div class="cic-row"><dt>{{ $typeLabels[$type] }} Number</dt><dd class="cic-mono">{{ $val($card['maskedCardNumber'] ?? '') }}</dd></div>
                        <div class="cic-row"><dt>Account Number</dt><dd class="cic-mono">{{ $val($card['accountNumber'] ?? '') }}@include('CIC.partials.copy', ['text' => $card['accountNumber'] ?? ''])</dd></div>
                        <div class="cic-row"><dt>Card Product Name</dt><dd>{{ $val($card['cardProductName'] ?? '') }}</dd></div>
                        <div class="cic-row"><dt>Card Type</dt><dd>{{ $val($card['cardType'] ?? '') }}</dd></div>
                        <div class="cic-row"><dt>Type Of Card</dt><dd>{{ $val($card['typeOfCard'] ?? '') }}</dd></div>
                        <div class="cic-row"><dt>Card Status</dt><dd><span class="cic-pill {{ $tone }}">{{ $statusText }}</span></dd></div>
                        <div class="cic-row"><dt>Card Balance</dt><dd>{{ $money($card['availableBalance'] ?? '') }}</dd></div>
                        <div class="cic-row"><dt>Branch Code</dt><dd class="cic-mono">{{ $val($card['branchCode'] ?? '') }}</dd></div>
                    </dl>
                @else
                    <div class="cic-panel-head"><i class="fa fa-university"></i><h5>Account Details</h5><span>{{ $val($account['currency'] ?? '') }}</span></div>
                    <dl class="mb-0">
                        <div class="cic-row"><dt>Account Number</dt><dd class="cic-mono">{{ $val($account['accountId'] ?? '') }}@include('CIC.partials.copy', ['text' => $account['accountId'] ?? ''])</dd></div>
                        <div class="cic-row"><dt>Product Name - Description</dt><dd>{{ $val($account['categoryTitle'] ?? '') }}</dd></div>
                        <div class="cic-row"><dt>A/C Status</dt><dd><span class="cic-pill {{ $tone }}">{{ $statusText }}</span></dd></div>
                        <div class="cic-row"><dt>A/C Open Date</dt><dd>{{ $val($formatDate($account['accountOpenDate'] ?? '')) }}</dd></div>
                        <div class="cic-row"><dt>A/C Balance</dt><dd>{{ $money($account['balance'] ?? '') }}</dd></div>
                        <div class="cic-row"><dt>Account Opening Branch</dt><dd>{{ $val($account['branchName'] ?? '') }}</dd></div>
                        <div class="cic-row"><dt>Branch Code</dt><dd class="cic-mono">{{ $val($account['branchCode'] ?? '') }}</dd></div>
                    </dl>
                @endif
            </section>

            @if($type == 'loan')
                <section class="cic-panel cic-full">
                    <div class="cic-panel-head"><i class="fa fa-line-chart"></i><h5>Loan Information</h5><span class="cic-mono">{{ $val($loan['accountId'] ?? '') }}</span></div>

                    <div class="cic-loan-figures">
                        <div class="cic-figure"><div class="label">Outstanding</div><div class="value">{{ $money($loan['outstandingAmount'] ?? '') }}</div></div>
                        <div class="cic-figure"><div class="label">Disbursement</div><div class="value">{{ $money($loan['disbursementAmount'] ?? '') }}</div></div>
                        <div class="cic-figure {{ is_numeric($loan['overdueAmount'] ?? null) && $loan['overdueAmount'] > 0 ? 'alert-tone' : '' }}"><div class="label">Overdue</div><div class="value">{{ $money($loan['overdueAmount'] ?? '') }}</div></div>
                        <div class="cic-figure"><div class="label">Interest Rate</div><div class="value">{{ $val($loan['interestRate'] ?? '') }}{{ isset($loan['interestRate']) && $loan['interestRate'] !== '' ? '%' : '' }}</div></div>
                    </div>

                    @if($repaidPercent !== null)
                        <div class="cic-progress-meta"><span>Repayment progress</span><b>{{ $repaidPercent }}% repaid</b></div>
                        <div class="cic-progress"><div data-width="{{ $repaidPercent }}"></div></div>
                    @endif

                    <div class="cic-grid" style="margin-top:12px">
                        <dl class="mb-0">
                            <div class="cic-row"><dt>Loan A/C Number</dt><dd class="cic-mono">{{ $val($loan['accountId'] ?? '') }}@include('CIC.partials.copy', ['text' => $loan['accountId'] ?? ''])</dd></div>
                            <div class="cic-row"><dt>Loan Type</dt><dd>{{ $val($loan['loanType'] ?? '') }}</dd></div>
                            <div class="cic-row"><dt>Value Date</dt><dd>{{ $val($formatDate($loan['valueDate'] ?? '')) }}</dd></div>
                            <div class="cic-row"><dt>Maturity Date</dt><dd>{{ $val($formatDate($loan['maturityDate'] ?? '')) }}</dd></div>
                        </dl>
                        <dl class="mb-0">
                            <div class="cic-row"><dt>Total Interest</dt><dd>{{ $money($loan['totalInterest'] ?? '') }}</dd></div>
                            <div class="cic-row"><dt>Installment Amount</dt><dd>{{ $money($loan['installmentAmount'] ?? '') }}</dd></div>
                            <div class="cic-row"><dt>Installment Repayment Date</dt><dd>{{ $val($formatDate($loan['installmentRepaymentDate'] ?? '')) }}</dd></div>
                            <div class="cic-row"><dt>Branch Code</dt><dd class="cic-mono">{{ $val($loan['branchCode'] ?? '') }}</dd></div>
                        </dl>
                    </div>
                </section>
            @endif
        </div>

        {{-- ============ Mini statement modal ============ --}}
        <div class="modal fade cic-modal" id="miniStatementModal" tabindex="-1" aria-labelledby="miniStatementTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="miniStatementTitle">
                            <i class="fa fa-list-alt"></i>
                            <span>Mini Statement <small>{{ $val($customerName) }} · Latest transactions</small></span>
                        </h5>
                        <button type="button" class="cic-modal-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <form class="cic-filter" id="miniFilter" onsubmit="loadMiniStatement(); return false;">
                            <div>
                                <label for="miniCard">Card</label>
                                <select id="miniCard" onchange="loadMiniStatement()"></select>
                            </div>
                            <div>
                                <label for="miniQuick">Quick Select</label>
                                <select id="miniQuick" onchange="applyQuickSelect('mini', this.value)">
                                    <option value="custom">Custom</option>
                                    <option value="today">Current Day</option>
                                    <option value="yesterday">Last Day</option>
                                    <option value="thisWeek">Current Week</option>
                                    <option value="lastWeek">Last Week</option>
                                    <option value="thisMonth">Current Month</option>
                                    <option value="lastMonth">Last Month</option>
                                    <option value="last3Months">Last 3 Months</option>
                                    <option value="last6Months">Last 6 Months</option>
                                </select>
                            </div>
                            <div>
                                <label for="miniFrom">From Date</label>
                                <input type="date" id="miniFrom" required onchange="onStatementDateChange('mini')">
                            </div>
                            <div>
                                <label for="miniTo">To Date</label>
                                <input type="date" id="miniTo" required onchange="onStatementDateChange('mini')">
                            </div>
                            <div>
                                <button type="submit" class="cic-btn cic-btn-primary" id="miniSearchBtn">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                            <div class="cic-filter-note" id="miniNote">Maximum date range is 1 year.</div>
                        </form>
                        <div id="miniStatementBody"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Dispute statement modal ============ --}}
        <div class="modal fade cic-modal" id="disputeStatementModal" tabindex="-1" aria-labelledby="disputeStatementTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="disputeStatementTitle">
                            <i class="fa fa-exclamation-circle"></i>
                            <span>Dispute Statement <small>{{ $val($customerName) }} · Card transaction log (max 1 year)</small></span>
                        </h5>
                        <button type="button" class="cic-modal-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <form class="cic-filter" id="disputeFilter" onsubmit="loadDisputeStatement(); return false;">
                            <div>
                                <label for="dispCard">Card</label>
                                <select id="dispCard" onchange="loadDisputeStatement()"></select>
                            </div>
                            <div>
                                <label for="dispQuick">Quick Select</label>
                                <select id="dispQuick" onchange="applyQuickSelect('disp', this.value)">
                                    <option value="custom">Custom</option>
                                    <option value="today">Current Day</option>
                                    <option value="yesterday">Last Day</option>
                                    <option value="thisWeek">Current Week</option>
                                    <option value="lastWeek">Last Week</option>
                                    <option value="thisMonth">Current Month</option>
                                    <option value="lastMonth">Last Month</option>
                                    <option value="last3Months">Last 3 Months</option>
                                    <option value="last6Months">Last 6 Months</option>
                                </select>
                            </div>
                            <div>
                                <label for="dispFrom">From Date</label>
                                <input type="date" id="dispFrom" required onchange="onStatementDateChange('disp')">
                            </div>
                            <div>
                                <label for="dispTo">To Date</label>
                                <input type="date" id="dispTo" required onchange="onStatementDateChange('disp')">
                            </div>
                            <div>
                                <button type="submit" class="cic-btn cic-btn-primary" id="disputeSearchBtn">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                            <div class="cic-filter-note" id="dispNote">Maximum date range is 1 year.</div>
                        </form>
                        <div id="disputeStatementBody"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Statement (card transactions) modal ============ --}}
        <div class="modal fade cic-modal" id="statementModal" tabindex="-1" aria-labelledby="statementTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statementTitle">
                            <i class="fa fa-file-text-o"></i>
                            <span>Statement <small>{{ $val($customerName) }} · {{ $type == 'ac' ? 'Account statement' : 'Card transactions' }} (max 1 year)</small></span>
                        </h5>
                        <button type="button" class="cic-modal-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <form class="cic-filter" id="statementFilter" onsubmit="loadCardTransactions(); return false;">
                            @if($type != 'ac')
                                <div>
                                    <label for="stmtCard">Card</label>
                                    <select id="stmtCard" onchange="loadCardTransactions()"></select>
                                </div>
                            @endif
                            <div>
                                <label for="stmtQuick">Quick Select</label>
                                <select id="stmtQuick" onchange="applyQuickSelect('stmt', this.value)">
                                    <option value="custom">Custom</option>
                                    <option value="today">Current Day</option>
                                    <option value="yesterday">Last Day</option>
                                    <option value="thisWeek">Current Week</option>
                                    <option value="lastWeek">Last Week</option>
                                    <option value="thisMonth">Current Month</option>
                                    <option value="lastMonth">Last Month</option>
                                    <option value="last3Months">Last 3 Months</option>
                                    <option value="last6Months">Last 6 Months</option>
                                </select>
                            </div>
                            <div>
                                <label for="stmtFrom">From Date</label>
                                <input type="date" id="stmtFrom" required onchange="onStatementDateChange('stmt')">
                            </div>
                            <div>
                                <label for="stmtTo">To Date</label>
                                <input type="date" id="stmtTo" required onchange="onStatementDateChange('stmt')">
                            </div>
                            <div>
                                <button type="submit" class="cic-btn cic-btn-primary" id="statementSearchBtn">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                            <div>
                                <button type="button" class="cic-btn cic-btn-green" onclick="exportCardTransactions()">
                                    <i class="fa fa-file-excel-o"></i> Excel
                                </button>
                            </div>
                            <div class="cic-filter-note" id="stmtNote">Maximum date range is 1 year.</div>
                        </form>
                        <div id="statementBody"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif

    <script type="text/javascript">
        // Modal filters: p => field id prefix 'mini' (Mini Statement), 'stmt' (Statement), 'disp' (Dispute Statement)
        // Opens with the last 1 month, card dropdown emptied so the next load fills it
        function cicResetFilter(p) {
            var today = cicToday();
            $('#' + p + 'Quick').val('custom');
            $('#' + p + 'From').val(cicYmd(cicAddMonths(today, -1)));
            $('#' + p + 'To').val(cicYmd(today));
            $('#' + p + 'Card').empty();
            setStatementLimits(p);
            validateStatementRange(p);
        }

        // Card list (cc/dc => the card, ac/loan => cards of the account) filled on the first load
        function cicFillCards(p, response) {
            var select = $('#' + p + 'Card');
            if (!select.find('option').length) {
                $.each(response.cards || [], function (i, option) {
                    select.append($('<option></option>').val(option.value).text(option.label));
                });
            }
            if (response.card !== null && response.card !== undefined) {
                select.val(response.card);
            }
        }

        // Mini statement API returns only the latest few transactions, so it opens with the last 3 months
        function openMiniStatementModal() {
            cicResetFilter('mini');
            $('#miniQuick').val('last3Months');
            applyQuickSelect('mini', 'last3Months');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('miniStatementModal')).show();
            loadMiniStatement();
        }

        function loadMiniStatement() {
            if (!validateStatementRange('mini')) {
                return;
            }

            var btn = $('#miniStatementBtn, #miniSearchBtn');
            btn.prop('disabled', true).find('i').attr('class', 'fa fa-spinner fa-spin');
            $('#miniStatementBody').html(
                '<div class="cic-loading"><i class="fa fa-spinner fa-spin"></i> Loading mini statement...</div>' +
                '<div class="cic-skeleton"></div><div class="cic-skeleton" style="width:85%"></div><div class="cic-skeleton" style="width:70%"></div>'
            );

            $.ajax({
                url: {!! json_encode($miniStatementUrl) !!},
                method: 'GET',
                data: {from: $('#miniFrom').val(), to: $('#miniTo').val(), card: $('#miniCard').val() || 0},
                success: function (response) {
                    if (response.status) {
                        cicFillCards('mini', response);
                        $('#miniStatementBody').html(response.html);
                    } else {
                        $('#miniStatementBody').html($('<div class="cic-alert"></div>').text(response.message));
                    }
                },
                error: function () {
                    $('#miniStatementBody').html('<div class="cic-alert">Something went wrong. Please try again.</div>');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    $('#miniStatementBtn i').attr('class', 'fa fa-list-alt');
                    $('#miniSearchBtn i').attr('class', 'fa fa-search');
                }
            });
        }

        function openDisputeStatementModal() {
            cicResetFilter('disp');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('disputeStatementModal')).show();
            loadDisputeStatement();
        }

        function loadDisputeStatement() {
            if (!validateStatementRange('disp')) {
                return;
            }

            var btn = $('#disputeStatementBtn, #disputeSearchBtn');
            btn.prop('disabled', true).find('i').attr('class', 'fa fa-spinner fa-spin');
            $('#disputeStatementBody').html(
                '<div class="cic-loading"><i class="fa fa-spinner fa-spin"></i> Loading dispute statement...</div>' +
                '<div class="cic-skeleton"></div><div class="cic-skeleton" style="width:85%"></div><div class="cic-skeleton" style="width:70%"></div>'
            );

            $.ajax({
                url: {!! json_encode($disputeStatementUrl) !!},
                method: 'GET',
                data: {from: $('#dispFrom').val(), to: $('#dispTo').val(), card: $('#dispCard').val() || 0},
                success: function (response) {
                    if (response.status) {
                        cicFillCards('disp', response);
                        $('#disputeStatementBody').html(response.html);
                    } else {
                        $('#disputeStatementBody').html($('<div class="cic-alert"></div>').text(response.message));
                    }
                },
                error: function () {
                    $('#disputeStatementBody').html('<div class="cic-alert">Something went wrong. Please try again.</div>');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    $('#disputeStatementBtn i').attr('class', 'fa fa-exclamation-circle');
                    $('#disputeSearchBtn i').attr('class', 'fa fa-search');
                }
            });
        }

        /* ---------- Statement (card transactions) ---------- */
        // Local yyyy-mm-dd (toISOString would shift the day by the timezone)
        function cicYmd(d) {
            return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
        }

        function cicParseYmd(value) {
            var p = (value || '').split('-');
            return p.length === 3 ? new Date(+p[0], +p[1] - 1, +p[2]) : null;
        }

        function cicToday() {
            var now = new Date();
            return new Date(now.getFullYear(), now.getMonth(), now.getDate());
        }

        // Same day n months away, clamped to the month end (31 Mar - 1 month => 28/29 Feb)
        function cicAddMonths(d, months) {
            var r = new Date(d.getFullYear(), d.getMonth() + months, 1);
            r.setDate(Math.min(d.getDate(), new Date(r.getFullYear(), r.getMonth() + 1, 0).getDate()));
            return r;
        }

        // Loads on open so the card dropdown gets filled
        function openStatementModal() {
            cicResetFilter('stmt');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('statementModal')).show();
            loadCardTransactions();
        }

        // Week starts on Sunday
        function applyQuickSelect(p, range) {
            var today = cicToday();
            var from = new Date(today), to = new Date(today);

            switch (range) {
                case 'today':
                    break;
                case 'yesterday':
                    from.setDate(today.getDate() - 1);
                    to = new Date(from);
                    break;
                case 'thisWeek':
                    from.setDate(today.getDate() - today.getDay());
                    break;
                case 'lastWeek':
                    from.setDate(today.getDate() - today.getDay() - 7);
                    to = new Date(from);
                    to.setDate(from.getDate() + 6);
                    break;
                case 'thisMonth':
                    from = new Date(today.getFullYear(), today.getMonth(), 1);
                    break;
                case 'lastMonth':
                    from = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    to = new Date(today.getFullYear(), today.getMonth(), 0);
                    break;
                case 'last3Months':
                    from = cicAddMonths(today, -3);
                    break;
                case 'last6Months':
                    from = cicAddMonths(today, -6);
                    break;
                default:
                    return;
            }

            $('#' + p + 'From').val(cicYmd(from));
            $('#' + p + 'To').val(cicYmd(to));
            setStatementLimits(p);
            validateStatementRange(p);
        }

        // Date pickers: nothing after today, range capped at 1 year
        function setStatementLimits(p) {
            var today = cicToday();
            var from = cicParseYmd($('#' + p + 'From').val());
            var to = cicParseYmd($('#' + p + 'To').val());

            $('#' + p + 'From').attr('max', cicYmd(to && to < today ? to : today));
            $('#' + p + 'From').attr('min', to ? cicYmd(cicAddMonths(to, -12)) : '');

            var toMax = today;
            if (from && cicAddMonths(from, 12) < today) {
                toMax = cicAddMonths(from, 12);
            }
            $('#' + p + 'To').attr('max', cicYmd(toMax)).attr('min', from ? cicYmd(from) : '');
        }

        function onStatementDateChange(p) {
            $('#' + p + 'Quick').val('custom');
            setStatementLimits(p);
            validateStatementRange(p);
        }

        function validateStatementRange(p) {
            var from = cicParseYmd($('#' + p + 'From').val());
            var to = cicParseYmd($('#' + p + 'To').val());
            var message = '';

            if (!from || !to) {
                message = 'Please select From Date and To Date.';
            } else if (from > to) {
                message = 'From Date can not be greater than To Date.';
            } else if (to > cicToday()) {
                message = 'To Date can not be a future date.';
            } else if (from < cicAddMonths(to, -12)) {
                message = 'Date range can not be more than 1 year.';
            }

            $('#' + p + 'Note').toggleClass('error', message !== '').text(message || 'Maximum date range is 1 year.');
            return message === '';
        }

        function loadCardTransactions() {
            if (!validateStatementRange('stmt')) {
                return;
            }

            var btn = $('#statementSearchBtn');
            btn.prop('disabled', true).find('i').attr('class', 'fa fa-spinner fa-spin');
            $('#statementBody').html(
                '<div class="cic-loading"><i class="fa fa-spinner fa-spin"></i> Loading {{ $type == 'ac' ? 'account statement' : 'card transactions' }}...</div>' +
                '<div class="cic-skeleton"></div><div class="cic-skeleton" style="width:85%"></div><div class="cic-skeleton" style="width:70%"></div>'
            );

            $.ajax({
                url: {!! json_encode($cardTransactionsUrl) !!},
                method: 'GET',
                data: {from: $('#stmtFrom').val(), to: $('#stmtTo').val(), card: $('#stmtCard').val() || 0},
                success: function (response) {
                    if (response.status) {
                        cicFillCards('stmt', response);
                        $('#statementBody').html(response.html);
                    } else {
                        $('#statementBody').html($('<div class="cic-alert"></div>').text(response.message));
                    }
                },
                error: function () {
                    $('#statementBody').html('<div class="cic-alert">Something went wrong. Please try again.</div>');
                },
                complete: function () {
                    btn.prop('disabled', false).find('i').attr('class', 'fa fa-search');
                }
            });
        }

        // Plain navigation so the browser downloads the .xlsx (validation errors come back as a flash message)
        function exportCardTransactions() {
            if (!validateStatementRange('stmt')) {
                return;
            }

            var url = {!! json_encode($cardTransactionsExportUrl) !!};
            window.location.href = url + (url.indexOf('?') === -1 ? '?' : '&') + $.param({from: $('#stmtFrom').val(), to: $('#stmtTo').val(), card: $('#stmtCard').val() || 0});
        }

        function cicCopy(btn) {
            var text = $(btn).data('copy').toString();
            var done = function () {
                $(btn).addClass('done').find('i').attr('class', 'fa fa-check');
                setTimeout(function () {
                    $(btn).removeClass('done').find('i').attr('class', 'fa fa-clone');
                }, 1500);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(done);
            } else {
                var tmp = $('<textarea>').val(text).css({position: 'fixed', opacity: 0}).appendTo('body').select();
                document.execCommand('copy');
                tmp.remove();
                done();
            }
        }

        $(document).ready(function () {
            $('.cic-progress > div').each(function () {
                var bar = $(this);
                setTimeout(function () { bar.css('width', bar.data('width') + '%'); }, 300);
            });
        });
    </script>
    {{-- ============ Customer Account Details Modal ============ --}}
    <div class="modal fade" id="AC_list_modal" data-bs-backdrop="static" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg" style="border-radius: 12px; border: 0;">
                <div class="modal-header" style="background: #375eb1; color: #fff; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title" style="color: #fff; font-weight: 600;"><i class="fa fa-university"></i> Customer Account Details</h5>
                    <button type="button" class="btn-close btn-close-white cic-modal-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">SL.</th>
                                    <th>A/C Number</th>
                                    <th>Customer Name</th>
                                    <th>Balance</th>
                                    <th>A/C Type</th>
                                    <th>Branch Name</th>
                                    <th style="width: 100px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($multiACData))
                                    @foreach($multiACData as $k => $item)
                                        <tr class="{{ ($type == 'ac' && ($key ?? null) === $k) ? 'table-primary' : '' }}">
                                            <td>{{ $k + 1 }}</td>
                                            <td class="fw-bold">{{ $item['accountId'] ?? '' }}</td>
                                            <td>{{ $item['accountTitle'] ?? '' }}</td>
                                            <td>{{ is_numeric($item['balance'] ?? null) ? number_format($item['balance'], 2) : ($item['balance'] ?? '—') }}</td>
                                            <td>{{ $item['categoryTitle'] ?? '' }}</td>
                                            <td>{{ $item['branchName'] ?? '' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('cic.customerInfo', ['ac', $k]) }}" class="btn btn-sm btn-info text-white text-nowrap" style="padding: 4px 14px; font-weight: 600;">
                                                    <i class="fa fa-eye"></i> Inquiry
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="7" class="text-center text-muted py-3">No account data available</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #dee2e6;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ Customer Credit Card Details Modal ============ --}}
    <div class="modal fade" id="CC_list_modal" data-bs-backdrop="static" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg" style="border-radius: 12px; border: 0;">
                <div class="modal-header" style="background: #375eb1; color: #fff; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title" style="color: #fff; font-weight: 600;"><i class="fa fa-credit-card"></i> Customer Credit Card Details</h5>
                    <button type="button" class="btn-close btn-close-white cic-modal-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">SL.</th>
                                    <th>Card Number</th>
                                    <th>Customer Name</th>
                                    <th>Available Balance</th>
                                    <th>Card Type</th>
                                    <th>Card Status</th>
                                    <th>Branch Code</th>
                                    <th style="width: 100px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($multiCCData))
                                    @foreach($multiCCData as $k => $cif)
                                        <tr class="{{ ($type == 'cc' && ($key ?? null) === $k) ? 'table-primary' : '' }} {{ ($cif['cardStatus'] ?? '') == 'Cancelled' ? 'table-danger' : '' }}">
                                            <td>{{ $k + 1 }}</td>
                                            <td class="fw-bold">{{ $cif['maskedCardNumber'] ?? ($cif['cardNumber'] ?? '') }}</td>
                                            <td>{{ $cif['cardHolderName'] ?? '' }}</td>
                                            <td>{{ is_numeric($cif['availableBalance'] ?? null) ? number_format($cif['availableBalance'], 2) : ($cif['availableBalance'] ?? '—') }}</td>
                                            <td>{{ $cif['cardType'] ?? '' }}</td>
                                            <td><span class="badge bg-secondary">{{ $cif['cardStatus'] ?? '' }}</span></td>
                                            <td>{{ $cif['branchCode'] ?? '' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('cic.customerInfo', ['cc', $k]) }}" class="btn btn-sm btn-info text-white text-nowrap" style="padding: 4px 14px; font-weight: 600;">
                                                    <i class="fa fa-eye"></i> Inquiry
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="8" class="text-center text-muted py-3">No card data available</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #dee2e6;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ Customer Debit Card Details Modal ============ --}}
    <div class="modal fade" id="DC_list_modal" data-bs-backdrop="static" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg" style="border-radius: 12px; border: 0;">
                <div class="modal-header" style="background: #375eb1; color: #fff; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title" style="color: #fff; font-weight: 600;"><i class="fa fa-credit-card"></i> Customer Debit Card Details</h5>
                    <button type="button" class="btn-close btn-close-white cic-modal-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">SL.</th>
                                    <th>Card Number</th>
                                    <th>Customer Name</th>
                                    <th>Balance</th>
                                    <th>Card Type</th>
                                    <th>Card Status</th>
                                    <th>Branch Code</th>
                                    <th style="width: 100px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($multiDCData))
                                    @foreach($multiDCData as $k => $cif)
                                        <tr class="{{ ($type == 'dc' && ($key ?? null) === $k) ? 'table-primary' : '' }} {{ ($cif['cardStatus'] ?? '') == 'Cancelled' ? 'table-danger' : '' }}">
                                            <td>{{ $k + 1 }}</td>
                                            <td class="fw-bold">{{ $cif['maskedCardNumber'] ?? ($cif['cardNumber'] ?? '') }}</td>
                                            <td>{{ $cif['cardHolderName'] ?? '' }}</td>
                                            <td>******</td>
                                            <td>{{ $cif['cardType'] ?? '' }}</td>
                                            <td><span class="badge bg-secondary">{{ $cif['cardStatus'] ?? '' }}</span></td>
                                            <td>{{ $cif['branchCode'] ?? '' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('cic.customerInfo', ['dc', $k]) }}" class="btn btn-sm btn-info text-white text-nowrap" style="padding: 4px 14px; font-weight: 600;">
                                                    <i class="fa fa-eye"></i> Inquiry
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="8" class="text-center text-muted py-3">No debit card data available</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #dee2e6;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ Customer Loan Details Modal ============ --}}
    <div class="modal fade" id="loan_list_modal" data-bs-backdrop="static" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg" style="border-radius: 12px; border: 0;">
                <div class="modal-header" style="background: #375eb1; color: #fff; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title" style="color: #fff; font-weight: 600;"><i class="fa fa-university"></i> Customer Loan Details</h5>
                    <button type="button" class="btn-close btn-close-white cic-modal-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">SL.</th>
                                    <th>Loan A/C Number</th>
                                    <th>Outstanding Amount</th>
                                    <th>Disbursement Amount</th>
                                    <th>Loan Type</th>
                                    <th style="width: 100px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($multiLoanData))
                                    @foreach($multiLoanData as $k => $loanItem)
                                        <tr class="{{ ($type == 'loan' && ($key ?? null) === $k) ? 'table-primary' : '' }}">
                                            <td>{{ $k + 1 }}</td>
                                            <td class="fw-bold">{{ $loanItem['accountId'] ?? '' }}</td>
                                            <td>{{ is_numeric($loanItem['outstandingAmount'] ?? null) ? number_format($loanItem['outstandingAmount'], 2) : ($loanItem['outstandingAmount'] ?? '—') }}</td>
                                            <td>{{ is_numeric($loanItem['disbursementAmount'] ?? null) ? number_format($loanItem['disbursementAmount'], 2) : ($loanItem['disbursementAmount'] ?? '—') }}</td>
                                            <td>{{ $loanItem['loanType'] ?? '' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('cic.customerInfo', ['loan', $k]) }}" class="btn btn-sm btn-info text-white text-nowrap" style="padding: 4px 14px; font-weight: 600;">
                                                    <i class="fa fa-eye"></i> Inquiry
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="6" class="text-center text-muted py-3">No loan data available</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #dee2e6;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showCicModal(modalId) {
            var el = document.getElementById(modalId);
            if (!el) return;
            if (window.bootstrap && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(el).show();
            } else if (window.jQuery && $(el).modal) {
                $(el).modal('show');
            }
        }

        $(document).ready(function () {
            $('.cic-account-type').on('change', function () {
                var type = $(this).val();
                if (type == '1' || type == '3') {
                    $('#cic_account_number').attr('placeholder', 'Card Number');
                    $('#cic_cardSearchingWrap, #cic_cardSearchingInputTag').show();
                } else {
                    $('#cic_account_number').attr('placeholder', 'Enter Account Number');
                    $('#cic_cardSearchingWrap, #cic_cardSearchingInputTag').hide();
                }
            });

            $('#cic_cardSearchingTag').on('change', function () {
                var val = $(this).val();
                $('#cic_cardSearchingInputTag').attr('placeholder', val === 'Mobile' ? 'Enter Mobile Number' : 'Enter CIF Number');
            });

            @if(!empty($cicOpenModal))
                @if(($type ?? '') == 'ac' && !empty($multiACData))
                    showCicModal('AC_list_modal');
                @elseif(($type ?? '') == 'cc' && !empty($multiCCData))
                    showCicModal('CC_list_modal');
                @elseif(($type ?? '') == 'dc' && !empty($multiDCData))
                    showCicModal('DC_list_modal');
                @elseif(($type ?? '') == 'loan' && !empty($multiLoanData))
                    showCicModal('loan_list_modal');
                @endif
            @endif
        });
    </script>
@endsection
