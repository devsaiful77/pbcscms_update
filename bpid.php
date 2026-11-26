<?php
$hostname = 'localhost';
$username = 'root';
$dPass    = '';
$dbname   = 'dbpbcms';
$port     = 3306;

try {
    $dbh = new PDO("mysql:host=$hostname;port=$port;dbname=$dbname;charset=utf8mb4", $username, $dPass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

function userIdPadLeftWith0($input, $digit, $padString)
{
    return str_pad($input, $digit, $padString, STR_PAD_LEFT);
}

// ট্রানজেকশন সেফ সিকোয়েন্স ফাংশন (লুপের ভেতরে কাজ করবে)
function dayWiseSequence($dbh, $sequenceFrom = "sr")
{
    $tableName = "sequences_" . $sequenceFrom;
    $now       = date('Y-m-d H:i:s');

    $stmtInsert = $dbh->prepare("INSERT INTO `$tableName` (`created_at`, `updated_at`) VALUES (?, ?)");
    $stmtInsert->execute([$now, $now]);

    return $dbh->lastInsertId();
}

// লুপ শুরুর আগেই পূর্বের দিনের সিকোয়েন্স ডাটা ক্লিয়ার করা (Implicit Commit এড়ানোর জন্য)
function resetOldSequenceIfNewDay($dbh, $sequenceFrom = "sr")
{
    $tableName = "sequences_" . $sequenceFrom;
    $todayDate = date('Y-m-d');

    $stmt = $dbh->prepare("SELECT `id` FROM `$tableName` WHERE DATE(`created_at`) < ? LIMIT 1");
    $stmt->execute([$todayDate]);
    if ($stmt->fetch()) {
        $dbh->exec("DELETE FROM `$tableName`");
        $dbh->exec("ALTER TABLE `$tableName` AUTO_INCREMENT = 1");
    }
}

// লুপের বাইরে একবার রান হবে
resetOldSequenceIfNewDay($dbh, 'sr');
$currentTime      = time();
$time_and_ext    = date('H:i:s', $currentTime);
$created_by         = 'ccm1';
$account_type       = 2;
$subgroup_id        = 209;
$sub_group_info_id  = 395;
$unit_id            = 2;
$issue_id           = 1118;
$access_by          = 'bp2';
$comments           = 'this is a existing BPID. Created by Script.';
$comment_user_id    = 'bp2';
$comment_action     = 'Close';
$product_type       = 2;
$w_form_type        = 1118;

$records = [
    [
        'bp_id'              => 12121212,
        'account_number'     => '2145216000005',
        'SIF_Number'         => 121212,

        'account_title'      => 'ABU NASER MD. BIN HOSSAIN',
        'customer_name'      => 'ABU NASER MD. BIN HOSSAIN',

        'contact_no_1'       => '8801611344344',
        'mobile_number'      => '8801611344344',

        'email_1'            => 'naser@gmail.com',
        'email_address'      => 'naser@gmail.com',

        'branch_name'        => 'LALDIGHI EAST BRANCH',
        'acc_opening_branch' => 'LALDIGHI EAST BRANCH',
    ],
    [
        'bp_id'              => 23232223,
        'account_number'     => '21452160001111',
        'SIF_Number'         => 232323,

        'account_title'      => 'ABU HOSSAIN',
        'customer_name'      => 'ABU HOSSAIN',

        'contact_no_1'       => '8801611111111',
        'mobile_number'      => '8801611111111',

        'email_1'            => 'hasan@gmail.com',
        'email_address'      => 'hasan@gmail.com',

        'branch_name'        => 'Dhaka BRANCH',
        'acc_opening_branch' => 'Dhaka BRANCH',
    ],

];

$successCount = 0;
$failedCount  = 0;
$failedRows   = [];

// স্টেটমেন্ট প্রিপেয়ার
$stmt_ref = $dbh->prepare("INSERT INTO `reference` (
    `reference_number`, `date`, `created_by`, `account_type`, `subgroup_id`, `sub_group_info_id`, 
    `unit_id`, `issue_id`, `is_tara`, `status`, `segment_priority`, `form_status`, 
    `api_status`, `iris_api_status`, `failed_api`, `failed_retry`, `memo`, `issues_from`, 
    `access_by`, `access_date`, `created_at`, `updated_at`
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 47, 0, 11, 0, 0, NULL, 0, NULL, 'wform', ?, ?, ?, ?)");

$stmt_bp = $dbh->prepare("INSERT INTO `bp_ids` (
    `bp_id`, `account_number`, `reference_number`, `branch_name`, `account_title`, 
    `contact_no_1`, `email_1`, `contact_no_2`, `email_2`, `contact_no_3`, `email_3`, 
    `contact_no_4`, `email_4`, `created_at`, `updated_at`
) VALUES (?, ?, ?, ?, ?, ?, ?, NULL, NULL, NULL, NULL, NULL, NULL, ?, ?)");

$stmt_com = $dbh->prepare("INSERT INTO `comments` (
    `reference_number`, `comments`, `time`, `user_id`, `unit_id`, `group_id`, 
    `subgroup_id`, `action`, `duration_in_minutes`, `isapproved`, `issendback`, 
    `sendbacksms`, `ip`, `created_at`, `updated_at`
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0.00, 0, 0, NULL, '127.0.0.1', ?, ?)");

$stmt_wform = $dbh->prepare("INSERT INTO `w_form` (
    `reference_number`, `account_number`, `mask_card_no`, `customer_name`, `acc_name`, 
    `mobile_number`, `email_address`, `segment`, `product_type`, `priority`, 
    `time_and_ext`, `source`, `tin_verified`, `caller_id`, `date_of_birth`, 
    `mother_name`, `father_name`, `mobile_number2`, `address`, `other`, 
    `dynamic_question`, `other2`, `notes`, `SIF_Number`, `myPrimeId`, 
    `static_verified`, `dynamic_verified`, `w_form_type`, `individual_acct_no`, 
    `balance`, `card_status`, `product_desc`, `account_status`, `acc_opening_branch`, 
    `branch_code`, `communication`, `customer_nid`, `passpor_number`, `created_at`, `updated_at`
) VALUES (
    ?, ?, NULL, ?, ?, 
    ?, ?, NULL, ?, NULL, 
    ?, NULL, NULL, ?, ?, 
    NULL, NULL, NULL, NULL, NULL, 
    NULL, NULL, NULL, ?, NULL, 
    NULL, NULL, ?, NULL, 
    0.00, NULL, NULL, NULL, ?, 
    NULL, NULL, NULL, NULL, ?, ?
)");

$stmt_wform_type = $dbh->prepare("INSERT INTO `w_form_type` (`reference_number`, `extra_field`, `check_list`) VALUES (?, ?, NULL)");

foreach ($records as $index => $row) {
    $dbh->beginTransaction();

    try {
        $currentTime      = time();
        $formattedDate    = date('Y-m-d H:i:s', $currentTime);

        $seqId            = dayWiseSequence($dbh, 'sr');
        $reference_number = "S" . date("ymd") . "AC" . userIdPadLeftWith0($seqId, 6, '0');

        // 1. Reference
        $stmt_ref->execute([
            $reference_number,
            $currentTime,
            $row['created_by'] ?? 'ccm1',
            $row['account_type'] ?? 2,
            $row['subgroup_id'] ?? 209,
            $row['sub_group_info_id'] ?? 395,
            $row['unit_id'] ?? 2,
            $row['issue_id'] ?? 1118,
            $row['access_by'] ?? 'bp2',
            $currentTime,
            $formattedDate,
            $formattedDate
        ]);

        // 2. BP IDs
        $stmt_bp->execute([
            $row['bp_id'] ?? NULL,
            $row['account_number'] ?? NULL,
            $reference_number,
            $row['branch_name'] ?? NULL,
            $row['account_title'] ?? NULL,
            $row['contact_no_1'] ?? NULL,
            $row['email_1'] ?? NULL,
            $formattedDate,
            $formattedDate
        ]);

        // 3. Comments
        $stmt_com->execute([
            $reference_number,
            $row['comments'] ?? 'Created by Script.',
            $currentTime,
            $row['comment_user_id'] ?? 'bp2',
            $row['unit_id'] ?? 2,
            $row['subgroup_id'] ?? 209,
            $row['sub_group_info_id'] ?? 395,
            $row['comment_action'] ?? 'Close',
            $formattedDate,
            $formattedDate
        ]);

        // 4. W_Form
        $stmt_wform->execute([
            $reference_number,
            $row['account_number'] ?? NULL,
            $row['customer_name'] ?? NULL,
            NULL,
            $row['mobile_number'] ?? NULL,
            $row['email_address'] ?? NULL,
            $product_type,
            $time_and_ext,
            NULL,
            NULL,
            $row['SIF_Number'] ?? NULL,
            $row['w_form_type'] ?? NULL,
            $row['acc_opening_branch'] ?? NULL,
            $formattedDate,
            $formattedDate
        ]);

        // 5. Checklist JSON
        $extraFieldArray = [
            ["BP Type Check:" => "Individual"],
            ["Residency" => "Resident"],
            ["Applicant Type" => "First"],
            ["Name of the Account:" => $row['account_title'] ?? ''],
            ["First Applicant Gender" => ''],
            ["First Applicant Date of Birth:" => ''],
            ["First Applicant Mother's Name" => ''],
            ["First Applicant Father's Name:" => ''],
            ["First Applicant NID/Passport:" => ''],
            ["First Applicant e-TIN No. (if any)" => ""],
            ["First Applicant Occupation:" => "Profession"],
            ["Second Applicant Gender" => ""],
            ["Second Applicant Date of Birth:" => ""],
            ["Second Applicant Mother's Name" => ""],
            ["Second Applicant Father's Name:" => ""],
            ["Second Applicant NID/Passport::" => ""],
            ["Second Applicant e-TIN No. (if any)" => ""],
            ["Second Applicant Occupation:" => ""],
            ["Third Applicant Gender" => ""],
            ["Third Applicant Date of Birth:" => ""],
            ["Third Applicant Mother's Name" => ""],
            ["Third Applicant Father's Name:" => ""],
            ["Third Applicant NID/Passport:" => ""],
            ["Third Applicant e-TIN No. (if any)" => ""],
            ["Third Applicant Occupation:" => ""],
            ["Fourth Applicant Gender" => ""],
            ["Fourth Applicant Date of Birth:" => ""],
            ["Fourth Applicant Mother's Name" => ""],
            ["Fourth Applicant Father's Name:" => ""],
            ["Fourth Applicant NID/Passport:" => ""],
            ["Fourth Applicant e-TIN No. (if any)" => ""],
            ["Fourth Applicant Occupation:" => ""],
            ["Present Address:" => ''],
            ["Permanent Address:" => ''],
            ["Phone No:" => $row['contact_no_1'] ?? ''],
            ["Mobile No:" => $row['contact_no_1'] ?? ''],
            ["Email:" => $row['email_1'] ?? ''],
            ["Bank Name:" => "Prime Bank PLC."],
            ["Branch Name:" => $row['branch_name'] ?? ''],
            ["Account Number:" => $row['account_number'] ?? ''],
            ["Account Type:" => "CONSUMER"],
            ["Routing No:" => ""],
            ["First Nominee Name" => ''],
            ["First Nominee NID/Passport/Birth No:" => ''],
            ["First Nominee Address" => ""],
            ["First Nominee Relation with Account Holder:" => ''],
            ["First Nominee DOB" => ""],
            ["First Nominee % Payable" => '50'],
            ["Second Nominee Name" => ""],
            ["Second Nominee NID/Passport/Birth No:" => ""],
            ["Second Nominee Address" => ""],
            ["Second Nominee Relation with Account Holder:" => ""],
            ["Second Nominee DOB" => ""],
            ["Second Nominee % Payable" => ""],
            ["Third Nominee Name" => ""],
            ["Third Nominee NID/Passport/Birth No:" => ""],
            ["Third Nominee Address:" => ""],
            ["Third Nominee Relation with Account Holder:" => ""],
            ["Third Nominee DOB:" => ""],
            ["Third Nominee % Payable" => ""],
            ["Fourth Nominee Name" => ""],
            ["Fourth Nominee NID/Passport/Birth No:" => ""],
            ["Fourth Nominee Address" => ""],
            ["Fourth Nominee Relation with Account Holder:" => ""],
            ["Fourth Nominee DOB" => ""],
            ["Fourth Nominee % Payable" => ""],
            ["First Applicant Name:" => $row['account_title'] ?? ''],
            ["Second Applicant Name:" => ""],
            ["Third Applicant Name:" => ""],
            ["Four Applicant Name:" => ""],
            ["First Applicant Contact No:" => $row['contact_no_1'] ?? ''],
            ["First Applicant Email:" => $row['email_1'] ?? ''],
            ["Second Applicant Contact No:" => ""],
            ["Second Applicant Email:" => ""],
            ["Third Applicant Contact No:" => ""],
            ["Third Applicant Email:" => ""],
            ["Four Applicant Contact No:" => ""],
            ["Four Applicant Email:" => ""]
        ];

        $exta_field_json = json_encode($extraFieldArray, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // 6. W_Form_Type
        $stmt_wform_type->execute([$reference_number, $exta_field_json]);

        $dbh->commit();
        $successCount++;
    } catch (Exception $e) {
        // Active ট্রানজেকশন থাকলে তবেই কেবল rollBack হবে
        if ($dbh->inTransaction()) {
            $dbh->rollBack();
        }
        $failedCount++;
        $failedRows[] = [
            'row_index'        => $index + 1,
            'account_number'   => $row['account_number'] ?? 'N/A',
            'bp_id'            => $row['bp_id'] ?? 'N/A',
            'reference_number' => $reference_number ?? 'N/A',
            'error_message'    => $e->getMessage()
        ];
    }
}

// ফলাফল আউটপুট
echo "\n====================================\n";
echo "           EXECUTION SUMMARY        \n";
echo "====================================\n";
echo "Total Processed : " . count($records) . "\n";
echo "Total Success   : " . $successCount . "\n";
echo "Total Failed    : " . $failedCount . "\n";
echo "====================================\n";

if (!empty($failedRows)) {
    echo "\nFAILED DETAILS:\n";
    foreach ($failedRows as $fail) {
        echo "Row #{$fail['row_index']} | BP ID: {$fail['bp_id']} | Acc: {$fail['account_number']} | Reason: {$fail['error_message']}\n";
    }
}
