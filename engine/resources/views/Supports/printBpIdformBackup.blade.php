@extends('layouts.admin')
@section('content')
    @php
        $formData = session()->get('bp_form_data');
        use Illuminate\Support\Str;

        // Get counts
        $applicantCount = isset($raw->applicant_count) ? (int)$raw->applicant_count : 1;
        $nomineeCount = isset($raw->nominee_count) ? (int)$raw->nominee_count : 1;

        // Helper function for applicant data
        function getApplicantData($raw, $index) {
            if ($index == 1) {
                return [
                    'gender' => $raw->gender ?? '',
                    'dob' => $raw->dob ?? '',
                    'mother_name' => $raw->mother_name ?? '',
                    'father_name' => $raw->father_name ?? '',
                    'nid' => $raw->nid ?? '',
                    'passport' => $raw->passport ?? '',
                    'e_tin' => $raw->e_tin ?? '',
                    'occupation' => $raw->occupation ?? ''
                ];
            } else {
                $suffix = '';
                if ($index == 2) $suffix = '_two';
                elseif ($index == 3) $suffix = '_third';
                elseif ($index == 4) $suffix = '_fourth';

                return [
                    'gender' => $raw->{'gender' . $suffix} ?? '',
                    'dob' => $raw->{'dob' . $suffix} ?? '',
                    'mother_name' => $raw->{'mother_name' . $suffix} ?? '',
                    'father_name' => $raw->{'father_name' . $suffix} ?? '',
                    'nid' => $raw->{'nid' . $suffix} ?? '',
                    'passport' => $raw->{'passport' . $suffix} ?? '',
                    'e_tin' => $raw->{'e_tin' . $suffix} ?? '',
                    'occupation' => $raw->{'occupation' . $suffix} ?? ''
                ];
            }
        }

        // Helper function for nominee data
        function getNomineeData($raw, $index) {
            $prefix = '';
            if ($index == 1) $prefix = 'f';
            elseif ($index == 2) $prefix = 's';
            elseif ($index == 3) $prefix = 't';
            elseif ($index == 4) $prefix = 'four';

            return [
                'name' => $raw->{$prefix . '_nom_name'} ?? '',
                'nid' => $raw->{$prefix . '_nom_nid'} ?? '',
                'address' => $raw->{$prefix . '_nom_address'} ?? '',
                'relation' => $raw->{$prefix . '_nom_relations'} ?? '',
                'dob' => $raw->{$prefix . '_nom_dob'} ?? '',
                'payable' => $raw->{$prefix . '_nom_payable'} ?? '',
                'signature' => $raw->{$prefix . '_nom_signature_path_url'} ?? ''
            ];
        }
    @endphp

    <style media="print">
        @page {
            size: A4 portrait;
            margin: 20mm 15mm;
        }

        @page:first {
            margin-top: 15mm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: white;
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-before: always;
            break-before: page;
        }

        .no-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .bp-print-wrap {
            width: 210mm;
            max-width: 210mm;
            margin: 0 auto;
            padding: 0;
            background-color: #fff;
        }

        /* Header Styles */
        .bp-header-wrap {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #1a3a6d;
            padding-bottom: 10px;
        }

        .header-img {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            margin-bottom: 10px;
        }

        .head-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1a3a6d;
            margin: 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .note-wrap {
            background: #f0f0f0;
            border: 1px solid #ddd;
            padding: 8px 12px;
            margin-bottom: 15px;
            font-size: 9.5pt;
            line-height: 1.3;
        }

        /* BP ID and Date Section */
        .bp-date-wrap {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .bp-id-wrap, .date-wrap {
            display: flex;
            align-items: center;
        }

        .bp-id {
            width: 20px;
            height: 25px;
            border: 1px solid #666;
            margin: 0 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            background: white;
        }

        /* Section Styles */
        .item-section-wrap {
            margin-bottom: 20px;
            padding-bottom: 15px;
        }

        .item-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            color: #1a3a6d;
            padding-bottom: 3px;
            border-bottom: 1px solid #ccc;
        }

        .item-wrap {
            display: flex;
        }

        .double-wrap {
            display: flex;
            gap: 30px;
        }

        .item-left-wrap, .item-right-wrap {
            flex: 1;
        }

        /* Checkbox Styles */
        .status-option {
            display: inline-flex;
            align-items: center;
            margin-right: 15px;
            margin-bottom: 5px;
            font-size: 10pt;
        }

        .status-option input[type="checkbox"] {
            margin-right: 5px;
            transform: scale(1.1);
        }

        /* Applicant Info Styles */
        .app-info-wrap {
            margin-top: 10px;
        }

        .info-text {
            display: flex;
            margin-bottom: 3px;
            font-size: 10pt;
        }

        .info-text > div:first-child {
            min-width: 120px;
            font-weight: 500;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-top: 10px;
        }

        table th {
            background: #1a3a6d;
            color: white;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #ddd;
            font-weight: bold;
        }

        table td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }

        /* Photo Section */
        .item-photo-wrap {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .photo-item {
            flex: 1;
            min-height: 120px;
            border: 1px dashed #999;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .img-box {
            margin: 5px 0;
        }

        .img-box img {
            max-width: 100px;
            max-height: 100px;
            object-fit: contain;
        }

        /* Document List */
        .doc-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }

        .doc-item {
            font-size: 9.5pt;
            padding: 10px;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }

        .doc-item b {
            color: #1a3a6d;
            display: block;
            margin-bottom: 5px;
        }

        .doc-item ol {
            margin: 5px 0;
            padding-left: 20px;
        }

        .doc-item li {
            margin-bottom: 3px;
        }

        /* Sub Text */
        .sub-text {
            font-size: 9.5pt;
            line-height: 1.4;
            margin-bottom: 8px;
        }

        /* Page Break Indicators */
        .page-end {
            height: 1px;
            background: transparent;
            margin-top: 30px;
        }

        /* Hide elements when printing */
        .no-print {
            display: none !important;
        }

        /* Print optimization */
        @media print {
            .no-print {
                display: none !important;
            }

            .bp-print-wrap {
                width: 100%;
                max-width: 100%;
            }

            table {
                page-break-inside: avoid;
            }

            .doc-item {
                page-break-inside: avoid;
            }
        }
    </style>

    <div class="bp-print-wrap">

        <!-- PAGE 1 -->
        <div class="page-1 no-break">
            <div class="bp-header-wrap">
                <div class="header-img">
                    <img class="img-fluid" src="{{ URL::asset('public/img/logo/Logo_new.png') }}" alt="Prime Bank PLC" style="height: 35px;" />
                    <img class="img-fluid" src="{{ URL::asset('public/img/logo/bp-mngm.png') }}" alt="Prime Bank PLC" style="height: 35px;" />
                </div>
                <p class="head-title">BP (Business Partner) ID OPENING FORM</p>
            </div>

            <div class="note-wrap">
                Please complete all details in <b>BLOCK</b> Letters. Fill all names correctly and mark <b>(✓)</b> the relevant fields.
                All Communication shall be sent only to the First Named Account Holder's correspondence address.
            </div>

            <div class="bp-date-wrap">
                <div class="bp-id-wrap">
                    <span style="margin-right: 10px; font-weight: bold;">BPID:</span>
                    @for($i = 0; $i < 15; $i++)
                        <div class="bp-id"></div>
                    @endfor
                </div>
                <div class="date-wrap">
                    <span style="margin-right: 10px; font-weight: bold;">Date:</span>
                    @php
                        $date = \Carbon\Carbon::now()->format('dmY');
                        $digits = str_split($date);
                    @endphp
                    @foreach ($digits as $digit)
                        <div class="bp-id">{{ $digit }}</div>
                    @endforeach
                </div>
            </div>

            <!-- Section 1: BP Type -->
            <div class="item-section-wrap">
                <div class="item-title">1. BP Type:</div>
                <div class="item-wrap">
                    <div class="item-middle-wrap">
                        @php
                            $bpType = $raw->bp_type ?? 'Individual';
                            $bpTypes = [
                                'Individual',
                                'Mutual Fund',
                                'General Insurance',
                                'Foreign Investors',
                                'Life Insurance',
                                'Provident/Pension/Trust/Gratuity Fund',
                                'Corporate Bodies',
                                'Others',
                                'Investment Companies',
                            ];
                        @endphp

                        @foreach(array_chunk($bpTypes, 3) as $chunk)
                            <div style="margin-bottom: 5px;">
                                @foreach($chunk as $type)
                                    <label class="status-option">
                                        <input type="checkbox" name="bp_type[]" value="{{ $type }}"
                                            {{ strtolower($bpType) == strtolower($type) ? 'checked' : '' }} disabled>
                                        {{ $type }}
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Section 2: Residency -->
            <div class="item-section-wrap">
                @php
                    $residencyStatus = $raw->residency ?? '';
                    $residencyOptions = ['Resident', 'Non-Resident'];
                @endphp
                <div class="item-title">2. Residency of the Applicant:</div>
                <div class="item-wrap">
                    <div class="item-middle-wrap">
                        @foreach ($residencyOptions as $option)
                            <label class="status-option">
                                <input type="checkbox" name="residency[]" value="{{ $option }}"
                                    {{ strtolower($residencyStatus) == strtolower($option) ? 'checked' : '' }} disabled>
                                {{ $option }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Section 3: Applicant's Detail -->
            <div class="item-section-wrap">
                @php
                    $accountTypeStatus = $raw->type ?? '';
                    $accountTypeOptions = ['First', 'Second'];
                @endphp
                <div class="item-title">3. Applicant's Detail:</div>
                <div class="item-wrap">
                    <div class="item-middle-wrap">
                        @foreach ($accountTypeOptions as $option)
                            <label class="status-option">
                                <input type="checkbox" name="residency[]" value="{{ $option }}"
                                    {{ strtolower($accountTypeStatus) == strtolower($option) ? 'checked' : '' }} disabled>
                                {{ $option == 'First' ? 'Single/First Applicant' : 'Second Applicant' }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Section 4: Account Name -->
            <div class="item-section-wrap">
                <div class="item-title">4. Name of the Account:</div>
                <div class="item-wrap">
                    <div class="bp-id-item" style="border: 1px solid #666; padding: 8px; min-height: 40px; font-size: 12pt;">
                        {{ $raw->account_name ?? '' }}
                    </div>
                </div>
            </div>

            <!-- Section 5: Individual Applicants -->
            @for ($i = 1; $i <= $applicantCount; $i++)
                @php
                    $applicantData = getApplicantData($raw, $i);
                    $applicantTitle = ($i == 1) ? 'First Applicant' :
                                      (($i == 2) ? 'Second Applicant' :
                                      (($i == 3) ? 'Third Applicant' : 'Fourth Applicant'));
                @endphp

                <div class="item-section-wrap no-break">
                    <div class="item-title">5.{{ $i > 1 ? $i : '' }} {{ $applicantTitle }}:</div>

                    <div class="item-wrap double-wrap">
                        <div class="item-left-wrap">
                            <div class="gender-wrap">
                                @php
                                    $genders = ['Male', 'Female', 'Other'];
                                    $gender = $applicantData['gender'] ?? '';
                                @endphp
                                @foreach ($genders as $g)
                                    <label class="status-option">
                                        <input type="checkbox" name="gender_{{ $i }}" value="{{ $g }}"
                                            {{ strtolower($gender) == strtolower($g) ? 'checked' : '' }} disabled>
                                        {{ $g }}
                                    </label>
                                @endforeach
                            </div>

                            <div class="app-info-wrap">
                                <div class="info-text">
                                    <div>Mother's Name:</div>
                                    <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $applicantData['mother_name'] ?? '' }}</div>
                                </div>
                                <div class="info-text">
                                    <div>NID No:</div>
                                    <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $applicantData['nid'] ?? '' }}</div>
                                </div>
                                <div class="info-text">
                                    <div>Passport No:</div>
                                    <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $applicantData['passport'] ?? '' }}</div>
                                </div>
                                <div class="info-text">
                                    <div>Occupation:</div>
                                    <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $applicantData['occupation'] ?? '' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="item-right-wrap">
                            <div class="app-info-wrap" style="margin-right: 0">
                                <div class="info-text">
                                    <div>Date of Birth:</div>
                                    <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $applicantData['dob'] ?? '' }}</div>
                                </div>
                                <div class="info-text">
                                    <div>Father's Name:</div>
                                    <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $applicantData['father_name'] ?? '' }}</div>
                                </div>
                                <div class="info-text">
                                    <div>e-TIN No. (if any):</div>
                                    <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $applicantData['e_tin'] ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- PAGE BREAK -->
        <div class="page-break"></div>

        <!-- PAGE 2 -->
        <div class="page-2 no-break">
            <div class="bp-header-wrap">
                <div class="header-img">
                    <img class="img-fluid" src="{{ URL::asset('public/img/logo/Logo_new.png') }}" alt="Prime Bank PLC" style="height: 35px;" />
                    <img class="img-fluid" src="{{ URL::asset('public/img/logo/bp-mngm.png') }}" alt="Prime Bank PLC" style="height: 35px;" />
                </div>
                <p class="head-title">BP (Business Partner) ID OPENING FORM</p>
            </div>

            <!-- Section 6: Contact Details -->
            <div class="item-section-wrap">
                <div class="item-title">{{ 5 + $applicantCount }}. Contact Details:</div>
                <div class="item-wrap">
                    <div class="item-left-wrap">
                        <div class="app-info-wrap">
                            <div class="info-text">
                                <div>Present Address/Business Address:</div>
                                <div style="border: 1px solid #666; padding: 8px; min-height: 40px; margin-top: 5px;">{{ $raw->present_address ?? '' }}</div>
                            </div>
                            <div class="info-text" style="margin-top: 10px;">
                                <div>Permanent Address:</div>
                                <div style="border: 1px solid #666; padding: 8px; min-height: 40px; margin-top: 5px;">{{ $raw->permanent_address ?? '' }}</div>
                            </div>
                        </div>

                        <div class="app-info-wrap" style="margin-top: 15px;">
                            <div class="info-text">
                                <div>Phone No:</div>
                                <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $raw->phone_number ?? '' }}</div>
                            </div>
                            <div class="info-text">
                                <div>Mobile No:</div>
                                <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $raw->mobile_number ?? '' }}</div>
                            </div>
                            <div class="info-text">
                                <div>Email:</div>
                                <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $raw->email ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 7: Bank Details -->
            <div class="item-section-wrap">
                <div class="item-title">{{ 6 + $applicantCount }}. Bank Details:</div>
                <div class="item-wrap">
                    <div class="item-left-wrap">
                        <div class="app-info-wrap">
                            <div class="info-text">
                                <div>Bank Name:</div>
                                <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $raw->bank_name ?? '' }}</div>
                            </div>
                            <div class="info-text">
                                <div>Branch Name:</div>
                                <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $raw->branch_name ?? '' }}</div>
                            </div>
                            <div class="info-text">
                                <div>Account Number:</div>
                                <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $raw->account_number ?? '' }}</div>
                            </div>
                            <div class="info-text">
                                <div>Account Type:</div>
                                <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $raw->account_type ?? '' }}</div>
                            </div>
                            <div class="info-text">
                                <div>Routing No.:</div>
                                <div style="border-bottom: 1px dotted #666; flex: 1; padding-left: 5px;">{{ $raw->rouning_no ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 8: Nominees -->
            <div class="item-section-wrap">
                <div class="item-title">{{ 7 + $applicantCount }}. Nominee(s) [Applicable for Individual Account Holder]:</div>
                <div class="sub-text">
                    I/we authorize the following person(s) as nominee(s) to receive/draw the amount in my/our account in the event of my/our death.
                </div>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 15%;">Name</th>
                            <th style="width: 20%;">NID/Passport/ Birth Certificate No</th>
                            <th style="width: 20%;">Address</th>
                            <th style="width: 15%;">Relation with Account Holder</th>
                            <th style="width: 12%;">Date of Birth</th>
                            <th style="width: 8%;">% Payable</th>
                            <th style="width: 10%;">Signature of the Nominee</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 1; $i <= $nomineeCount; $i++)
                            @php
                                $nomineeData = getNomineeData($raw, $i);
                            @endphp
                            <tr>
                                <td>{{ $nomineeData['name'] }}</td>
                                <td>{{ $nomineeData['nid'] }}</td>
                                <td>{{ $nomineeData['address'] }}</td>
                                <td>{{ $nomineeData['relation'] }}</td>
                                <td>{{ $nomineeData['dob'] }}</td>
                                <td>{{ $nomineeData['payable'] }}</td>
                                <td>
                                    @if(!empty($nomineeData['signature']))
                                        <div class="img-box">
                                            <img src="{{ asset($nomineeData['signature']) }}" style="max-width: 80px; max-height: 40px;">
                                        </div>
                                    @else
                                        <div style="height: 40px; display: flex; align-items: center; justify-content: center;">---</div>
                                    @endif
                                </td>
                            </tr>
                        @endfor

                        <!-- Fill remaining rows if less than 4 -->
                        @for ($i = $nomineeCount + 1; $i <= 4; $i++)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="page-end"></div>
        </div>

        <!-- PAGE BREAK -->
        <div class="page-break"></div>

        <!-- PAGE 3 -->
        <div class="page-3 no-break">
            <div class="bp-header-wrap">
                <div class="header-img">
                    <img class="img-fluid" src="{{ URL::asset('public/img/logo/Logo_new.png') }}" alt="Prime Bank PLC" style="height: 35px;" />
                    <img class="img-fluid" src="{{ URL::asset('public/img/logo/bp-mngm.png') }}" alt="Prime Bank PLC" style="height: 35px;" />
                </div>
                <p class="head-title">BP (Business Partner) ID OPENING FORM</p>
            </div>

            <!-- Section 9: Photographs -->
            <div class="item-section-wrap">
                <div class="item-title">{{ 8 + $applicantCount }}. Photographs:</div>
                <div class="item-wrap">
                    <div class="item-photo-wrap">
                        @for ($i = 1; $i <= min($applicantCount, 3); $i++)
                            @php
                                $suffix = ($i == 1) ? '' :
                                         (($i == 2) ? '_two' : '_third');
                                $passportField = 'passport_image' . $suffix . '_path_url';
                                $signatureField = 'signature_image' . $suffix . '_path_url';
                                $applicantTitle = ($i == 1) ? 'First' : (($i == 2) ? 'Second' : 'Third');
                            @endphp

                            <div class="photo-item">
                                <div style="font-weight: bold; margin-bottom: 10px;">{{ $applicantTitle }} Applicant/Authorized Signatory</div>

                                <div style="display: flex; gap: 15px; width: 100%; justify-content: center;">
                                    @if(!empty($raw->{$passportField}))
                                        <div class="img-box">
                                            <div style="font-size: 9pt; margin-bottom: 5px;">Passport Photo</div>
                                            <img src="{{ asset($raw->{$passportField}) }}" style="width: 90px; height: 110px; object-fit: cover; border: 1px solid #ccc;">
                                        </div>
                                    @else
                                        <div class="img-box">
                                            <div style="font-size: 9pt; margin-bottom: 5px;">Passport Photo</div>
                                            <div style="width: 90px; height: 110px; border: 1px dashed #999; display: flex; align-items: center; justify-content: center; color: #666;">
                                                Photo
                                            </div>
                                        </div>
                                    @endif

                                    @if(!empty($raw->{$signatureField}))
                                        <div class="img-box">
                                            <div style="font-size: 9pt; margin-bottom: 5px;">Signature</div>
                                            <img src="{{ asset($raw->{$signatureField}) }}" style="width: 90px; height: 110px; object-fit: contain; border: 1px solid #ccc;">
                                        </div>
                                    @else
                                        <div class="img-box">
                                            <div style="font-size: 9pt; margin-bottom: 5px;">Signature</div>
                                            <div style="width: 90px; height: 110px; border: 1px dashed #999; display: flex; align-items: center; justify-content: center; color: #666;">
                                                Signature
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Section 10: Specimen Signature -->
            <div class="item-section-wrap">
                <div class="item-title">{{ 9 + $applicantCount }}. Specimen Signature:</div>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 20%;">Applicants</th>
                            <th style="width: 40%;">Name of Applicant/Authorized Signatory</th>
                            <th style="width: 40%;">Signature with Date<br>(Official Seal is Mandatory for Signatory)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 1; $i <= 3; $i++)
                            @php
                                $applicantData = getApplicantData($raw, $i);
                                $suffix = ($i == 1) ? '' : (($i == 2) ? '_two' : '_third');
                                $signatureField = 'signature_image' . $suffix . '_path_url';
                                $applicantTitle = ($i == 1) ? 'First Applicant' : (($i == 2) ? 'Second Applicant' : 'Third Applicant');
                                $name = ($i == 1) ? ($raw->account_name ?? '') : ($raw->{'app_name' . $suffix} ?? '');
                            @endphp
                            <tr>
                                <td>{{ $applicantTitle }}</td>
                                <td>{{ $name }}</td>
                                <td style="min-height: 60px; vertical-align: top;">
                                    @if(!empty($raw->{$signatureField}) && $i <= $applicantCount)
                                        <div style="display: flex; flex-direction: column; align-items: center;">
                                            <img src="{{ asset($raw->{$signatureField}) }}" style="max-width: 150px; max-height: 50px; margin-bottom: 5px;">
                                            <div style="font-size: 9pt;">Date: {{ date('d/m/Y') }}</div>
                                        </div>
                                    @else
                                        <div style="height: 60px; display: flex; align-items: center; justify-content: center; color: #999;">
                                            @if($i <= $applicantCount)
                                                [Signature]
                                            @else
                                                ---
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <!-- Section 11: Special Instructions -->
            <div class="item-section-wrap">
                <div class="item-title">{{ 10 + $applicantCount }}. Special Instruction on Operation of Account (If Applicable):</div>
                <div class="item-wrap">
                    <div class="item-left-wrap">
                        <div class="gender-wrap">
                            <label class="status-option">
                                <input type="checkbox" name="operation_type" value="either" disabled>
                                Either or Survivor
                            </label>
                            <label class="status-option">
                                <input type="checkbox" name="operation_type" value="anyone" disabled>
                                Anyone Can Operate
                            </label>
                            <label class="status-option">
                                <input type="checkbox" name="operation_type" value="anytwo" disabled>
                                Any Two Will Operate
                            </label>
                        </div>
                        <div class="gender-wrap" style="margin-top: 10px;">
                            <label class="status-option">
                                <input type="checkbox" name="operation_type" value="only" disabled>
                                Only _____________________
                            </label>
                        </div>
                        <div class="gender-wrap" style="margin-top: 10px;">
                            <label class="status-option">
                                <input type="checkbox" name="operation_type" value="specific" disabled>
                                Account will be operated by _____________________
                            </label>
                        </div>
                    </div>
                </div>

                <div class="item-wrap" style="margin-top: 30px;">
                    <div class="sub-text" style="text-align: center; margin-bottom: 0; font-weight: bold;">For the Use of Bank Only</div>
                </div>

                <div class="item-wrap" style="margin-top: 50px;">
                    <div class="double-wrap" style="padding-top: 20px;">
                        <div class="item-left-wrap">
                            <div class="sub-text" style="text-align: center; margin-bottom: 0; white-space: nowrap; border-top: 1px solid #666; padding-top: 5px;">
                                ...................................................
                            </div>
                            <div class="sub-text" style="text-align: center; margin-bottom: 0;">Initiated By</div>
                        </div>
                        <div class="item-right-wrap">
                            <div class="sub-text" style="text-align: center; margin-bottom: 0; white-space: nowrap; border-top: 1px solid #666; padding-top: 5px;">
                                ...................................................
                            </div>
                            <div class="sub-text" style="text-align: center; margin-bottom: 0; font-size: 9pt;">
                                Authorized Officer of Government Securities Investment Window/Manager/Head of Treasury
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="item-section-wrap" style="margin-top: 30px;">
                <div class="sub-text" style="margin-bottom: 0; margin-top: 16px; font-weight: bold;">N.B.</div>
                <div class="sub-text" style="margin-bottom: 5px;">
                    1) Certificate of Incorporation no. or Internal Revenue Service (IRS) or relevant document no. may be used instead of trade license in SL no. 6 for Non-Resident individuals and institutional investors.
                </div>
                <div class="sub-text">
                    2) Based on relevant SWIFT messages, the information of the authorized signatories of the custodian bank may be used in SL. No. 10, 11, 12 and 13 for Non-Resident individuals and institutional investors.
                </div>
            </div>

            <div class="page-end"></div>
        </div>

        <!-- PAGE BREAK -->
        <div class="page-break"></div>

        <!-- PAGE 4: Required Documents -->
        <div class="page-4 no-break">
            <div class="bp-header-wrap">
                <div class="header-img">
                    <img class="img-fluid" src="{{ URL::asset('public/img/logo/Logo_new.png') }}" alt="Prime Bank PLC" style="height: 35px;" />
                    <img class="img-fluid" src="{{ URL::asset('public/img/logo/bp-mngm.png') }}" alt="Prime Bank PLC" style="height: 35px;" />
                </div>
                <p class="head-title">REQUIRED DOCUMENTS</p>
            </div>

            <div class="doc-wrap">
                <div class="doc-item">
                    <b>For Banks/FIs/Limited Company:</b>
                    <ol>
                        <li>Application Form</li>
                        <li>Bank Account Details/DAB Account Details (for Banks/FIs)</li>
                        <li>Certificate of Incorporation</li>
                        <li>Certificate of Commencement (if applicable)</li>
                        <li>License From the Respective Authority (if applicable)</li>
                        <li>Memorandum/Articles of Association</li>
                        <li>Board Resolution</li>
                        <li>e-TIN</li>
                        <li>Registered Address</li>
                        <li>Contact Details of Relevant Personnel</li>
                        <li>Photo and NID of the authorized signatory(s)</li>
                    </ol>
                </div>

                <div class="doc-item">
                    <b>For Pension/Provident/Gratuity/Mutual Fund:</b>
                    <ol>
                        <li>Application Form</li>
                        <li>Bank Account Details</li>
                        <li>Registration/Approval Certificate</li>
                        <li>Deed of Trust (if applicable)</li>
                        <li>Resolution/Meeting Minutes of Board of Trustees</li>
                        <li>NBR's Certificate (if applicable)</li>
                        <li>Contact details of Authorized Signatory</li>
                        <li>Photo and NID of the authorized signatory(s)</li>
                    </ol>
                </div>

                <div class="doc-item">
                    <b>For Sole Proprietorship (SP)/Partnership Business:</b>
                    <ol>
                        <li>Application Form</li>
                        <li>Bank Account Details</li>
                        <li>Trade License</li>
                        <li>Partnership Deed (for Partnership Business)</li>
                        <li>e-TIN</li>
                        <li>NID of proprietor/partners</li>
                        <li>Contact details of proprietor/partners</li>
                        <li>Photo and Information of the Nominee(s) (for SP)</li>
                        <li>NID/Passport of the Nominee(s) (for SP)</li>
                    </ol>
                </div>

                <div class="doc-item">
                    <b>For Individuals:</b>
                    <ol>
                        <li>Application Form</li>
                        <li>Bank Account Details</li>
                        <li>NID/Passport</li>
                        <li>Photo</li>
                        <li>e-TIN</li>
                        <li>Contact details</li>
                        <li>Photo and Information of the Nominee(s)</li>
                        <li>NID/Passport/Birth Certificate of the Nominee(s)</li>
                    </ol>
                </div>

                <div class="doc-item">
                    <b>Foreign/Non-Resident Individuals:</b>
                    <ol>
                        <li>Application Form</li>
                        <li>Bank Details for Investor's NFCA/NITA account</li>
                        <li>Photo and Passport</li>
                        <li>TIN/Tax Certificate/Related Certificate (if applicable)</li>
                        <li>Contact details</li>
                        <li>Photo and Passport/ID/Birth Certificate of the Nominee(s)</li>
                    </ol>
                </div>

                <div class="doc-item">
                    <b>Foreign/Non-Resident Institutions:</b>
                    <ol>
                        <li>Application Form</li>
                        <li>Bank Details for Investor's NFCA/NITA account</li>
                        <li>Certificate of Incorporation/or Relevant Document</li>
                        <li>Memorandum/Articles of Association (if applicable)</li>
                        <li>Partnership Deed (for Partnership Business) (if applicable)</li>
                        <li>Resolution/Meeting Minutes of Board of Trustees (if applicable)</li>
                        <li>TIN/Tax Certificate/Related Certificate (if applicable)</li>
                        <li>Registered Address</li>
                        <li>Contact Details of Relevant Personnel (Official of the Custodian Bank)</li>
                        <li>Photo and NID of the authorized signatory(s) (Official of the Custodian Bank)</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>
@endsection

<script type="text/javascript">
    // Auto print when page loads
    window.onload = function() {
        setTimeout(function() {
            window.print();
        }, 500);
    };

    // Close window after printing
    window.onafterprint = function() {
        setTimeout(function() {
            window.close();
        }, 500);
    };
</script>
