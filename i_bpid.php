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

function dayWiseSequence($dbh, $sequenceFrom = "sr")
{
    $tableName = "sequences_" . $sequenceFrom;
    $now       = date('Y-m-d H:i:s');

    $stmtInsert = $dbh->prepare("INSERT INTO `$tableName` (`created_at`, `updated_at`) VALUES (?, ?)");
    $stmtInsert->execute([$now, $now]);

    return $dbh->lastInsertId();
}

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

resetOldSequenceIfNewDay($dbh, 'sr');
$currentTime      = time();
$time_and_ext    = date('H:i:s', $currentTime);
$created_by         = 'ccm1';
$account_type       = 2;
$subgroup_id        = 215;
$sub_group_info_id  = 1518;
$unit_id            = 2;
$issue_id           = 1192;
$access_by          = 'kawser14';
$comments           = 'this is a existing BPID. Created by Script.';
$comment_user_id    = 'kawser14';
$comment_action     = 'Close';
$product_type       = 2;
$w_form_type        = 1192;

$records = [
    [
        'bp_id'              => 'GI137SELIMULLA',
        'account_number'     => '2118218034480',
        'SIF_Number'         => '2505788',

        'account_title'      => 'MOHAMMED SELIM ULLAH',
        'customer_name'      => 'MOHAMMED SELIM ULLAH',

        'contact_no_1'       => '8801610009004',
        'mobile_number'      => '8801610009004',

        'email_1'            => 'shobuj81@gmail.com',
        'email_address'      => 'shobuj81@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137AAKASADPB',
        'account_number'     => '2118216035828',
        'SIF_Number'         => '2574670',

        'account_title'      => 'AAKA ASAD',
        'customer_name'      => 'AAKA ASAD',

        'contact_no_1'       => '8801729297676',
        'mobile_number'      => '8801729297676',

        'email_1'            => 'aaka.asad@hotmail.com',
        'email_address'      => 'aaka.asad@hotmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABBASUDDI',
        'account_number'     => '2205219001037',
        'SIF_Number'         => '1015534',

        'account_title'      => 'ABBASUDDIN HILALEE',
        'customer_name'      => 'ABBASUDDIN HILALEE',

        'contact_no_1'       => '8801716280487',
        'mobile_number'      => '8801716280487',

        'email_1'            => 'a.hilalee@yahoo.com',
        'email_address'      => 'a.hilalee@yahoo.com',

        'branch_name'        => 'IBRAHIMPUR BRANCH',
        'acc_opening_branch' => 'IBRAHIMPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABDAWALPB',
        'account_number'     => '2174211009910',
        'SIF_Number'         => '2047358',

        'account_title'      => 'MD ABDUL AWAL',
        'customer_name'      => 'MD ABDUL AWAL',

        'contact_no_1'       => '8801737700516',
        'mobile_number'      => '8801737700516',

        'email_1'            => 'kkc.awal@yahoo.com',
        'email_address'      => 'kkc.awal@yahoo.com',

        'branch_name'        => 'MYMENSINGH BRANCH',
        'acc_opening_branch' => 'MYMENSINGH BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABDRAZPRM',
        'account_number'     => '2104214039208',
        'SIF_Number'         => '2504053',

        'account_title'      => 'MD. ABDUR RAZZAQUE',
        'customer_name'      => 'MD. ABDUR RAZZAQUE',

        'contact_no_1'       => '8801819833013',
        'mobile_number'      => '8801819833013',

        'email_1'            => 'mdabdur@yahoo.com',
        'email_address'      => 'mdabdur@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABDULALPB',
        'account_number'     => '2126217015001',
        'SIF_Number'         => '2720266',

        'account_title'      => 'MD. ABDULLAH AL MAHMUD',
        'customer_name'      => 'MD. ABDULLAH AL MAHMUD',

        'contact_no_1'       => '8801751141851',
        'mobile_number'      => '8801751141851',

        'email_1'            => 'mahmud389@gmail.com',
        'email_address'      => 'mahmud389@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137ABDULKAFI',
        'account_number'     => '2104218038339',
        'SIF_Number'         => '2421372',

        'account_title'      => 'MD ABDULLAH AL KAFI',
        'customer_name'      => 'MD ABDULLAH AL KAFI',

        'contact_no_1'       => '8801716558089',
        'mobile_number'      => '8801716558089',

        'email_1'            => 'abdullah.alkafi@bb.org.bd',
        'email_address'      => 'abdullah.alkafi@bb.org.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABDULMAMU',
        'account_number'     => '2146213019482',
        'SIF_Number'         => '2485212',

        'account_title'      => 'ABDULLAH-AL-MAMUN',
        'customer_name'      => 'ABDULLAH-AL-MAMUN',

        'contact_no_1'       => '8801626328986',
        'mobile_number'      => '8801626328986',

        'email_1'            => 'mamun_eco07@yahoo.com',
        'email_address'      => 'mamun_eco07@yahoo.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABDUSALPB',
        'account_number'     => '2219213015318',
        'SIF_Number'         => '2859263',

        'account_title'      => 'ABDUS SALAM MD TANVIR HOSSAIN SIDDIQUI',
        'customer_name'      => 'ABDUS SALAM MD TANVIR HOSSAIN SIDDIQUI',

        'contact_no_1'       => '8801713409239',
        'mobile_number'      => '8801713409239',

        'email_1'            => 'tanvir.siddiqui@gmail.com',
        'email_address'      => 'tanvir.siddiqui@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABKAZADPB',
        'account_number'     => '2148214006197',
        'SIF_Number'         => '1165473',

        'account_title'      => 'ABUL KALAM AZAD',
        'customer_name'      => 'ABUL KALAM AZAD',

        'contact_no_1'       => '8801819295643',
        'mobile_number'      => '8801819295643',

        'email_1'            => 'azadabul.2008@gmail.com',
        'email_address'      => 'azadabul.2008@gmail.com',

        'branch_name'        => 'KAKRAIL BRANCH',
        'acc_opening_branch' => 'KAKRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABRAHMANP',
        'account_number'     => '2162219015210',
        'SIF_Number'         => '2257510',

        'account_title'      => 'MOHAMMED ABDUR RAHIM',
        'customer_name'      => 'MOHAMMED ABDUR RAHIM',

        'contact_no_1'       => '8801682408381',
        'mobile_number'      => '8801682408381',

        'email_1'            => 'mgrahim.dev@gmail.com',
        'email_address'      => 'mgrahim.dev@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABRAIHAN',
        'account_number'     => '3134218022129',
        'SIF_Number'         => '2428602',

        'account_title'      => 'ABDULLAH -AL- RAIHAN',
        'customer_name'      => 'ABDULLAH -AL- RAIHAN',

        'contact_no_1'       => '8801919804798',
        'mobile_number'      => '8801919804798',

        'email_1'            => 'alvi_raihan@yahoo.com',
        'email_address'      => 'alvi_raihan@yahoo.com',

        'branch_name'        => 'IBB O.R. NIZAM ROAD BR',
        'acc_opening_branch' => 'IBB O.R. NIZAM ROAD BR',
    ],
    [
        'bp_id'              => 'IN137ABRAPRM',
        'account_number'     => '3134211000396',
        'SIF_Number'         => '1515487',

        'account_title'      => 'MR MD. ABRAR QUASHEM',
        'customer_name'      => 'MR MD. ABRAR QUASHEM',

        'contact_no_1'       => '8801778891137',
        'mobile_number'      => '8801778891137',

        'email_1'            => 'abrarquashem@icloud.com',
        'email_address'      => 'abrarquashem@icloud.com',

        'branch_name'        => 'IBB O.R. NIZAM ROAD BR',
        'acc_opening_branch' => 'IBB O.R. NIZAM ROAD BR',
    ],
    [
        'bp_id'              => 'IN137ABTMZAKPB',
        'account_number'     => '2118212009223',
        'SIF_Number'         => '75505',

        'account_title'      => 'ABU TAHER MD. ZAKARIA',
        'customer_name'      => 'ABU TAHER MD. ZAKARIA',

        'contact_no_1'       => '8801713081779',
        'mobile_number'      => '8801713081779',

        'email_1'            => 'zakariaewu@yahoo.com',
        'email_address'      => 'zakariaewu@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABUHANFPB',
        'account_number'     => '2203217005552',
        'SIF_Number'         => '2297327',

        'account_title'      => 'MD. ABU HANIF',
        'customer_name'      => 'MD. ABU HANIF',

        'contact_no_1'       => '8801329155526',
        'mobile_number'      => '8801329155526',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'ASHUGONJ BRANCH',
        'acc_opening_branch' => 'ASHUGONJ BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABUMUSA',
        'account_number'     => '2170216025295',
        'SIF_Number'         => '2489627',

        'account_title'      => 'ABU MUSA SUMAN',
        'customer_name'      => 'ABU MUSA SUMAN',

        'contact_no_1'       => '8801722549803',
        'mobile_number'      => '8801722549803',

        'email_1'            => 'musasuman@gmail.com',
        'email_address'      => 'musasuman@gmail.com',

        'branch_name'        => 'GARIB-E-NEWAZ AVE. BR',
        'acc_opening_branch' => 'GARIB-E-NEWAZ AVE. BR',
    ],
    [
        'bp_id'              => 'IN137ABURASEL',
        'account_number'     => '2132212035527',
        'SIF_Number'         => '2731017',

        'account_title'      => 'ABU RASEL MUHAMMAD MASUM',
        'customer_name'      => 'ABU RASEL MUHAMMAD MASUM',

        'contact_no_1'       => '8801713201868',
        'mobile_number'      => '8801713201868',

        'email_1'            => 'rasel.masum93@gmail.com',
        'email_address'      => 'rasel.masum93@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABURNAYAN',
        'account_number'     => '2104215033978',
        'SIF_Number'         => '1939890',

        'account_title'      => 'ABU RASHEL NAYAN',
        'customer_name'      => 'ABU RASHEL NAYAN',

        'contact_no_1'       => '8801717022287',
        'mobile_number'      => '8801717022287',

        'email_1'            => 'rashel.nayan@gmail.com',
        'email_address'      => 'rashel.nayan@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABUSALEPB',
        'account_number'     => '3133211022524',
        'SIF_Number'         => '2502093',

        'account_title'      => 'ABU ZAFAR MD. SALEH',
        'customer_name'      => 'ABU ZAFAR MD. SALEH',

        'contact_no_1'       => '8801747225856',
        'mobile_number'      => '8801747225856',

        'email_1'            => 'salehazm287@gmail.com',
        'email_address'      => 'salehazm287@gmail.com',

        'branch_name'        => 'IBB MIRPUR BRANCH',
        'acc_opening_branch' => 'IBB MIRPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137ABYOUSUFS',
        'account_number'     => '2148219007414',
        'SIF_Number'         => '378949',

        'account_title'      => 'ABU YOUSUF MUHAMMAD SOLAIMAN',
        'customer_name'      => 'ABU YOUSUF MUHAMMAD SOLAIMAN',

        'contact_no_1'       => '8801911403696',
        'mobile_number'      => '8801911403696',

        'email_1'            => 'abu.solaiman@bd.org.bd',
        'email_address'      => 'abu.solaiman@bd.org.bd',

        'branch_name'        => 'KAKRAIL BRANCH',
        'acc_opening_branch' => 'KAKRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ADIBAFAPB',
        'account_number'     => '2187211012720',
        'SIF_Number'         => '2401694',

        'account_title'      => 'ADIBA FAIRUZ PROME',
        'customer_name'      => 'ADIBA FAIRUZ PROME',

        'contact_no_1'       => '8801515632651',
        'mobile_number'      => '8801515632651',

        'email_1'            => 'adibapromel17@gmail.com',
        'email_address'      => 'adibapromel17@gmail.com',

        'branch_name'        => 'WARI BRANCH',
        'acc_opening_branch' => 'WARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137ADILUDDPB',
        'account_number'     => '3133211002450',
        'SIF_Number'         => '8010705',

        'account_title'      => 'MD ADIL UDDIN',
        'customer_name'      => 'MD ADIL UDDIN',

        'contact_no_1'       => '8801711113341',
        'mobile_number'      => '8801711113341',

        'email_1'            => 'adil.uddin@primebank.com.bd',
        'email_address'      => 'adil.uddin@primebank.com.bd',

        'branch_name'        => 'IBB MIRPUR BRANCH',
        'acc_opening_branch' => 'IBB MIRPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137ADNANPRIB',
        'account_number'     => '2118211033332',
        'SIF_Number'         => '2442267',

        'account_title'      => 'ADNAN HOSSAIN ANIK',
        'customer_name'      => 'ADNAN HOSSAIN ANIK',

        'contact_no_1'       => '8801314678221',
        'mobile_number'      => '8801314678221',

        'email_1'            => 'adnananik7991@gmail.com',
        'email_address'      => 'adnananik7991@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ADNANPRIM',
        'account_number'     => '0021030005105',
        'SIF_Number'         => '1017244',

        'account_title'      => 'ADNAN ABBAS HILALEE',
        'customer_name'      => 'ADNAN ABBAS HILALEE',

        'contact_no_1'       => '8801671406291',
        'mobile_number'      => '8801671406291',

        'email_1'            => 'mahfuzarani505@gmail.com',
        'email_address'      => 'mahfuzarani505@gmail.com',

        'branch_name'        => 'LALDIGHI EAST BRANCH',
        'acc_opening_branch' => 'LALDIGHI EAST BRANCH',
    ],
    [
        'bp_id'              => 'IN137AFIFAKTER',
        'account_number'     => '2118213033032',
        'SIF_Number'         => '2429073',

        'account_title'      => 'AFIFA AKTER FARIA',
        'customer_name'      => 'AFIFA AKTER FARIA',

        'contact_no_1'       => '8801701220969',
        'mobile_number'      => '8801701220969',

        'email_1'            => 'farta1301@gmail.com',
        'email_address'      => 'farta1301@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137AFZALULPB',
        'account_number'     => '2118215026190',
        'SIF_Number'         => '2150265',

        'account_title'      => 'MD AFZALUL ALAM BHUIYAN',
        'customer_name'      => 'MD AFZALUL ALAM BHUIYAN',

        'contact_no_1'       => '8801712224354',
        'mobile_number'      => '8801712224354',

        'email_1'            => 'afzalulalambhuiyan@yahoo.com',
        'email_address'      => 'afzalulalambhuiyan@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137AHANIKPB',
        'account_number'     => '2118211033332',
        'SIF_Number'         => '2442267',

        'account_title'      => 'ADNAN HOSSAIN ANIK',
        'customer_name'      => 'ADNAN HOSSAIN ANIK',

        'contact_no_1'       => '8801314678221',
        'mobile_number'      => '8801314678221',

        'email_1'            => 'adnananik7991@gmail.com',
        'email_address'      => 'adnananik7991@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137AHASNATPB',
        'account_number'     => '2112219002482',
        'SIF_Number'         => '574770',

        'account_title'      => 'MD. ABUL HASNAT KHAN',
        'customer_name'      => 'MD. ABUL HASNAT KHAN',

        'contact_no_1'       => '8801715673416',
        'mobile_number'      => '8801715673416',

        'email_1'            => 'jollyhasnali@gmail.com',
        'email_address'      => 'jollyhasnali@gmail.com',

        'branch_name'        => 'RAJSHAHI BRANCH',
        'acc_opening_branch' => 'RAJSHAHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137AHMFEROZ',
        'account_number'     => '2104213011976',
        'SIF_Number'         => '5987',

        'account_title'      => 'FEROZ AHMED',
        'customer_name'      => 'FEROZ AHMED',

        'contact_no_1'       => '8801718387647',
        'mobile_number'      => '8801718387647',

        'email_1'            => 'fa111406@primebank.com.bd',
        'email_address'      => 'fa111406@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137AHMMASPB',
        'account_number'     => '2219211013159',
        'SIF_Number'         => '2657728',

        'account_title'      => 'A H M MASUM',
        'customer_name'      => 'A H M MASUM',

        'contact_no_1'       => '8801730029274',
        'mobile_number'      => '8801730029274',

        'email_1'            => 'ahmmasum@gmail.com',
        'email_address'      => 'ahmmasum@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137AHSANAMPB',
        'account_number'     => '2207212007864',
        'SIF_Number'         => '1060320',

        'account_title'      => 'AHSANUL AMIN',
        'customer_name'      => 'AHSANUL AMIN',

        'contact_no_1'       => '61411793961',
        'mobile_number'      => '61411793961',

        'email_1'            => 'rony1217@gmail.com',
        'email_address'      => 'rony1217@gmail.com',

        'branch_name'        => 'TEJGAON BRANCH',
        'acc_opening_branch' => 'TEJGAON BRANCH',
    ],
    [
        'bp_id'              => 'IN137AHSANKPRM',
        'account_number'     => '2145216020942',
        'SIF_Number'         => '2066086',

        'account_title'      => 'MD. AHSAN-UL-KARIM',
        'customer_name'      => 'MD. AHSAN-UL-KARIM',

        'contact_no_1'       => '8801817608125',
        'mobile_number'      => '8801817608125',

        'email_1'            => 's13079@alumni.wmu.se',
        'email_address'      => 's13079@alumni.wmu.se',

        'branch_name'        => 'LALDIGHI EAST BRANCH',
        'acc_opening_branch' => 'LALDIGHI EAST BRANCH',
    ],
    [
        'bp_id'              => 'IN137AHYEPRIME',
        'account_number'     => '2127213002879',
        'SIF_Number'         => '364783',

        'account_title'      => 'MD. ABDUL HYE',
        'customer_name'      => 'MD. ABDUL HYE',

        'contact_no_1'       => '8801860755406',
        'mobile_number'      => '8801860755406',

        'email_1'            => 'ua.hye68@gmail.com',
        'email_address'      => 'ua.hye68@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137AIESHAAYU',
        'account_number'     => '2137215005118',
        'SIF_Number'         => '1316728',

        'account_title'      => 'AIESHA AYUB',
        'customer_name'      => 'AIESHA AYUB',

        'contact_no_1'       => '8801813836431',
        'mobile_number'      => '8801813836431',

        'email_1'            => 'aieshaayub@bb.org.bd',
        'email_address'      => 'aieshaayub@bb.org.bd',

        'branch_name'        => 'SHIMRAIL BRANCH',
        'acc_opening_branch' => 'SHIMRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137AIMTIAZPB',
        'account_number'     => '2105212000011',
        'SIF_Number'         => '1788544',

        'account_title'      => 'MD ABU IMTIAZ CHOWDHURY',
        'customer_name'      => 'MD ABU IMTIAZ CHOWDHURY',

        'contact_no_1'       => '8801617246933',
        'mobile_number'      => '8801617246933',

        'email_1'            => 'imtiazchowdhury2@gmail.com',
        'email_address'      => 'imtiazchowdhury2@gmail.com',

        'branch_name'        => 'KHATUNGONJ BRANCH',
        'acc_opening_branch' => 'KHATUNGONJ BRANCH',
    ],
    [
        'bp_id'              => 'IN137AJESMINPB',
        'account_number'     => '2127219022269',
        'SIF_Number'         => '2321057',

        'account_title'      => 'AFROZA JESMIN',
        'customer_name'      => 'AFROZA JESMIN',

        'contact_no_1'       => '8801683069673',
        'mobile_number'      => '8801683069673',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137AJITKUMAR',
        'account_number'     => '2110211000908',
        'SIF_Number'         => '273340',

        'account_title'      => 'AJIT KUMAR MAJUMDER',
        'customer_name'      => 'AJIT KUMAR MAJUMDER',

        'contact_no_1'       => '8801711145041',
        'mobile_number'      => '8801711145041',

        'email_1'            => 'ajit@juniv.edu',
        'email_address'      => 'ajit@juniv.edu',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137AKASADPRM',
        'account_number'     => '2118216035828',
        'SIF_Number'         => '2574670',

        'account_title'      => 'AAKA ASAD',
        'customer_name'      => 'AAKA ASAD',

        'contact_no_1'       => '8801729297676',
        'mobile_number'      => '8801729297676',

        'email_1'            => 'aaka.asad@hotmail.com',
        'email_address'      => 'aaka.asad@hotmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137AKAZADPRM',
        'account_number'     => '2117219008534',
        'SIF_Number'         => '146609',

        'account_title'      => 'MD. ABUL KALAM AZAD',
        'customer_name'      => 'MD. ABUL KALAM AZAD',

        'contact_no_1'       => '8801712889150',
        'mobile_number'      => '8801712889150',

        'email_1'            => 'mdazad2006@yahoo.com',
        'email_address'      => 'mdazad2006@yahoo.com',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137AKBAHARUL',
        'account_number'     => '2110213006473',
        'SIF_Number'         => '274477',

        'account_title'      => 'MR A K M GOLAM BAHARUL',
        'customer_name'      => 'MR A K M GOLAM BAHARUL',

        'contact_no_1'       => '8801550151309',
        'mobile_number'      => '8801550151309',

        'email_1'            => 'akmbaharul@gmail.com',
        'email_address'      => 'akmbaharul@gmail.com',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137AKHANDPB',
        'account_number'     => '2125211005124',
        'SIF_Number'         => '1667311',

        'account_title'      => 'MAHMUDUL HASAN AKHAND',
        'customer_name'      => 'MAHMUDUL HASAN AKHAND',

        'contact_no_1'       => '8801711124419',
        'mobile_number'      => '8801711124419',

        'email_1'            => 'akhand308@gmail.com',
        'email_address'      => 'akhand308@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137AKHINAJPB',
        'account_number'     => '2152216031188',
        'SIF_Number'         => '2583981',

        'account_title'      => 'AKHI NAJNIN',
        'customer_name'      => 'AKHI NAJNIN',

        'contact_no_1'       => '8801789345993',
        'mobile_number'      => '8801789345993',

        'email_1'            => 'akhinajnin03@gmail.com',
        'email_address'      => 'akhinajnin03@gmail.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137AKMATI',
        'account_number'     => '2125219001083',
        'SIF_Number'         => '974924',

        'account_title'      => 'A.K.M ATIQUR RAHMAN',
        'customer_name'      => 'A.K.M ATIQUR RAHMAN',

        'contact_no_1'       => '8801726134173',
        'mobile_number'      => '8801726134173',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137AKMHASAN',
        'account_number'     => '2118212031086',
        'SIF_Number'         => '2369369',

        'account_title'      => 'A K M HASANUZZAMAN',
        'customer_name'      => 'A K M HASANUZZAMAN',

        'contact_no_1'       => '8801710254550',
        'mobile_number'      => '8801710254550',

        'email_1'            => 'tfs.hasan@gmail.com',
        'email_address'      => 'tfs.hasan@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137AKTERBL',
        'account_number'     => '2192219004317',
        'SIF_Number'         => '1179203',

        'account_title'      => 'HASINA AKTER',
        'customer_name'      => 'HASINA AKTER',

        'contact_no_1'       => '8801533578303',
        'mobile_number'      => '8801533578303',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'BANASREE BRANCH',
        'acc_opening_branch' => 'BANASREE BRANCH',
    ],
    [
        'bp_id'              => 'IN137AKTHFARUK',
        'account_number'     => '2127219013821',
        'SIF_Number'         => '794268',

        'account_title'      => 'AKTHARUZZAMAN FARUK CHOWDHURY',
        'customer_name'      => 'AKTHARUZZAMAN FARUK CHOWDHURY',

        'contact_no_1'       => '8801849730144',
        'mobile_number'      => '8801849730144',

        'email_1'            => 'c.faruk4@gmail.com',
        'email_address'      => 'c.faruk4@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137AKTRHOSPB',
        'account_number'     => '2104217001932',
        'SIF_Number'         => '52188',

        'account_title'      => 'AKTER HOSSAIN',
        'customer_name'      => 'AKTER HOSSAIN',

        'contact_no_1'       => '8801971931291',
        'mobile_number'      => '8801971931291',

        'email_1'            => 'akter.hossain759@gmail.com',
        'email_address'      => 'akter.hossain759@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137AKULCPAUL',
        'account_number'     => '2127216000037',
        'SIF_Number'         => '382248',

        'account_title'      => 'AKUL CHANDRA PAUL',
        'customer_name'      => 'AKUL CHANDRA PAUL',

        'contact_no_1'       => '8801715077765',
        'mobile_number'      => '8801715077765',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137ALAKPRIME',
        'account_number'     => '2118211033753',
        'SIF_Number'         => '2479393',

        'account_title'      => 'ALAK CHAKRABORTY',
        'customer_name'      => 'ALAK CHAKRABORTY',

        'contact_no_1'       => '8801715043511',
        'mobile_number'      => '8801715043511',

        'email_1'            => 'alakbaec@gmail.com',
        'email_address'      => 'alakbaec@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ALAMINPB',
        'account_number'     => '2159218018806',
        'SIF_Number'         => '2725708',

        'account_title'      => 'MD. AL-AMIN',
        'customer_name'      => 'MD. AL-AMIN',

        'contact_no_1'       => '8801920932103',
        'mobile_number'      => '8801920932103',

        'email_1'            => 'alamin.vatax@gmail.com',
        'email_address'      => 'alamin.vatax@gmail.com',

        'branch_name'        => 'BIJOY NAGAR BRANCH',
        'acc_opening_branch' => 'BIJOY NAGAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137ALAMINSKH',
        'account_number'     => '2219215005150',
        'SIF_Number'         => '2093130',

        'account_title'      => 'MD. AL-AMIN SHEIKH',
        'customer_name'      => 'MD. AL-AMIN SHEIKH',

        'contact_no_1'       => '8801918968696',
        'mobile_number'      => '8801918968696',

        'email_1'            => 'pro.alamin1234@gmail.com',
        'email_address'      => 'pro.alamin1234@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ALIEMAMSF',
        'account_number'     => '2128213020714',
        'SIF_Number'         => '2483793',

        'account_title'      => 'MD. ALI EMAM SOROWAR FIROJI',
        'customer_name'      => 'MD. ALI EMAM SOROWAR FIROJI',

        'contact_no_1'       => '8801712563455',
        'mobile_number'      => '8801712563455',

        'email_1'            => 'ienanfiroji@yahoo.com',
        'email_address'      => 'ienanfiroji@yahoo.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137ALIHUSSA',
        'account_number'     => '2128212009601',
        'SIF_Number'         => '993532',

        'account_title'      => 'ALI HUSSAIN',
        'customer_name'      => 'ALI HUSSAIN',

        'contact_no_1'       => '8801711592253',
        'mobile_number'      => '8801711592253',

        'email_1'            => 'gtcl.ah@gmail.com',
        'email_address'      => 'gtcl.ah@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137ALINURPB',
        'account_number'     => '2219215005010',
        'SIF_Number'         => '2077760',

        'account_title'      => 'MD. ALINUR ISLAM',
        'customer_name'      => 'MD. ALINUR ISLAM',

        'contact_no_1'       => '8801913400453',
        'mobile_number'      => '8801913400453',

        'email_1'            => 'onirbanbadol@gmail.com',
        'email_address'      => 'onirbanbadol@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ALMANSURP',
        'account_number'     => '2146216004762',
        'SIF_Number'         => '1087764',

        'account_title'      => 'AL-MANSUR AL-MANSUR',
        'customer_name'      => 'AL-MANSUR AL-MANSUR',

        'contact_no_1'       => '8801727307194',
        'mobile_number'      => '8801727307194',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137ALMEHDIPB',
        'account_number'     => '2207215001021',
        'SIF_Number'         => '1731223',

        'account_title'      => 'AL-MEHEDI MUHAMMAD MIZANUR RAHMAN',
        'customer_name'      => 'AL-MEHEDI MUHAMMAD MIZANUR RAHMAN',

        'contact_no_1'       => '8801680172975',
        'mobile_number'      => '8801680172975',

        'email_1'            => 'jony006@gmail.com',
        'email_address'      => 'jony006@gmail.com',

        'branch_name'        => 'TEJGAON BRANCH',
        'acc_opening_branch' => 'TEJGAON BRANCH',
    ],
    [
        'bp_id'              => 'IN137ALMERAZ',
        'account_number'     => '2104219019726',
        'SIF_Number'         => '21885',

        'account_title'      => 'AL MERAZ AHMED',
        'customer_name'      => 'AL MERAZ AHMED',

        'contact_no_1'       => '8801715231685',
        'mobile_number'      => '8801715231685',

        'email_1'            => 'ameraza@gmail.com',
        'email_address'      => 'ameraza@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ALMONIRPB',
        'account_number'     => '2128212015699',
        'SIF_Number'         => '2002962',

        'account_title'      => 'MOHAMMAD AL MONIR KHAN',
        'customer_name'      => 'MOHAMMAD AL MONIR KHAN',

        'contact_no_1'       => '8801714168555',
        'mobile_number'      => '8801714168555',

        'email_1'            => 'monir@peccbd.com',
        'email_address'      => 'monir@peccbd.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137ALOKKMBPB',
        'account_number'     => '2104217038082',
        'SIF_Number'         => '2404614',

        'account_title'      => 'ALOKE KUMAR BOSU',
        'customer_name'      => 'ALOKE KUMAR BOSU',

        'contact_no_1'       => '8801713425807',
        'mobile_number'      => '8801713425807',

        'email_1'            => 'aloke.kumar@dse.com',
        'email_address'      => 'aloke.kumar@dse.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ALOKPSBK',
        'account_number'     => '2127215005250',
        'SIF_Number'         => '1093302',

        'account_title'      => 'ALOK PAUL',
        'customer_name'      => 'ALOK PAUL',

        'contact_no_1'       => '8801717041259',
        'mobile_number'      => '8801717041259',

        'email_1'            => 'alokpaul2004@gmail.com',
        'email_address'      => 'alokpaul2004@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137ALTAFHPB',
        'account_number'     => '2118216001481',
        'SIF_Number'         => '939503',

        'account_title'      => 'ALTAF HOSSAIN',
        'customer_name'      => 'ALTAF HOSSAIN',

        'contact_no_1'       => '8801675413861',
        'mobile_number'      => '8801675413861',

        'email_1'            => 'ah1854253@gmail.com',
        'email_address'      => 'ah1854253@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137AMAMUNPB',
        'account_number'     => '2104219033727',
        'SIF_Number'         => '2140803',

        'account_title'      => 'MD. ABDULLAH AL MAMUN',
        'customer_name'      => 'MD. ABDULLAH AL MAMUN',

        'contact_no_1'       => '8801818439485',
        'mobile_number'      => '8801818439485',

        'email_1'            => 'mamun439485@gmail.com',
        'email_address'      => 'mamun439485@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137AMANULPBL',
        'account_number'     => '3108212005825',
        'SIF_Number'         => '39000',

        'account_title'      => 'MD.AMANULLAH AMAN',
        'customer_name'      => 'MD.AMANULLAH AMAN',

        'contact_no_1'       => '8801713257865',
        'mobile_number'      => '8801713257865',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137AMITDIPU',
        'account_number'     => '2126215014584',
        'SIF_Number'         => '2643024',

        'account_title'      => 'AMIT CHAKRABORTY DIPU',
        'customer_name'      => 'AMIT CHAKRABORTY DIPU',

        'contact_no_1'       => '8801824655320',
        'mobile_number'      => '8801824655320',

        'email_1'            => 'amitchakraborty074@gmail.com',
        'email_address'      => 'amitchakraborty074@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137AMZADPB',
        'account_number'     => '2165219004070',
        'SIF_Number'         => '48811',

        'account_title'      => 'MD. AMZAD HOSSAIN',
        'customer_name'      => 'MD. AMZAD HOSSAIN',

        'contact_no_1'       => '8801730312516',
        'mobile_number'      => '8801730312516',

        'email_1'            => 'hossainamzad412@gmail.com',
        'email_address'      => 'hossainamzad412@gmail.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ANAMICSPB',
        'account_number'     => '2126217013444',
        'SIF_Number'         => '2531998',

        'account_title'      => 'ANAMICA SHIKDER',
        'customer_name'      => 'ANAMICA SHIKDER',

        'contact_no_1'       => '8801732640444',
        'mobile_number'      => '8801732640444',

        'email_1'            => 'ishtiack.mitul@gmail.com',
        'email_address'      => 'ishtiack.mitul@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137ANANBPRIM',
        'account_number'     => '2125214011855',
        'SIF_Number'         => '882107',

        'account_title'      => 'ANANNYA BISWAS',
        'customer_name'      => 'ANANNYA BISWAS',

        'contact_no_1'       => '8801717048247',
        'mobile_number'      => '8801717048247',

        'email_1'            => 'anannya1980@hotmail.com',
        'email_address'      => 'anannya1980@hotmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ANAYETRA',
        'account_number'     => '2118216034300',
        'SIF_Number'         => '2498622',

        'account_title'      => 'ANAYET RASHID',
        'customer_name'      => 'ANAYET RASHID',

        'contact_no_1'       => '8801844111144',
        'mobile_number'      => '8801844111144',

        'email_1'            => 'anayet@trucklagbe.com',
        'email_address'      => 'anayet@trucklagbe.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ANGSHUPB',
        'account_number'     => '2165216018495',
        'SIF_Number'         => '2643162',

        'account_title'      => 'ANGSHUMAN CHOWDHURY',
        'customer_name'      => 'ANGSHUMAN CHOWDHURY',

        'contact_no_1'       => '8801713301466',
        'mobile_number'      => '8801713301466',

        'email_1'            => 'a_chowdhury@live.com',
        'email_address'      => 'a_chowdhury@live.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ANGURABPB',
        'account_number'     => '2128215000987',
        'SIF_Number'         => '899021',

        'account_title'      => 'ANGURA BEGUM',
        'customer_name'      => 'ANGURA BEGUM',

        'contact_no_1'       => '8801816439341',
        'mobile_number'      => '8801816439341',

        'email_1'            => 'angurabegum987@gmail.com',
        'email_address'      => 'angurabegum987@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137ANISHURPB',
        'account_number'     => '2118217028301',
        'SIF_Number'         => '2248241',

        'account_title'      => 'MD ANISHUR RAHMAN',
        'customer_name'      => 'MD ANISHUR RAHMAN',

        'contact_no_1'       => '8801515265698',
        'mobile_number'      => '8801515265698',

        'email_1'            => 'accruon@gmail.com',
        'email_address'      => 'accruon@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ANISPRM',
        'account_number'     => '2146213005739',
        'SIF_Number'         => '161541',

        'account_title'      => 'MD. ANISUR RAHMAN',
        'customer_name'      => 'MD. ANISUR RAHMAN',

        'contact_no_1'       => '8801711526930',
        'mobile_number'      => '8801711526930',

        'email_1'            => 'rahmananisur1954@gmail.com',
        'email_address'      => 'rahmananisur1954@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137ANISRAHPB',
        'account_number'     => '2140213008937',
        'SIF_Number'         => '100503',

        'account_title'      => 'S.M. ANISUR RAHMAN',
        'customer_name'      => 'S.M. ANISUR RAHMAN',

        'contact_no_1'       => '8801973041347',
        'mobile_number'      => '8801973041347',

        'email_1'            => 'anis.rcc@gmail.com',
        'email_address'      => 'anis.rcc@gmail.com',

        'branch_name'        => 'TONGI BRANCH',
        'acc_opening_branch' => 'TONGI BRANCH',
    ],
    [
        'bp_id'              => 'IN137ANISURPRM',
        'account_number'     => '2104217005412',
        'SIF_Number'         => '4471',

        'account_title'      => 'MOHAMMAD ANISUR RAHMAN',
        'customer_name'      => 'MOHAMMAD ANISUR RAHMAN',

        'contact_no_1'       => '8801716094538',
        'mobile_number'      => '8801716094538',

        'email_1'            => 'mdanis1975@gmail.com',
        'email_address'      => 'mdanis1975@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ANTARAAM',
        'account_number'     => '2146218007565',
        'SIF_Number'         => '1353148',

        'account_title'      => 'ANTARA AMIN MIRZA',
        'customer_name'      => 'ANTARA AMIN MIRZA',

        'contact_no_1'       => '8801711529150',
        'mobile_number'      => '8801711529150',

        'email_1'            => 'erfan@venturegrp.net',
        'email_address'      => 'erfan@venturegrp.net',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137ANTARAFK',
        'account_number'     => '2104216032508',
        'SIF_Number'         => '2076378',

        'account_title'      => 'ANTARA FAIROOZ KHAN',
        'customer_name'      => 'ANTARA FAIROOZ KHAN',

        'contact_no_1'       => '8801797565759',
        'mobile_number'      => '8801797565759',

        'email_1'            => 'antarafairoozkhan@gmail.com',
        'email_address'      => 'antarafairoozkhan@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ANUJAPB',
        'account_number'     => '2126218008823',
        'SIF_Number'         => '1809255',

        'account_title'      => 'ANUJA NIZAM',
        'customer_name'      => 'ANUJA NIZAM',

        'contact_no_1'       => '8801920101553',
        'mobile_number'      => '8801920101553',

        'email_1'            => 'anujanizam@primebank.com.bd',
        'email_address'      => 'anujanizam@primebank.com.bd',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137ANUPAMPB',
        'account_number'     => '2512215000957',
        'SIF_Number'         => '1672882',

        'account_title'      => 'MD.ANUPAM HAYET CHOWDHURY',
        'customer_name'      => 'MD.ANUPAM HAYET CHOWDHURY',

        'contact_no_1'       => '8801713798471',
        'mobile_number'      => '8801713798471',

        'email_1'            => 'moonthakurgaon@gmail.com',
        'email_address'      => 'moonthakurgaon@gmail.com',

        'branch_name'        => 'THAKURGAON SME BR/SC',
        'acc_opening_branch' => 'THAKURGAON SME BR/SC',
    ],
    [
        'bp_id'              => 'IN137ANUPKUMPB',
        'account_number'     => '2118211034909',
        'SIF_Number'         => '2442259',

        'account_title'      => 'ANUPAM KUMAR NATH',
        'customer_name'      => 'ANUPAM KUMAR NATH',

        'contact_no_1'       => '8801839690633',
        'mobile_number'      => '8801839690633',

        'email_1'            => 'anupamkumarice@gmail.com',
        'email_address'      => 'anupamkumarice@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ANURDHPRM',
        'account_number'     => '2148211008899',
        'SIF_Number'         => '2136663',

        'account_title'      => 'ANURADHA DUTT',
        'customer_name'      => 'ANURADHA DUTT',

        'contact_no_1'       => '8801741255211',
        'mobile_number'      => '8801741255211',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'KAKRAIL BRANCH',
        'acc_opening_branch' => 'KAKRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ANWERKAPB',
        'account_number'     => '2141211000232',
        'SIF_Number'         => '283799',

        'account_title'      => 'MD ANWER KAMAL BHUIYAN',
        'customer_name'      => 'MD ANWER KAMAL BHUIYAN',

        'contact_no_1'       => '8801712020080',
        'mobile_number'      => '8801712020080',

        'email_1'            => 'anwer.kamalnn@primebank.com.bd',
        'email_address'      => 'anwer.kamalnn@primebank.com.bd',

        'branch_name'        => 'PANTHAPATH BRANCH',
        'acc_opening_branch' => 'PANTHAPATH BRANCH',
    ],
    [
        'bp_id'              => 'IN137AOWLADPB',
        'account_number'     => '2186211003745',
        'SIF_Number'         => '1262313',

        'account_title'      => 'MD. AOWLAD HOSSAIN',
        'customer_name'      => 'MD. AOWLAD HOSSAIN',

        'contact_no_1'       => '8801849393388',
        'mobile_number'      => '8801849393388',

        'email_1'            => 'aowlad346@gmail.com',
        'email_address'      => 'aowlad346@gmail.com',

        'branch_name'        => 'JATRABARI BRANCH',
        'acc_opening_branch' => 'JATRABARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137APARAJITA',
        'account_number'     => '2118211033357',
        'SIF_Number'         => '2442779',

        'account_title'      => 'APARAJITA SAHA',
        'customer_name'      => 'APARAJITA SAHA',

        'contact_no_1'       => '8801719907626',
        'mobile_number'      => '8801719907626',

        'email_1'            => 'apora166@gmail.com',
        'email_address'      => 'apora166@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ARAFATMAH',
        'account_number'     => '2126213011814',
        'SIF_Number'         => '2267863',

        'account_title'      => 'ARAFAT MAHMUD',
        'customer_name'      => 'ARAFAT MAHMUD',

        'contact_no_1'       => '8801717572849',
        'mobile_number'      => '8801717572849',

        'email_1'            => 'amahmud09@gmail.com',
        'email_address'      => 'amahmud09@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137ARAFATUPB',
        'account_number'     => '2129212013321',
        'SIF_Number'         => '2187799',

        'account_title'      => 'MOSAMMAT ARAFATUN NAHAR',
        'customer_name'      => 'MOSAMMAT ARAFATUN NAHAR',

        'contact_no_1'       => '8801711316289',
        'mobile_number'      => '8801711316289',

        'email_1'            => 'faruqmustahid@gmail.com',
        'email_address'      => 'faruqmustahid@gmail.com',

        'branch_name'        => 'BOGURA BRANCH',
        'acc_opening_branch' => 'BOGURA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ARAHIMPRM',
        'account_number'     => '2104295031503',
        'SIF_Number'         => '1023083',

        'account_title'      => 'MD. ATAUR RAHIM',
        'customer_name'      => 'MD. ATAUR RAHIM',

        'contact_no_1'       => '8801777407079',
        'mobile_number'      => '8801777407079',

        'email_1'            => 'rajibaub15@gmail.com',
        'email_address'      => 'rajibaub15@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ARBSKRPRM',
        'account_number'     => '2104216039198',
        'SIF_Number'         => '2503548',

        'account_title'      => 'AROBINDU SARKER',
        'customer_name'      => 'AROBINDU SARKER',

        'contact_no_1'       => '8801738143180',
        'mobile_number'      => '8801738143180',

        'email_1'            => 'arobindu92@gmail.com',
        'email_address'      => 'arobindu92@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ARCHANPB',
        'account_number'     => '2104219044229',
        'SIF_Number'         => '151828',

        'account_title'      => 'ARCHANA RANI ROY',
        'customer_name'      => 'ARCHANA RANI ROY',

        'contact_no_1'       => '8801709900578',
        'mobile_number'      => '8801709900578',

        'email_1'            => 'ramkrishnaroy@gmail.com',
        'email_address'      => 'ramkrishnaroy@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137AREEBAPRM',
        'account_number'     => '2184211023845',
        'SIF_Number'         => '2575949',

        'account_title'      => 'MS AREEBA IBNAD',
        'customer_name'      => 'MS AREEBA IBNAD',

        'contact_no_1'       => '8801766667730',
        'mobile_number'      => '8801766667730',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'OXYGEN MORE BRANCH',
        'acc_opening_branch' => 'OXYGEN MORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137ARHAMPRIM',
        'account_number'     => '2184211023844',
        'SIF_Number'         => '2575947',

        'account_title'      => 'MR ARHAM FAIYAZ',
        'customer_name'      => 'MR ARHAM FAIYAZ',

        'contact_no_1'       => '8801755656465',
        'mobile_number'      => '8801755656465',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'OXYGEN MORE BRANCH',
        'acc_opening_branch' => 'OXYGEN MORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137ARIFHOSPB',
        'account_number'     => '2159212000750',
        'SIF_Number'         => '1334785',

        'account_title'      => 'MD. ARIF HOSSAIN',
        'customer_name'      => 'MD. ARIF HOSSAIN',

        'contact_no_1'       => '8801672675801',
        'mobile_number'      => '8801672675801',

        'email_1'            => 'arif.h@primebank.com.bd',
        'email_address'      => 'arif.h@primebank.com.bd',

        'branch_name'        => 'BIJOY NAGAR BRANCH',
        'acc_opening_branch' => 'BIJOY NAGAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137ARIFPB',
        'account_number'     => '2104212031751',
        'SIF_Number'         => '2029038',

        'account_title'      => 'MD. ARIF HOSEN',
        'customer_name'      => 'MD. ARIF HOSEN',

        'contact_no_1'       => '8801620912833',
        'mobile_number'      => '8801620912833',

        'email_1'            => 'hosen.arif@gmail.com',
        'email_address'      => 'hosen.arif@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ARIFPRIME',
        'account_number'     => '2174211000935',
        'SIF_Number'         => '1722035',

        'account_title'      => 'MUHAMMAD ARIFUL ISLAM',
        'customer_name'      => 'MUHAMMAD ARIFUL ISLAM',

        'contact_no_1'       => '8801680020373',
        'mobile_number'      => '8801680020373',

        'email_1'            => 'arif151193@gmail.com',
        'email_address'      => 'arif151193@gmail.com',

        'branch_name'        => 'MYMENSINGH BRANCH',
        'acc_opening_branch' => 'MYMENSINGH BRANCH',
    ],
    [
        'bp_id'              => 'IN137ARIFUDDIN',
        'account_number'     => '2170219009171',
        'SIF_Number'         => '177161',

        'account_title'      => 'ARIFUDDIN AHMED',
        'customer_name'      => 'ARIFUDDIN AHMED',

        'contact_no_1'       => '8801714260346',
        'mobile_number'      => '8801714260346',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'GARIB-E-NEWAZ AVE. BR',
        'acc_opening_branch' => 'GARIB-E-NEWAZ AVE. BR',
    ],
    [
        'bp_id'              => 'IN137ARIFULISL',
        'account_number'     => '2104215005323',
        'SIF_Number'         => '1902',

        'account_title'      => 'MUHAMMAD ARIFUL ISLAM',
        'customer_name'      => 'MUHAMMAD ARIFUL ISLAM',

        'contact_no_1'       => '8801730320015',
        'mobile_number'      => '8801730320015',

        'email_1'            => 'islam@primebank.com.bd',
        'email_address'      => 'islam@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ARIFURPRM',
        'account_number'     => '2128219020239',
        'SIF_Number'         => '2413798',

        'account_title'      => 'MD ARIFUR RAHMAN',
        'customer_name'      => 'MD ARIFUR RAHMAN',

        'contact_no_1'       => '8801793596594',
        'mobile_number'      => '8801793596594',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137ARTUSHEPB',
        'account_number'     => '3108213047628',
        'SIF_Number'         => '2725801',

        'account_title'      => 'ASHIQUR RAHMAN TUSHER',
        'customer_name'      => 'ASHIQUR RAHMAN TUSHER',

        'contact_no_1'       => '8801798026247',
        'mobile_number'      => '8801798026247',

        'email_1'            => 'ashikurt77@gmail.com',
        'email_address'      => 'ashikurt77@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASADPRIME',
        'account_number'     => '2104211005319',
        'SIF_Number'         => '4664',

        'account_title'      => 'MD. ASADUZZAMAN',
        'customer_name'      => 'MD. ASADUZZAMAN',

        'contact_no_1'       => '8801713069624',
        'mobile_number'      => '8801713069624',

        'email_1'            => 'ma111025@primebank.com.bd',
        'email_address'      => 'ma111025@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASADUPRM',
        'account_number'     => '2118217016934',
        'SIF_Number'         => '76774',

        'account_title'      => 'ASADUZZAMAN',
        'customer_name'      => 'ASADUZZAMAN',

        'contact_no_1'       => '8801714090715',
        'mobile_number'      => '8801714090715',

        'email_1'            => 'asadzr@gmail.com',
        'email_address'      => 'asadzr@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASHFAQNPB',
        'account_number'     => '2118212022713',
        'SIF_Number'         => '2028798',

        'account_title'      => 'MD ASHFAQ NOOR ISLAM KHAN',
        'customer_name'      => 'MD ASHFAQ NOOR ISLAM KHAN',

        'contact_no_1'       => '8801712191101',
        'mobile_number'      => '8801712191101',

        'email_1'            => 'ashfaqnoor77@gmail.com',
        'email_address'      => 'ashfaqnoor77@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASHFAQUPB',
        'account_number'     => '2104214018285',
        'SIF_Number'         => '47786',

        'account_title'      => 'SK ASHFAQUE AL MAMUN',
        'customer_name'      => 'SK ASHFAQUE AL MAMUN',

        'contact_no_1'       => '8801730320018',
        'mobile_number'      => '8801730320018',

        'email_1'            => 'sm120301@primebank.com.bd',
        'email_address'      => 'sm120301@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASHIFPB',
        'account_number'     => '2165216016309',
        'SIF_Number'         => '2421291',

        'account_title'      => 'ASHIF SHAHNAWAZ',
        'customer_name'      => 'ASHIF SHAHNAWAZ',

        'contact_no_1'       => '8801718295507',
        'mobile_number'      => '8801718295507',

        'email_1'            => 'ashifshahnawaz786@gmail.com',
        'email_address'      => 'ashifshahnawaz786@gmail.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASHMITPRM',
        'account_number'     => '2117212018654',
        'SIF_Number'         => '2442553',

        'account_title'      => 'ASHMITA MAZUMDER',
        'customer_name'      => 'ASHMITA MAZUMDER',

        'contact_no_1'       => '8801673099552',
        'mobile_number'      => '8801673099552',

        'email_1'            => 'mazumderashmita118@gmail.com',
        'email_address'      => 'mazumderashmita118@gmail.com',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASHRAFPB',
        'account_number'     => '2192217011363',
        'SIF_Number'         => '2515619',

        'account_title'      => 'ASHRAF UL ISLAM CHOWDHURY',
        'customer_name'      => 'ASHRAF UL ISLAM CHOWDHURY',

        'contact_no_1'       => '8801764585903',
        'mobile_number'      => '8801764585903',

        'email_1'            => 'achowdhury@schertech.com',
        'email_address'      => 'achowdhury@schertech.com',

        'branch_name'        => 'BANASREE BRANCH',
        'acc_opening_branch' => 'BANASREE BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASHRAFPRM',
        'account_number'     => '2219215012305',
        'SIF_Number'         => '2599467',

        'account_title'      => 'ASHRAFUL ALAM',
        'customer_name'      => 'ASHRAFUL ALAM',

        'contact_no_1'       => '8801711503822',
        'mobile_number'      => '8801711503822',

        'email_1'            => 'ashraful_alam@hotmail.com',
        'email_address'      => 'ashraful_alam@hotmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASHRAFZMN',
        'account_number'     => '2219212008983',
        'SIF_Number'         => '2402091',

        'account_title'      => 'SK. ASHRAFUZZAMAN',
        'customer_name'      => 'SK. ASHRAFUZZAMAN',

        'contact_no_1'       => '8801712996542',
        'mobile_number'      => '8801712996542',

        'email_1'            => 'ashrafhasan@gmail.com',
        'email_address'      => 'ashrafhasan@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASHUTOSPB',
        'account_number'     => '2104214015877',
        'SIF_Number'         => '1458992',

        'account_title'      => 'ASHUTOSH BISWAS',
        'customer_name'      => 'ASHUTOSH BISWAS',

        'contact_no_1'       => '8801721666288',
        'mobile_number'      => '8801721666288',

        'email_1'            => 'ashu0635@gmail.com',
        'email_address'      => 'ashu0635@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASIFREZPB',
        'account_number'     => '2170217008183',
        'SIF_Number'         => '731649',

        'account_title'      => 'ASIF REZA ANIK',
        'customer_name'      => 'ASIF REZA ANIK',

        'contact_no_1'       => '8801764474416',
        'mobile_number'      => '8801764474416',

        'email_1'            => 'anikbd1979@gmail.com',
        'email_address'      => 'anikbd1979@gmail.com',

        'branch_name'        => 'GARIB-E-NEWAZ AVE. BR',
        'acc_opening_branch' => 'GARIB-E-NEWAZ AVE. BR',
    ],
    [
        'bp_id'              => 'IN137ASIFWAPB',
        'account_number'     => '2146212015754',
        'SIF_Number'         => '2227636',

        'account_title'      => 'ASIF MOHAMMAD WASIH',
        'customer_name'      => 'ASIF MOHAMMAD WASIH',

        'contact_no_1'       => '8801713481197',
        'mobile_number'      => '8801713481197',

        'email_1'            => 'asif_wasih@yahoo.com',
        'email_address'      => 'asif_wasih@yahoo.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASMAHNUPB',
        'account_number'     => '2127219028423',
        'SIF_Number'         => '2740489',

        'account_title'      => 'A S MD ABDUL HASEEB &amp; NURUN NAHAR',
        'customer_name'      => 'A S MD ABDUL HASEEB &amp; NURUN NAHAR',

        'contact_no_1'       => '8801745307218',
        'mobile_number'      => '8801745307218',

        'email_1'            => 'haseeb@nce.buet.ac.bd',
        'email_address'      => 'haseeb@nce.buet.ac.bd',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASMHABIBR',
        'account_number'     => '2118217031016',
        'SIF_Number'         => '2366785',

        'account_title'      => 'A.S.M. HABIBUR RAHMAN',
        'customer_name'      => 'A.S.M. HABIBUR RAHMAN',

        'contact_no_1'       => '8801334747870',
        'mobile_number'      => '8801334747870',

        'email_1'            => 'asmhabibur@gmail.com',
        'email_address'      => 'asmhabibur@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASRAFAL',
        'account_number'     => '3108216031876',
        'SIF_Number'         => '2464117',

        'account_title'      => 'MD. ASRAF ALI',
        'customer_name'      => 'MD. ASRAF ALI',

        'contact_no_1'       => '8801759131555',
        'mobile_number'      => '8801759131555',

        'email_1'            => 'getasraf@gmail.com',
        'email_address'      => 'getasraf@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASRAFPRIM',
        'account_number'     => '2104211021028',
        'SIF_Number'         => '987412',

        'account_title'      => 'MD ASRAFUL ISLAM',
        'customer_name'      => 'MD ASRAFUL ISLAM',

        'contact_no_1'       => '8801719344561',
        'mobile_number'      => '8801719344561',

        'email_1'            => 'aloasraf@yahoo.com',
        'email_address'      => 'aloasraf@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ASRFZMNPB',
        'account_number'     => '2126217013501',
        'SIF_Number'         => '2543962',

        'account_title'      => 'ASHRAF-UZ-ZAMAN',
        'customer_name'      => 'ASHRAF-UZ-ZAMAN',

        'contact_no_1'       => '8801912194430',
        'mobile_number'      => '8801912194430',

        'email_1'            => 'liton.du175@gmail.com',
        'email_address'      => 'liton.du175@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137ATIQULPB',
        'account_number'     => '2112211029534',
        'SIF_Number'         => '2638325',

        'account_title'      => 'MD. ATIQUL HOSSAIN',
        'customer_name'      => 'MD. ATIQUL HOSSAIN',

        'contact_no_1'       => '8801711501336',
        'mobile_number'      => '8801711501336',

        'email_1'            => 'm_atiqul@grameenphone.com',
        'email_address'      => 'm_atiqul@grameenphone.com',

        'branch_name'        => 'RAJSHAHI BRANCH',
        'acc_opening_branch' => 'RAJSHAHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137ATMZAHIR',
        'account_number'     => '2132218006076',
        'SIF_Number'         => '63475',

        'account_title'      => 'A. T. M. ZAHIRUL HAMID',
        'customer_name'      => 'A. T. M. ZAHIRUL HAMID',

        'contact_no_1'       => '8801713035631',
        'mobile_number'      => '8801713035631',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137AVINITAPB',
        'account_number'     => '2165215017951',
        'SIF_Number'         => '2206593',

        'account_title'      => 'AVINITA CHOWDHURY',
        'customer_name'      => 'AVINITA CHOWDHURY',

        'contact_no_1'       => '8801737800456',
        'mobile_number'      => '8801737800456',

        'email_1'            => 'avinita_annya@yahoo.com',
        'email_address'      => 'avinita_annya@yahoo.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137AYATULLAH',
        'account_number'     => '2118211032409',
        'SIF_Number'         => '2408708',

        'account_title'      => 'MD AYATULLAH',
        'customer_name'      => 'MD AYATULLAH',

        'contact_no_1'       => '8801711069092',
        'mobile_number'      => '8801711069092',

        'email_1'            => 'khomeni404@gmail.com',
        'email_address'      => 'khomeni404@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137AYESHACHO',
        'account_number'     => '0021060014907',
        'SIF_Number'         => '377469',

        'account_title'      => 'AYESHA CHOWDHURY',
        'customer_name'      => 'AYESHA CHOWDHURY',

        'contact_no_1'       => '8801312934732',
        'mobile_number'      => '8801312934732',

        'email_1'            => 'mayarahman23@gmail.com',
        'email_address'      => 'mayarahman23@gmail.com',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137AYESHAPB',
        'account_number'     => '2104219006285',
        'SIF_Number'         => '29423',

        'account_title'      => 'AYESHA SIDDIQA',
        'customer_name'      => 'AYESHA SIDDIQA',

        'contact_no_1'       => '8801716863889',
        'mobile_number'      => '8801716863889',

        'email_1'            => 'as082950@primebank.com.bd',
        'email_address'      => 'as082950@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137AYESHAPRI',
        'account_number'     => '2113219013918',
        'SIF_Number'         => '2142848',

        'account_title'      => 'AYESHA PARVEEN',
        'customer_name'      => 'AYESHA PARVEEN',

        'contact_no_1'       => '8801718651607',
        'mobile_number'      => '8801718651607',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137AYESHASPB',
        'account_number'     => '2127215025357',
        'SIF_Number'         => '1957065',

        'account_title'      => 'AYESHA SIDDIQUA',
        'customer_name'      => 'AYESHA SIDDIQUA',

        'contact_no_1'       => '8801915163737',
        'mobile_number'      => '8801915163737',

        'email_1'            => 'munee1971@yahoo.com',
        'email_address'      => 'munee1971@yahoo.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137AYESHSSPB',
        'account_number'     => '2128211008282',
        'SIF_Number'         => '1307484',

        'account_title'      => 'AYESHA SIDDIQUA SHELLEY',
        'customer_name'      => 'AYESHA SIDDIQUA SHELLEY',

        'contact_no_1'       => '8801743038818',
        'mobile_number'      => '8801743038818',

        'email_1'            => 'ayeshasiddiqua10bd@gmail.com',
        'email_address'      => 'ayeshasiddiqua10bd@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137AYNOONPB',
        'account_number'     => '2138216008920',
        'SIF_Number'         => '441918',

        'account_title'      => 'AYNOON NAHAR',
        'customer_name'      => 'AYNOON NAHAR',

        'contact_no_1'       => '8801552312965',
        'mobile_number'      => '8801552312965',

        'email_1'            => 'aynoonnahar@gmail.com',
        'email_address'      => 'aynoonnahar@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137AZADIBEG',
        'account_number'     => '0021010000589',
        'SIF_Number'         => '42821',

        'account_title'      => 'AZADI BEGUM',
        'customer_name'      => 'AZADI BEGUM',

        'contact_no_1'       => '8801730009331',
        'mobile_number'      => '8801730009331',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'JESSORE BRANCH',
        'acc_opening_branch' => 'JESSORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137AZGORTAPB',
        'account_number'     => '2162212020159',
        'SIF_Number'         => '2761565',

        'account_title'      => 'MOHAMMAD ALI AZGOR TALUKDER',
        'customer_name'      => 'MOHAMMAD ALI AZGOR TALUKDER',

        'contact_no_1'       => '8801712741943',
        'mobile_number'      => '8801712741943',

        'email_1'            => 'azgortalukder@gmail.com',
        'email_address'      => 'azgortalukder@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137AZHARULPB',
        'account_number'     => '3108211061093',
        'SIF_Number'         => '1821644',

        'account_title'      => 'AZHARUL ISLAM',
        'customer_name'      => 'AZHARUL ISLAM',

        'contact_no_1'       => '8801711082518',
        'mobile_number'      => '8801711082518',

        'email_1'            => 'azhar.gpdigital@gmail.com',
        'email_address'      => 'azhar.gpdigital@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137AZIMHOSSA',
        'account_number'     => '2104211009568',
        'SIF_Number'         => '1626205',

        'account_title'      => 'MD AZIM HOSSAIN',
        'customer_name'      => 'MD AZIM HOSSAIN',

        'contact_no_1'       => '8801842534302',
        'mobile_number'      => '8801842534302',

        'email_1'            => 'mailazimh@gmail.com',
        'email_address'      => 'mailazimh@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137AZMOLAZPB',
        'account_number'     => '2118218029340',
        'SIF_Number'         => '2293840',

        'account_title'      => 'AZMOL HOSSAIN AZAD',
        'customer_name'      => 'AZMOL HOSSAIN AZAD',

        'contact_no_1'       => '8801717250361',
        'mobile_number'      => '8801717250361',

        'email_1'            => 'md.tuhin2007@gmail.com',
        'email_address'      => 'md.tuhin2007@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137BABULCDPB',
        'account_number'     => '2104214042616',
        'SIF_Number'         => '5175',

        'account_title'      => 'BABUL CHANDRA DEBNATH',
        'customer_name'      => 'BABUL CHANDRA DEBNATH',

        'contact_no_1'       => '8801711349457',
        'mobile_number'      => '8801711349457',

        'email_1'            => 'debnathbabul67@gmail.com',
        'email_address'      => 'debnathbabul67@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137BADALCHPB',
        'account_number'     => '2104212042279',
        'SIF_Number'         => '2728276',

        'account_title'      => 'BADAL CHANDRA SHARMA',
        'customer_name'      => 'BADAL CHANDRA SHARMA',

        'contact_no_1'       => '8801601167624',
        'mobile_number'      => '8801601167624',

        'email_1'            => 'piclbadal@gmail.com',
        'email_address'      => 'piclbadal@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137BADREJAM',
        'account_number'     => '2104213014772',
        'SIF_Number'         => '949400',

        'account_title'      => 'BADRE JAMIL MUNAWAR',
        'customer_name'      => 'BADRE JAMIL MUNAWAR',

        'contact_no_1'       => '8801769030307',
        'mobile_number'      => '8801769030307',

        'email_1'            => 'bm111502@primebank.com.bd',
        'email_address'      => 'bm111502@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137BASAKBANG',
        'account_number'     => '2104216003913',
        'SIF_Number'         => '691209',

        'account_title'      => 'BANGAJIT BASAK',
        'customer_name'      => 'BANGAJIT BASAK',

        'contact_no_1'       => '8801911361049',
        'mobile_number'      => '8801911361049',

        'email_1'            => 'bb072007@primebank.com.bd',
        'email_address'      => 'bb072007@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137BEAUTYPB',
        'account_number'     => '3108217058191',
        'SIF_Number'         => '2574568',

        'account_title'      => 'AFROZA ABEDIN BEAUTY',
        'customer_name'      => 'AFROZA ABEDIN BEAUTY',

        'contact_no_1'       => '8801714171132',
        'mobile_number'      => '8801714171132',

        'email_1'            => 'afro.abedin@gmail.com',
        'email_address'      => 'afro.abedin@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137BEDAURAPB',
        'account_number'     => '2146216022780',
        'SIF_Number'         => '2708770',

        'account_title'      => 'BEDAURA BIPASHA HUQ',
        'customer_name'      => 'BEDAURA BIPASHA HUQ',

        'contact_no_1'       => '8801711506653',
        'mobile_number'      => '8801711506653',

        'email_1'            => 'bedaura@yahoo.com',
        'email_address'      => 'bedaura@yahoo.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137BELALHO',
        'account_number'     => '2165213009661',
        'SIF_Number'         => '25519',

        'account_title'      => 'BELAL HOSSAIN',
        'customer_name'      => 'BELAL HOSSAIN',

        'contact_no_1'       => '8801713366652',
        'mobile_number'      => '8801713366652',

        'email_1'            => 'belal.hossain@bg.com.bd',
        'email_address'      => 'belal.hossain@bg.com.bd',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137BHASKARPB',
        'account_number'     => '2118213033339',
        'SIF_Number'         => '2442297',

        'account_title'      => 'BHASKAR SARKAR',
        'customer_name'      => 'BHASKAR SARKAR',

        'contact_no_1'       => '8801670840376',
        'mobile_number'      => '8801670840376',

        'email_1'            => 'sarkar3698@gmail.com',
        'email_address'      => 'sarkar3698@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137BIBIAMEPB',
        'account_number'     => '2171213021122',
        'SIF_Number'         => '2904745',

        'account_title'      => 'BIBI AMENA',
        'customer_name'      => 'BIBI AMENA',

        'contact_no_1'       => '8801907708182',
        'mobile_number'      => '8801907708182',

        'email_1'            => 'fahdmhmmd@gmail.com',
        'email_address'      => 'fahdmhmmd@gmail.com',

        'branch_name'        => 'PROBARTAK MORE BRANCH',
        'acc_opening_branch' => 'PROBARTAK MORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137BIBIMRMPB',
        'account_number'     => '2186212024534',
        'SIF_Number'         => '2042480',

        'account_title'      => 'BIBI MARIAM',
        'customer_name'      => 'BIBI MARIAM',

        'contact_no_1'       => '8801313697841',
        'mobile_number'      => '8801313697841',

        'email_1'            => 'bibimariam645@gmail.com',
        'email_address'      => 'bibimariam645@gmail.com',

        'branch_name'        => 'JATRABARI BRANCH',
        'acc_opening_branch' => 'JATRABARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137BIDDUTBA',
        'account_number'     => '2140211021668',
        'SIF_Number'         => '2178614',

        'account_title'      => 'BIDDUT BARAN BARUA',
        'customer_name'      => 'BIDDUT BARAN BARUA',

        'contact_no_1'       => '8801815883780',
        'mobile_number'      => '8801815883780',

        'email_1'            => 'biddut.baran@gmail.com',
        'email_address'      => 'biddut.baran@gmail.com',

        'branch_name'        => 'TONGI BRANCH',
        'acc_opening_branch' => 'TONGI BRANCH',
    ],
    [
        'bp_id'              => 'IN137BIDHANKPB',
        'account_number'     => '2140212008540',
        'SIF_Number'         => '1282040',

        'account_title'      => 'BIDHAN KUMAR CHOWDHURY',
        'customer_name'      => 'BIDHAN KUMAR CHOWDHURY',

        'contact_no_1'       => '8801819747955',
        'mobile_number'      => '8801819747955',

        'email_1'            => 'bidhanctg55@gmail.com',
        'email_address'      => 'bidhanctg55@gmail.com',

        'branch_name'        => 'TONGI BRANCH',
        'acc_opening_branch' => 'TONGI BRANCH',
    ],
    [
        'bp_id'              => 'IN137BIDHANPB',
        'account_number'     => '2104212025720',
        'SIF_Number'         => '1444055',

        'account_title'      => 'BIDHAN NATH',
        'customer_name'      => 'BIDHAN NATH',

        'contact_no_1'       => '8801632161718',
        'mobile_number'      => '8801632161718',

        'email_1'            => 'bidhan.du@gmail.com',
        'email_address'      => 'bidhan.du@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137BIDKUDPBK',
        'account_number'     => '2104216035519',
        'SIF_Number'         => '5516',

        'account_title'      => 'MR BIDYUT KUMAR DAS',
        'customer_name'      => 'MR BIDYUT KUMAR DAS',

        'contact_no_1'       => '8801711945969',
        'mobile_number'      => '8801711945969',

        'email_1'            => 'kdbidyut@yahoo.com',
        'email_address'      => 'kdbidyut@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137BIKCPAULP',
        'account_number'     => '2118211006882',
        'SIF_Number'         => '82734',

        'account_title'      => 'BIKASH CHNDRA PAUL',
        'customer_name'      => 'BIKASH CHNDRA PAUL',

        'contact_no_1'       => '8801979534164',
        'mobile_number'      => '8801979534164',

        'email_1'            => 'bchandrapaul2015@gmail.com',
        'email_address'      => 'bchandrapaul2015@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137BILKISBPB',
        'account_number'     => '2128211018356',
        'SIF_Number'         => '2177420',

        'account_title'      => 'BILKIS BANU',
        'customer_name'      => 'BILKIS BANU',

        'contact_no_1'       => '8801738194131',
        'mobile_number'      => '8801738194131',

        'email_1'            => 'mushfiq41@yahoo.com',
        'email_address'      => 'mushfiq41@yahoo.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137BILKISNPB',
        'account_number'     => '2125219002206',
        'SIF_Number'         => '1188510',

        'account_title'      => 'SULTANA BILKIS NAHER',
        'customer_name'      => 'SULTANA BILKIS NAHER',

        'contact_no_1'       => '8801873101110',
        'mobile_number'      => '8801873101110',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137BILLAHPB',
        'account_number'     => '2113215019174',
        'SIF_Number'         => '2878048',

        'account_title'      => 'SALAH UDDIN MD BAKI BILLAH',
        'customer_name'      => 'SALAH UDDIN MD BAKI BILLAH',

        'contact_no_1'       => '8801711815527',
        'mobile_number'      => '8801711815527',

        'email_1'            => 'smbaki65@yahoo.com',
        'email_address'      => 'smbaki65@yahoo.com',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137BIMAKBPRM',
        'account_number'     => '2146211005061',
        'SIF_Number'         => '161381',

        'account_title'      => 'BIMAN KUMAR BHADDRA',
        'customer_name'      => 'BIMAN KUMAR BHADDRA',

        'contact_no_1'       => '8801913805578',
        'mobile_number'      => '8801913805578',

        'email_1'            => 'bimankbhaddra70@gmail.com',
        'email_address'      => 'bimankbhaddra70@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137BINDUPB',
        'account_number'     => '2221218003257',
        'SIF_Number'         => '2162641',

        'account_title'      => 'BINDU PODDER',
        'customer_name'      => 'BINDU PODDER',

        'contact_no_1'       => '8801911236084',
        'mobile_number'      => '8801911236084',

        'email_1'            => 'chanddro@gmail.com',
        'email_address'      => 'chanddro@gmail.com',

        'branch_name'        => 'PATUATULY BRANCH',
        'acc_opening_branch' => 'PATUATULY BRANCH',
    ],
    [
        'bp_id'              => 'IN137BISHWAJIT',
        'account_number'     => '2118212031029',
        'SIF_Number'         => '2367380',

        'account_title'      => 'BISHWAJIT SAHA',
        'customer_name'      => 'BISHWAJIT SAHA',

        'contact_no_1'       => '8801713185570',
        'mobile_number'      => '8801713185570',

        'email_1'            => 'bishwajitsaha@live.com',
        'email_address'      => 'bishwajitsaha@live.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137BITHIDPRM',
        'account_number'     => '2125212013127',
        'SIF_Number'         => '1249715',

        'account_title'      => 'BITHI DAS',
        'customer_name'      => 'BITHI DAS',

        'contact_no_1'       => '8801721500525',
        'mobile_number'      => '8801721500525',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137BIZEDKPRM',
        'account_number'     => '2152212030259',
        'SIF_Number'         => '2521179',

        'account_title'      => 'MD. BIZED KHAN',
        'customer_name'      => 'MD. BIZED KHAN',

        'contact_no_1'       => '8801759711306',
        'mobile_number'      => '8801759711306',

        'email_1'            => 'hellobized@gmail.com',
        'email_address'      => 'hellobized@gmail.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137BODRULPRI',
        'account_number'     => '2112214006488',
        'SIF_Number'         => '577602',

        'account_title'      => 'MR MD. BODRUL MILLAT',
        'customer_name'      => 'MR MD. BODRUL MILLAT',

        'contact_no_1'       => '8801714781859',
        'mobile_number'      => '8801714781859',

        'email_1'            => 'bmillat75@gmail.com',
        'email_address'      => 'bmillat75@gmail.com',

        'branch_name'        => 'RAJSHAHI BRANCH',
        'acc_opening_branch' => 'RAJSHAHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137BUDDHKPRM',
        'account_number'     => '2118214021374',
        'SIF_Number'         => '1981751',

        'account_title'      => 'BUDDHADEB KARMAKAR',
        'customer_name'      => 'BUDDHADEB KARMAKAR',

        'contact_no_1'       => '8801712626531',
        'mobile_number'      => '8801712626531',

        'email_1'            => 'buddha@knitasia.com',
        'email_address'      => 'buddha@knitasia.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137BUSBIBHUT',
        'account_number'     => '2118218002339',
        'SIF_Number'         => '78567',

        'account_title'      => 'Bibhuti Bushan Bala',
        'customer_name'      => 'Bibhuti Bushan Bala',

        'contact_no_1'       => '8801714090601',
        'mobile_number'      => '8801714090601',

        'email_1'            => 'bibhuti@transcombd.com',
        'email_address'      => 'bibhuti@transcombd.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137CAMELLIAN',
        'account_number'     => '2132211016081',
        'SIF_Number'         => '912768',

        'account_title'      => 'CAMELLIA NASREEN',
        'customer_name'      => 'CAMELLIA NASREEN',

        'contact_no_1'       => '8801550553099',
        'mobile_number'      => '8801550553099',

        'email_1'            => 'nitu2k6@gmail.com',
        'email_address'      => 'nitu2k6@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137CHAMELIPR',
        'account_number'     => '2141211006973',
        'SIF_Number'         => '1781343',

        'account_title'      => 'CHAMELI AKTER',
        'customer_name'      => 'CHAMELI AKTER',

        'contact_no_1'       => '8801316100383',
        'mobile_number'      => '8801316100383',

        'email_1'            => 'chameliakter26@gmail.com',
        'email_address'      => 'chameliakter26@gmail.com',

        'branch_name'        => 'PANTHAPATH BRANCH',
        'acc_opening_branch' => 'PANTHAPATH BRANCH',
    ],
    [
        'bp_id'              => 'IN137CHANCHALK',
        'account_number'     => '2222213007690',
        'SIF_Number'         => '1280481',

        'account_title'      => 'DR. CHANCHAL KUMAR BISWAS',
        'customer_name'      => 'DR. CHANCHAL KUMAR BISWAS',

        'contact_no_1'       => '8801711946362',
        'mobile_number'      => '8801711946362',

        'email_1'            => 'ckbiswas68@gmail.com',
        'email_address'      => 'ckbiswas68@gmail.com',

        'branch_name'        => 'MAGURA BRANCH',
        'acc_opening_branch' => 'MAGURA BRANCH',
    ],
    [
        'bp_id'              => 'IN137DELHOSPBL',
        'account_number'     => '2135211017101',
        'SIF_Number'         => '109912',

        'account_title'      => 'MD DELWAR HOSSAIN',
        'customer_name'      => 'MD DELWAR HOSSAIN',

        'contact_no_1'       => '8801979212357',
        'mobile_number'      => '8801979212357',

        'email_1'            => 'delwerp2000@yahoo.com',
        'email_address'      => 'delwerp2000@yahoo.com',

        'branch_name'        => 'PRAGATI SARANI BRANCH',
        'acc_opening_branch' => 'PRAGATI SARANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137DELWARPB',
        'account_number'     => '2135211017101',
        'SIF_Number'         => '109912',

        'account_title'      => 'MD.DELWAR HOSSAIN',
        'customer_name'      => 'MD.DELWAR HOSSAIN',

        'contact_no_1'       => '8801979212357',
        'mobile_number'      => '8801979212357',

        'email_1'            => 'delwerp2000@yahoo.com',
        'email_address'      => 'delwerp2000@yahoo.com',

        'branch_name'        => 'PRAGATI SARANI BRANCH',
        'acc_opening_branch' => 'PRAGATI SARANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137DELWARPRM',
        'account_number'     => '2104218031946',
        'SIF_Number'         => '1919580',

        'account_title'      => 'MD. DELWAR HOSSAIN',
        'customer_name'      => 'MD. DELWAR HOSSAIN',

        'contact_no_1'       => '8801912387829',
        'mobile_number'      => '8801912387829',

        'email_1'            => 'dhossain54@gmail.com',
        'email_address'      => 'dhossain54@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137DHIMANKPB',
        'account_number'     => '2117211013417',
        'SIF_Number'         => '1994165',

        'account_title'      => 'DHIMAN KANTI BARUA',
        'customer_name'      => 'DHIMAN KANTI BARUA',

        'contact_no_1'       => '8801974193193',
        'mobile_number'      => '8801974193193',

        'email_1'            => 'dkbarua2013@gmail.com',
        'email_address'      => 'dkbarua2013@gmail.com',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137DIDARIPRM',
        'account_number'     => '2206216010337',
        'SIF_Number'         => '939261',

        'account_title'      => 'DIDARUL ISLAM',
        'customer_name'      => 'DIDARUL ISLAM',

        'contact_no_1'       => '258869648702',
        'mobile_number'      => '258869648702',

        'email_1'            => 'didarulislam07.ctg@gmail.com',
        'email_address'      => 'didarulislam07.ctg@gmail.com',

        'branch_name'        => 'KHULSHI BRANCH',
        'acc_opening_branch' => 'KHULSHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137DILAFPB',
        'account_number'     => '2192211006203',
        'SIF_Number'         => '1757400',

        'account_title'      => 'DIL AFROZ',
        'customer_name'      => 'DIL AFROZ',

        'contact_no_1'       => '8801714220725',
        'mobile_number'      => '8801714220725',

        'email_1'            => 'dipirahman@gmail.com',
        'email_address'      => 'dipirahman@gmail.com',

        'branch_name'        => 'BANASREE BRANCH',
        'acc_opening_branch' => 'BANASREE BRANCH',
    ],
    [
        'bp_id'              => 'IN137DILAFROZD',
        'account_number'     => '2192212009735',
        'SIF_Number'         => '2349213',

        'account_title'      => 'DIL AFROZ DINA',
        'customer_name'      => 'DIL AFROZ DINA',

        'contact_no_1'       => '8801711506679',
        'mobile_number'      => '8801711506679',

        'email_1'            => 'dafroz8@gmail.com',
        'email_address'      => 'dafroz8@gmail.com',

        'branch_name'        => 'BANASREE BRANCH',
        'acc_opening_branch' => 'BANASREE BRANCH',
    ],
    [
        'bp_id'              => 'IN137DILARANPB',
        'account_number'     => '2104217033984',
        'SIF_Number'         => '2157865',

        'account_title'      => 'DILARA NAHAR',
        'customer_name'      => 'DILARA NAHAR',

        'contact_no_1'       => '8801552300765',
        'mobile_number'      => '8801552300765',

        'email_1'            => 'dilara.nahar1977@gmail.com',
        'email_address'      => 'dilara.nahar1977@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137DIPAKPB',
        'account_number'     => '2502216010841',
        'SIF_Number'         => '2871693',

        'account_title'      => 'DIPAK KUMAR SAHA',
        'customer_name'      => 'DIPAK KUMAR SAHA',

        'contact_no_1'       => '8801861050333',
        'mobile_number'      => '8801861050333',

        'email_1'            => 'dipak.ngc@gmail.com',
        'email_address'      => 'dipak.ngc@gmail.com',

        'branch_name'        => 'NARSINGDI SME BR/SC',
        'acc_opening_branch' => 'NARSINGDI SME BR/SC',
    ],
    [
        'bp_id'              => 'IN137DIPONROY',
        'account_number'     => '2198216012572',
        'SIF_Number'         => '2285948',

        'account_title'      => 'DIPON ROY',
        'customer_name'      => 'DIPON ROY',

        'contact_no_1'       => '8801719289217',
        'mobile_number'      => '8801719289217',

        'email_1'            => 'diponsust@gmail.com',
        'email_address'      => 'diponsust@gmail.com',

        'branch_name'        => 'BASHABO BRANCH',
        'acc_opening_branch' => 'BASHABO BRANCH',
    ],
    [
        'bp_id'              => 'IN137DOLIRANIP',
        'account_number'     => '2127212023413',
        'SIF_Number'         => '294120',

        'account_title'      => 'DOLI RANI GOWSAMI',
        'customer_name'      => 'DOLI RANI GOWSAMI',

        'contact_no_1'       => '8801711622934',
        'mobile_number'      => '8801711622934',

        'email_1'            => 'drdolly@icddrb.org',
        'email_address'      => 'drdolly@icddrb.org',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137DRDEBAPPB',
        'account_number'     => '3134213002167',
        'SIF_Number'         => '8028610',

        'account_title'      => 'DR DEBA PRASAD PAUL',
        'customer_name'      => 'DR DEBA PRASAD PAUL',

        'contact_no_1'       => '8801819339365',
        'mobile_number'      => '8801819339365',

        'email_1'            => 'dprasad@cu.ac.bd',
        'email_address'      => 'dprasad@cu.ac.bd',

        'branch_name'        => 'IBB O.R. NIZAM ROAD BR',
        'acc_opening_branch' => 'IBB O.R. NIZAM ROAD BR',
    ],
    [
        'bp_id'              => 'IN137DULALHOPB',
        'account_number'     => '2118217036866',
        'SIF_Number'         => '2608199',

        'account_title'      => 'MD. DULAL HOSSAIN',
        'customer_name'      => 'MD. DULAL HOSSAIN',

        'contact_no_1'       => '8801928977114',
        'mobile_number'      => '8801928977114',

        'email_1'            => 'dulalhr.dola@gmail.com',
        'email_address'      => 'dulalhr.dola@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137EASINPB',
        'account_number'     => '2129218019611',
        'SIF_Number'         => '2701670',

        'account_title'      => 'MD. EASIN AHAMED',
        'customer_name'      => 'MD. EASIN AHAMED',

        'contact_no_1'       => '8801713967360',
        'mobile_number'      => '8801713967360',

        'email_1'            => 'eab100100100@gmail.com',
        'email_address'      => 'eab100100100@gmail.com',

        'branch_name'        => 'BOGURA BRANCH',
        'acc_opening_branch' => 'BOGURA BRANCH',
    ],
    [
        'bp_id'              => 'IN137EHSANULQ',
        'account_number'     => '2104216027292',
        'SIF_Number'         => '745493',

        'account_title'      => 'EHSANUL QUADER CHOWDHURY',
        'customer_name'      => 'EHSANUL QUADER CHOWDHURY',

        'contact_no_1'       => '8801714174553',
        'mobile_number'      => '8801714174553',

        'email_1'            => 'eqchowdhury@gmail.com',
        'email_address'      => 'eqchowdhury@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137EIDJAHPRM',
        'account_number'     => '2104213038763',
        'SIF_Number'         => '62081',

        'account_title'      => 'EIDUL JAHAN',
        'customer_name'      => 'EIDUL JAHAN',

        'contact_no_1'       => '8801925787007',
        'mobile_number'      => '8801925787007',

        'email_1'            => 'shahan.kaz27@gmail.com',
        'email_address'      => 'shahan.kaz27@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137EIKBALPB',
        'account_number'     => '2118211037522',
        'SIF_Number'         => '2631875',

        'account_title'      => 'MOHAMMED EIKBAL HOSSAIN',
        'customer_name'      => 'MOHAMMED EIKBAL HOSSAIN',

        'contact_no_1'       => '8801794195000',
        'mobile_number'      => '8801794195000',

        'email_1'            => 'emspeikbal@gmail.com',
        'email_address'      => 'emspeikbal@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137EIVYYASPB',
        'account_number'     => '2127211023860',
        'SIF_Number'         => '35551',

        'account_title'      => 'EIVY YASMIN',
        'customer_name'      => 'EIVY YASMIN',

        'contact_no_1'       => '8801711207285',
        'mobile_number'      => '8801711207285',

        'email_1'            => 'eivy73@yahoo.com',
        'email_address'      => 'eivy73@yahoo.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137EKRAMHPRM',
        'account_number'     => '2104212007116',
        'SIF_Number'         => '1513006',

        'account_title'      => 'MD. EKRAMUL HABIB',
        'customer_name'      => 'MD. EKRAMUL HABIB',

        'contact_no_1'       => '8801911733256',
        'mobile_number'      => '8801911733256',

        'email_1'            => 'ekram1969@yahoo.com',
        'email_address'      => 'ekram1969@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ELAAZMI',
        'account_number'     => '2120213022841',
        'SIF_Number'         => '2318643',

        'account_title'      => 'ELA AZMI',
        'customer_name'      => 'ELA AZMI',

        'contact_no_1'       => '8801815127841',
        'mobile_number'      => '8801815127841',

        'email_1'            => 'elaazmi91@gmail.com',
        'email_address'      => 'elaazmi91@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137ELAAZMIPB',
        'account_number'     => '2120213022841',
        'SIF_Number'         => '2318643',

        'account_title'      => 'ELA AZMI',
        'customer_name'      => 'ELA AZMI',

        'contact_no_1'       => '8801815127841',
        'mobile_number'      => '8801815127841',

        'email_1'            => 'elaazmi91@gmail.com',
        'email_address'      => 'elaazmi91@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137ELTITOPB',
        'account_number'     => '2113214017567',
        'SIF_Number'         => '2646942',

        'account_title'      => 'MOHAMMAD ELIAS TITO',
        'customer_name'      => 'MOHAMMAD ELIAS TITO',

        'contact_no_1'       => '8801719607080',
        'mobile_number'      => '8801719607080',

        'email_1'            => 'tito_3r@yahoo.com',
        'email_address'      => 'tito_3r@yahoo.com',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137EMAHBOOB',
        'account_number'     => '2132219016074',
        'SIF_Number'         => '65486',

        'account_title'      => 'Mahboob - E-Azam',
        'customer_name'      => 'Mahboob - E-Azam',

        'contact_no_1'       => '8801727739787',
        'mobile_number'      => '8801727739787',

        'email_1'            => 'm.azambd@gmail.com',
        'email_address'      => 'm.azambd@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137EMDADPRIM',
        'account_number'     => '2128215020712',
        'SIF_Number'         => '2483780',

        'account_title'      => 'MD. EMDADUL HAQUE',
        'customer_name'      => 'MD. EMDADUL HAQUE',

        'contact_no_1'       => '8801552319712',
        'mobile_number'      => '8801552319712',

        'email_1'            => 'emdad1959@gmail.com',
        'email_address'      => 'emdad1959@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137ESHADAS',
        'account_number'     => '2118215036728',
        'SIF_Number'         => '2317819',

        'account_title'      => 'ESHA DAS',
        'customer_name'      => 'ESHA DAS',

        'contact_no_1'       => '8801715823251',
        'mobile_number'      => '8801715823251',

        'email_1'            => 'eshadas_90@yahoo.com',
        'email_address'      => 'eshadas_90@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137EVANAKTPB',
        'account_number'     => '2110215000953',
        'SIF_Number'         => '277146',

        'account_title'      => 'EVANA AKTER',
        'customer_name'      => 'EVANA AKTER',

        'contact_no_1'       => '8801552373070',
        'mobile_number'      => '8801552373070',

        'email_1'            => 'evanamz@yahoo.com',
        'email_address'      => 'evanamz@yahoo.com',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137FABEDINPB',
        'account_number'     => '2141217015010',
        'SIF_Number'         => '2359998',

        'account_title'      => 'FARHANA ABEDIN',
        'customer_name'      => 'FARHANA ABEDIN',

        'contact_no_1'       => '8801610071336',
        'mobile_number'      => '8801610071336',

        'email_1'            => 'ziabedin@yahoo.co.uk',
        'email_address'      => 'ziabedin@yahoo.co.uk',

        'branch_name'        => 'PANTHAPATH BRANCH',
        'acc_opening_branch' => 'PANTHAPATH BRANCH',
    ],
    [
        'bp_id'              => 'IN137FAHIMAPB',
        'account_number'     => '2118215032800',
        'SIF_Number'         => '2421342',

        'account_title'      => 'FAHIMA NASRIN',
        'customer_name'      => 'FAHIMA NASRIN',

        'contact_no_1'       => '8801850736039',
        'mobile_number'      => '8801850736039',

        'email_1'            => 'fahima.nasrin@ymail.com',
        'email_address'      => 'fahima.nasrin@ymail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137FAHIMYAPB',
        'account_number'     => '2132219030794',
        'SIF_Number'         => '2565509',

        'account_title'      => 'FAHIM YASIR RAHMAN',
        'customer_name'      => 'FAHIM YASIR RAHMAN',

        'contact_no_1'       => '8801743028519',
        'mobile_number'      => '8801743028519',

        'email_1'            => 'fahimedru@gmail.com',
        'email_address'      => 'fahimedru@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137FAHMIDAPB',
        'account_number'     => '2104211039020',
        'SIF_Number'         => '2492002',

        'account_title'      => 'FAHMIDA ISLAM',
        'customer_name'      => 'FAHMIDA ISLAM',

        'contact_no_1'       => '8801723322496',
        'mobile_number'      => '8801723322496',

        'email_1'            => 'farhana1487@yahoo.com',
        'email_address'      => 'farhana1487@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137FAHMIDJPB',
        'account_number'     => '2162219017695',
        'SIF_Number'         => '2530893',

        'account_title'      => 'FAHMIDA JAHAN',
        'customer_name'      => 'FAHMIDA JAHAN',

        'contact_no_1'       => '8801556315966',
        'mobile_number'      => '8801556315966',

        'email_1'            => 'fahmidaific@gmail.com',
        'email_address'      => 'fahmidaific@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137FAHMIDPBL',
        'account_number'     => '2132219001902',
        'SIF_Number'         => '65043',

        'account_title'      => 'FAHMIDA RASHID',
        'customer_name'      => 'FAHMIDA RASHID',

        'contact_no_1'       => '8801716634564',
        'mobile_number'      => '8801716634564',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137FAHMIDSPB',
        'account_number'     => '2148219003272',
        'SIF_Number'         => '1313043',

        'account_title'      => 'FAHAMIDA SHARWAR',
        'customer_name'      => 'FAHAMIDA SHARWAR',

        'contact_no_1'       => '8801911206285',
        'mobile_number'      => '8801911206285',

        'email_1'            => 'mourynoshin@gmail.com',
        'email_address'      => 'mourynoshin@gmail.com',

        'branch_name'        => 'KAKRAIL BRANCH',
        'acc_opening_branch' => 'KAKRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137FAISALRAH',
        'account_number'     => '2118217022684',
        'SIF_Number'         => '2027627',

        'account_title'      => 'FAISAL RAHMAN',
        'customer_name'      => 'FAISAL RAHMAN',

        'contact_no_1'       => '8801313476911',
        'mobile_number'      => '8801313476911',

        'email_1'            => 'faisal.rahman369@gmail.com',
        'email_address'      => 'faisal.rahman369@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137FAIYADHPB',
        'account_number'     => '2127211027274',
        'SIF_Number'         => '1856355',

        'account_title'      => 'SHEIKH FAIYADH ZILLUR',
        'customer_name'      => 'SHEIKH FAIYADH ZILLUR',

        'contact_no_1'       => '8801689224847',
        'mobile_number'      => '8801689224847',

        'email_1'            => 'faiy097@gmail.com',
        'email_address'      => 'faiy097@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARAFRJPB',
        'account_number'     => '3133218011891',
        'SIF_Number'         => '8010280',

        'account_title'      => 'FARHANA AFROJ',
        'customer_name'      => 'FARHANA AFROJ',

        'contact_no_1'       => '8801882539309',
        'mobile_number'      => '8801882539309',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'IBB MIRPUR BRANCH',
        'acc_opening_branch' => 'IBB MIRPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARDPRIME',
        'account_number'     => '2104215005539',
        'SIF_Number'         => '1213264',

        'account_title'      => 'S.M. FARDAOUS ALAM',
        'customer_name'      => 'S.M. FARDAOUS ALAM',

        'contact_no_1'       => '8801780018750',
        'mobile_number'      => '8801780018750',

        'email_1'            => 'fardaous@gmail.com',
        'email_address'      => 'fardaous@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137FAREBAPB',
        'account_number'     => '2118211005644',
        'SIF_Number'         => '1439923',

        'account_title'      => 'FAREBA NAZ SHAULE',
        'customer_name'      => 'FAREBA NAZ SHAULE',

        'contact_no_1'       => '8801711114552',
        'mobile_number'      => '8801711114552',

        'email_1'            => 'nazfareba@gmail.com',
        'email_address'      => 'nazfareba@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARHADPB',
        'account_number'     => '2148216011559',
        'SIF_Number'         => '2533130',

        'account_title'      => 'MD. FARHAD MIA',
        'customer_name'      => 'MD. FARHAD MIA',

        'contact_no_1'       => '8801924797261',
        'mobile_number'      => '8801924797261',

        'email_1'            => 'forhad.mia@gmail.com',
        'email_address'      => 'forhad.mia@gmail.com',

        'branch_name'        => 'KAKRAIL BRANCH',
        'acc_opening_branch' => 'KAKRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARHAISPB',
        'account_number'     => '2104214039018',
        'SIF_Number'         => '2491997',

        'account_title'      => 'FARHANA ISLAM',
        'customer_name'      => 'FARHANA ISLAM',

        'contact_no_1'       => '8801830770486',
        'mobile_number'      => '8801830770486',

        'email_1'            => 'farhana1487@gmail.com',
        'email_address'      => 'farhana1487@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARHANAGH',
        'account_number'     => '2169218003855',
        'SIF_Number'         => '904093',

        'account_title'      => 'FARHANA BINTE GHOUS',
        'customer_name'      => 'FARHANA BINTE GHOUS',

        'contact_no_1'       => '8801715117913',
        'mobile_number'      => '8801715117913',

        'email_1'            => 'farhanaghous@yahoo.com',
        'email_address'      => 'farhanaghous@yahoo.com',

        'branch_name'        => 'DINAJPUR BRANCH',
        'acc_opening_branch' => 'DINAJPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARHANAI',
        'account_number'     => '2118215033733',
        'SIF_Number'         => '2478859',

        'account_title'      => 'FARHANA ISLAM',
        'customer_name'      => 'FARHANA ISLAM',

        'contact_no_1'       => '8801817181255',
        'mobile_number'      => '8801817181255',

        'email_1'            => 'farhana_islam00@gmail.com',
        'email_address'      => 'farhana_islam00@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARHANAPB',
        'account_number'     => '2165219014616',
        'SIF_Number'         => '2330620',

        'account_title'      => 'FARHANA SHARMIN',
        'customer_name'      => 'FARHANA SHARMIN',

        'contact_no_1'       => '8801913375592',
        'mobile_number'      => '8801913375592',

        'email_1'            => 'sadiamehrin26@gmail.com',
        'email_address'      => 'sadiamehrin26@gmail.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARHANAPR',
        'account_number'     => '2148216003143',
        'SIF_Number'         => '1772722',

        'account_title'      => 'FARHANA YASMIN',
        'customer_name'      => 'FARHANA YASMIN',

        'contact_no_1'       => '8801723127648',
        'mobile_number'      => '8801723127648',

        'email_1'            => 'farhahaq@yahoo.com',
        'email_address'      => 'farhahaq@yahoo.com',

        'branch_name'        => 'KAKRAIL BRANCH',
        'acc_opening_branch' => 'KAKRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARHANAU',
        'account_number'     => '2118218033862',
        'SIF_Number'         => '2483346',

        'account_title'      => 'FARHANA RAFIQ UZZAMAN',
        'customer_name'      => 'FARHANA RAFIQ UZZAMAN',

        'contact_no_1'       => '8801969602239',
        'mobile_number'      => '8801969602239',

        'email_1'            => 'farhana.rafiquzzaman@gmail.com',
        'email_address'      => 'farhana.rafiquzzaman@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARHANLA',
        'account_number'     => '2192212000718',
        'SIF_Number'         => '1217926',

        'account_title'      => 'FARHAN LABIB',
        'customer_name'      => 'FARHAN LABIB',

        'contact_no_1'       => '8801307282597',
        'mobile_number'      => '8801307282597',

        'email_1'            => 'flabib1999@gmail.com',
        'email_address'      => 'flabib1999@gmail.com',

        'branch_name'        => 'BANASREE BRANCH',
        'acc_opening_branch' => 'BANASREE BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARHANPRM',
        'account_number'     => '2146214013658',
        'SIF_Number'         => '2063908',

        'account_title'      => 'FARHAN TANVEER TANU',
        'customer_name'      => 'FARHAN TANVEER TANU',

        'contact_no_1'       => '8801712558226',
        'mobile_number'      => '8801712558226',

        'email_1'            => 'mdfarhantanveertanu@gmail.com',
        'email_address'      => 'mdfarhantanveertanu@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARHEENPR',
        'account_number'     => '2118219042714',
        'SIF_Number'         => '2863834',

        'account_title'      => 'FARHEEN RAHMAN',
        'customer_name'      => 'FARHEEN RAHMAN',

        'contact_no_1'       => '8801713082237',
        'mobile_number'      => '8801713082237',

        'email_1'            => 'farheenrahman691@gmail.com',
        'email_address'      => 'farheenrahman691@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARIAMA',
        'account_number'     => '2118219033960',
        'SIF_Number'         => '2486599',

        'account_title'      => 'FARIA MAHBUB',
        'customer_name'      => 'FARIA MAHBUB',

        'contact_no_1'       => '8801866000999',
        'mobile_number'      => '8801866000999',

        'email_1'            => 'fariamahbub@gmail.com',
        'email_address'      => 'fariamahbub@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARIAPRM',
        'account_number'     => '2118219038043',
        'SIF_Number'         => '2658545',

        'account_title'      => 'FARIA ISLAM',
        'customer_name'      => 'FARIA ISLAM',

        'contact_no_1'       => '8801849666646',
        'mobile_number'      => '8801849666646',

        'email_1'            => 'faria.islam09@gmail.com',
        'email_address'      => 'faria.islam09@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARIDAKPB',
        'account_number'     => '2162216021278',
        'SIF_Number'         => '2859476',

        'account_title'      => 'FARID AHMED KHAN',
        'customer_name'      => 'FARID AHMED KHAN',

        'contact_no_1'       => '8801912083947',
        'mobile_number'      => '8801912083947',

        'email_1'            => 'faridahmedkhan1961@gmail.com',
        'email_address'      => 'faridahmedkhan1961@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARUKHOPB',
        'account_number'     => '2192213011888',
        'SIF_Number'         => '1283145',

        'account_title'      => 'MD. FARUK HOSSAIN',
        'customer_name'      => 'MD. FARUK HOSSAIN',

        'contact_no_1'       => '8801860755883',
        'mobile_number'      => '8801860755883',

        'email_1'            => 'bdfarukhossain@yahoo.com',
        'email_address'      => 'bdfarukhossain@yahoo.com',

        'branch_name'        => 'BANASREE BRANCH',
        'acc_opening_branch' => 'BANASREE BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARZANAHD',
        'account_number'     => '2146212012678',
        'SIF_Number'         => '304741',

        'account_title'      => 'FARZANA NAHID',
        'customer_name'      => 'FARZANA NAHID',

        'contact_no_1'       => '8801770408855',
        'mobile_number'      => '8801770408855',

        'email_1'            => 'fnahid1980@gmail.com',
        'email_address'      => 'fnahid1980@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARZANANP',
        'account_number'     => '2146212012678',
        'SIF_Number'         => '304741',

        'account_title'      => 'FARZANA NAHID',
        'customer_name'      => 'FARZANA NAHID',

        'contact_no_1'       => '8801770408855',
        'mobile_number'      => '8801770408855',

        'email_1'            => 'fnahid1980@gmail.com',
        'email_address'      => 'fnahid1980@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137FARZANAPB',
        'account_number'     => '2132212018333',
        'SIF_Number'         => '1941788',

        'account_title'      => 'FARZANA AKTER MITA',
        'customer_name'      => 'FARZANA AKTER MITA',

        'contact_no_1'       => '8801755531861',
        'mobile_number'      => '8801755531861',

        'email_1'            => 'mitasen12345@gmail.com',
        'email_address'      => 'mitasen12345@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137FASULTNPB',
        'account_number'     => '2117219023772',
        'SIF_Number'         => '2902853',

        'account_title'      => 'FARHANA SULTANA',
        'customer_name'      => 'FARHANA SULTANA',

        'contact_no_1'       => '8801617622466',
        'mobile_number'      => '8801617622466',

        'email_1'            => 'farhana.sultana.nsu@gmail.com',
        'email_address'      => 'farhana.sultana.nsu@gmail.com',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137FATEMAKTE',
        'account_number'     => '3108213040342',
        'SIF_Number'         => '2404838',

        'account_title'      => 'FATEMA AKTER',
        'customer_name'      => 'FATEMA AKTER',

        'contact_no_1'       => '8801962424292',
        'mobile_number'      => '8801962424292',

        'email_1'            => 'fatema.akternaz@gmail.com',
        'email_address'      => 'fatema.akternaz@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137FATEMAPB',
        'account_number'     => '2159212016533',
        'SIF_Number'         => '1911643',

        'account_title'      => 'FATEMA BEGUM',
        'customer_name'      => 'FATEMA BEGUM',

        'contact_no_1'       => '8801713115397',
        'mobile_number'      => '8801713115397',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'BIJOY NAGAR BRANCH',
        'acc_opening_branch' => 'BIJOY NAGAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137FATIMAPRM',
        'account_number'     => '2125212014737',
        'SIF_Number'         => '1682166',

        'account_title'      => 'FATIMA KIBRIA',
        'customer_name'      => 'FATIMA KIBRIA',

        'contact_no_1'       => '8801552465768',
        'mobile_number'      => '8801552465768',

        'email_1'            => 'fatimakibria@yahoo.com',
        'email_address'      => 'fatimakibria@yahoo.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137FAUZIAPB',
        'account_number'     => '2104216007799',
        'SIF_Number'         => '1751550',

        'account_title'      => 'FAUZIA TANBIR',
        'customer_name'      => 'FAUZIA TANBIR',

        'contact_no_1'       => '8801713279700',
        'mobile_number'      => '8801713279700',

        'email_1'            => 'fauziatanbir@outlook.com',
        'email_address'      => 'fauziatanbir@outlook.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137FAYZULKPB',
        'account_number'     => '2148217005683',
        'SIF_Number'         => '842787',

        'account_title'      => 'MD. FAYZUL KARIM',
        'customer_name'      => 'MD. FAYZUL KARIM',

        'contact_no_1'       => '8801717613004',
        'mobile_number'      => '8801717613004',

        'email_1'            => 'fayzul_karim@yahoo.com',
        'email_address'      => 'fayzul_karim@yahoo.com',

        'branch_name'        => 'KAKRAIL BRANCH',
        'acc_opening_branch' => 'KAKRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137FERARAPB',
        'account_number'     => '2141218013543',
        'SIF_Number'         => '2230198',

        'account_title'      => 'PROF MST. FERDOUS ARA BEGUM',
        'customer_name'      => 'PROF MST. FERDOUS ARA BEGUM',

        'contact_no_1'       => '8801320680420',
        'mobile_number'      => '8801320680420',

        'email_1'            => 'tofa_hossain@hotmail.com',
        'email_address'      => 'tofa_hossain@hotmail.com',

        'branch_name'        => 'PANTHAPATH BRANCH',
        'acc_opening_branch' => 'PANTHAPATH BRANCH',
    ],
    [
        'bp_id'              => 'IN137FERDOUSAB',
        'account_number'     => '2105211001622',
        'SIF_Number'         => '222109',

        'account_title'      => 'FERDOUSH ARA BEGUM',
        'customer_name'      => 'FERDOUSH ARA BEGUM',

        'contact_no_1'       => '8801911116474',
        'mobile_number'      => '8801911116474',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'KHATUNGONJ BRANCH',
        'acc_opening_branch' => 'KHATUNGONJ BRANCH',
    ],
    [
        'bp_id'              => 'IN137FEROZPRIM',
        'account_number'     => '2104211018487',
        'SIF_Number'         => '1673084',

        'account_title'      => 'MD FEROZ AL AZAD',
        'customer_name'      => 'MD FEROZ AL AZAD',

        'contact_no_1'       => '8801713201864',
        'mobile_number'      => '8801713201864',

        'email_1'            => 'feroz.al.azad@gmail.com',
        'email_address'      => 'feroz.al.azad@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137FIRZKHNPB',
        'account_number'     => '2128215019805',
        'SIF_Number'         => '2238566',

        'account_title'      => 'FIROZA KHANAM',
        'customer_name'      => 'FIROZA KHANAM',

        'contact_no_1'       => '8801917749596',
        'mobile_number'      => '8801917749596',

        'email_1'            => 'firozakhan0791@gmail.com',
        'email_address'      => 'firozakhan0791@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137FORHAPRM',
        'account_number'     => '2104212041511',
        'SIF_Number'         => '2643197',

        'account_title'      => 'A.K.M. FORHAD',
        'customer_name'      => 'A.K.M. FORHAD',

        'contact_no_1'       => '8801788846088',
        'mobile_number'      => '8801788846088',

        'email_1'            => 'forhad.shojol@outlook.com',
        'email_address'      => 'forhad.shojol@outlook.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137FUADHASA',
        'account_number'     => '2110213005335',
        'SIF_Number'         => '1024512',

        'account_title'      => 'FUAD HASAN PRODHAN',
        'customer_name'      => 'FUAD HASAN PRODHAN',

        'contact_no_1'       => '8801731300436',
        'mobile_number'      => '8801731300436',

        'email_1'            => 'rp041752@gmail.com',
        'email_address'      => 'rp041752@gmail.com',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137GAZIASIF',
        'account_number'     => '2165214013126',
        'SIF_Number'         => '1670391',

        'account_title'      => 'GAZI ASIF MOHAMMED ISLAM',
        'customer_name'      => 'GAZI ASIF MOHAMMED ISLAM',

        'contact_no_1'       => '8801301374456',
        'mobile_number'      => '8801301374456',

        'email_1'            => 'asifsujon@gmail.com',
        'email_address'      => 'asifsujon@gmail.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137GEETARPRM',
        'account_number'     => '2117213018653',
        'SIF_Number'         => '2442542',

        'account_title'      => 'GEETA RANI BISWAS',
        'customer_name'      => 'GEETA RANI BISWAS',

        'contact_no_1'       => '8801676213962',
        'mobile_number'      => '8801676213962',

        'email_1'            => 'ranibiswasdrgeeta@gmail.com',
        'email_address'      => 'ranibiswasdrgeeta@gmail.com',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137GHALIBPRM',
        'account_number'     => '2118217037765',
        'SIF_Number'         => '1437478',

        'account_title'      => 'GHALIB HAIDER',
        'customer_name'      => 'GHALIB HAIDER',

        'contact_no_1'       => '8801914630524',
        'mobile_number'      => '8801914630524',

        'email_1'            => 'haider.ghalib@gmail.com',
        'email_address'      => 'haider.ghalib@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137GHOLAMERF',
        'account_number'     => '2146214003113',
        'SIF_Number'         => '162494',

        'account_title'      => 'MIRZA GHOLAM ERFAN',
        'customer_name'      => 'MIRZA GHOLAM ERFAN',

        'contact_no_1'       => '8801711529150',
        'mobile_number'      => '8801711529150',

        'email_1'            => 'erfan@venturegrp.net',
        'email_address'      => 'erfan@venturegrp.net',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137GITACHWPB',
        'account_number'     => '2120213008014',
        'SIF_Number'         => '196052',

        'account_title'      => 'GITA CHOWDHURY',
        'customer_name'      => 'GITA CHOWDHURY',

        'contact_no_1'       => '8801812042970',
        'mobile_number'      => '8801812042970',

        'email_1'            => 'mithun.chowdhury@primebank.com.bd',
        'email_address'      => 'mithun.chowdhury@primebank.com.bd',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137GOBINDPRM',
        'account_number'     => '2126211010792',
        'SIF_Number'         => '40142',

        'account_title'      => 'GOBINDA CHANDRA ROY',
        'customer_name'      => 'GOBINDA CHANDRA ROY',

        'contact_no_1'       => '8801712097802',
        'mobile_number'      => '8801712097802',

        'email_1'            => 'gobinda@unicomtex.com',
        'email_address'      => 'gobinda@unicomtex.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137GOLAMMSPB',
        'account_number'     => '2104214040099',
        'SIF_Number'         => '2564454',

        'account_title'      => 'MD. GOLAM MOSTAFA',
        'customer_name'      => 'MD. GOLAM MOSTAFA',

        'contact_no_1'       => '8801730335003',
        'mobile_number'      => '8801730335003',

        'email_1'            => 'golammostafa1961@gmail.com',
        'email_address'      => 'golammostafa1961@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137GOLAMSRPB',
        'account_number'     => '2104218019602',
        'SIF_Number'         => '2212',

        'account_title'      => 'MD. GOLAM SARWAR',
        'customer_name'      => 'MD. GOLAM SARWAR',

        'contact_no_1'       => '8801747555587',
        'mobile_number'      => '8801747555587',

        'email_1'            => 'sarwar1994@yahoo.com',
        'email_address'      => 'sarwar1994@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137GOURDAS',
        'account_number'     => '2165211000499',
        'SIF_Number'         => '259963',

        'account_title'      => 'GOUR DAS PODDER',
        'customer_name'      => 'GOUR DAS PODDER',

        'contact_no_1'       => '8801711506190',
        'mobile_number'      => '8801711506190',

        'email_1'            => 'gour77@yahoo.com',
        'email_address'      => 'gour77@yahoo.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137GRABPRIME',
        'account_number'     => '2104211025623',
        'SIF_Number'         => '2557',

        'account_title'      => 'MD GOLAM RABBANI',
        'customer_name'      => 'MD GOLAM RABBANI',

        'contact_no_1'       => '8801747686623',
        'mobile_number'      => '8801747686623',

        'email_1'            => 'mgrabbani2015@gmail.com',
        'email_address'      => 'mgrabbani2015@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137GUKCSARPB',
        'account_number'     => '2114211003834',
        'SIF_Number'         => '266038',

        'account_title'      => 'GUKUL CHANDRA SARKER',
        'customer_name'      => 'GUKUL CHANDRA SARKER',

        'contact_no_1'       => '8801914342272',
        'mobile_number'      => '8801914342272',

        'email_1'            => 'gokulsarker123@gmail.com',
        'email_address'      => 'gokulsarker123@gmail.com',

        'branch_name'        => 'ELEPHANT ROAD BRANCH',
        'acc_opening_branch' => 'ELEPHANT ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137HABIBABPB',
        'account_number'     => '2125211033218',
        'SIF_Number'         => '2466764',

        'account_title'      => 'HABIBA BEGUM',
        'customer_name'      => 'HABIBA BEGUM',

        'contact_no_1'       => '8801713201863',
        'mobile_number'      => '8801713201863',

        'email_1'            => 'kashemma@gmail.com',
        'email_address'      => 'kashemma@gmail.com',

        'branch_name'        => 'NIKUNJA SUB-BRANCH',
        'acc_opening_branch' => 'NIKUNJA SUB-BRANCH',
    ],
    [
        'bp_id'              => 'IN137HABIBRPRM',
        'account_number'     => '2118212035038',
        'SIF_Number'         => '2531247',

        'account_title'      => 'MD. HABIBUR RAHMAN',
        'customer_name'      => 'MD. HABIBUR RAHMAN',

        'contact_no_1'       => '8801718809707',
        'mobile_number'      => '8801718809707',

        'email_1'            => 'mahmudur.razu@icloud.com',
        'email_address'      => 'mahmudur.razu@icloud.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137HABIBURPB',
        'account_number'     => '2219213009816',
        'SIF_Number'         => '2419308',

        'account_title'      => 'MD HABIBUR RAHMAN',
        'customer_name'      => 'MD HABIBUR RAHMAN',

        'contact_no_1'       => '8801716090500',
        'mobile_number'      => '8801716090500',

        'email_1'            => 'habibburrahman60@gmail.com',
        'email_address'      => 'habibburrahman60@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137HAFIZRHMN',
        'account_number'     => '2104211024922',
        'SIF_Number'         => '6838',

        'account_title'      => 'MD HAFIZUR RAHMAN MALLICK',
        'customer_name'      => 'MD HAFIZUR RAHMAN MALLICK',

        'contact_no_1'       => '8801711129313',
        'mobile_number'      => '8801711129313',

        'email_1'            => 'mallick.prime@yahoo.com',
        'email_address'      => 'mallick.prime@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137HAIDERPRM',
        'account_number'     => '2125219008047',
        'SIF_Number'         => '410380',

        'account_title'      => 'SHAH ZULFIQUER HAIDER',
        'customer_name'      => 'SHAH ZULFIQUER HAIDER',

        'contact_no_1'       => '8801747445500',
        'mobile_number'      => '8801747445500',

        'email_1'            => 'zhaider17@gmail.com',
        'email_address'      => 'zhaider17@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137HAMIDULI',
        'account_number'     => '2161211006896',
        'SIF_Number'         => '264316',

        'account_title'      => 'DR.MD.HAMIDUL ISLAM',
        'customer_name'      => 'DR.MD.HAMIDUL ISLAM',

        'contact_no_1'       => '8801783215955',
        'mobile_number'      => '8801783215955',

        'email_1'            => 'nusrat.afroze@gmail.com',
        'email_address'      => 'nusrat.afroze@gmail.com',

        'branch_name'        => 'RANGPUR BRANCH',
        'acc_opening_branch' => 'RANGPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137HAPPYPRIM',
        'account_number'     => '2222211008576',
        'SIF_Number'         => '2200514',

        'account_title'      => 'JANNATUL FERDUSH HAPPY',
        'customer_name'      => 'JANNATUL FERDUSH HAPPY',

        'contact_no_1'       => '8801303145111',
        'mobile_number'      => '8801303145111',

        'email_1'            => 'thpy07@gmail.com',
        'email_address'      => 'thpy07@gmail.com',

        'branch_name'        => 'MAGURA BRANCH',
        'acc_opening_branch' => 'MAGURA BRANCH',
    ],
    [
        'bp_id'              => 'IN137HARASPRIM',
        'account_number'     => '2118216005151',
        'SIF_Number'         => '1184418',

        'account_title'      => 'MD HARUN OR RASHID',
        'customer_name'      => 'MD HARUN OR RASHID',

        'contact_no_1'       => '8801552455215',
        'mobile_number'      => '8801552455215',

        'email_1'            => 'harun@ipdcbd.com',
        'email_address'      => 'harun@ipdcbd.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137HARUNUDPB',
        'account_number'     => '3108215050042',
        'SIF_Number'         => '697512',

        'account_title'      => 'MD. HARUN UDDIN MAMUN',
        'customer_name'      => 'MD. HARUN UDDIN MAMUN',

        'contact_no_1'       => '8801717130664',
        'mobile_number'      => '8801717130664',

        'email_1'            => 'harun.u.mamun@gmail.com',
        'email_address'      => 'harun.u.mamun@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137HASANNZPB',
        'account_number'     => '2138211011598',
        'SIF_Number'         => '981407',

        'account_title'      => 'DR. MD. HASAN NAZMUL ALAM',
        'customer_name'      => 'DR. MD. HASAN NAZMUL ALAM',

        'contact_no_1'       => '8801732792773',
        'mobile_number'      => '8801732792773',

        'email_1'            => 'drrimi29@yahoo.com',
        'email_address'      => 'drrimi29@yahoo.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137HASINAFPB',
        'account_number'     => '2110211019072',
        'SIF_Number'         => '2207885',

        'account_title'      => 'HASINA FARDOUS',
        'customer_name'      => 'HASINA FARDOUS',

        'contact_no_1'       => '8801714084848',
        'mobile_number'      => '8801714084848',

        'email_1'            => 'hasina.fardous@primebank.com.bd',
        'email_address'      => 'hasina.fardous@primebank.com.bd',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137HASINAPB',
        'account_number'     => '2171211011795',
        'SIF_Number'         => '2106049',

        'account_title'      => 'HASINA ARA BEGUM',
        'customer_name'      => 'HASINA ARA BEGUM',

        'contact_no_1'       => '8801711749888',
        'mobile_number'      => '8801711749888',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'PROBARTAK MORE BRANCH',
        'acc_opening_branch' => 'PROBARTAK MORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137HASINASPB',
        'account_number'     => '2164213004135',
        'SIF_Number'         => '695057',

        'account_title'      => 'HASINA NASRIN',
        'customer_name'      => 'HASINA NASRIN',

        'contact_no_1'       => '8801717174529',
        'mobile_number'      => '8801717174529',

        'email_1'            => 'nasrinpallabi@gmail.com',
        'email_address'      => 'nasrinpallabi@gmail.com',

        'branch_name'        => 'PALLABI BRANCH',
        'acc_opening_branch' => 'PALLABI BRANCH',
    ],
    [
        'bp_id'              => 'IN137HAZIRAPB',
        'account_number'     => '2125211034082',
        'SIF_Number'         => '2657701',

        'account_title'      => 'MOSAMMAT HAZIRA BANU',
        'customer_name'      => 'MOSAMMAT HAZIRA BANU',

        'contact_no_1'       => '8801321163701',
        'mobile_number'      => '8801321163701',

        'email_1'            => 'rafiqul.dl@gmail.com',
        'email_address'      => 'rafiqul.dl@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137HEDAYETUL',
        'account_number'     => '2118214020111',
        'SIF_Number'         => '1925770',

        'account_title'      => 'MD. HEDAYETUL ISLAM',
        'customer_name'      => 'MD. HEDAYETUL ISLAM',

        'contact_no_1'       => '8801711404305',
        'mobile_number'      => '8801711404305',

        'email_1'            => 'hislam@tdcl.transcombd.com',
        'email_address'      => 'hislam@tdcl.transcombd.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137HEMEYETPB',
        'account_number'     => '2146218006286',
        'SIF_Number'         => '175790',

        'account_title'      => 'MD. HEMAYET UDDIN SIKDER',
        'customer_name'      => 'MD. HEMAYET UDDIN SIKDER',

        'contact_no_1'       => '8801711730861',
        'mobile_number'      => '8801711730861',

        'email_1'            => 'hemayet.uddin.sikder@gmail.com',
        'email_address'      => 'hemayet.uddin.sikder@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137HILALPRIM',
        'account_number'     => '2507211013993',
        'SIF_Number'         => '2440406',

        'account_title'      => 'MD. SHAHARIAR UDDIN HILALI',
        'customer_name'      => 'MD. SHAHARIAR UDDIN HILALI',

        'contact_no_1'       => '8801798232254',
        'mobile_number'      => '8801798232254',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MADHABPUR SME BR/SC',
        'acc_opening_branch' => 'MADHABPUR SME BR/SC',
    ],
    [
        'bp_id'              => 'IN137HILLOLPB',
        'account_number'     => '2125217030951',
        'SIF_Number'         => '2546201',

        'account_title'      => 'HILLOL SOBHAN',
        'customer_name'      => 'HILLOL SOBHAN',

        'contact_no_1'       => '8801711854099',
        'mobile_number'      => '8801711854099',

        'email_1'            => 'hillolsobhan@yahoo.com',
        'email_address'      => 'hillolsobhan@yahoo.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137HILLOLSPR',
        'account_number'     => '2125217030951',
        'SIF_Number'         => '2546201',

        'account_title'      => 'HILLOL SOBHAN',
        'customer_name'      => 'HILLOL SOBHAN',

        'contact_no_1'       => '8801711854099',
        'mobile_number'      => '8801711854099',

        'email_1'            => 'hillolsobhan@yahoo.com',
        'email_address'      => 'hillolsobhan@yahoo.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137HMEHEDI',
        'account_number'     => '2132214011062',
        'SIF_Number'         => '1394152',

        'account_title'      => 'MEHEDI HASAN',
        'customer_name'      => 'MEHEDI HASAN',

        'contact_no_1'       => '8801715273117',
        'mobile_number'      => '8801715273117',

        'email_1'            => 'mehediview@gmail.com',
        'email_address'      => 'mehediview@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137HMHARUN',
        'account_number'     => '2191218007577',
        'SIF_Number'         => '1988363',

        'account_title'      => 'H M HARUN',
        'customer_name'      => 'H M HARUN',

        'contact_no_1'       => '8801711836931',
        'mobile_number'      => '8801711836931',

        'email_1'            => 'harun@wms.com.bd',
        'email_address'      => 'harun@wms.com.bd',

        'branch_name'        => 'HALISHAHOR BRANCH',
        'acc_opening_branch' => 'HALISHAHOR BRANCH',
    ],
    [
        'bp_id'              => 'IN137HMKAMRUPB',
        'account_number'     => '2135219014589',
        'SIF_Number'         => '108284',

        'account_title'      => 'H.M. KAMRUZZAMAN SUMAN',
        'customer_name'      => 'H.M. KAMRUZZAMAN SUMAN',

        'contact_no_1'       => '8801722012660',
        'mobile_number'      => '8801722012660',

        'email_1'            => 'kamruzzaman_suman@yahoo.com.au',
        'email_address'      => 'kamruzzaman_suman@yahoo.com.au',

        'branch_name'        => 'PRAGATI SARANI BRANCH',
        'acc_opening_branch' => 'PRAGATI SARANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137HMMAMUN',
        'account_number'     => '3133211033745',
        'SIF_Number'         => '1295739',

        'account_title'      => 'H. M. MAMUN',
        'customer_name'      => 'H. M. MAMUN',

        'contact_no_1'       => '8801712789303',
        'mobile_number'      => '8801712789303',

        'email_1'            => 'hm.mamun@primebank.com.bd',
        'email_address'      => 'hm.mamun@primebank.com.bd',

        'branch_name'        => 'IBB MIRPUR BRANCH',
        'acc_opening_branch' => 'IBB MIRPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137HOMAYUN',
        'account_number'     => '2118218004897',
        'SIF_Number'         => '85680',

        'account_title'      => 'A H M HOMAYUN KABIR',
        'customer_name'      => 'A H M HOMAYUN KABIR',

        'contact_no_1'       => '8801916570501',
        'mobile_number'      => '8801916570501',

        'email_1'            => 'hkabir754.hk@gmail.com',
        'email_address'      => 'hkabir754.hk@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137HOSHARPB',
        'account_number'     => '2118216005151',
        'SIF_Number'         => '1184418',

        'account_title'      => 'HOSNEY ARA RASHID &amp; MD. HARUN-OR-RASHID',
        'customer_name'      => 'HOSNEY ARA RASHID &amp; MD. HARUN-OR-RASHID',

        'contact_no_1'       => '8801552455215',
        'mobile_number'      => '8801552455215',

        'email_1'            => 'harun@ipdcbd.com',
        'email_address'      => 'harun@ipdcbd.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137HOSNEARA',
        'account_number'     => '2207213007194',
        'SIF_Number'         => '1825887',

        'account_title'      => 'HOSNE ARA',
        'customer_name'      => 'HOSNE ARA',

        'contact_no_1'       => '8801720130758',
        'mobile_number'      => '8801720130758',

        'email_1'            => 'hosneara130@gmail.com',
        'email_address'      => 'hosneara130@gmail.com',

        'branch_name'        => 'TEJGAON BRANCH',
        'acc_opening_branch' => 'TEJGAON BRANCH',
    ],
    [
        'bp_id'              => 'IN137HOSSAMPRI',
        'account_number'     => '2158211008590',
        'SIF_Number'         => '701499',

        'account_title'      => 'HOSSAM HYDER CHOWDHURY',
        'customer_name'      => 'HOSSAM HYDER CHOWDHURY',

        'contact_no_1'       => '8801815549266',
        'mobile_number'      => '8801815549266',

        'email_1'            => 'hossam.chowdhury@sky.com',
        'email_address'      => 'hossam.chowdhury@sky.com',

        'branch_name'        => 'COMILLA BRANCH',
        'acc_opening_branch' => 'COMILLA BRANCH',
    ],
    [
        'bp_id'              => 'IN137HUMAIRAP',
        'account_number'     => '2192216009913',
        'SIF_Number'         => '2372412',

        'account_title'      => 'HUMAIRA PARVEEN',
        'customer_name'      => 'HUMAIRA PARVEEN',

        'contact_no_1'       => '8801817064966',
        'mobile_number'      => '8801817064966',

        'email_1'            => 'musfiqdse@yahoo.com',
        'email_address'      => 'musfiqdse@yahoo.com',

        'branch_name'        => 'BANASREE BRANCH',
        'acc_opening_branch' => 'BANASREE BRANCH',
    ],
    [
        'bp_id'              => 'IN137HUMAYUNPB',
        'account_number'     => '2113215003160',
        'SIF_Number'         => '305777',

        'account_title'      => 'KAZI HUMAYUN KABIR',
        'customer_name'      => 'KAZI HUMAYUN KABIR',

        'contact_no_1'       => '8801730982653',
        'mobile_number'      => '8801730982653',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137HUMZAKPBK',
        'account_number'     => '2205214006296',
        'SIF_Number'         => '271603',

        'account_title'      => 'HUMAIRA ZAKARIA',
        'customer_name'      => 'HUMAIRA ZAKARIA',

        'contact_no_1'       => '8801792527195',
        'mobile_number'      => '8801792527195',

        'email_1'            => 'humairaza@gmail.com',
        'email_address'      => 'humairaza@gmail.com',

        'branch_name'        => 'IBRAHIMPUR BRANCH',
        'acc_opening_branch' => 'IBRAHIMPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137HUNAINPB',
        'account_number'     => '2104219019486',
        'SIF_Number'         => '1445029',

        'account_title'      => 'HUNAIN MAHMUD',
        'customer_name'      => 'HUNAIN MAHMUD',

        'contact_no_1'       => '8801924828415',
        'mobile_number'      => '8801924828415',

        'email_1'            => 'nfs.ce05@gmail.com',
        'email_address'      => 'nfs.ce05@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137HUQENAMUL',
        'account_number'     => '2118219032988',
        'SIF_Number'         => '2427762',

        'account_title'      => 'MOHAMMAD ENAMUL HUQUE',
        'customer_name'      => 'MOHAMMAD ENAMUL HUQUE',

        'contact_no_1'       => '8801819251266',
        'mobile_number'      => '8801819251266',

        'email_1'            => 'enam.huque@gmail.com',
        'email_address'      => 'enam.huque@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137HUSZAHID',
        'account_number'     => '2118213006533',
        'SIF_Number'         => '78952',

        'account_title'      => 'MD. ZAHID HUSSAIN',
        'customer_name'      => 'MD. ZAHID HUSSAIN',

        'contact_no_1'       => '8801713065483',
        'mobile_number'      => '8801713065483',

        'email_1'            => 'zhn@transcombd.com',
        'email_address'      => 'zhn@transcombd.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137IBNEHALIM',
        'account_number'     => '2104216010207',
        'SIF_Number'         => '988501',

        'account_title'      => 'MUHAMMAD MAHFUZ IBNE HALIM',
        'customer_name'      => 'MUHAMMAD MAHFUZ IBNE HALIM',

        'contact_no_1'       => '8801717858822',
        'mobile_number'      => '8801717858822',

        'email_1'            => 'mahfuz.halim@primebank.com.bd',
        'email_address'      => 'mahfuz.halim@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137IDRISALPB',
        'account_number'     => '2206214010115',
        'SIF_Number'         => '2598990',

        'account_title'      => 'IDRIS ALAM',
        'customer_name'      => 'IDRIS ALAM',

        'contact_no_1'       => '8801717042195',
        'mobile_number'      => '8801717042195',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'KHULSHI BRANCH',
        'acc_opening_branch' => 'KHULSHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137IDRISPRM',
        'account_number'     => '2146218019263',
        'SIF_Number'         => '2472317',

        'account_title'      => 'DR. S. M. IDRIS ALI',
        'customer_name'      => 'DR. S. M. IDRIS ALI',

        'contact_no_1'       => '8801711155034',
        'mobile_number'      => '8801711155034',

        'email_1'            => 'idrisali1957@gmail.com',
        'email_address'      => 'idrisali1957@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137IFFATZAPB',
        'account_number'     => '2104215007394',
        'SIF_Number'         => '866718',

        'account_title'      => 'IFFAT ZAHAN',
        'customer_name'      => 'IFFAT ZAHAN',

        'contact_no_1'       => '8801791574889',
        'mobile_number'      => '8801791574889',

        'email_1'            => 'iffat.isrt.du@gmail.com',
        'email_address'      => 'iffat.isrt.du@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137IFTEKHAPB',
        'account_number'     => '2197214007641',
        'SIF_Number'         => '1921658',

        'account_title'      => 'IFTEKHAR AHAMMAD',
        'customer_name'      => 'IFTEKHAR AHAMMAD',

        'contact_no_1'       => '8801759283048',
        'mobile_number'      => '8801759283048',

        'email_1'            => 'naimbdkst@gmail.com',
        'email_address'      => 'naimbdkst@gmail.com',

        'branch_name'        => 'KUSHTIA BRANCH',
        'acc_opening_branch' => 'KUSHTIA BRANCH',
    ],
    [
        'bp_id'              => 'IN137IKRAMMAY',
        'account_number'     => '2118215035415',
        'SIF_Number'         => '2551252',

        'account_title'      => 'Ikram Mayeen Choudhury',
        'customer_name'      => 'Ikram Mayeen Choudhury',

        'contact_no_1'       => '8801922111000',
        'mobile_number'      => '8801922111000',

        'email_1'            => 'ikram.mayeen@gmail.com',
        'email_address'      => 'ikram.mayeen@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ILIASAPRM',
        'account_number'     => '2211215008772',
        'SIF_Number'         => '2655733',

        'account_title'      => 'ILIASPB DUPLICATE',
        'customer_name'      => 'ILIASPB DUPLICATE',

        'contact_no_1'       => '8801787261251',
        'mobile_number'      => '8801787261251',

        'email_1'            => 'iliasahmed050@gmail.com',
        'email_address'      => 'iliasahmed050@gmail.com',

        'branch_name'        => 'BRAHMANBARIA BRANCH',
        'acc_opening_branch' => 'BRAHMANBARIA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ILIASPB',
        'account_number'     => '2211215008772',
        'SIF_Number'         => '2655733',

        'account_title'      => 'ILIAS AHAMMAD',
        'customer_name'      => 'ILIAS AHAMMAD',

        'contact_no_1'       => '8801787261251',
        'mobile_number'      => '8801787261251',

        'email_1'            => 'iliasahmed050@gmail.com',
        'email_address'      => 'iliasahmed050@gmail.com',

        'branch_name'        => 'BRAHMANBARIA BRANCH',
        'acc_opening_branch' => 'BRAHMANBARIA BRANCH',
    ],
    [
        'bp_id'              => 'IN137IMAMRASUL',
        'account_number'     => '0021090031575',
        'SIF_Number'         => '1542122',

        'account_title'      => 'IMAM RASUL MOHAMMED IMTIAZ',
        'customer_name'      => 'IMAM RASUL MOHAMMED IMTIAZ',

        'contact_no_1'       => '8801819628346',
        'mobile_number'      => '8801819628346',

        'email_1'            => 'imtiaz.riad@gmail.com',
        'email_address'      => 'imtiaz.riad@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137IMAMUPRIM',
        'account_number'     => '0021070002363',
        'SIF_Number'         => '292605',

        'account_title'      => 'MD. IMAMUS SULTAN',
        'customer_name'      => 'MD. IMAMUS SULTAN',

        'contact_no_1'       => '8801713493380',
        'mobile_number'      => '8801713493380',

        'email_1'            => 'imamus.sultan@gmail.com',
        'email_address'      => 'imamus.sultan@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137IMRANHOSS',
        'account_number'     => '2118219024308',
        'SIF_Number'         => '2080460',

        'account_title'      => 'MD IMRAN HOSSAIN',
        'customer_name'      => 'MD IMRAN HOSSAIN',

        'contact_no_1'       => '8801912093851',
        'mobile_number'      => '8801912093851',

        'email_1'            => 'ihossain80@gmail.com',
        'email_address'      => 'ihossain80@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137IMRANHPB',
        'account_number'     => '2187217012863',
        'SIF_Number'         => '2417818',

        'account_title'      => 'MD. IMRAN KHAN',
        'customer_name'      => 'MD. IMRAN KHAN',

        'contact_no_1'       => '8801739301143',
        'mobile_number'      => '8801739301143',

        'email_1'            => 'mdimran.khan@sc.com',
        'email_address'      => 'mdimran.khan@sc.com',

        'branch_name'        => 'WARI BRANCH',
        'acc_opening_branch' => 'WARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137IMRANIBPB',
        'account_number'     => '3115213017234',
        'SIF_Number'         => '2150153',

        'account_title'      => 'IMRAN IBNUL HUSSAIN',
        'customer_name'      => 'IMRAN IBNUL HUSSAIN',

        'contact_no_1'       => '8801745933368',
        'mobile_number'      => '8801745933368',

        'email_1'            => 'iih@hotmail.co.uk',
        'email_address'      => 'iih@hotmail.co.uk',

        'branch_name'        => 'IBB AMBERKHANA BRANCH',
        'acc_opening_branch' => 'IBB AMBERKHANA BRANCH',
    ],
    [
        'bp_id'              => 'IN137IMRANPRIM',
        'account_number'     => '2192214008941',
        'SIF_Number'         => '2258280',

        'account_title'      => 'MOHAMMAD IMRANUR RASHID',
        'customer_name'      => 'MOHAMMAD IMRANUR RASHID',

        'contact_no_1'       => '8801715826218',
        'mobile_number'      => '8801715826218',

        'email_1'            => 'md.rashidscb@gmail.com',
        'email_address'      => 'md.rashidscb@gmail.com',

        'branch_name'        => 'BANASREE BRANCH',
        'acc_opening_branch' => 'BANASREE BRANCH',
    ],
    [
        'bp_id'              => 'IN137IMTIAZAH',
        'account_number'     => '2118218032872',
        'SIF_Number'         => '2424484',

        'account_title'      => 'IMTIAZ AHMAD',
        'customer_name'      => 'IMTIAZ AHMAD',

        'contact_no_1'       => '8801711090299',
        'mobile_number'      => '8801711090299',

        'email_1'            => 'shushar@gmail.com',
        'email_address'      => 'shushar@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137IMTSHIHAB',
        'account_number'     => '2219215005200',
        'SIF_Number'         => '2097073',

        'account_title'      => 'AHMED IMTIAZ SHIHAB',
        'customer_name'      => 'AHMED IMTIAZ SHIHAB',

        'contact_no_1'       => '8801676767801',
        'mobile_number'      => '8801676767801',

        'email_1'            => 'imtiaz_shihab@yahoo.com',
        'email_address'      => 'imtiaz_shihab@yahoo.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137IQBALANWR',
        'account_number'     => '2104218018778',
        'SIF_Number'         => '18763',

        'account_title'      => 'IQBAL ANWAR',
        'customer_name'      => 'IQBAL ANWAR',

        'contact_no_1'       => '8801713069905',
        'mobile_number'      => '8801713069905',

        'email_1'            => 'iqbalanwar@primebank.com.bd',
        'email_address'      => 'iqbalanwar@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137IQUBALPB',
        'account_number'     => '2132215033396',
        'SIF_Number'         => '2651573',

        'account_title'      => 'MD. JAFAR IQUBAL',
        'customer_name'      => 'MD. JAFAR IQUBAL',

        'contact_no_1'       => '8801711363500',
        'mobile_number'      => '8801711363500',

        'email_1'            => 'jafar326@gmail.com',
        'email_address'      => 'jafar326@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137IRINPERV',
        'account_number'     => '2126219014515',
        'SIF_Number'         => '2628793',

        'account_title'      => 'IRIN PERVIN',
        'customer_name'      => 'IRIN PERVIN',

        'contact_no_1'       => '8801723354212',
        'mobile_number'      => '8801723354212',

        'email_1'            => 'shamsnsu062@gmail.com',
        'email_address'      => 'shamsnsu062@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137IRTIZAPRM',
        'account_number'     => '2188215015032',
        'SIF_Number'         => '2634734',

        'account_title'      => 'IRTIZA SADEE MAHBUB',
        'customer_name'      => 'IRTIZA SADEE MAHBUB',

        'contact_no_1'       => '8801911796136',
        'mobile_number'      => '8801911796136',

        'email_1'            => 'irtiza2007@gmail.com',
        'email_address'      => 'irtiza2007@gmail.com',

        'branch_name'        => 'SAVAR BRANCH',
        'acc_opening_branch' => 'SAVAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137ISBAHULPB',
        'account_number'     => '2104213018369',
        'SIF_Number'         => '22243',

        'account_title'      => 'ISBAHUL BAR CHOWDHURY',
        'customer_name'      => 'ISBAHUL BAR CHOWDHURY',

        'contact_no_1'       => '8801713080385',
        'mobile_number'      => '8801713080385',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ISHTIAQPB',
        'account_number'     => '2126211013466',
        'SIF_Number'         => '2537705',

        'account_title'      => 'MD. ISHTIAK SARWAR',
        'customer_name'      => 'MD. ISHTIAK SARWAR',

        'contact_no_1'       => '8801674927668',
        'mobile_number'      => '8801674927668',

        'email_1'            => 'sarwar.ishtiak7579@gmail.com',
        'email_address'      => 'sarwar.ishtiak7579@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137ISHTIAQUS',
        'account_number'     => '2135211036391',
        'SIF_Number'         => '2386681',

        'account_title'      => 'ISHTIAQUE HOSSAIN SAJID',
        'customer_name'      => 'ISHTIAQUE HOSSAIN SAJID',

        'contact_no_1'       => '8801749802700',
        'mobile_number'      => '8801749802700',

        'email_1'            => 'ishtisajid@gmail.com',
        'email_address'      => 'ishtisajid@gmail.com',

        'branch_name'        => 'PRAGATI SARANI BRANCH',
        'acc_opening_branch' => 'PRAGATI SARANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137IYANAHAQ',
        'account_number'     => '2191217006984',
        'SIF_Number'         => '1932407',

        'account_title'      => 'IYANA HAQUE',
        'customer_name'      => 'IYANA HAQUE',

        'contact_no_1'       => '8801712146992',
        'mobile_number'      => '8801712146992',

        'email_1'            => 'harun@wms.com.bd',
        'email_address'      => 'harun@wms.com.bd',

        'branch_name'        => 'HALISHAHOR BRANCH',
        'acc_opening_branch' => 'HALISHAHOR BRANCH',
    ],
    [
        'bp_id'              => 'IN137IZAZHAQPB',
        'account_number'     => '2138211021242',
        'SIF_Number'         => '2556639',

        'account_title'      => 'IZAZUL HAQUE',
        'customer_name'      => 'IZAZUL HAQUE',

        'contact_no_1'       => '8801674671767',
        'mobile_number'      => '8801674671767',

        'email_1'            => 'ihaque33@gmail.com',
        'email_address'      => 'ihaque33@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137JAFARAHPB',
        'account_number'     => '2105219003695',
        'SIF_Number'         => '1374548',

        'account_title'      => 'JAFAR AHMED',
        'customer_name'      => 'JAFAR AHMED',

        'contact_no_1'       => '8801776513636',
        'mobile_number'      => '8801776513636',

        'email_1'            => 'jafaramd@gmail.com',
        'email_address'      => 'jafaramd@gmail.com',

        'branch_name'        => 'KHATUNGONJ BRANCH',
        'acc_opening_branch' => 'KHATUNGONJ BRANCH',
    ],
    [
        'bp_id'              => 'IN137JAHANARPR',
        'account_number'     => '2198211003758',
        'SIF_Number'         => '1213932',

        'account_title'      => 'JAHANARA AKTER',
        'customer_name'      => 'JAHANARA AKTER',

        'contact_no_1'       => '8801995062056',
        'mobile_number'      => '8801995062056',

        'email_1'            => 'jhumak218@gmail.com',
        'email_address'      => 'jhumak218@gmail.com',

        'branch_name'        => 'BASHABO BRANCH',
        'acc_opening_branch' => 'BASHABO BRANCH',
    ],
    [
        'bp_id'              => 'IN137JAHIDAPB',
        'account_number'     => '2107217035283',
        'SIF_Number'         => '2863993',

        'account_title'      => 'JAHIDA AKTER',
        'customer_name'      => 'JAHIDA AKTER',

        'contact_no_1'       => '8801818664685',
        'mobile_number'      => '8801818664685',

        'email_1'            => 'shantonu26@gmail.com',
        'email_address'      => 'shantonu26@gmail.com',

        'branch_name'        => 'KHULNA BRANCH',
        'acc_opening_branch' => 'KHULNA BRANCH',
    ],
    [
        'bp_id'              => 'IN137JAHNARZPB',
        'account_number'     => '2118213037041',
        'SIF_Number'         => '1613773',

        'account_title'      => 'JAHANARA ARZOO',
        'customer_name'      => 'JAHANARA ARZOO',

        'contact_no_1'       => '8801711547184',
        'mobile_number'      => '8801711547184',

        'email_1'            => 'arzooneer@gmail.com',
        'email_address'      => 'arzooneer@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137JALALPRM',
        'account_number'     => '2128215000623',
        'SIF_Number'         => '235957',

        'account_title'      => 'JALAL AHMED',
        'customer_name'      => 'JALAL AHMED',

        'contact_no_1'       => '8801715017355',
        'mobile_number'      => '8801715017355',

        'email_1'            => 'jalal61@gmail.com',
        'email_address'      => 'jalal61@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137JALALUAPB',
        'account_number'     => '2205211016554',
        'SIF_Number'         => '57382',

        'account_title'      => 'JALAL UDDIN AHMED',
        'customer_name'      => 'JALAL UDDIN AHMED',

        'contact_no_1'       => '8801711332610',
        'mobile_number'      => '8801711332610',

        'email_1'            => 'jalalfp@gmail.com',
        'email_address'      => 'jalalfp@gmail.com',

        'branch_name'        => 'IBRAHIMPUR BRANCH',
        'acc_opening_branch' => 'IBRAHIMPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137JAMSHEDPR',
        'account_number'     => '2145218011956',
        'SIF_Number'         => '962918',

        'account_title'      => 'MIRZA MD. JAMSHED ALI',
        'customer_name'      => 'MIRZA MD. JAMSHED ALI',

        'contact_no_1'       => '8801713102479',
        'mobile_number'      => '8801713102479',

        'email_1'            => 'jamshed@ritzygroup.com',
        'email_address'      => 'jamshed@ritzygroup.com',

        'branch_name'        => 'LALDIGHI EAST BRANCH',
        'acc_opening_branch' => 'LALDIGHI EAST BRANCH',
    ],
    [
        'bp_id'              => 'IN137JANAFERPB',
        'account_number'     => '2120211020045',
        'SIF_Number'         => '2102393',

        'account_title'      => 'JANNATUL FERDOUS',
        'customer_name'      => 'JANNATUL FERDOUS',

        'contact_no_1'       => '8801819559883',
        'mobile_number'      => '8801819559883',

        'email_1'            => 'rashed.yousuf@gmail.com',
        'email_address'      => 'rashed.yousuf@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137JANFARPRM',
        'account_number'     => '2104217001684',
        'SIF_Number'         => '1844612',

        'account_title'      => 'JANNATUL FARDOUS',
        'customer_name'      => 'JANNATUL FARDOUS',

        'contact_no_1'       => '8801682337885',
        'mobile_number'      => '8801682337885',

        'email_1'            => 'tushajannat@gmail.com',
        'email_address'      => 'tushajannat@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137JASHIMPB',
        'account_number'     => '2219219008697',
        'SIF_Number'         => '2397396',

        'account_title'      => 'MD. JASHIM UDDIN',
        'customer_name'      => 'MD. JASHIM UDDIN',

        'contact_no_1'       => '8801710230032',
        'mobile_number'      => '8801710230032',

        'email_1'            => 'jasimfakir17@gmail.com',
        'email_address'      => 'jasimfakir17@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137JASHIMUPB',
        'account_number'     => '2121216007004',
        'SIF_Number'         => '214291',

        'account_title'      => 'MOHAMMAD JASHIM UDDIN',
        'customer_name'      => 'MOHAMMAD JASHIM UDDIN',

        'contact_no_1'       => '8801711308078',
        'mobile_number'      => '8801711308078',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'JUBILEE ROAD BRANCH',
        'acc_opening_branch' => 'JUBILEE ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137JESMIAKPB',
        'account_number'     => '2148218003307',
        'SIF_Number'         => '923625',

        'account_title'      => 'JESMIN AKHTER',
        'customer_name'      => 'JESMIN AKHTER',

        'contact_no_1'       => '8801552440447',
        'mobile_number'      => '8801552440447',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'KAKRAIL BRANCH',
        'acc_opening_branch' => 'KAKRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137JESMINEPB',
        'account_number'     => '2162218011996',
        'SIF_Number'         => '2002748',

        'account_title'      => 'DR. JESMINE AKTER',
        'customer_name'      => 'DR. JESMINE AKTER',

        'contact_no_1'       => '8801817580677',
        'mobile_number'      => '8801817580677',

        'email_1'            => 'mmrmizan@yahoo.com',
        'email_address'      => 'mmrmizan@yahoo.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137JESMINPRI',
        'account_number'     => '2120219024445',
        'SIF_Number'         => '2426317',

        'account_title'      => 'MS SHAWKAT ARA JESMIN CHOWDHURY',
        'customer_name'      => 'MS SHAWKAT ARA JESMIN CHOWDHURY',

        'contact_no_1'       => '8801844556854',
        'mobile_number'      => '8801844556854',

        'email_1'            => 'jasminara@yahoo.com',
        'email_address'      => 'jasminara@yahoo.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137JHUMKAPB',
        'account_number'     => '2118213025988',
        'SIF_Number'         => '2139832',

        'account_title'      => 'JHUMKA RANI MANDAL',
        'customer_name'      => 'JHUMKA RANI MANDAL',

        'contact_no_1'       => '8801753702940',
        'mobile_number'      => '8801753702940',

        'email_1'            => 'mayajhumka@gmail.com',
        'email_address'      => 'mayajhumka@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137JILLPRM',
        'account_number'     => '2213218002408',
        'SIF_Number'         => '1399624',

        'account_title'      => 'S.M. JILLUR RAHMAN',
        'customer_name'      => 'S.M. JILLUR RAHMAN',

        'contact_no_1'       => '8801750442270',
        'mobile_number'      => '8801750442270',

        'email_1'            => 'jillurrahman0803@gmail.com',
        'email_address'      => 'jillurrahman0803@gmail.com',

        'branch_name'        => 'DAULATPUR BRANCH',
        'acc_opening_branch' => 'DAULATPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137JNUSRAT',
        'account_number'     => '2104214005746',
        'SIF_Number'         => '861268',

        'account_title'      => 'NUSRAT JAHAN',
        'customer_name'      => 'NUSRAT JAHAN',

        'contact_no_1'       => '8801635372522',
        'mobile_number'      => '8801635372522',

        'email_1'            => 'nusrat16@gmail.com',
        'email_address'      => 'nusrat16@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137JOARDERPB',
        'account_number'     => '2118219032798',
        'SIF_Number'         => '1161292',

        'account_title'      => 'JOARDER TANVIR FAISAL',
        'customer_name'      => 'JOARDER TANVIR FAISAL',

        'contact_no_1'       => '8801713304876',
        'mobile_number'      => '8801713304876',

        'email_1'            => 'jtfaisal@gmail.com',
        'email_address'      => 'jtfaisal@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137JOWELSHI',
        'account_number'     => '3134218007708',
        'SIF_Number'         => '1171379',

        'account_title'      => 'JOWEL SHIL',
        'customer_name'      => 'JOWEL SHIL',

        'contact_no_1'       => '8801866480410',
        'mobile_number'      => '8801866480410',

        'email_1'            => 'shiljowel@gmail.com',
        'email_address'      => 'shiljowel@gmail.com',

        'branch_name'        => 'IBB O.R. NIZAM ROAD BR',
        'acc_opening_branch' => 'IBB O.R. NIZAM ROAD BR',
    ],
    [
        'bp_id'              => 'IN137JOYANTAP',
        'account_number'     => '2219217006453',
        'SIF_Number'         => '2224171',

        'account_title'      => 'JOYANTA PAL',
        'customer_name'      => 'JOYANTA PAL',

        'contact_no_1'       => '8801912352185',
        'mobile_number'      => '8801912352185',

        'email_1'            => 'joyanta717@gmail.com',
        'email_address'      => 'joyanta717@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137JOYASREES',
        'account_number'     => '2118217027386',
        'SIF_Number'         => '748374',

        'account_title'      => 'JOYASREE SAHA',
        'customer_name'      => 'JOYASREE SAHA',

        'contact_no_1'       => '8801712823051',
        'mobile_number'      => '8801712823051',

        'email_1'            => 'deyrajib900@gmail.com',
        'email_address'      => 'deyrajib900@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137JOYDEBKUN',
        'account_number'     => '2509219012383',
        'SIF_Number'         => '1182350',

        'account_title'      => 'JOYDEB KUNDU',
        'customer_name'      => 'JOYDEB KUNDU',

        'contact_no_1'       => '8801715013644',
        'mobile_number'      => '8801715013644',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'TAKERHAT SME BR/SC',
        'acc_opening_branch' => 'TAKERHAT SME BR/SC',
    ],
    [
        'bp_id'              => 'IN137JOYPBARUA',
        'account_number'     => '2152213029292',
        'SIF_Number'         => '2445358',

        'account_title'      => 'JOY PRAKASH BARUA',
        'customer_name'      => 'JOY PRAKASH BARUA',

        'contact_no_1'       => '8801911051962',
        'mobile_number'      => '8801911051962',

        'email_1'            => 'joyprakashbarua@gmail.com',
        'email_address'      => 'joyprakashbarua@gmail.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137JUBAYEDPB',
        'account_number'     => '2118217038052',
        'SIF_Number'         => '2658969',

        'account_title'      => 'S M JUBAYED HOSSEN',
        'customer_name'      => 'S M JUBAYED HOSSEN',

        'contact_no_1'       => '8801866272682',
        'mobile_number'      => '8801866272682',

        'email_1'            => 'jubayedhossen66@gmail.com',
        'email_address'      => 'jubayedhossen66@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137KABIRPBL',
        'account_number'     => '2122216004091',
        'SIF_Number'         => '396480',

        'account_title'      => 'SARQUME MOHD KABIR',
        'customer_name'      => 'SARQUME MOHD KABIR',

        'contact_no_1'       => '8801714071124',
        'mobile_number'      => '8801714071124',

        'email_1'            => 'sarqumemkabir@gmail.com',
        'email_address'      => 'sarqumemkabir@gmail.com',

        'branch_name'        => 'BANGSHAL BRANCH',
        'acc_opening_branch' => 'BANGSHAL BRANCH',
    ],
    [
        'bp_id'              => 'IN137KABIRPRIM',
        'account_number'     => '2104219025962',
        'SIF_Number'         => '1862238',

        'account_title'      => 'MAHMOOD KABIR',
        'customer_name'      => 'MAHMOOD KABIR',

        'contact_no_1'       => '8801911367890',
        'mobile_number'      => '8801911367890',

        'email_1'            => 'mkmishu@yahoo.com',
        'email_address'      => 'mkmishu@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137KABUSHPRM',
        'account_number'     => '2126216013304',
        'SIF_Number'         => '2507589',

        'account_title'      => 'KHANDOKAR ABU SHAMS',
        'customer_name'      => 'KHANDOKAR ABU SHAMS',

        'contact_no_1'       => '8801716592170',
        'mobile_number'      => '8801716592170',

        'email_1'            => 'abu.shams@bb.org.bd',
        'email_address'      => 'abu.shams@bb.org.bd',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137KAISPRIME',
        'account_number'     => '2118218015679',
        'SIF_Number'         => '76381',

        'account_title'      => 'A N M KAMRUL ISLAM',
        'customer_name'      => 'A N M KAMRUL ISLAM',

        'contact_no_1'       => '8801713061674',
        'mobile_number'      => '8801713061674',

        'email_1'            => 'anmki@yahoo.com',
        'email_address'      => 'anmki@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137KAJINUSR',
        'account_number'     => '2117211013301',
        'SIF_Number'         => '1983408',

        'account_title'      => 'KAJI NUSRAT TARANNUM',
        'customer_name'      => 'KAJI NUSRAT TARANNUM',

        'contact_no_1'       => '8801915819596',
        'mobile_number'      => '8801915819596',

        'email_1'            => 'tannikn@yahoo.com',
        'email_address'      => 'tannikn@yahoo.com',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137KAMALUAPB',
        'account_number'     => '2128214015846',
        'SIF_Number'         => '2013942',

        'account_title'      => 'KAMAL UL ALAM',
        'customer_name'      => 'KAMAL UL ALAM',

        'contact_no_1'       => '8801713005214',
        'mobile_number'      => '8801713005214',

        'email_1'            => 'Yalam@yahoo.com',
        'email_address'      => 'Yalam@yahoo.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137KAMRULHPR',
        'account_number'     => '2118215038062',
        'SIF_Number'         => '2659528',

        'account_title'      => 'MD KAMRUL HASAN SHOHEL',
        'customer_name'      => 'MD KAMRUL HASAN SHOHEL',

        'contact_no_1'       => '8801740033515',
        'mobile_number'      => '8801740033515',

        'email_1'            => 'kamrulshohelazi@gmail.com',
        'email_address'      => 'kamrulshohelazi@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137KAMRUZPB',
        'account_number'     => '2104211021002',
        'SIF_Number'         => '1181813',

        'account_title'      => 'KAMRUZZAMAN KHAN',
        'customer_name'      => 'KAMRUZZAMAN KHAN',

        'contact_no_1'       => '8801787353535',
        'mobile_number'      => '8801787353535',

        'email_1'            => 'kzaman.khan@primebank.com.bd',
        'email_address'      => 'kzaman.khan@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137KANIZFPRM',
        'account_number'     => '2162211014104',
        'SIF_Number'         => '1672036',

        'account_title'      => 'KANIZ FATEMA RUNA',
        'customer_name'      => 'KANIZ FATEMA RUNA',

        'contact_no_1'       => '8801971809295',
        'mobile_number'      => '8801971809295',

        'email_1'            => 'runa2208@gmail.com',
        'email_address'      => 'runa2208@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137KANIZPRIM',
        'account_number'     => '2126218012809',
        'SIF_Number'         => '2408605',

        'account_title'      => 'MOST KANIZ FATIMA',
        'customer_name'      => 'MOST KANIZ FATIMA',

        'contact_no_1'       => '8801712829473',
        'mobile_number'      => '8801712829473',

        'email_1'            => 'kaniz.fatima7786@bb.org.bd',
        'email_address'      => 'kaniz.fatima7786@bb.org.bd',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137KANKANDPB',
        'account_number'     => '2120213026883',
        'SIF_Number'         => '2587461',

        'account_title'      => 'KANKA NANDY',
        'customer_name'      => 'KANKA NANDY',

        'contact_no_1'       => '8801715027199',
        'mobile_number'      => '8801715027199',

        'email_1'            => 'kankanandy@gmail.com',
        'email_address'      => 'kankanandy@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137KARTICPRM',
        'account_number'     => '2123217008108',
        'SIF_Number'         => '925680',

        'account_title'      => 'KARTICK CHANDRA CHAKROBORTY',
        'customer_name'      => 'KARTICK CHANDRA CHAKROBORTY',

        'contact_no_1'       => '8801712501492',
        'mobile_number'      => '8801712501492',

        'email_1'            => 'chakrobortykc@gmail.com',
        'email_address'      => 'chakrobortykc@gmail.com',

        'branch_name'        => 'JESSORE BRANCH',
        'acc_opening_branch' => 'JESSORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137KARTIKBPB',
        'account_number'     => '2104211024162',
        'SIF_Number'         => '1224954',

        'account_title'      => 'KARTTIK BANIK',
        'customer_name'      => 'KARTTIK BANIK',

        'contact_no_1'       => '8801715701003',
        'mobile_number'      => '8801715701003',

        'email_1'            => 'karttik_banik@yahoo.com',
        'email_address'      => 'karttik_banik@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137KASHFIAPB',
        'account_number'     => '2104216015685',
        'SIF_Number'         => '865834',

        'account_title'      => 'KASHFIA NEHEREEN',
        'customer_name'      => 'KASHFIA NEHEREEN',

        'contact_no_1'       => '8801720254573',
        'mobile_number'      => '8801720254573',

        'email_1'            => 'kn4prime@gmail.com',
        'email_address'      => 'kn4prime@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137KAZIFARPB',
        'account_number'     => '2125211023671',
        'SIF_Number'         => '1267459',

        'account_title'      => 'KAZI FARUQUE',
        'customer_name'      => 'KAZI FARUQUE',

        'contact_no_1'       => '8801715282251',
        'mobile_number'      => '8801715282251',

        'email_1'            => 'kazifaruque1@gmail.com',
        'email_address'      => 'kazifaruque1@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137KAZIFOOPB',
        'account_number'     => '2120216005959',
        'SIF_Number'         => '710679',

        'account_title'      => 'KAZI FOORKAN UDDIN',
        'customer_name'      => 'KAZI FOORKAN UDDIN',

        'contact_no_1'       => '8801818186307',
        'mobile_number'      => '8801818186307',

        'email_1'            => 'ku031401@primebank.com.bd',
        'email_address'      => 'ku031401@primebank.com.bd',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137KAZIKHUR',
        'account_number'     => '2206214013275',
        'SIF_Number'         => '2882615',

        'account_title'      => 'KAZI KHURSHID ZAHAN',
        'customer_name'      => 'KAZI KHURSHID ZAHAN',

        'contact_no_1'       => '8801943908409',
        'mobile_number'      => '8801943908409',

        'email_1'            => 'yasminhc@gmail.com',
        'email_address'      => 'yasminhc@gmail.com',

        'branch_name'        => 'KHULSHI BRANCH',
        'acc_opening_branch' => 'KHULSHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137KAZIMASPB',
        'account_number'     => '2107214003235',
        'SIF_Number'         => '993680',

        'account_title'      => 'KAZI MASUD RANA',
        'customer_name'      => 'KAZI MASUD RANA',

        'contact_no_1'       => '8801916136969',
        'mobile_number'      => '8801916136969',

        'email_1'            => 'ranakln@hotmail.com',
        'email_address'      => 'ranakln@hotmail.com',

        'branch_name'        => 'KHULNA BRANCH',
        'acc_opening_branch' => 'KHULNA BRANCH',
    ],
    [
        'bp_id'              => 'IN137KAZIRESH',
        'account_number'     => '2219217008608',
        'SIF_Number'         => '2390508',

        'account_title'      => 'KAZI RESHAD MAHBOOB',
        'customer_name'      => 'KAZI RESHAD MAHBOOB',

        'contact_no_1'       => '8801967809811',
        'mobile_number'      => '8801967809811',

        'email_1'            => 'reshad.mahboob@primebank.com.bd',
        'email_address'      => 'reshad.mahboob@primebank.com.bd',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137KAZIZAKIH',
        'account_number'     => '2118318016949',
        'SIF_Number'         => '74220',

        'account_title'      => 'KAZI ZAKIR HOSSAIN',
        'customer_name'      => 'KAZI ZAKIR HOSSAIN',

        'contact_no_1'       => '8801711533009',
        'mobile_number'      => '8801711533009',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137KHALAKTPB',
        'account_number'     => '2120211026885',
        'SIF_Number'         => '2587462',

        'account_title'      => 'KHALEDA AKHTER',
        'customer_name'      => 'KHALEDA AKHTER',

        'contact_no_1'       => '8801557268651',
        'mobile_number'      => '8801557268651',

        'email_1'            => 'pinkyiiuc5@gmail.com',
        'email_address'      => 'pinkyiiuc5@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137KHALEDAPB',
        'account_number'     => '3133216023856',
        'SIF_Number'         => '2522961',

        'account_title'      => 'KHALEDA BEGUM',
        'customer_name'      => 'KHALEDA BEGUM',

        'contact_no_1'       => '8801925632031',
        'mobile_number'      => '8801925632031',

        'email_1'            => 'uapalamin.mahmud@gmail.com',
        'email_address'      => 'uapalamin.mahmud@gmail.com',

        'branch_name'        => 'IBB MIRPUR BRANCH',
        'acc_opening_branch' => 'IBB MIRPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137KHALEDMPB',
        'account_number'     => '2118219036245',
        'SIF_Number'         => '2592697',

        'account_title'      => 'KHALED MUHAMMAD SAIFULLAH',
        'customer_name'      => 'KHALED MUHAMMAD SAIFULLAH',

        'contact_no_1'       => '8801711470462',
        'mobile_number'      => '8801711470462',

        'email_1'            => 'khaledsaifullah.md@gmail.com',
        'email_address'      => 'khaledsaifullah.md@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137KHALIDHPB',
        'account_number'     => '2104218039105',
        'SIF_Number'         => '2497792',

        'account_title'      => 'MD. KHALID HOSSAIN',
        'customer_name'      => 'MD. KHALID HOSSAIN',

        'contact_no_1'       => '8801758926232',
        'mobile_number'      => '8801758926232',

        'email_1'            => 'khalidbd2002@gmail.com',
        'email_address'      => 'khalidbd2002@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137KHANASIRU',
        'account_number'     => '2187215007600',
        'SIF_Number'         => '1099826',

        'account_title'      => 'KHANDAKER ASIRUL HOQUE',
        'customer_name'      => 'KHANDAKER ASIRUL HOQUE',

        'contact_no_1'       => '8801712838785',
        'mobile_number'      => '8801712838785',

        'email_1'            => 'shaki_mce@yahoo.com',
        'email_address'      => 'shaki_mce@yahoo.com',

        'branch_name'        => 'WARI BRANCH',
        'acc_opening_branch' => 'WARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137KHANJAVE',
        'account_number'     => '2132218028815',
        'SIF_Number'         => '2486249',

        'account_title'      => 'KHAN JAVED ANWAR',
        'customer_name'      => 'KHAN JAVED ANWAR',

        'contact_no_1'       => '8801819489784',
        'mobile_number'      => '8801819489784',

        'email_1'            => 'anwar.khanjaved@gmail.com',
        'email_address'      => 'anwar.khanjaved@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137KHANPL',
        'account_number'     => '2120212005292',
        'SIF_Number'         => '197587',

        'account_title'      => 'MUHAMMAD ZAMAL HOSSAIN KHAN',
        'customer_name'      => 'MUHAMMAD ZAMAL HOSSAIN KHAN',

        'contact_no_1'       => '8801911035132',
        'mobile_number'      => '8801911035132',

        'email_1'            => 'zamal_hossain@yahoo.com',
        'email_address'      => 'zamal_hossain@yahoo.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137KHNABIDPB',
        'account_number'     => '2138211015871',
        'SIF_Number'         => '2039635',

        'account_title'      => 'KHANDOKAR ABID IBRAHIM',
        'customer_name'      => 'KHANDOKAR ABID IBRAHIM',

        'contact_no_1'       => '8801616889050',
        'mobile_number'      => '8801616889050',

        'email_1'            => 'abid.ibra9407@gmail.com',
        'email_address'      => 'abid.ibra9407@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137KHUGESTPB',
        'account_number'     => '3108214023297',
        'SIF_Number'         => '2530196',

        'account_title'      => 'KHUGESTA NUR- E NAHARIN',
        'customer_name'      => 'KHUGESTA NUR- E NAHARIN',

        'contact_no_1'       => '8801919856570',
        'mobile_number'      => '8801919856570',

        'email_1'            => 'khugesta.naharin@gmail.com',
        'email_address'      => 'khugesta.naharin@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137KHURSHEDP',
        'account_number'     => '0021090016647',
        'SIF_Number'         => '1109648',

        'account_title'      => 'KHURSHED JAHAN',
        'customer_name'      => 'KHURSHED JAHAN',

        'contact_no_1'       => '8801715351454',
        'mobile_number'      => '8801715351454',

        'email_1'            => 'julkarnaeen@gmail.com',
        'email_address'      => 'julkarnaeen@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137KHWAZAMPB',
        'account_number'     => '2118217039142',
        'SIF_Number'         => '2707130',

        'account_title'      => 'KHWAZA MD REZWANUL HAQUE KHAN',
        'customer_name'      => 'KHWAZA MD REZWANUL HAQUE KHAN',

        'contact_no_1'       => '8801711285325',
        'mobile_number'      => '8801711285325',

        'email_1'            => 'rezwank@gmail.com',
        'email_address'      => 'rezwank@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137KHZAKIRPB',
        'account_number'     => '2118218042327',
        'SIF_Number'         => '233700',

        'account_title'      => 'KHANDAKER ZAKIR HOSSAIN',
        'customer_name'      => 'KHANDAKER ZAKIR HOSSAIN',

        'contact_no_1'       => '8801711220111',
        'mobile_number'      => '8801711220111',

        'email_1'            => 'dr.zakir_sdc@yahoo.com',
        'email_address'      => 'dr.zakir_sdc@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137KISHORKPB',
        'account_number'     => '2118213035250',
        'SIF_Number'         => '2543440',

        'account_title'      => 'KISHOR KUMER PAUL',
        'customer_name'      => 'KISHOR KUMER PAUL',

        'contact_no_1'       => '8801915930081',
        'mobile_number'      => '8801915930081',

        'email_1'            => 'paulkishorkumer@gmail.com',
        'email_address'      => 'paulkishorkumer@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137KKHALEDPB',
        'account_number'     => '2122215004985',
        'SIF_Number'         => '726696',

        'account_title'      => 'KAZI KHALED BIN SAYEED',
        'customer_name'      => 'KAZI KHALED BIN SAYEED',

        'contact_no_1'       => '8801714224277',
        'mobile_number'      => '8801714224277',

        'email_1'            => 'shantokazishanto@gmail.com',
        'email_address'      => 'shantokazishanto@gmail.com',

        'branch_name'        => 'BANGSHAL BRANCH',
        'acc_opening_branch' => 'BANGSHAL BRANCH',
    ],
    [
        'bp_id'              => 'IN137KKOBRAPRM',
        'account_number'     => '2104219038734',
        'SIF_Number'         => '2473821',

        'account_title'      => 'KHADIZATUL KOBRA',
        'customer_name'      => 'KHADIZATUL KOBRA',

        'contact_no_1'       => '8801722530426',
        'mobile_number'      => '8801722530426',

        'email_1'            => 'khadiza30@gmail.com',
        'email_address'      => 'khadiza30@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137KMASUDRPB',
        'account_number'     => '2138217021641',
        'SIF_Number'         => '2601754',

        'account_title'      => 'KHONDOKER MASUDUR RAHMAN',
        'customer_name'      => 'KHONDOKER MASUDUR RAHMAN',

        'contact_no_1'       => '8801730045478',
        'mobile_number'      => '8801730045478',

        'email_1'            => 'kmrahman2002@yahoo.com',
        'email_address'      => 'kmrahman2002@yahoo.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137KMRAZIBPB',
        'account_number'     => '2104298032128',
        'SIF_Number'         => '2049568',

        'account_title'      => 'K.M. RAZIBUL ISLAM',
        'customer_name'      => 'K.M. RAZIBUL ISLAM',

        'contact_no_1'       => '8801734122584',
        'mobile_number'      => '8801734122584',

        'email_1'            => 'kmrazibulislam2013@gmail.com',
        'email_address'      => 'kmrazibulislam2013@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137KMRUZMNPB',
        'account_number'     => '2132213025162',
        'SIF_Number'         => '1927456',

        'account_title'      => 'MD. KAMRUZZAMAN',
        'customer_name'      => 'MD. KAMRUZZAMAN',

        'contact_no_1'       => '8801712977843',
        'mobile_number'      => '8801712977843',

        'email_1'            => 'babuzamanerp@gmail.com',
        'email_address'      => 'babuzamanerp@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137KMSAIFUPB',
        'account_number'     => '2118216005375',
        'SIF_Number'         => '1771445',

        'account_title'      => 'KHALED MD SAIFUL HAQUE',
        'customer_name'      => 'KHALED MD SAIFUL HAQUE',

        'contact_no_1'       => '8801715106500',
        'mobile_number'      => '8801715106500',

        'email_1'            => 'khaledhaque1@gmail.com',
        'email_address'      => 'khaledhaque1@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137KNRUNUPB',
        'account_number'     => '2125215031100',
        'SIF_Number'         => '2553574',

        'account_title'      => 'KAMRUN NAHER RUNU',
        'customer_name'      => 'KAMRUN NAHER RUNU',

        'contact_no_1'       => '8801894084300',
        'mobile_number'      => '8801894084300',

        'email_1'            => 'aldeencorporatin@gmail.com',
        'email_address'      => 'aldeencorporatin@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137KOBIABDPB',
        'account_number'     => '2118214034708',
        'SIF_Number'         => '2511730',

        'account_title'      => 'MD KOBIR HOSSAIN AND MD ABDUR RAKIB',
        'customer_name'      => 'MD KOBIR HOSSAIN AND MD ABDUR RAKIB',

        'contact_no_1'       => '8801719168015',
        'mobile_number'      => '8801719168015',

        'email_1'            => 'hossainkobir394@gmail.com',
        'email_address'      => 'hossainkobir394@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137KONBARUA',
        'account_number'     => '2184215020705',
        'SIF_Number'         => '2302034',

        'account_title'      => 'KONICA BARUA',
        'customer_name'      => 'KONICA BARUA',

        'contact_no_1'       => '8801979350500',
        'mobile_number'      => '8801979350500',

        'email_1'            => 'sat.accessories@yahoo.com',
        'email_address'      => 'sat.accessories@yahoo.com',

        'branch_name'        => 'OXYGEN MORE BRANCH',
        'acc_opening_branch' => 'OXYGEN MORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137KOWSERMUN',
        'account_number'     => '2110215021850',
        'SIF_Number'         => '2432204',

        'account_title'      => 'MOHAMMAD KOWSER MUNSHI',
        'customer_name'      => 'MOHAMMAD KOWSER MUNSHI',

        'contact_no_1'       => '8801552406147',
        'mobile_number'      => '8801552406147',

        'email_1'            => 'kowser.munshi@gmail.com',
        'email_address'      => 'kowser.munshi@gmail.com',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137KPARVINPB',
        'account_number'     => '2152216002668',
        'SIF_Number'         => '380085',

        'account_title'      => 'MST. KHURSHIDA PARVIN',
        'customer_name'      => 'MST. KHURSHIDA PARVIN',

        'contact_no_1'       => '8801766447777',
        'mobile_number'      => '8801766447777',

        'email_1'            => 'khushi2019@gmail.com',
        'email_address'      => 'khushi2019@gmail.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137KRISHNAPB',
        'account_number'     => '2169211004638',
        'SIF_Number'         => '1349016',

        'account_title'      => 'KRISHNA SAHA',
        'customer_name'      => 'KRISHNA SAHA',

        'contact_no_1'       => '8801717506854',
        'mobile_number'      => '8801717506854',

        'email_1'            => 'krishcsebd@gmail.com',
        'email_address'      => 'krishcsebd@gmail.com',

        'branch_name'        => 'DINAJPUR BRANCH',
        'acc_opening_branch' => 'DINAJPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137KRISHNAR',
        'account_number'     => '2104218025740',
        'SIF_Number'         => '1371764',

        'account_title'      => 'RAM KRISHNA ROY',
        'customer_name'      => 'RAM KRISHNA ROY',

        'contact_no_1'       => '8801552456282',
        'mobile_number'      => '8801552456282',

        'email_1'            => 'ramkrishnaroy@gmail.com',
        'email_address'      => 'ramkrishnaroy@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137KRISHPRM',
        'account_number'     => '2110211003259',
        'SIF_Number'         => '272924',

        'account_title'      => 'BIMAL KRISHNA PAUL',
        'customer_name'      => 'BIMAL KRISHNA PAUL',

        'contact_no_1'       => '8801710287862',
        'mobile_number'      => '8801710287862',

        'email_1'            => 'paulbimal@yahoo.com',
        'email_address'      => 'paulbimal@yahoo.com',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137KSALAHUPB',
        'account_number'     => '2126211006320',
        'SIF_Number'         => '854064',

        'account_title'      => 'KAZI SALAH UDDIN',
        'customer_name'      => 'KAZI SALAH UDDIN',

        'contact_no_1'       => '8801730444981',
        'mobile_number'      => '8801730444981',

        'email_1'            => 'ksuksa@yahoo.com',
        'email_address'      => 'ksuksa@yahoo.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137KSANJANA',
        'account_number'     => '2104212037162',
        'SIF_Number'         => '2322275',

        'account_title'      => 'KAZI SANJANA TAUFIQUE',
        'customer_name'      => 'KAZI SANJANA TAUFIQUE',

        'contact_no_1'       => '8801756009957',
        'mobile_number'      => '8801756009957',

        'email_1'            => 'sanjana_toufik12@live.com',
        'email_address'      => 'sanjana_toufik12@live.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137KUHUMUPRM',
        'account_number'     => '2125217030621',
        'SIF_Number'         => '2523999',

        'account_title'      => 'KUHU MUTSUDDY',
        'customer_name'      => 'KUHU MUTSUDDY',

        'contact_no_1'       => '8801816357156',
        'mobile_number'      => '8801816357156',

        'email_1'            => 'kuhumutsuddy210@gmail.com',
        'email_address'      => 'kuhumutsuddy210@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137KUNTOLAPB',
        'account_number'     => '2132215008455',
        'SIF_Number'         => '53862',

        'account_title'      => 'KUNTOLA DEY SARKER',
        'customer_name'      => 'KUNTOLA DEY SARKER',

        'contact_no_1'       => '8801715660030',
        'mobile_number'      => '8801715660030',

        'email_1'            => 'kuntolabd@gmail.com',
        'email_address'      => 'kuntolabd@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137KUSMAKTPB',
        'account_number'     => '2152211015343',
        'SIF_Number'         => '380059',

        'account_title'      => 'KUSHUM AKTER',
        'customer_name'      => 'KUSHUM AKTER',

        'contact_no_1'       => '8801819331961',
        'mobile_number'      => '8801819331961',

        'email_1'            => 'ka111201@primebank.com.bd',
        'email_address'      => 'ka111201@primebank.com.bd',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137LATIKAPB',
        'account_number'     => '2141211003666',
        'SIF_Number'         => '683347',

        'account_title'      => 'LATIKA YESMIN',
        'customer_name'      => 'LATIKA YESMIN',

        'contact_no_1'       => '8801711334605',
        'mobile_number'      => '8801711334605',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'PANTHAPATH BRANCH',
        'acc_opening_branch' => 'PANTHAPATH BRANCH',
    ],
    [
        'bp_id'              => 'IN137LAYLAKTPB',
        'account_number'     => '2186214018765',
        'SIF_Number'         => '2166247',

        'account_title'      => 'LAYLA AKTER',
        'customer_name'      => 'LAYLA AKTER',

        'contact_no_1'       => '8801517107231',
        'mobile_number'      => '8801517107231',

        'email_1'            => 'chemistazhar@gmail.com',
        'email_address'      => 'chemistazhar@gmail.com',

        'branch_name'        => 'JATRABARI BRANCH',
        'acc_opening_branch' => 'JATRABARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137LAZIMHCPB',
        'account_number'     => '2118218035313',
        'SIF_Number'         => '2159892',

        'account_title'      => 'LAZIMZA HASANAT CHOWDHURY',
        'customer_name'      => 'LAZIMZA HASANAT CHOWDHURY',

        'contact_no_1'       => '8801711690167',
        'mobile_number'      => '8801711690167',

        'email_1'            => 'lazima67@gmail.com',
        'email_address'      => 'lazima67@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137LIZAPRIME',
        'account_number'     => '2118219011792',
        'SIF_Number'         => '364041',

        'account_title'      => 'LIZA FAHMIDA',
        'customer_name'      => 'LIZA FAHMIDA',

        'contact_no_1'       => '8801789476049',
        'mobile_number'      => '8801789476049',

        'email_1'            => 'liza_fahmida@yahoo.com',
        'email_address'      => 'liza_fahmida@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137LUBNACHPB',
        'account_number'     => '2162218017860',
        'SIF_Number'         => '2017107',

        'account_title'      => 'LUBNA CHOWDHURY',
        'customer_name'      => 'LUBNA CHOWDHURY',

        'contact_no_1'       => '8801817534915',
        'mobile_number'      => '8801817534915',

        'email_1'            => 'chowdhurylubna1971@gmail.com',
        'email_address'      => 'chowdhurylubna1971@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137LUTFABEG',
        'account_number'     => '2118212033728',
        'SIF_Number'         => '2478775',

        'account_title'      => 'LUTFA BEGUM',
        'customer_name'      => 'LUTFA BEGUM',

        'contact_no_1'       => '8801717121080',
        'mobile_number'      => '8801717121080',

        'email_1'            => 'lutfa20450@gmail.com',
        'email_address'      => 'lutfa20450@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137LUTFULPB',
        'account_number'     => '2104211020433',
        'SIF_Number'         => '8488',

        'account_title'      => 'MD. LUTFUL HOQUE',
        'customer_name'      => 'MD. LUTFUL HOQUE',

        'contact_no_1'       => '8801715027979',
        'mobile_number'      => '8801715027979',

        'email_1'            => 'lutfulfbl@gmail.com',
        'email_address'      => 'lutfulfbl@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAALAMPRM',
        'account_number'     => '2125211030867',
        'SIF_Number'         => '1668554',

        'account_title'      => 'MD MAHBUBUL ALAM',
        'customer_name'      => 'MD MAHBUBUL ALAM',

        'contact_no_1'       => '8801755554411',
        'mobile_number'      => '8801755554411',

        'email_1'            => 'rafelalam@gmail.com',
        'email_address'      => 'rafelalam@gmail.com',

        'branch_name'        => 'NIKUNJA SUB-BRANCH',
        'acc_opening_branch' => 'NIKUNJA SUB-BRANCH',
    ],
    [
        'bp_id'              => 'IN137MABDURKPB',
        'account_number'     => '2104217007152',
        'SIF_Number'         => '1636',

        'account_title'      => 'MD. ABDUR RAHMAN KHAN',
        'customer_name'      => 'MD. ABDUR RAHMAN KHAN',

        'contact_no_1'       => '8801713009688',
        'mobile_number'      => '8801713009688',

        'email_1'            => 'khanfcma@gmail.com',
        'email_address'      => 'khanfcma@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MABIAPB',
        'account_number'     => '2118211040089',
        'SIF_Number'         => '2730461',

        'account_title'      => 'MABIA AKTER',
        'customer_name'      => 'MABIA AKTER',

        'contact_no_1'       => '8801712652972',
        'mobile_number'      => '8801712652972',

        'email_1'            => 'akter.mabia1982@gmail.com',
        'email_address'      => 'akter.mabia1982@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MABIAPRIM',
        'account_number'     => '2118211040089',
        'SIF_Number'         => '2730461',

        'account_title'      => 'MABIA AKTER',
        'customer_name'      => 'MABIA AKTER',

        'contact_no_1'       => '8801712652972',
        'mobile_number'      => '8801712652972',

        'email_1'            => 'akter.mabia1982@gmail.com',
        'email_address'      => 'akter.mabia1982@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MABINSHA',
        'account_number'     => '2132213026236',
        'SIF_Number'         => '2356582',

        'account_title'      => 'MASHFEE BINTE SHAMS',
        'customer_name'      => 'MASHFEE BINTE SHAMS',

        'contact_no_1'       => '8801753714370',
        'mobile_number'      => '8801753714370',

        'email_1'            => 'mashfee_shams@yahoo.com',
        'email_address'      => 'mashfee_shams@yahoo.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MADKHAPRM',
        'account_number'     => '2145213025100',
        'SIF_Number'         => '2421396',

        'account_title'      => 'MASUM AMANUDDIN KHAN',
        'customer_name'      => 'MASUM AMANUDDIN KHAN',

        'contact_no_1'       => '8801781111311',
        'mobile_number'      => '8801781111311',

        'email_1'            => 'masum2u@gmail.com',
        'email_address'      => 'masum2u@gmail.com',

        'branch_name'        => 'LALDIGHI EAST BRANCH',
        'acc_opening_branch' => 'LALDIGHI EAST BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAFIAKHPB',
        'account_number'     => '2118211042159',
        'SIF_Number'         => '1725314',

        'account_title'      => 'MOST. MAFIA KHATUN',
        'customer_name'      => 'MOST. MAFIA KHATUN',

        'contact_no_1'       => '8801755940194',
        'mobile_number'      => '8801755940194',

        'email_1'            => 'mahiamunny1@gmail.com',
        'email_address'      => 'mahiamunny1@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAFZALRPB',
        'account_number'     => '2104216027342',
        'SIF_Number'         => '3718',

        'account_title'      => 'MOHAMMAD AFZALUR RAHMAN KHAN',
        'customer_name'      => 'MOHAMMAD AFZALUR RAHMAN KHAN',

        'contact_no_1'       => '8801734600600',
        'mobile_number'      => '8801734600600',

        'email_1'            => 'afzal@primebank.com.bd',
        'email_address'      => 'afzal@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHABUBAP',
        'account_number'     => '2126215003975',
        'SIF_Number'         => '1768202',

        'account_title'      => 'MD. MAHABUB ALAM SHIKDER',
        'customer_name'      => 'MD. MAHABUB ALAM SHIKDER',

        'contact_no_1'       => '8801916645054',
        'mobile_number'      => '8801916645054',

        'email_1'            => 'mahabubalamsikder@gmail.com',
        'email_address'      => 'mahabubalamsikder@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137MAHAMUDH',
        'account_number'     => '2170218007622',
        'SIF_Number'         => '1476893',

        'account_title'      => 'MD. MAHAMUD HOSSAIN',
        'customer_name'      => 'MD. MAHAMUD HOSSAIN',

        'contact_no_1'       => '8801911274690',
        'mobile_number'      => '8801911274690',

        'email_1'            => 'mmhossain@primebank.com.bd',
        'email_address'      => 'mmhossain@primebank.com.bd',

        'branch_name'        => 'GARIB-E-NEWAZ AVE. BR',
        'acc_opening_branch' => 'GARIB-E-NEWAZ AVE. BR',
    ],
    [
        'bp_id'              => 'IN137MAHBUBHPB',
        'account_number'     => '2104215014929',
        'SIF_Number'         => '1792690',

        'account_title'      => 'M M MAHBUB HASAN',
        'customer_name'      => 'M M MAHBUB HASAN',

        'contact_no_1'       => '8801716569212',
        'mobile_number'      => '8801716569212',

        'email_1'            => 'mmmahbubhasan111@gmail.com',
        'email_address'      => 'mmmahbubhasan111@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHBUBPRM',
        'account_number'     => '2192218010273',
        'SIF_Number'         => '1937841',

        'account_title'      => 'MST MAHBUB ARA BEGUM',
        'customer_name'      => 'MST MAHBUB ARA BEGUM',

        'contact_no_1'       => '8801718201836',
        'mobile_number'      => '8801718201836',

        'email_1'            => 'nn.tandra@gmail.com',
        'email_address'      => 'nn.tandra@gmail.com',

        'branch_name'        => 'BANASREE BRANCH',
        'acc_opening_branch' => 'BANASREE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHBUBRPB',
        'account_number'     => '2186216000001',
        'SIF_Number'         => '861472',

        'account_title'      => 'MOHAMMAD MAHBUBUR RAHMAN SARKER',
        'customer_name'      => 'MOHAMMAD MAHBUBUR RAHMAN SARKER',

        'contact_no_1'       => '8801716350325',
        'mobile_number'      => '8801716350325',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'JATRABARI BRANCH',
        'acc_opening_branch' => 'JATRABARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHBUBUL',
        'account_number'     => '2118213042918',
        'SIF_Number'         => '2873295',

        'account_title'      => 'MOHAMMED MAHBUBUL ALAM',
        'customer_name'      => 'MOHAMMED MAHBUBUL ALAM',

        'contact_no_1'       => '8801711567210',
        'mobile_number'      => '8801711567210',

        'email_1'            => 'alamm2@gmail.com',
        'email_address'      => 'alamm2@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHFUZAPB',
        'account_number'     => '2162218000239',
        'SIF_Number'         => '39563',

        'account_title'      => 'FERDOUSI MAHFUZA',
        'customer_name'      => 'FERDOUSI MAHFUZA',

        'contact_no_1'       => '8801711441822',
        'mobile_number'      => '8801711441822',

        'email_1'            => 'ferdoushimahfuza@gmail.com',
        'email_address'      => 'ferdoushimahfuza@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHFUZPB',
        'account_number'     => '2104219023942',
        'SIF_Number'         => '363483',

        'account_title'      => 'MAHFUZ IKBAL',
        'customer_name'      => 'MAHFUZ IKBAL',

        'contact_no_1'       => '8801714101080',
        'mobile_number'      => '8801714101080',

        'email_1'            => 'peyal@hotmail.com',
        'email_address'      => 'peyal@hotmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHFUZUPB',
        'account_number'     => '2169219012277',
        'SIF_Number'         => '2583949',

        'account_title'      => 'MD. MAHFUZUR RAHMAN',
        'customer_name'      => 'MD. MAHFUZUR RAHMAN',

        'contact_no_1'       => '8801707009383',
        'mobile_number'      => '8801707009383',

        'email_1'            => 'r.gitla@gmail.com',
        'email_address'      => 'r.gitla@gmail.com',

        'branch_name'        => 'DINAJPUR BRANCH',
        'acc_opening_branch' => 'DINAJPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHFUZURP',
        'account_number'     => '2118219039447',
        'SIF_Number'         => '2548944',

        'account_title'      => 'MOHAMMAD MAHFUZUR RAHMAN KHAN',
        'customer_name'      => 'MOHAMMAD MAHFUZUR RAHMAN KHAN',

        'contact_no_1'       => '8801714097273',
        'mobile_number'      => '8801714097273',

        'email_1'            => 'mahfuzur.khan@syngenta.com',
        'email_address'      => 'mahfuzur.khan@syngenta.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHINAPRM',
        'account_number'     => '2179215026899',
        'SIF_Number'         => '2624882',

        'account_title'      => 'MAHIN AFROZ',
        'customer_name'      => 'MAHIN AFROZ',

        'contact_no_1'       => '8801725285283',
        'mobile_number'      => '8801725285283',

        'email_1'            => 'mahinafroz03@gmail.com',
        'email_address'      => 'mahinafroz03@gmail.com',

        'branch_name'        => 'ISHWARDI BRANCH',
        'acc_opening_branch' => 'ISHWARDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHINZAPB',
        'account_number'     => '2164216022050',
        'SIF_Number'         => '1483077',

        'account_title'      => 'MAHIN ZAKARIA ALAM',
        'customer_name'      => 'MAHIN ZAKARIA ALAM',

        'contact_no_1'       => '8801792527195',
        'mobile_number'      => '8801792527195',

        'email_1'            => 'humairaza@gmail.com',
        'email_address'      => 'humairaza@gmail.com',

        'branch_name'        => 'PALLABI BRANCH',
        'acc_opening_branch' => 'PALLABI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHIULPBL',
        'account_number'     => '0021010022383',
        'SIF_Number'         => '1262260',

        'account_title'      => 'SK. MAHIUL ISLAM',
        'customer_name'      => 'SK. MAHIUL ISLAM',

        'contact_no_1'       => '8801711080497',
        'mobile_number'      => '8801711080497',

        'email_1'            => 'mahi000303@yahoo.com',
        'email_address'      => 'mahi000303@yahoo.com',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHMOODPB',
        'account_number'     => '2135211034057',
        'SIF_Number'         => '2316221',

        'account_title'      => 'ASM MAHMOOD HASAN',
        'customer_name'      => 'ASM MAHMOOD HASAN',

        'contact_no_1'       => '8801769003261',
        'mobile_number'      => '8801769003261',

        'email_1'            => 'mahmood3261@yahoo.com',
        'email_address'      => 'mahmood3261@yahoo.com',

        'branch_name'        => 'PRAGATI SARANI BRANCH',
        'acc_opening_branch' => 'PRAGATI SARANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHMUDA',
        'account_number'     => '2118218023475',
        'SIF_Number'         => '2056299',

        'account_title'      => 'MAHMUDA AKTER',
        'customer_name'      => 'MAHMUDA AKTER',

        'contact_no_1'       => '8801911740140',
        'mobile_number'      => '8801911740140',

        'email_1'            => 'makter02@yahoo.com',
        'email_address'      => 'makter02@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHMUDAPB',
        'account_number'     => '2141218016173',
        'SIF_Number'         => '1187904',

        'account_title'      => 'MST. MAHMUDA KHATUN',
        'customer_name'      => 'MST. MAHMUDA KHATUN',

        'contact_no_1'       => '8801931403094',
        'mobile_number'      => '8801931403094',

        'email_1'            => 'mahmuda@mertaz.com.bd',
        'email_address'      => 'mahmuda@mertaz.com.bd',

        'branch_name'        => 'PANTHAPATH BRANCH',
        'acc_opening_branch' => 'PANTHAPATH BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHMUDPRM',
        'account_number'     => '2118216038178',
        'SIF_Number'         => '2665623',

        'account_title'      => 'MD. MAHMUD HASSAN',
        'customer_name'      => 'MD. MAHMUD HASSAN',

        'contact_no_1'       => '8801711083001',
        'mobile_number'      => '8801711083001',

        'email_1'            => 'rume.mahmudhassan@gmail.com',
        'email_address'      => 'rume.mahmudhassan@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHMUHQPB',
        'account_number'     => '2117213006385',
        'SIF_Number'         => '137846',

        'account_title'      => 'MD. MAHMUDUL HOQUE',
        'customer_name'      => 'MD. MAHMUDUL HOQUE',

        'contact_no_1'       => '008180065336082',
        'mobile_number'      => '008180065336082',

        'email_1'            => 'danny1109jp@icloud.com',
        'email_address'      => 'danny1109jp@icloud.com',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHRAHPB',
        'account_number'     => '2132216015376',
        'SIF_Number'         => '1383911',

        'account_title'      => 'MAHBUBUR RAHMAN',
        'customer_name'      => 'MAHBUBUR RAHMAN',

        'contact_no_1'       => '8801915820804',
        'mobile_number'      => '8801915820804',

        'email_1'            => 'sumon_jnu@yahoo.com',
        'email_address'      => 'sumon_jnu@yahoo.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAHZAMPRM',
        'account_number'     => '2104211032503',
        'SIF_Number'         => '2076336',

        'account_title'      => 'MD MAHAMUD ZAMAN',
        'customer_name'      => 'MD MAHAMUD ZAMAN',

        'contact_no_1'       => '8801738756662',
        'mobile_number'      => '8801738756662',

        'email_1'            => 'mz.shawon@gmail.com',
        'email_address'      => 'mz.shawon@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAIDUISPB',
        'account_number'     => '2104213039019',
        'SIF_Number'         => '2491998',

        'account_title'      => 'MD. MAIDUL ISLAM',
        'customer_name'      => 'MD. MAIDUL ISLAM',

        'contact_no_1'       => '8801717627999',
        'mobile_number'      => '8801717627999',

        'email_1'            => 'maidul_sarker@yahoo.com',
        'email_address'      => 'maidul_sarker@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAISPRIME',
        'account_number'     => '2105215001933',
        'SIF_Number'         => '222436',

        'account_title'      => 'MOHAMMAD ASHIKUL ISLAM',
        'customer_name'      => 'MOHAMMAD ASHIKUL ISLAM',

        'contact_no_1'       => '8801709648756',
        'mobile_number'      => '8801709648756',

        'email_1'            => 'mi012902@primebank.com.bd',
        'email_address'      => 'mi012902@primebank.com.bd',

        'branch_name'        => 'KHATUNGONJ BRANCH',
        'acc_opening_branch' => 'KHATUNGONJ BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAJEDABPB',
        'account_number'     => '2162217015881',
        'SIF_Number'         => '2330575',

        'account_title'      => 'MST. MAJEDA BEGUM',
        'customer_name'      => 'MST. MAJEDA BEGUM',

        'contact_no_1'       => '8801732293213',
        'mobile_number'      => '8801732293213',

        'email_1'            => 'irinsultana88@gmail.com',
        'email_address'      => 'irinsultana88@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAJHEEPB',
        'account_number'     => '2104215006198',
        'SIF_Number'         => '1816410',

        'account_title'      => 'ABDUR RAZZAK MAJHEE',
        'customer_name'      => 'ABDUR RAZZAK MAJHEE',

        'contact_no_1'       => '8801730711430',
        'mobile_number'      => '8801730711430',

        'email_1'            => 'majheeabdurrazzak@yahoo.com',
        'email_address'      => 'majheeabdurrazzak@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAKSUDURN',
        'account_number'     => '2118213031465',
        'SIF_Number'         => '2383432',

        'account_title'      => 'MAKSUDUR RAHMAN',
        'customer_name'      => 'MAKSUDUR RAHMAN',

        'contact_no_1'       => '8801707465363',
        'mobile_number'      => '8801707465363',

        'email_1'            => 'maksudurrahman259@gmail.com',
        'email_address'      => 'maksudurrahman259@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MALHASPB',
        'account_number'     => '2138216017228',
        'SIF_Number'         => '2113203',

        'account_title'      => 'MALIHA HASAN',
        'customer_name'      => 'MALIHA HASAN',

        'contact_no_1'       => '8801732792773',
        'mobile_number'      => '8801732792773',

        'email_1'            => 'malihahasan612@gmail.com',
        'email_address'      => 'malihahasan612@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MALIHAHPB',
        'account_number'     => '2138216017228',
        'SIF_Number'         => '2113203',

        'account_title'      => 'MALIHA HASAN',
        'customer_name'      => 'MALIHA HASAN',

        'contact_no_1'       => '8801732792773',
        'mobile_number'      => '8801732792773',

        'email_1'            => 'malihahasan612@gmail.com',
        'email_address'      => 'malihahasan612@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MALMAMUN',
        'account_number'     => '2104211039342',
        'SIF_Number'         => '2469710',

        'account_title'      => 'MOHAMMAD ABDULLAH-AL-MAMUN',
        'customer_name'      => 'MOHAMMAD ABDULLAH-AL-MAMUN',

        'contact_no_1'       => '8801911711469',
        'mobile_number'      => '8801911711469',

        'email_1'            => 'abdullah_mamun@rocketmail.com',
        'email_address'      => 'abdullah_mamun@rocketmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MALOYPRM',
        'account_number'     => '2155216000271',
        'SIF_Number'         => '637210',

        'account_title'      => 'MALOY CHANDRA BASAK',
        'customer_name'      => 'MALOY CHANDRA BASAK',

        'contact_no_1'       => '8801711668881',
        'mobile_number'      => '8801711668881',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'TANGAIL BRANCH',
        'acc_opening_branch' => 'TANGAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAMAMUNPB',
        'account_number'     => '2104211040028',
        'SIF_Number'         => '2555885',

        'account_title'      => 'MOHAMMAD ABDULLAH AL MAMUN',
        'customer_name'      => 'MOHAMMAD ABDULLAH AL MAMUN',

        'contact_no_1'       => '8801977069206',
        'mobile_number'      => '8801977069206',

        'email_1'            => 'almamin690011@gmail.com',
        'email_address'      => 'almamin690011@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAMOMENPB',
        'account_number'     => '2174215003505',
        'SIF_Number'         => '1278969',

        'account_title'      => 'MOHAMMAD ABDUL MOMEN',
        'customer_name'      => 'MOHAMMAD ABDUL MOMEN',

        'contact_no_1'       => '8801794413465',
        'mobile_number'      => '8801794413465',

        'email_1'            => 'mamomen2019@gmail.com',
        'email_address'      => 'mamomen2019@gmail.com',

        'branch_name'        => 'MYMENSINGH BRANCH',
        'acc_opening_branch' => 'MYMENSINGH BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAMOONPRM',
        'account_number'     => '3108211009927',
        'SIF_Number'         => '1396605',

        'account_title'      => 'MD MAMOONUR RASHID',
        'customer_name'      => 'MD MAMOONUR RASHID',

        'contact_no_1'       => '8801708107018',
        'mobile_number'      => '8801708107018',

        'email_1'            => 'mamoonur.rashid@bb.org.bd',
        'email_address'      => 'mamoonur.rashid@bb.org.bd',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAMRASHPB',
        'account_number'     => '2126217013261',
        'SIF_Number'         => '2501103',

        'account_title'      => 'MD. MAMUNUR RASHID',
        'customer_name'      => 'MD. MAMUNUR RASHID',

        'contact_no_1'       => '8801841922585',
        'mobile_number'      => '8801841922585',

        'email_1'            => 'mdmamunur.rashid@hotmail.com',
        'email_address'      => 'mdmamunur.rashid@hotmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137MAMTAJPB',
        'account_number'     => '3134212007415',
        'SIF_Number'         => '8027898',

        'account_title'      => 'AYESHA MAMTAJ',
        'customer_name'      => 'AYESHA MAMTAJ',

        'contact_no_1'       => '8801581231660',
        'mobile_number'      => '8801581231660',

        'email_1'            => '92.aysha.sky@gmail.com',
        'email_address'      => '92.aysha.sky@gmail.com',

        'branch_name'        => 'IBB O.R. NIZAM ROAD BR',
        'acc_opening_branch' => 'IBB O.R. NIZAM ROAD BR',
    ],
    [
        'bp_id'              => 'IN137MAMUNRSPB',
        'account_number'     => '2104214009532',
        'SIF_Number'         => '737843',

        'account_title'      => 'MD MAMUNUR RASHID',
        'customer_name'      => 'MD MAMUNUR RASHID',

        'contact_no_1'       => '8801819159670',
        'mobile_number'      => '8801819159670',

        'email_1'            => 'mamuniup7@gmail.com',
        'email_address'      => 'mamuniup7@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAMUNURRA',
        'account_number'     => '2123215010767',
        'SIF_Number'         => '560280',

        'account_title'      => 'MOHAMMED MAMUNUR RASHID',
        'customer_name'      => 'MOHAMMED MAMUNUR RASHID',

        'contact_no_1'       => '8801730000079',
        'mobile_number'      => '8801730000079',

        'email_1'            => 'mamun.rasid2004@gmail.com',
        'email_address'      => 'mamun.rasid2004@gmail.com',

        'branch_name'        => 'JESSORE BRANCH',
        'acc_opening_branch' => 'JESSORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAMURASPB',
        'account_number'     => '2104213021819',
        'SIF_Number'         => '1443139',

        'account_title'      => 'MAMUNUR RASHID',
        'customer_name'      => 'MAMUNUR RASHID',

        'contact_no_1'       => '8801717401925',
        'mobile_number'      => '8801717401925',

        'email_1'            => 'ur.mamun06@gmail.com',
        'email_address'      => 'ur.mamun06@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MANGALPB',
        'account_number'     => '2104211043873',
        'SIF_Number'         => '2795267',

        'account_title'      => 'MANGAL CHANDRA DATTA',
        'customer_name'      => 'MANGAL CHANDRA DATTA',

        'contact_no_1'       => '8801730712655',
        'mobile_number'      => '8801730712655',

        'email_1'            => 'mangaldatta2016@gmail.com',
        'email_address'      => 'mangaldatta2016@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MANIAPB',
        'account_number'     => '2170215022616',
        'SIF_Number'         => '1954789',

        'account_title'      => 'MANIA MURSALIN RAHMAN ANUSHKA',
        'customer_name'      => 'MANIA MURSALIN RAHMAN ANUSHKA',

        'contact_no_1'       => '8801711537702',
        'mobile_number'      => '8801711537702',

        'email_1'            => 'mursalin050804@gmail.com',
        'email_address'      => 'mursalin050804@gmail.com',

        'branch_name'        => 'GARIB-E-NEWAZ AVE. BR',
        'acc_opening_branch' => 'GARIB-E-NEWAZ AVE. BR',
    ],
    [
        'bp_id'              => 'IN137MANIKPRIM',
        'account_number'     => '2178211030649',
        'SIF_Number'         => '2431998',

        'account_title'      => 'MANIK BARUA',
        'customer_name'      => 'MANIK BARUA',

        'contact_no_1'       => '8801861433759',
        'mobile_number'      => '8801861433759',

        'email_1'            => 'manikbarua55@gmail.com',
        'email_address'      => 'manikbarua55@gmail.com',

        'branch_name'        => 'BANSHKHALI BRANCH',
        'acc_opening_branch' => 'BANSHKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MANISUR',
        'account_number'     => '0021070076136',
        'SIF_Number'         => '1445034',

        'account_title'      => 'MD.ANISUR RAHMAN BALI',
        'customer_name'      => 'MD.ANISUR RAHMAN BALI',

        'contact_no_1'       => '8801719094366',
        'mobile_number'      => '8801719094366',

        'email_1'            => 'bmanis007@gmail.com',
        'email_address'      => 'bmanis007@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137MANISURPB',
        'account_number'     => '2125218005846',
        'SIF_Number'         => '411931',

        'account_title'      => 'MOHAMMAD ANISUR RAHMAN SARKER',
        'customer_name'      => 'MOHAMMAD ANISUR RAHMAN SARKER',

        'contact_no_1'       => '8801711231082',
        'mobile_number'      => '8801711231082',

        'email_1'            => 'mars.litu@gmail.com',
        'email_address'      => 'mars.litu@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MANJURPRM',
        'account_number'     => '2138213022023',
        'SIF_Number'         => '2638772',

        'account_title'      => 'SHEIKH MUHAMMAD MANJUR-E-ALAM',
        'customer_name'      => 'SHEIKH MUHAMMAD MANJUR-E-ALAM',

        'contact_no_1'       => '8801708495395',
        'mobile_number'      => '8801708495395',

        'email_1'            => 'smanjur@gmail.com',
        'email_address'      => 'smanjur@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MANJURUL',
        'account_number'     => '3108216039283',
        'SIF_Number'         => '2309924',

        'account_title'      => 'MANJURUL KARIM',
        'customer_name'      => 'MANJURUL KARIM',

        'contact_no_1'       => '8801915929177',
        'mobile_number'      => '8801915929177',

        'email_1'            => 'monjurulkarim82@yahoo.com',
        'email_address'      => 'monjurulkarim82@yahoo.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MANNAPB',
        'account_number'     => '3115219026551',
        'SIF_Number'         => '2691180',

        'account_title'      => 'SYED MAZHARUL ISLAM MANNA',
        'customer_name'      => 'SYED MAZHARUL ISLAM MANNA',

        'contact_no_1'       => '8801712861956',
        'mobile_number'      => '8801712861956',

        'email_1'            => 'mazharmanna81@gmail.com',
        'email_address'      => 'mazharmanna81@gmail.com',

        'branch_name'        => 'IBB AMBERKHANA BRANCH',
        'acc_opening_branch' => 'IBB AMBERKHANA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MANOWARPB',
        'account_number'     => '2145214013931',
        'SIF_Number'         => '181659',

        'account_title'      => 'MUHAMMAD ANOWARUL ISLAM',
        'customer_name'      => 'MUHAMMAD ANOWARUL ISLAM',

        'contact_no_1'       => '8801711748400',
        'mobile_number'      => '8801711748400',

        'email_1'            => 'anowarul.islam@primebank.com.bd',
        'email_address'      => 'anowarul.islam@primebank.com.bd',

        'branch_name'        => 'LALDIGHI EAST BRANCH',
        'acc_opening_branch' => 'LALDIGHI EAST BRANCH',
    ],
    [
        'bp_id'              => 'IN137MANWARAPB',
        'account_number'     => '2113217014959',
        'SIF_Number'         => '2277085',

        'account_title'      => 'MUHAMMAD ANWARUL ALAM',
        'customer_name'      => 'MUHAMMAD ANWARUL ALAM',

        'contact_no_1'       => '8801730345192',
        'mobile_number'      => '8801730345192',

        'email_1'            => 'anwarul.alam@dhl.com',
        'email_address'      => 'anwarul.alam@dhl.com',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MANWARHOS',
        'account_number'     => '2126215012810',
        'SIF_Number'         => '2408609',

        'account_title'      => 'MD ANWAR HOSSAIN',
        'customer_name'      => 'MD ANWAR HOSSAIN',

        'contact_no_1'       => '8801610688958',
        'mobile_number'      => '8801610688958',

        'email_1'            => 'onom.fes@gmail.com',
        'email_address'      => 'onom.fes@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137MARJANPB',
        'account_number'     => '2118212035574',
        'SIF_Number'         => '2442337',

        'account_title'      => 'MARJAN ALAM',
        'customer_name'      => 'MARJAN ALAM',

        'contact_no_1'       => '8801881485445',
        'mobile_number'      => '8801881485445',

        'email_1'            => 'marjanalam996@gmail.com',
        'email_address'      => 'marjanalam996@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MARUFAPRI',
        'account_number'     => '2138217009745',
        'SIF_Number'         => '441089',

        'account_title'      => 'MARUFA RAHMAN',
        'customer_name'      => 'MARUFA RAHMAN',

        'contact_no_1'       => '8801711189950',
        'mobile_number'      => '8801711189950',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MARUFISPB',
        'account_number'     => '2219113007622',
        'SIF_Number'         => '1113497',

        'account_title'      => 'MARUFA ISLAM',
        'customer_name'      => 'MARUFA ISLAM',

        'contact_no_1'       => '8801749399030',
        'mobile_number'      => '8801749399030',

        'email_1'            => 'tushi_marufa@yahoo.com',
        'email_address'      => 'tushi_marufa@yahoo.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MARUFMAPB',
        'account_number'     => '2118216041768',
        'SIF_Number'         => '1271543',

        'account_title'      => 'MARUF MASUD CHOWDHURY',
        'customer_name'      => 'MARUF MASUD CHOWDHURY',

        'contact_no_1'       => '8801711527844',
        'mobile_number'      => '8801711527844',

        'email_1'            => 'marufmc@yahoo.com',
        'email_address'      => 'marufmc@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MARUFPRIM',
        'account_number'     => '2118213024270',
        'SIF_Number'         => '1832174',

        'account_title'      => 'MARUF AHMED',
        'customer_name'      => 'MARUF AHMED',

        'contact_no_1'       => '8801755305878',
        'mobile_number'      => '8801755305878',

        'email_1'            => 'marufmx@gmail.com',
        'email_address'      => 'marufmx@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MARYSENPR',
        'account_number'     => '2233215000591',
        'SIF_Number'         => '2747047',

        'account_title'      => 'MARY SEN',
        'customer_name'      => 'MARY SEN',

        'contact_no_1'       => '8801811662537',
        'mobile_number'      => '8801811662537',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'ABU TORAB BAZAR BRANCH',
        'acc_opening_branch' => 'ABU TORAB BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASABURPB',
        'account_number'     => '2104219026689',
        'SIF_Number'         => '894418',

        'account_title'      => 'MD. ABDUS SABUR',
        'customer_name'      => 'MD. ABDUS SABUR',

        'contact_no_1'       => '8801915472871',
        'mobile_number'      => '8801915472871',

        'email_1'            => 'abdus.sabur@primebank.com.bd',
        'email_address'      => 'abdus.sabur@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASHAMEPB',
        'account_number'     => '2132212021618',
        'SIF_Number'         => '2078392',

        'account_title'      => 'MRIMMOAEE AHMED SHAMEE',
        'customer_name'      => 'MRIMMOAEE AHMED SHAMEE',

        'contact_no_1'       => '8801953303300',
        'mobile_number'      => '8801953303300',

        'email_1'            => 'mrimmoaee@gmail.com',
        'email_address'      => 'mrimmoaee@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASHFIQUE',
        'account_number'     => '2104214033599',
        'SIF_Number'         => '2134758',

        'account_title'      => 'MD. MASHFIQUE ANWAR',
        'customer_name'      => 'MD. MASHFIQUE ANWAR',

        'contact_no_1'       => '8801713012797',
        'mobile_number'      => '8801713012797',

        'email_1'            => 'mashfique.anwar@gmail.com',
        'email_address'      => 'mashfique.anwar@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASHFIQUR',
        'account_number'     => '2132214022838',
        'SIF_Number'         => '1983425',

        'account_title'      => 'KHANDAKER MD. MASHFIQUR RAHMAN',
        'customer_name'      => 'KHANDAKER MD. MASHFIQUR RAHMAN',

        'contact_no_1'       => '8801791352156',
        'mobile_number'      => '8801791352156',

        'email_1'            => 'pial74@hotmail.com',
        'email_address'      => 'pial74@hotmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASHIADPB',
        'account_number'     => '2138216017095',
        'SIF_Number'         => '2108589',

        'account_title'      => 'MASHIAD HASAN',
        'customer_name'      => 'MASHIAD HASAN',

        'contact_no_1'       => '8801732792773',
        'mobile_number'      => '8801732792773',

        'email_1'            => 'mashiad.hasan14@gmail.com',
        'email_address'      => 'mashiad.hasan14@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASHQCHPB',
        'account_number'     => '2104213037526',
        'SIF_Number'         => '2381760',

        'account_title'      => 'MASIHUL HUQ CHOWDHURY',
        'customer_name'      => 'MASIHUL HUQ CHOWDHURY',

        'contact_no_1'       => '8801713006396',
        'mobile_number'      => '8801713006396',

        'email_1'            => 'masihul1811@gmail.com',
        'email_address'      => 'masihul1811@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASHRAFPB',
        'account_number'     => '2104216003632',
        'SIF_Number'         => '4986',

        'account_title'      => 'MOHAMMAD ASHRAFUR RAHMAN',
        'customer_name'      => 'MOHAMMAD ASHRAFUR RAHMAN',

        'contact_no_1'       => '8801711128704',
        'mobile_number'      => '8801711128704',

        'email_1'            => 'ashrafur76@gmail.com',
        'email_address'      => 'ashrafur76@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASIFBIPB',
        'account_number'     => '2104217004589',
        'SIF_Number'         => '5079',

        'account_title'      => 'MD. ASIF BIN IDRISH',
        'customer_name'      => 'MD. ASIF BIN IDRISH',

        'contact_no_1'       => '8801819495060',
        'mobile_number'      => '8801819495060',

        'email_1'            => 'mdasifbinidrish@gmail.com',
        'email_address'      => 'mdasifbinidrish@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASKATPB',
        'account_number'     => '2125217001184',
        'SIF_Number'         => '1229462',

        'account_title'      => 'MASKAT JAHAN SUMI',
        'customer_name'      => 'MASKAT JAHAN SUMI',

        'contact_no_1'       => '8801714042324',
        'mobile_number'      => '8801714042324',

        'email_1'            => 'imaskat@gmail.com',
        'email_address'      => 'imaskat@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASODURPB',
        'account_number'     => '2187218015840',
        'SIF_Number'         => '1006789',

        'account_title'      => 'MD. MASODUR RAHMAN',
        'customer_name'      => 'MD. MASODUR RAHMAN',

        'contact_no_1'       => '8801720422234',
        'mobile_number'      => '8801720422234',

        'email_1'            => 'raselchowdhury2019@gmail.com',
        'email_address'      => 'raselchowdhury2019@gmail.com',

        'branch_name'        => 'WARI BRANCH',
        'acc_opening_branch' => 'WARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASRANAPB',
        'account_number'     => '2104211015764',
        'SIF_Number'         => '54555',

        'account_title'      => 'MD. MASUD RANA',
        'customer_name'      => 'MD. MASUD RANA',

        'contact_no_1'       => '8801730320016',
        'mobile_number'      => '8801730320016',

        'email_1'            => 'masudrana29@yahoo.com',
        'email_address'      => 'masudrana29@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASREZAPB',
        'account_number'     => '2219216006173',
        'SIF_Number'         => '2088321',

        'account_title'      => 'MASHRUR REZA',
        'customer_name'      => 'MASHRUR REZA',

        'contact_no_1'       => '8801713078177',
        'mobile_number'      => '8801713078177',

        'email_1'            => 'mashrur.reza@aegisint.net',
        'email_address'      => 'mashrur.reza@aegisint.net',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASUDKHPB',
        'account_number'     => '2165216018057',
        'SIF_Number'         => '2595983',

        'account_title'      => 'MD MASUD KHAN',
        'customer_name'      => 'MD MASUD KHAN',

        'contact_no_1'       => '8801919063471',
        'mobile_number'      => '8801919063471',

        'email_1'            => 'mmk957@gmail.com',
        'email_address'      => 'mmk957@gmail.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASUDPRME',
        'account_number'     => '2125211024521',
        'SIF_Number'         => '2130799',

        'account_title'      => 'MD MASUD UL ALAM',
        'customer_name'      => 'MD MASUD UL ALAM',

        'contact_no_1'       => '8801741055478',
        'mobile_number'      => '8801741055478',

        'email_1'            => 'masudalam157@gmail.com',
        'email_address'      => 'masudalam157@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASUDUDED',
        'account_number'     => '2127211004448',
        'SIF_Number'         => '818606',

        'account_title'      => 'MASUD UDDIN AHMED',
        'customer_name'      => 'MASUD UDDIN AHMED',

        'contact_no_1'       => '8801700711480',
        'mobile_number'      => '8801700711480',

        'email_1'            => 'masud.ahmed@primebank.com.bd',
        'email_address'      => 'masud.ahmed@primebank.com.bd',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASUDURPB',
        'account_number'     => '2104213019813',
        'SIF_Number'         => '1445182',

        'account_title'      => 'MD.MASUDUR RAHMAN',
        'customer_name'      => 'MD.MASUDUR RAHMAN',

        'contact_no_1'       => '8801716560044',
        'mobile_number'      => '8801716560044',

        'email_1'            => 'masudur17@gmail.com',
        'email_address'      => 'masudur17@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MASUMLCPB',
        'account_number'     => '2104211002051',
        'SIF_Number'         => '9239',

        'account_title'      => 'MASUM MALLICK',
        'customer_name'      => 'MASUM MALLICK',

        'contact_no_1'       => '8801915327854',
        'mobile_number'      => '8801915327854',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAWADUDPB',
        'account_number'     => '2107218024450',
        'SIF_Number'         => '2062700',

        'account_title'      => 'MD. ABDUL WADUD SARDAR',
        'customer_name'      => 'MD. ABDUL WADUD SARDAR',

        'contact_no_1'       => '8801912677737',
        'mobile_number'      => '8801912677737',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'KHULNA BRANCH',
        'acc_opening_branch' => 'KHULNA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAZHARIPB',
        'account_number'     => '2118215014642',
        'SIF_Number'         => '76447',

        'account_title'      => 'MD. MAZHARUL ISLAM',
        'customer_name'      => 'MD. MAZHARUL ISLAM',

        'contact_no_1'       => '8801713065470',
        'mobile_number'      => '8801713065470',

        'email_1'            => 'mazhar@transcombd.com',
        'email_address'      => 'mazhar@transcombd.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MAZHARPRM',
        'account_number'     => '2125211010116',
        'SIF_Number'         => '1389181',

        'account_title'      => 'MAZHARUL ISLAM',
        'customer_name'      => 'MAZHARUL ISLAM',

        'contact_no_1'       => '8801709753609',
        'mobile_number'      => '8801709753609',

        'email_1'            => 'pratyahik@gmail.com',
        'email_address'      => 'pratyahik@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MBODRUDD',
        'account_number'     => '2159211002112',
        'SIF_Number'         => '36546',

        'account_title'      => 'MOHAMMAD BODRUDDOZA',
        'customer_name'      => 'MOHAMMAD BODRUDDOZA',

        'contact_no_1'       => '8801715012037',
        'mobile_number'      => '8801715012037',

        'email_1'            => 'mohd.bodruddoza@gmail.com',
        'email_address'      => 'mohd.bodruddoza@gmail.com',

        'branch_name'        => 'BIJOY NAGAR BRANCH',
        'acc_opening_branch' => 'BIJOY NAGAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MCPALPB',
        'account_number'     => '3108214002879',
        'SIF_Number'         => '737299',

        'account_title'      => 'MITHUN CHANDRA PAL',
        'customer_name'      => 'MITHUN CHANDRA PAL',

        'contact_no_1'       => '8801913696744',
        'mobile_number'      => '8801913696744',

        'email_1'            => 'mithunpalca2023@gmail.com',
        'email_address'      => 'mithunpalca2023@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDABULKAS',
        'account_number'     => '2118212000545',
        'SIF_Number'         => '1807204',

        'account_title'      => 'MD ABUL KASHEM',
        'customer_name'      => 'MD ABUL KASHEM',

        'contact_no_1'       => '8801713061712',
        'mobile_number'      => '8801713061712',

        'email_1'            => 'kashemma@gmail.com',
        'email_address'      => 'kashemma@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDAKAZADP',
        'account_number'     => '2135218041278',
        'SIF_Number'         => '2553700',

        'account_title'      => 'MOHAMMED ABUL KALAM AZAD',
        'customer_name'      => 'MOHAMMED ABUL KALAM AZAD',

        'contact_no_1'       => '8801911714109',
        'mobile_number'      => '8801911714109',

        'email_1'            => 'azad.paxar@gmail.com',
        'email_address'      => 'azad.paxar@gmail.com',

        'branch_name'        => 'PRAGATI SARANI BRANCH',
        'acc_opening_branch' => 'PRAGATI SARANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDALAMPRM',
        'account_number'     => '2170215019596',
        'SIF_Number'         => '2153957',

        'account_title'      => 'MD. MAHABUBUL ALAM',
        'customer_name'      => 'MD. MAHABUBUL ALAM',

        'contact_no_1'       => '8801406289854',
        'mobile_number'      => '8801406289854',

        'email_1'            => 'mahabub2217@gmail.com',
        'email_address'      => 'mahabub2217@gmail.com',

        'branch_name'        => 'GARIB-E-NEWAZ AVE. BR',
        'acc_opening_branch' => 'GARIB-E-NEWAZ AVE. BR',
    ],
    [
        'bp_id'              => 'IN137MDANISUZZ',
        'account_number'     => '2152214028962',
        'SIF_Number'         => '846837',

        'account_title'      => 'MD ANISUZZAMAN',
        'customer_name'      => 'MD ANISUZZAMAN',

        'contact_no_1'       => '8801684962154',
        'mobile_number'      => '8801684962154',

        'email_1'            => 'hameem.ueep@gmail.com',
        'email_address'      => 'hameem.ueep@gmail.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDASADPRM',
        'account_number'     => '2104211036801',
        'SIF_Number'         => '2356498',

        'account_title'      => 'MD. ASHADUZZAMAN',
        'customer_name'      => 'MD. ASHADUZZAMAN',

        'contact_no_1'       => '8801887157946',
        'mobile_number'      => '8801887157946',

        'email_1'            => 'asadbabu89@gmail.com',
        'email_address'      => 'asadbabu89@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDATIQURA',
        'account_number'     => '2146219001674',
        'SIF_Number'         => '388941',

        'account_title'      => 'MD. ATIQUR RAHMAN',
        'customer_name'      => 'MD. ATIQUR RAHMAN',

        'contact_no_1'       => '8801711803475',
        'mobile_number'      => '8801711803475',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDAZHARPB',
        'account_number'     => '2186215003410',
        'SIF_Number'         => '1222118',

        'account_title'      => 'MD. AZHARUL ISLAM',
        'customer_name'      => 'MD. AZHARUL ISLAM',

        'contact_no_1'       => '8801916145990',
        'mobile_number'      => '8801916145990',

        'email_1'            => 'azhar5990@gmail.com',
        'email_address'      => 'azhar5990@gmail.com',

        'branch_name'        => 'JATRABARI BRANCH',
        'acc_opening_branch' => 'JATRABARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDFAISKPB',
        'account_number'     => '2148217001286',
        'SIF_Number'         => '1313756',

        'account_title'      => 'MD FARIDUL ISLAM KHAN',
        'customer_name'      => 'MD FARIDUL ISLAM KHAN',

        'contact_no_1'       => '8801671081424',
        'mobile_number'      => '8801671081424',

        'email_1'            => 'ronyais59@gmail.com',
        'email_address'      => 'ronyais59@gmail.com',

        'branch_name'        => 'KAKRAIL BRANCH',
        'acc_opening_branch' => 'KAKRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDKAMRUZ',
        'account_number'     => '2135211022077',
        'SIF_Number'         => '2064727',

        'account_title'      => 'MD. KAMRUZZAMAN',
        'customer_name'      => 'MD. KAMRUZZAMAN',

        'contact_no_1'       => '8801712885858',
        'mobile_number'      => '8801712885858',

        'email_1'            => 'romkamruzzaman@gmail.com',
        'email_address'      => 'romkamruzzaman@gmail.com',

        'branch_name'        => 'PRAGATI SARANI BRANCH',
        'acc_opening_branch' => 'PRAGATI SARANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDMASUDPB',
        'account_number'     => '2104212044002',
        'SIF_Number'         => '2104362',

        'account_title'      => 'MD MASUD RANA',
        'customer_name'      => 'MD MASUD RANA',

        'contact_no_1'       => '8801911046302',
        'mobile_number'      => '8801911046302',

        'email_1'            => 'masud.rana.infs@gmail.com',
        'email_address'      => 'masud.rana.infs@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDMEZANU',
        'account_number'     => '2205296010637',
        'SIF_Number'         => '2125940',

        'account_title'      => 'MD MEZANUR RAHAMAN',
        'customer_name'      => 'MD MEZANUR RAHAMAN',

        'contact_no_1'       => '8801766699517',
        'mobile_number'      => '8801766699517',

        'email_1'            => 'mezannorth@gmail.com',
        'email_address'      => 'mezannorth@gmail.com',

        'branch_name'        => 'IBRAHIMPUR BRANCH',
        'acc_opening_branch' => 'IBRAHIMPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDMINAR',
        'account_number'     => '3108216050041',
        'SIF_Number'         => '2749298',

        'account_title'      => 'MD MINAR HUSSAIN RABBI',
        'customer_name'      => 'MD MINAR HUSSAIN RABBI',

        'contact_no_1'       => '8801679783536',
        'mobile_number'      => '8801679783536',

        'email_1'            => 'minarhussain619@gmail.com',
        'email_address'      => 'minarhussain619@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDMOSAHOS',
        'account_number'     => '2152219021145',
        'SIF_Number'         => '2029237',

        'account_title'      => 'MD MOSARROF HOSSAIN',
        'customer_name'      => 'MD MOSARROF HOSSAIN',

        'contact_no_1'       => '8801817592402',
        'mobile_number'      => '8801817592402',

        'email_1'            => 'mosharaf_hossain@live.com',
        'email_address'      => 'mosharaf_hossain@live.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDMOSRPBK',
        'account_number'     => '2152214029069',
        'SIF_Number'         => '2435498',

        'account_title'      => 'MD MOSTAFIZUR RAHMAN',
        'customer_name'      => 'MD MOSTAFIZUR RAHMAN',

        'contact_no_1'       => '8801712337624',
        'mobile_number'      => '8801712337624',

        'email_1'            => 'mostafizliton@gmail.com',
        'email_address'      => 'mostafizliton@gmail.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDMRAHMAN',
        'account_number'     => '2118217032600',
        'SIF_Number'         => '2414492',

        'account_title'      => 'MD MAHMUDUR RAHMAN',
        'customer_name'      => 'MD MAHMUDUR RAHMAN',

        'contact_no_1'       => '8801700707606',
        'mobile_number'      => '8801700707606',

        'email_1'            => 'mahmudur.razu707@gmail.com',
        'email_address'      => 'mahmudur.razu707@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDMUBASHA',
        'account_number'     => '2138294015408',
        'SIF_Number'         => '10354',

        'account_title'      => 'MD. MUBASHER HASAN',
        'customer_name'      => 'MD. MUBASHER HASAN',

        'contact_no_1'       => '8801916100499',
        'mobile_number'      => '8801916100499',

        'email_1'            => 'agalibhasan@gmail.com',
        'email_address'      => 'agalibhasan@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDNAPRIME',
        'account_number'     => '2114215007129',
        'SIF_Number'         => '259733',

        'account_title'      => 'MD NAZRUL ISLAM',
        'customer_name'      => 'MD NAZRUL ISLAM',

        'contact_no_1'       => '8801720452828',
        'mobile_number'      => '8801720452828',

        'email_1'            => 'nazrul.rita@gmail.com',
        'email_address'      => 'nazrul.rita@gmail.com',

        'branch_name'        => 'ELEPHANT ROAD BRANCH',
        'acc_opening_branch' => 'ELEPHANT ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDNOJIBUR',
        'account_number'     => '2104216010314',
        'SIF_Number'         => '11394',

        'account_title'      => 'MD NOJIBUR RAHMAN',
        'customer_name'      => 'MD NOJIBUR RAHMAN',

        'contact_no_1'       => '8801771256829',
        'mobile_number'      => '8801771256829',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDRAFIQPB',
        'account_number'     => '2104214006868',
        'SIF_Number'         => '1207522',

        'account_title'      => 'MD RAFIQUL ISLAM',
        'customer_name'      => 'MD RAFIQUL ISLAM',

        'contact_no_1'       => '8801715012502',
        'mobile_number'      => '8801715012502',

        'email_1'            => 'ri060301@primebank.com.bd',
        'email_address'      => 'ri060301@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDSAFAYET',
        'account_number'     => '2104218023588',
        'SIF_Number'         => '1389395',

        'account_title'      => 'MD. SAFAYET',
        'customer_name'      => 'MD. SAFAYET',

        'contact_no_1'       => '8801715059805',
        'mobile_number'      => '8801715059805',

        'email_1'            => 'kazisafayet@gmail.com',
        'email_address'      => 'kazisafayet@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDSAIFUR',
        'account_number'     => '2118211033548',
        'SIF_Number'         => '2451971',

        'account_title'      => 'MD SAIFUR RAHMAN',
        'customer_name'      => 'MD SAIFUR RAHMAN',

        'contact_no_1'       => '8801711903311',
        'mobile_number'      => '8801711903311',

        'email_1'            => 'imranfb9@gmail.com',
        'email_address'      => 'imranfb9@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDSHAFIQ',
        'account_number'     => '2104214002404',
        'SIF_Number'         => '804408',

        'account_title'      => 'MOHAMMAD SHAFIQUL ISLAM',
        'customer_name'      => 'MOHAMMAD SHAFIQUL ISLAM',

        'contact_no_1'       => '8801733600611',
        'mobile_number'      => '8801733600611',

        'email_1'            => 'ontu007@gmail.com',
        'email_address'      => 'ontu007@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDSHAFPBK',
        'account_number'     => '2118215030036',
        'SIF_Number'         => '2319035',

        'account_title'      => 'MD SHAFIUDDIN',
        'customer_name'      => 'MD SHAFIUDDIN',

        'contact_no_1'       => '8801817047247',
        'mobile_number'      => '8801817047247',

        'email_1'            => 'cfo.travelshop@gmail.com',
        'email_address'      => 'cfo.travelshop@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDSHAHIDU',
        'account_number'     => '2118213033727',
        'SIF_Number'         => '2478702',

        'account_title'      => 'MD. SHAHID ULLAH',
        'customer_name'      => 'MD. SHAHID ULLAH',

        'contact_no_1'       => '8801713104556',
        'mobile_number'      => '8801713104556',

        'email_1'            => 'shahid_UNISOFT@YAHOO.COM',
        'email_address'      => 'shahid_UNISOFT@YAHOO.COM',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDSHAHPB',
        'account_number'     => '2117211023771',
        'SIF_Number'         => '2902831',

        'account_title'      => 'MD SHAHIDULLAH',
        'customer_name'      => 'MD SHAHIDULLAH',

        'contact_no_1'       => '8801716580491',
        'mobile_number'      => '8801716580491',

        'email_1'            => 'professor.shahidullah@gmail.com',
        'email_address'      => 'professor.shahidullah@gmail.com',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDSHAMIHA',
        'account_number'     => '2124214005239',
        'SIF_Number'         => '820252',

        'account_title'      => 'MD. SHAMIMUL HAQUE',
        'customer_name'      => 'MD. SHAMIMUL HAQUE',

        'contact_no_1'       => '8801713365442',
        'mobile_number'      => '8801713365442',

        'email_1'            => 'shamim@msml.dbl-group.com',
        'email_address'      => 'shamim@msml.dbl-group.com',

        'branch_name'        => 'GANAKBARI BRANCH',
        'acc_opening_branch' => 'GANAKBARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDSHAMSTA',
        'account_number'     => '2126217004849',
        'SIF_Number'         => '366821',

        'account_title'      => 'MD. SHAMSUDDOHA TAPOS',
        'customer_name'      => 'MD. SHAMSUDDOHA TAPOS',

        'contact_no_1'       => '8801971800100',
        'mobile_number'      => '8801971800100',

        'email_1'            => 'tapos_bd@yahoo.com',
        'email_address'      => 'tapos_bd@yahoo.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137MDSHIKDER',
        'account_number'     => '0021030013519',
        'SIF_Number'         => '771473',

        'account_title'      => 'MD EKRAMUL HAQ SHIKDER',
        'customer_name'      => 'MD EKRAMUL HAQ SHIKDER',

        'contact_no_1'       => '8801819366632',
        'mobile_number'      => '8801819366632',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDTANVIAL',
        'account_number'     => '0021040009115',
        'SIF_Number'         => '1669478',

        'account_title'      => 'MD. TANVIRUL AL AZAD',
        'customer_name'      => 'MD. TANVIRUL AL AZAD',

        'contact_no_1'       => '8801762873847',
        'mobile_number'      => '8801762873847',

        'email_1'            => 'tanvirrakibl@gmail.com',
        'email_address'      => 'tanvirrakibl@gmail.com',

        'branch_name'        => 'PALLABI BRANCH',
        'acc_opening_branch' => 'PALLABI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDTARIQIS',
        'account_number'     => '2138219008273',
        'SIF_Number'         => '1812993',

        'account_title'      => 'MD. TARIQUL ISLAM',
        'customer_name'      => 'MD. TARIQUL ISLAM',

        'contact_no_1'       => '8801715026457',
        'mobile_number'      => '8801715026457',

        'email_1'            => 'tarik.forest@gmail.com',
        'email_address'      => 'tarik.forest@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MDZAHAD',
        'account_number'     => '2132211001091',
        'SIF_Number'         => '63502',

        'account_title'      => 'MD. ZAHADUL RASHID',
        'customer_name'      => 'MD. ZAHADUL RASHID',

        'contact_no_1'       => '8801711696888',
        'mobile_number'      => '8801711696888',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MEHDIHAS',
        'account_number'     => '0031030054503',
        'SIF_Number'         => '883801',

        'account_title'      => 'MEHDI HASSAN',
        'customer_name'      => 'MEHDI HASSAN',

        'contact_no_1'       => '8801713409185',
        'mobile_number'      => '8801713409185',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MEHEDIHAN',
        'account_number'     => '2145212026059',
        'SIF_Number'         => '724728',

        'account_title'      => 'MEHEDI HASSAN',
        'customer_name'      => 'MEHEDI HASSAN',

        'contact_no_1'       => '8801911952062',
        'mobile_number'      => '8801911952062',

        'email_1'            => 'mehedi.bb10@gmail.com',
        'email_address'      => 'mehedi.bb10@gmail.com',

        'branch_name'        => 'LALDIGHI EAST BRANCH',
        'acc_opening_branch' => 'LALDIGHI EAST BRANCH',
    ],
    [
        'bp_id'              => 'IN137MEHEDIPB',
        'account_number'     => '2124211004780',
        'SIF_Number'         => '1870931',

        'account_title'      => 'MOHAMMAD MEHEDI HASAN BHUIYAN',
        'customer_name'      => 'MOHAMMAD MEHEDI HASAN BHUIYAN',

        'contact_no_1'       => '8801717897028',
        'mobile_number'      => '8801717897028',

        'email_1'            => 'mehedi.aust69@gmail.com',
        'email_address'      => 'mehedi.aust69@gmail.com',

        'branch_name'        => 'GANAKBARI BRANCH',
        'acc_opening_branch' => 'GANAKBARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MERYKHPRM',
        'account_number'     => '2104213039654',
        'SIF_Number'         => '2530688',

        'account_title'      => 'MERY KHATUN',
        'customer_name'      => 'MERY KHATUN',

        'contact_no_1'       => '8801957477906',
        'mobile_number'      => '8801957477906',

        'email_1'            => 'mereykhatun234@gmail.com',
        'email_address'      => 'mereykhatun234@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MESBAHBB',
        'account_number'     => '2104217008572',
        'SIF_Number'         => '1044364',

        'account_title'      => 'MD MESBAHUL HOSSEN',
        'customer_name'      => 'MD MESBAHUL HOSSEN',

        'contact_no_1'       => '8801918665532',
        'mobile_number'      => '8801918665532',

        'email_1'            => 'mesbah.du@gmail.com',
        'email_address'      => 'mesbah.du@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MEZBUACPB',
        'account_number'     => '2145219010684',
        'SIF_Number'         => '962291',

        'account_title'      => 'MEJBAH UDDIN AHMED CHOWDHURY',
        'customer_name'      => 'MEJBAH UDDIN AHMED CHOWDHURY',

        'contact_no_1'       => '8801817211319',
        'mobile_number'      => '8801817211319',

        'email_1'            => 'mejbah@ritzygroupbd.com',
        'email_address'      => 'mejbah@ritzygroupbd.com',

        'branch_name'        => 'LALDIGHI EAST BRANCH',
        'acc_opening_branch' => 'LALDIGHI EAST BRANCH',
    ],
    [
        'bp_id'              => 'IN137MHOSNABPB',
        'account_number'     => '2127211026079',
        'SIF_Number'         => '2466935',

        'account_title'      => 'MOSAMMAT HOSNA BEGUM',
        'customer_name'      => 'MOSAMMAT HOSNA BEGUM',

        'contact_no_1'       => '8801715054915',
        'mobile_number'      => '8801715054915',

        'email_1'            => 'osru25m@gmail.com',
        'email_address'      => 'osru25m@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIBOJRAH',
        'account_number'     => '2132213026210',
        'SIF_Number'         => '1325651',

        'account_title'      => 'MIRZA BOJLUR RAHMAN',
        'customer_name'      => 'MIRZA BOJLUR RAHMAN',

        'contact_no_1'       => '8801711536330',
        'mobile_number'      => '8801711536330',

        'email_1'            => 'mbrahman@gmail.com',
        'email_address'      => 'mbrahman@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIFTAKISL',
        'account_number'     => '2120211025581',
        'SIF_Number'         => '2507634',

        'account_title'      => 'MD. IFTAKHARUL ISLAM',
        'customer_name'      => 'MD. IFTAKHARUL ISLAM',

        'contact_no_1'       => '8801776332555',
        'mobile_number'      => '8801776332555',

        'email_1'            => 'iftakharulislam216@gmail.com',
        'email_address'      => 'iftakharulislam216@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIMORSPRM',
        'account_number'     => '2128213003017',
        'SIF_Number'         => '236839',

        'account_title'      => 'MIR MOHAMMED MORSHED',
        'customer_name'      => 'MIR MOHAMMED MORSHED',

        'contact_no_1'       => '8801550151852',
        'mobile_number'      => '8801550151852',

        'email_1'            => 'morshedbtcl@gmail.com',
        'email_address'      => 'morshedbtcl@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIQBALHPB',
        'account_number'     => '2125212031640',
        'SIF_Number'         => '1404994',

        'account_title'      => 'MD. IQBAL HOSSAIN',
        'customer_name'      => 'MD. IQBAL HOSSAIN',

        'contact_no_1'       => '8801718125783',
        'mobile_number'      => '8801718125783',

        'email_1'            => 'iqbal052005@yahoo.com',
        'email_address'      => 'iqbal052005@yahoo.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIQBALPRM',
        'account_number'     => '2128216008278',
        'SIF_Number'         => '236307',

        'account_title'      => 'MD. IQBAL',
        'customer_name'      => 'MD. IQBAL',

        'contact_no_1'       => '8801819273280',
        'mobile_number'      => '8801819273280',

        'email_1'            => 'md.iqbal6906@gmail.com',
        'email_address'      => 'md.iqbal6906@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIRAJPRM',
        'account_number'     => '2138214003840',
        'SIF_Number'         => '1860189',

        'account_title'      => 'MIRAJ AHMED CHOWDHURY',
        'customer_name'      => 'MIRAJ AHMED CHOWDHURY',

        'contact_no_1'       => '8801755580662',
        'mobile_number'      => '8801755580662',

        'email_1'            => 'miraj@digitallyright.org',
        'email_address'      => 'miraj@digitallyright.org',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIRATULPB',
        'account_number'     => '2118216010763',
        'SIF_Number'         => '78435',

        'account_title'      => 'MIRATUL FARZANA',
        'customer_name'      => 'MIRATUL FARZANA',

        'contact_no_1'       => '8801714083312',
        'mobile_number'      => '8801714083312',

        'email_1'            => 'tulipfarzana@gmail.com',
        'email_address'      => 'tulipfarzana@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIRAZMOR',
        'account_number'     => '2118212034320',
        'SIF_Number'         => '2499106',

        'account_title'      => 'MIRAZ MORSHED RIFAT',
        'customer_name'      => 'MIRAZ MORSHED RIFAT',

        'contact_no_1'       => '8801687975491',
        'mobile_number'      => '8801687975491',

        'email_1'            => 'miraz.rifat@bkash.com',
        'email_address'      => 'miraz.rifat@bkash.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIRFAUZPB',
        'account_number'     => '2187215000274',
        'SIF_Number'         => '1354055',

        'account_title'      => 'MIR FAUZIA RAHMAN RAHMAN',
        'customer_name'      => 'MIR FAUZIA RAHMAN RAHMAN',

        'contact_no_1'       => '8801711546432',
        'mobile_number'      => '8801711546432',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'WARI BRANCH',
        'acc_opening_branch' => 'WARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIRHASSAN',
        'account_number'     => '2104215007725',
        'SIF_Number'         => '21448',

        'account_title'      => 'MIR MD HASSANUL ZAHED',
        'customer_name'      => 'MIR MD HASSANUL ZAHED',

        'contact_no_1'       => '8801713069925',
        'mobile_number'      => '8801713069925',

        'email_1'            => 'mir.hassan.zahed@gmail.com',
        'email_address'      => 'mir.hassan.zahed@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MITHILAPB',
        'account_number'     => '2118217037963',
        'SIF_Number'         => '2652785',

        'account_title'      => 'MITHILA ISLAM',
        'customer_name'      => 'MITHILA ISLAM',

        'contact_no_1'       => '8801712844371',
        'mobile_number'      => '8801712844371',

        'email_1'            => 'mithila07@gmail.com',
        'email_address'      => 'mithila07@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MITHLPRM',
        'account_number'     => '2118217037963',
        'SIF_Number'         => '2652785',

        'account_title'      => 'MITHILA ISLAM',
        'customer_name'      => 'MITHILA ISLAM',

        'contact_no_1'       => '8801712844371',
        'mobile_number'      => '8801712844371',

        'email_1'            => 'mithila07@gmail.com',
        'email_address'      => 'mithila07@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MITHUNC',
        'account_number'     => '0021030076162',
        'SIF_Number'         => '1445176',

        'account_title'      => 'MITHUN CHOWDHURY',
        'customer_name'      => 'MITHUN CHOWDHURY',

        'contact_no_1'       => '8801815045853',
        'mobile_number'      => '8801815045853',

        'email_1'            => 'mibbafin@gmail.com',
        'email_address'      => 'mibbafin@gmail.com',

        'branch_name'        => 'JOYPURHAT BRANCH',
        'acc_opening_branch' => 'JOYPURHAT BRANCH',
    ],
    [
        'bp_id'              => 'IN137MITHUNPB',
        'account_number'     => '2104211024443',
        'SIF_Number'         => '1445176',

        'account_title'      => 'MITHUN CHOWDHURY',
        'customer_name'      => 'MITHUN CHOWDHURY',

        'contact_no_1'       => '8801815045853',
        'mobile_number'      => '8801815045853',

        'email_1'            => 'mibbafin@gmail.com',
        'email_address'      => 'mibbafin@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIZANURPR',
        'account_number'     => '2142212003853',
        'SIF_Number'         => '373692',

        'account_title'      => 'MIZANUR RAHMAN CHOWDHURY',
        'customer_name'      => 'MIZANUR RAHMAN CHOWDHURY',

        'contact_no_1'       => '8801746722300',
        'mobile_number'      => '8801746722300',

        'email_1'            => 'mizanur9929@sky.com',
        'email_address'      => 'mizanur9929@sky.com',

        'branch_name'        => 'TAJPUR BRANCH',
        'acc_opening_branch' => 'TAJPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIZANURRA',
        'account_number'     => '2142212003853',
        'SIF_Number'         => '373692',

        'account_title'      => 'MIZANUR RAHMAN CHOWDHURY',
        'customer_name'      => 'MIZANUR RAHMAN CHOWDHURY',

        'contact_no_1'       => '8801746722300',
        'mobile_number'      => '8801746722300',

        'email_1'            => 'mizanur9929@sky.com',
        'email_address'      => 'mizanur9929@sky.com',

        'branch_name'        => 'TAJPUR BRANCH',
        'acc_opening_branch' => 'TAJPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIZANURRC',
        'account_number'     => '2142212003853',
        'SIF_Number'         => '373692',

        'account_title'      => 'MIZANUR RAHMAN CHOWDHURY',
        'customer_name'      => 'MIZANUR RAHMAN CHOWDHURY',

        'contact_no_1'       => '8801746722300',
        'mobile_number'      => '8801746722300',

        'email_1'            => 'mizanur9929@sky.com',
        'email_address'      => 'mizanur9929@sky.com',

        'branch_name'        => 'TAJPUR BRANCH',
        'acc_opening_branch' => 'TAJPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MIZAPRIME',
        'account_number'     => '2126213012309',
        'SIF_Number'         => '2343505',

        'account_title'      => 'MOHAMMED MIZANUR RAHMAN',
        'customer_name'      => 'MOHAMMED MIZANUR RAHMAN',

        'contact_no_1'       => '8801833325651',
        'mobile_number'      => '8801833325651',

        'email_1'            => 'mmrmizan@gmail.com',
        'email_address'      => 'mmrmizan@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137MIZMAMPB',
        'account_number'     => '2125214006624',
        'SIF_Number'         => '1370999',

        'account_title'      => 'MD. MIZANUR RAHMAN MAMUN',
        'customer_name'      => 'MD. MIZANUR RAHMAN MAMUN',

        'contact_no_1'       => '8801711537702',
        'mobile_number'      => '8801711537702',

        'email_1'            => 'mizan@hatil.com',
        'email_address'      => 'mizan@hatil.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MKHALEDPB',
        'account_number'     => '2167218032179',
        'SIF_Number'         => '2365102',

        'account_title'      => 'MD. KHALED KHAN',
        'customer_name'      => 'MD. KHALED KHAN',

        'contact_no_1'       => '8801401463032',
        'mobile_number'      => '8801401463032',

        'email_1'            => 'khaledkhan3032@gmail.com',
        'email_address'      => 'khaledkhan3032@gmail.com',

        'branch_name'        => 'ADAMJEE EPZ BRANCH',
        'acc_opening_branch' => 'ADAMJEE EPZ BRANCH',
    ],
    [
        'bp_id'              => 'IN137MKRSAYERA',
        'account_number'     => '3134211020956',
        'SIF_Number'         => '2345668',

        'account_title'      => 'MD MAHIBUL KARIM & SAYERA YASMIN',
        'customer_name'      => 'MD MAHIBUL KARIM & SAYERA YASMIN',

        'contact_no_1'       => '8801766685932',
        'mobile_number'      => '8801766685932',

        'email_1'            => 'kmahib@gmail.com',
        'email_address'      => 'kmahib@gmail.com',

        'branch_name'        => 'IBB O.R. NIZAM ROAD BR',
        'acc_opening_branch' => 'IBB O.R. NIZAM ROAD BR',
    ],
    [
        'bp_id'              => 'IN137MKSOWDAPB',
        'account_number'     => '2161219014396',
        'SIF_Number'         => '2197027',

        'account_title'      => 'MANASH KUMAR SOWDAGOR',
        'customer_name'      => 'MANASH KUMAR SOWDAGOR',

        'contact_no_1'       => '8801896131184',
        'mobile_number'      => '8801896131184',

        'email_1'            => 'manashkuman1976@gmail.com',
        'email_address'      => 'manashkuman1976@gmail.com',

        'branch_name'        => 'RANGPUR BRANCH',
        'acc_opening_branch' => 'RANGPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MLUTFOR',
        'account_number'     => '2104216011940',
        'SIF_Number'         => '8452',

        'account_title'      => 'MD. LUTFOR RAHMAN TALUKDER',
        'customer_name'      => 'MD. LUTFOR RAHMAN TALUKDER',

        'contact_no_1'       => '8801712257707',
        'mobile_number'      => '8801712257707',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MMAHHAQ',
        'account_number'     => '2104713019291',
        'SIF_Number'         => '8938',

        'account_title'      => 'MR. MD. MAHBUBUL HOQUE',
        'customer_name'      => 'MR. MD. MAHBUBUL HOQUE',

        'contact_no_1'       => '8801750039301',
        'mobile_number'      => '8801750039301',

        'email_1'            => 'harmonypackbd@gmail.com',
        'email_address'      => 'harmonypackbd@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MMAINULPB',
        'account_number'     => '2219215011091',
        'SIF_Number'         => '977664',

        'account_title'      => 'MD. MAINUL ISLAM',
        'customer_name'      => 'MD. MAINUL ISLAM',

        'contact_no_1'       => '8801713248051',
        'mobile_number'      => '8801713248051',

        'email_1'            => 'mainul0402@gmail.com',
        'email_address'      => 'mainul0402@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MMAMUNR',
        'account_number'     => '2132218013825',
        'SIF_Number'         => '1389715',

        'account_title'      => 'MD. MAMUNUR RASHID',
        'customer_name'      => 'MD. MAMUNUR RASHID',

        'contact_no_1'       => '8801979113353',
        'mobile_number'      => '8801979113353',

        'email_1'            => 'mamunur008@gmail.com',
        'email_address'      => 'mamunur008@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MMEHEDI',
        'account_number'     => '2118214033726',
        'SIF_Number'         => '2478572',

        'account_title'      => 'MOHAMMED MEHEDI HASAN',
        'customer_name'      => 'MOHAMMED MEHEDI HASAN',

        'contact_no_1'       => '8801711085812',
        'mobile_number'      => '8801711085812',

        'email_1'            => 'mehedi.bueteee.23@gmail.com',
        'email_address'      => 'mehedi.bueteee.23@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MMIZANURR',
        'account_number'     => '2104217025659',
        'SIF_Number'         => '57143',

        'account_title'      => 'MD. MIZANUR RAHMAN',
        'customer_name'      => 'MD. MIZANUR RAHMAN',

        'contact_no_1'       => '8801730058995',
        'mobile_number'      => '8801730058995',

        'email_1'            => 'mr032302@primebank.com.bd',
        'email_address'      => 'mr032302@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MMJAKARPB',
        'account_number'     => '2162216020981',
        'SIF_Number'         => '2831164',

        'account_title'      => 'M.M. JAKARIA',
        'customer_name'      => 'M.M. JAKARIA',

        'contact_no_1'       => '8801614225335',
        'mobile_number'      => '8801614225335',

        'email_1'            => 'civilengineer.uk22@gmail.com',
        'email_address'      => 'civilengineer.uk22@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137MMOHAS',
        'account_number'     => '2164211001133',
        'SIF_Number'         => '361598',

        'account_title'      => 'MD. MOHASHINUL BARI KHANDAKER',
        'customer_name'      => 'MD. MOHASHINUL BARI KHANDAKER',

        'contact_no_1'       => '8801711148707',
        'mobile_number'      => '8801711148707',

        'email_1'            => 'mohashin176@gmail.com',
        'email_address'      => 'mohashin176@gmail.com',

        'branch_name'        => 'PALLABI BRANCH',
        'acc_opening_branch' => 'PALLABI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MMOHSINPB',
        'account_number'     => '2125215015202',
        'SIF_Number'         => '1348894',

        'account_title'      => 'MOHAMMAD MOHSIN',
        'customer_name'      => 'MOHAMMAD MOHSIN',

        'contact_no_1'       => '8801707077999',
        'mobile_number'      => '8801707077999',

        'email_1'            => 'romofashion11@gmail.com',
        'email_address'      => 'romofashion11@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MMOKHLRAH',
        'account_number'     => '2132211020520',
        'SIF_Number'         => '2033147',

        'account_title'      => 'MD. MOKHLESUR RAHMAN',
        'customer_name'      => 'MD. MOKHLESUR RAHMAN',

        'contact_no_1'       => '8801821215601',
        'mobile_number'      => '8801821215601',

        'email_1'            => 'mrahman55@gmail.com',
        'email_address'      => 'mrahman55@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MMONIRUKH',
        'account_number'     => '2146216011916',
        'SIF_Number'         => '1906182',

        'account_title'      => 'MD.MONIRUZZAMAN KHAN',
        'customer_name'      => 'MD.MONIRUZZAMAN KHAN',

        'contact_no_1'       => '6593392930',
        'mobile_number'      => '6593392930',

        'email_1'            => 'biplob001@yahoo.com',
        'email_address'      => 'biplob001@yahoo.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MMORSHE',
        'account_number'     => '2118219034299',
        'SIF_Number'         => '2498621',

        'account_title'      => 'MD MORSHED ALAM',
        'customer_name'      => 'MD MORSHED ALAM',

        'contact_no_1'       => '8801713191388',
        'mobile_number'      => '8801713191388',

        'email_1'            => 'morshed.alam@gmail.com',
        'email_address'      => 'morshed.alam@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MNASIMHAI',
        'account_number'     => '2118211004043',
        'SIF_Number'         => '736188',

        'account_title'      => 'MUHAMMAD NASIM HAIDER',
        'customer_name'      => 'MUHAMMAD NASIM HAIDER',

        'contact_no_1'       => '8801745772278',
        'mobile_number'      => '8801745772278',

        'email_1'            => 'nhaider@yahoo.com',
        'email_address'      => 'nhaider@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MNAZIRPRM',
        'account_number'     => '3134219023423',
        'SIF_Number'         => '2499903',

        'account_title'      => 'MR S M NAZIR AHMED',
        'customer_name'      => 'MR S M NAZIR AHMED',

        'contact_no_1'       => '8801761573719',
        'mobile_number'      => '8801761573719',

        'email_1'            => 'nazirpdb@gmail.com',
        'email_address'      => 'nazirpdb@gmail.com',

        'branch_name'        => 'IBB O.R. NIZAM ROAD BR',
        'acc_opening_branch' => 'IBB O.R. NIZAM ROAD BR',
    ],
    [
        'bp_id'              => 'IN137MOAZZEMPB',
        'account_number'     => '2118217027774',
        'SIF_Number'         => '2227226',

        'account_title'      => 'MD. MOAZZEM HOSSAIN SHUMON',
        'customer_name'      => 'MD. MOAZZEM HOSSAIN SHUMON',

        'contact_no_1'       => '8801755576299',
        'mobile_number'      => '8801755576299',

        'email_1'            => 'mhshumon@gmail.com',
        'email_address'      => 'mhshumon@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOGANYPB',
        'account_number'     => '2206213010439',
        'SIF_Number'         => '2638054',

        'account_title'      => 'MOHAMMAD OSMAN GANY',
        'customer_name'      => 'MOHAMMAD OSMAN GANY',

        'contact_no_1'       => '8801749449265',
        'mobile_number'      => '8801749449265',

        'email_1'            => 'osmangonybappy@gmail.com',
        'email_address'      => 'osmangonybappy@gmail.com',

        'branch_name'        => 'KHULSHI BRANCH',
        'acc_opening_branch' => 'KHULSHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOHALIPB',
        'account_number'     => '2118211040188',
        'SIF_Number'         => '2759215',

        'account_title'      => 'MOHAMMAD ALI',
        'customer_name'      => 'MOHAMMAD ALI',

        'contact_no_1'       => '8801718452878',
        'mobile_number'      => '8801718452878',

        'email_1'            => 'captma@gmail.com',
        'email_address'      => 'captma@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOHIDPRIM',
        'account_number'     => '2219211008877',
        'SIF_Number'         => '2400820',

        'account_title'      => 'MOHIDUL HAQUE SAUROV',
        'customer_name'      => 'MOHIDUL HAQUE SAUROV',

        'contact_no_1'       => '8801716197765',
        'mobile_number'      => '8801716197765',

        'email_1'            => 'du.saurov@gmail.com',
        'email_address'      => 'du.saurov@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOHMONIPB',
        'account_number'     => '3108211063197',
        'SIF_Number'         => '2873550',

        'account_title'      => 'MOHAMMOD MONIRUL ISLAM',
        'customer_name'      => 'MOHAMMOD MONIRUL ISLAM',

        'contact_no_1'       => '8801841018363',
        'mobile_number'      => '8801841018363',

        'email_1'            => 'monirul.fcc27@gmail.com',
        'email_address'      => 'monirul.fcc27@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOHNOORPB',
        'account_number'     => '2104213021132',
        'SIF_Number'         => '742316',

        'account_title'      => 'MOHAMMAD NOOR A ALAM',
        'customer_name'      => 'MOHAMMAD NOOR A ALAM',

        'contact_no_1'       => '8801730329949',
        'mobile_number'      => '8801730329949',

        'email_1'            => 'mn080201@primebank.com.bd',
        'email_address'      => 'mn080201@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOINISPRM',
        'account_number'     => '2107219032726',
        'SIF_Number'         => '2621454',

        'account_title'      => 'MOHD. MOINUL ISLAM',
        'customer_name'      => 'MOHD. MOINUL ISLAM',

        'contact_no_1'       => '8801819710113',
        'mobile_number'      => '8801819710113',

        'email_1'            => 'shantonu26@gmail.com',
        'email_address'      => 'shantonu26@gmail.com',

        'branch_name'        => 'KHULNA BRANCH',
        'acc_opening_branch' => 'KHULNA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOINPRIME',
        'account_number'     => '2126214005930',
        'SIF_Number'         => '1784872',

        'account_title'      => 'MOIN UDDIN AHMED .',
        'customer_name'      => 'MOIN UDDIN AHMED .',

        'contact_no_1'       => '8801713062928',
        'mobile_number'      => '8801713062928',

        'email_1'            => 'moin.ahmed.bd@gmail.com',
        'email_address'      => 'moin.ahmed.bd@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137MOIPRIMEB',
        'account_number'     => '2162217016715',
        'SIF_Number'         => '945',

        'account_title'      => 'MD MOINUL HASAN',
        'customer_name'      => 'MD MOINUL HASAN',

        'contact_no_1'       => '8801714045738',
        'mobile_number'      => '8801714045738',

        'email_1'            => 'mh072001@gmail.com',
        'email_address'      => 'mh072001@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOKTADPRM',
        'account_number'     => '3133212031523',
        'SIF_Number'         => '2642456',

        'account_title'      => 'MOKTADIR RAHMAN',
        'customer_name'      => 'MOKTADIR RAHMAN',

        'contact_no_1'       => '8801914403657',
        'mobile_number'      => '8801914403657',

        'email_1'            => 'moktadir1979@gmail.com',
        'email_address'      => 'moktadir1979@gmail.com',

        'branch_name'        => 'IBB MIRPUR BRANCH',
        'acc_opening_branch' => 'IBB MIRPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOLLAHPRM',
        'account_number'     => '2125211007774',
        'SIF_Number'         => '410286',

        'account_title'      => 'DELWAR HOSSAIN MOLLAH',
        'customer_name'      => 'DELWAR HOSSAIN MOLLAH',

        'contact_no_1'       => '8801824544872',
        'mobile_number'      => '8801824544872',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOLLASLPB',
        'account_number'     => '2192215010318',
        'SIF_Number'         => '2420146',

        'account_title'      => 'MOLLA SALAUDDIN',
        'customer_name'      => 'MOLLA SALAUDDIN',

        'contact_no_1'       => '8801713069127',
        'mobile_number'      => '8801713069127',

        'email_1'            => 'amir.sons@yahoo.com',
        'email_address'      => 'amir.sons@yahoo.com',

        'branch_name'        => 'BANASREE BRANCH',
        'acc_opening_branch' => 'BANASREE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOMAHMPRM',
        'account_number'     => '2159213010154',
        'SIF_Number'         => '1902721',

        'account_title'      => 'MOMOTAJ AHMED',
        'customer_name'      => 'MOMOTAJ AHMED',

        'contact_no_1'       => '8801819277509',
        'mobile_number'      => '8801819277509',

        'email_1'            => 'shameemahmed304@gmail.com',
        'email_address'      => 'shameemahmed304@gmail.com',

        'branch_name'        => 'BIJOY NAGAR BRANCH',
        'acc_opening_branch' => 'BIJOY NAGAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOMENTPRM',
        'account_number'     => '2165211002891',
        'SIF_Number'         => '818441',

        'account_title'      => 'ABU WAHED NURUL MOMEN TALUKDAR',
        'customer_name'      => 'ABU WAHED NURUL MOMEN TALUKDAR',

        'contact_no_1'       => '8801817144137',
        'mobile_number'      => '8801817144137',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOMEPRM',
        'account_number'     => '2146219019262',
        'SIF_Number'         => '2472316',

        'account_title'      => 'MOMENA KHATUN',
        'customer_name'      => 'MOMENA KHATUN',

        'contact_no_1'       => '8801742572899',
        'mobile_number'      => '8801742572899',

        'email_1'            => 'khatun.momena2@gmail.com',
        'email_address'      => 'khatun.momena2@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOMINUJPB',
        'account_number'     => '2152214009970',
        'SIF_Number'         => '155634',

        'account_title'      => 'MOMIN ULLAH JAVED',
        'customer_name'      => 'MOMIN ULLAH JAVED',

        'contact_no_1'       => '8801911148262',
        'mobile_number'      => '8801911148262',

        'email_1'            => 'javedmomin1960@gmail.com',
        'email_address'      => 'javedmomin1960@gmail.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOMOTAJA',
        'account_number'     => '2187217010701',
        'SIF_Number'         => '2171432',

        'account_title'      => 'MOMOTAJ AKTER AKHI',
        'customer_name'      => 'MOMOTAJ AKTER AKHI',

        'contact_no_1'       => '8801969301762',
        'mobile_number'      => '8801969301762',

        'email_1'            => 'rahmanakhi852@gmail.com',
        'email_address'      => 'rahmanakhi852@gmail.com',

        'branch_name'        => 'WARI BRANCH',
        'acc_opening_branch' => 'WARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONIRAAH',
        'account_number'     => '0021010023076',
        'SIF_Number'         => '1382966',

        'account_title'      => 'MONIRA AHMED CHOWDHURY',
        'customer_name'      => 'MONIRA AHMED CHOWDHURY',

        'contact_no_1'       => '8801894635355',
        'mobile_number'      => '8801894635355',

        'email_1'            => 'zayed.islam.88@gmail.com',
        'email_address'      => 'zayed.islam.88@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONIRAKPB',
        'account_number'     => '2118214031365',
        'SIF_Number'         => '2380658',

        'account_title'      => 'MONIRA KHATUN',
        'customer_name'      => 'MONIRA KHATUN',

        'contact_no_1'       => '8801670113724',
        'mobile_number'      => '8801670113724',

        'email_1'            => 'yousuf.findu@gmail.com',
        'email_address'      => 'yousuf.findu@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONIRCFPB',
        'account_number'     => '2104216036715',
        'SIF_Number'         => '2353712',

        'account_title'      => 'MD MONIRUZZAMAN',
        'customer_name'      => 'MD MONIRUZZAMAN',

        'contact_no_1'       => '8801730328813',
        'mobile_number'      => '8801730328813',

        'email_1'            => 'monir1213@gmail.com',
        'email_address'      => 'monir1213@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONIRUDPB',
        'account_number'     => '2186211025144',
        'SIF_Number'         => '2601812',

        'account_title'      => 'MD. MONIR UDDIN',
        'customer_name'      => 'MD. MONIR UDDIN',

        'contact_no_1'       => '8801878904875',
        'mobile_number'      => '8801878904875',

        'email_1'            => 'moniruddin907080@gmail.com',
        'email_address'      => 'moniruddin907080@gmail.com',

        'branch_name'        => 'JATRABARI BRANCH',
        'acc_opening_branch' => 'JATRABARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONIRULAL',
        'account_number'     => '2104211023610',
        'SIF_Number'         => '1871394',

        'account_title'      => 'MOHAMMAD MONIRUL ALAM',
        'customer_name'      => 'MOHAMMAD MONIRUL ALAM',

        'contact_no_1'       => '8801711272772',
        'mobile_number'      => '8801711272772',

        'email_1'            => 'monirul_alam@live.com',
        'email_address'      => 'monirul_alam@live.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONIRULPB',
        'account_number'     => '2118217033178',
        'SIF_Number'         => '2430026',

        'account_title'      => 'MD MONIRUL HAQ',
        'customer_name'      => 'MD MONIRUL HAQ',

        'contact_no_1'       => '8801818438927',
        'mobile_number'      => '8801818438927',

        'email_1'            => 'monirulhaq1983@gmail.com',
        'email_address'      => 'monirulhaq1983@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONISBBPB',
        'account_number'     => '2104213038599',
        'SIF_Number'         => '2440611',

        'account_title'      => 'MONIRUL ISLAM',
        'customer_name'      => 'MONIRUL ISLAM',

        'contact_no_1'       => '8801714288685',
        'mobile_number'      => '8801714288685',

        'email_1'            => 'monirdubba@gmail.com',
        'email_address'      => 'monirdubba@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONJURPB',
        'account_number'     => '2104212000079',
        'SIF_Number'         => '903286',

        'account_title'      => 'MONJURUL HASAN',
        'customer_name'      => 'MONJURUL HASAN',

        'contact_no_1'       => '8801712528483',
        'mobile_number'      => '8801712528483',

        'email_1'            => 'mh080111@primebank.com.bd',
        'email_address'      => 'mh080111@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONKHAPB',
        'account_number'     => '2141212018282',
        'SIF_Number'         => '2671058',

        'account_title'      => 'MST MONOARA KHANOM',
        'customer_name'      => 'MST MONOARA KHANOM',

        'contact_no_1'       => '8801711188255',
        'mobile_number'      => '8801711188255',

        'email_1'            => 'dr.pia090@gmail.com',
        'email_address'      => 'dr.pia090@gmail.com',

        'branch_name'        => 'PANTHAPATH BRANCH',
        'acc_opening_branch' => 'PANTHAPATH BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONOARAPB',
        'account_number'     => '2118211036475',
        'SIF_Number'         => '2600017',

        'account_title'      => 'MONOARA KHATOON',
        'customer_name'      => 'MONOARA KHATOON',

        'contact_no_1'       => '8801711080129',
        'mobile_number'      => '8801711080129',

        'email_1'            => 'shoma264@gmail.com',
        'email_address'      => 'shoma264@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONOWARPB',
        'account_number'     => '2104211004594',
        'SIF_Number'         => '10321',

        'account_title'      => 'MOHAMMAD MONOWAR HOSSAIN',
        'customer_name'      => 'MOHAMMAD MONOWAR HOSSAIN',

        'contact_no_1'       => '8801847052272',
        'mobile_number'      => '8801847052272',

        'email_1'            => 'monowar.hossain@primebank.com.bd',
        'email_address'      => 'monowar.hossain@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONPRIMEB',
        'account_number'     => '3108212020519',
        'SIF_Number'         => '1541245',

        'account_title'      => 'NURJAHAN MONI',
        'customer_name'      => 'NURJAHAN MONI',

        'contact_no_1'       => '8801791103244',
        'mobile_number'      => '8801791103244',

        'email_1'            => 'nurjahan.moni1973@gmail.com',
        'email_address'      => 'nurjahan.moni1973@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONWARAPB',
        'account_number'     => '2138211015707',
        'SIF_Number'         => '2030622',

        'account_title'      => 'MONWARA BEGUM',
        'customer_name'      => 'MONWARA BEGUM',

        'contact_no_1'       => '8801713279700',
        'mobile_number'      => '8801713279700',

        'email_1'            => 'raleeniba@gmail.com',
        'email_address'      => 'raleeniba@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONZUHQPR',
        'account_number'     => '2140211020025',
        'SIF_Number'         => '2106280',

        'account_title'      => 'MD. MONZURUL HOQUE',
        'customer_name'      => 'MD. MONZURUL HOQUE',

        'contact_no_1'       => '8801756970683',
        'mobile_number'      => '8801756970683',

        'email_1'            => 'drmonzurulhaque@gmail.com',
        'email_address'      => 'drmonzurulhaque@gmail.com',

        'branch_name'        => 'TONGI BRANCH',
        'acc_opening_branch' => 'TONGI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MONZURMPB',
        'account_number'     => '2118216040935',
        'SIF_Number'         => '2782115',

        'account_title'      => 'SHAH MD. MONZUR MORSHED',
        'customer_name'      => 'SHAH MD. MONZUR MORSHED',

        'contact_no_1'       => '8801673099485',
        'mobile_number'      => '8801673099485',

        'email_1'            => 's3mimran@yahoo.com',
        'email_address'      => 's3mimran@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MORSHEDPB',
        'account_number'     => '2118214029575',
        'SIF_Number'         => '271348',

        'account_title'      => 'KAZI NAIM MORSHED',
        'customer_name'      => 'KAZI NAIM MORSHED',

        'contact_no_1'       => '8801812375105',
        'mobile_number'      => '8801812375105',

        'email_1'            => 'knmbd@yahoo.com',
        'email_address'      => 'knmbd@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MORTUPRM',
        'account_number'     => '2104214002867',
        'SIF_Number'         => '14357',

        'account_title'      => 'SHEIKH MORTUZA AHMED',
        'customer_name'      => 'SHEIKH MORTUZA AHMED',

        'contact_no_1'       => '8801714007666',
        'mobile_number'      => '8801714007666',

        'email_1'            => 'mortuza.ahmed@gmail.com',
        'email_address'      => 'mortuza.ahmed@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOSAAHPRM',
        'account_number'     => '2159218000002',
        'SIF_Number'         => '160920',

        'account_title'      => 'MOHAMMED SHAMEEM AHMED',
        'customer_name'      => 'MOHAMMED SHAMEEM AHMED',

        'contact_no_1'       => '8801819277509',
        'mobile_number'      => '8801819277509',

        'email_1'            => 'shameemahmed304@gmail.com',
        'email_address'      => 'shameemahmed304@gmail.com',

        'branch_name'        => 'BIJOY NAGAR BRANCH',
        'acc_opening_branch' => 'BIJOY NAGAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOSAHPRIM',
        'account_number'     => '2117212010024',
        'SIF_Number'         => '139947',

        'account_title'      => 'MD MOSHARRAF HOSSAIN',
        'customer_name'      => 'MD MOSHARRAF HOSSAIN',

        'contact_no_1'       => '8801711592605',
        'mobile_number'      => '8801711592605',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOSAPRIME',
        'account_number'     => '2104215027699',
        'SIF_Number'         => '906',

        'account_title'      => 'MONOWARA SARKAR',
        'customer_name'      => 'MONOWARA SARKAR',

        'contact_no_1'       => '8801711638452',
        'mobile_number'      => '8801711638452',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOSAYEBPB',
        'account_number'     => '2118211033877',
        'SIF_Number'         => '2484109',

        'account_title'      => 'SEYED MOSAYEB ALAM',
        'customer_name'      => 'SEYED MOSAYEB ALAM',

        'contact_no_1'       => '8801313795795',
        'mobile_number'      => '8801313795795',

        'email_1'            => 'syedmosayebalam@gmail.com',
        'email_address'      => 'syedmosayebalam@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOSTFZRPB',
        'account_number'     => '2125213013076',
        'SIF_Number'         => '1047564',

        'account_title'      => 'MOSTAFIZUR RAHMAN',
        'customer_name'      => 'MOSTAFIZUR RAHMAN',

        'contact_no_1'       => '8801689273341',
        'mobile_number'      => '8801689273341',

        'email_1'            => 'mostafiz23@gmail.com',
        'email_address'      => 'mostafiz23@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOSUMISPB',
        'account_number'     => '2122214000778',
        'SIF_Number'         => '1023464',

        'account_title'      => 'MOUSUMIE SUBRIN',
        'customer_name'      => 'MOUSUMIE SUBRIN',

        'contact_no_1'       => '8801924400822',
        'mobile_number'      => '8801924400822',

        'email_1'            => 'mousumi.subrin@gmail.com',
        'email_address'      => 'mousumi.subrin@gmail.com',

        'branch_name'        => 'BANGSHAL BRANCH',
        'acc_opening_branch' => 'BANGSHAL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOTIARPB',
        'account_number'     => '3133217033409',
        'SIF_Number'         => '2214951',

        'account_title'      => 'MD. MOTIAR RAHMAN',
        'customer_name'      => 'MD. MOTIAR RAHMAN',

        'contact_no_1'       => '8801730098076',
        'mobile_number'      => '8801730098076',

        'email_1'            => 'motiar.rahman@primebank.com.bd',
        'email_address'      => 'motiar.rahman@primebank.com.bd',

        'branch_name'        => 'IBB MIRPUR BRANCH',
        'acc_opening_branch' => 'IBB MIRPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOYJDAHPB',
        'account_number'     => '2128212021168',
        'SIF_Number'         => '2537838',

        'account_title'      => 'MD MOYJUDDIN AHMED',
        'customer_name'      => 'MD MOYJUDDIN AHMED',

        'contact_no_1'       => '8801517260120',
        'mobile_number'      => '8801517260120',

        'email_1'            => 'moyjuddin@gmail.com',
        'email_address'      => 'moyjuddin@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137MOZAMKHAN',
        'account_number'     => '2118219027442',
        'SIF_Number'         => '2213253',

        'account_title'      => 'MD MOZAMMEL HOSSAIN KHAN',
        'customer_name'      => 'MD MOZAMMEL HOSSAIN KHAN',

        'contact_no_1'       => '8801713045262',
        'mobile_number'      => '8801713045262',

        'email_1'            => 'mhk1022@gmail.com',
        'email_address'      => 'mhk1022@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MRARIFEEN',
        'account_number'     => '2104216013284',
        'SIF_Number'         => '23110',

        'account_title'      => 'MR. QUAZI M.M. ARIFEEN',
        'customer_name'      => 'MR. QUAZI M.M. ARIFEEN',

        'contact_no_1'       => '8801777689729',
        'mobile_number'      => '8801777689729',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MRASHIDPB',
        'account_number'     => '3133218040502',
        'SIF_Number'         => '2756537',

        'account_title'      => 'MD MAMUN OR RASHID',
        'customer_name'      => 'MD MAMUN OR RASHID',

        'contact_no_1'       => '8801715715277',
        'mobile_number'      => '8801715715277',

        'email_1'            => 'mamun1982@gmail.com',
        'email_address'      => 'mamun1982@gmail.com',

        'branch_name'        => 'IBB MIRPUR BRANCH',
        'acc_opening_branch' => 'IBB MIRPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MREHPRIME',
        'account_number'     => '2104212028146',
        'SIF_Number'         => '857',

        'account_title'      => 'MOHAMMAD REZA HYDER',
        'customer_name'      => 'MOHAMMAD REZA HYDER',

        'contact_no_1'       => '8801730333013',
        'mobile_number'      => '8801730333013',

        'email_1'            => 'reza.hyder@gmail.com',
        'email_address'      => 'reza.hyder@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MRMDTAFOY',
        'account_number'     => '2120215018995',
        'SIF_Number'         => '2044529',

        'account_title'      => 'MR MD. TANVIR FOYSAL',
        'customer_name'      => 'MR MD. TANVIR FOYSAL',

        'contact_no_1'       => '8801886534648',
        'mobile_number'      => '8801886534648',

        'email_1'            => 'tanvir3703@gmail.com',
        'email_address'      => 'tanvir3703@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137MRSAFROZ',
        'account_number'     => '2125218025075',
        'SIF_Number'         => '2165081',

        'account_title'      => 'MRS. FARZANA AFROZ',
        'customer_name'      => 'MRS. FARZANA AFROZ',

        'contact_no_1'       => '8801611220052',
        'mobile_number'      => '8801611220052',

        'email_1'            => 'ahmfaysal@gmail.com',
        'email_address'      => 'ahmfaysal@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MRSMEHNAF',
        'account_number'     => '2132213019991',
        'SIF_Number'         => '2017772',

        'account_title'      => 'MRS. MEHNAZ AFROZ',
        'customer_name'      => 'MRS. MEHNAZ AFROZ',

        'contact_no_1'       => '8801685678383',
        'mobile_number'      => '8801685678383',

        'email_1'            => 'mehnazafroz26@gmail.com',
        'email_address'      => 'mehnazafroz26@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MRUBAIYAT',
        'account_number'     => '2104211031266',
        'SIF_Number'         => '1997790',

        'account_title'      => 'MD. RUBAIYAT ZAHIR',
        'customer_name'      => 'MD. RUBAIYAT ZAHIR',

        'contact_no_1'       => '8801711442481',
        'mobile_number'      => '8801711442481',

        'email_1'            => 'rubaiyat2013@gmail.com',
        'email_address'      => 'rubaiyat2013@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSAIFU',
        'account_number'     => '2207215004991',
        'SIF_Number'         => '2104994',

        'account_title'      => 'MD. SAIFUL HUQUE',
        'customer_name'      => 'MD. SAIFUL HUQUE',

        'contact_no_1'       => '8801601950344',
        'mobile_number'      => '8801601950344',

        'email_1'            => 'saifhuque13@gmail.com',
        'email_address'      => 'saifhuque13@gmail.com',

        'branch_name'        => 'TEJGAON BRANCH',
        'acc_opening_branch' => 'TEJGAON BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSAIFUDPB',
        'account_number'     => '2207213008242',
        'SIF_Number'         => '2544649',

        'account_title'      => 'MOHAMMAD SAIFUDDIN KHAN',
        'customer_name'      => 'MOHAMMAD SAIFUDDIN KHAN',

        'contact_no_1'       => '8801730333070',
        'mobile_number'      => '8801730333070',

        'email_1'            => 'mdsaifuddinkhan@yahoo.com',
        'email_address'      => 'mdsaifuddinkhan@yahoo.com',

        'branch_name'        => 'TEJGAON BRANCH',
        'acc_opening_branch' => 'TEJGAON BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSAIFULHU',
        'account_number'     => '2207215004991',
        'SIF_Number'         => '2104994',

        'account_title'      => 'MD. SAIFUL HUQUE',
        'customer_name'      => 'MD. SAIFUL HUQUE',

        'contact_no_1'       => '8801601950344',
        'mobile_number'      => '8801601950344',

        'email_1'            => 'saifhuque13@gmail.com',
        'email_address'      => 'saifhuque13@gmail.com',

        'branch_name'        => 'TEJGAON BRANCH',
        'acc_opening_branch' => 'TEJGAON BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSAJIDPRM',
        'account_number'     => '2120213003882',
        'SIF_Number'         => '807050',

        'account_title'      => 'MOHAMMAD SAJID RAIHAN',
        'customer_name'      => 'MOHAMMAD SAJID RAIHAN',

        'contact_no_1'       => '8801713162409',
        'mobile_number'      => '8801713162409',

        'email_1'            => 'sajidctg@gmail.com',
        'email_address'      => 'sajidctg@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSAJJADPB',
        'account_number'     => '2104215021411',
        'SIF_Number'         => '803264',

        'account_title'      => 'MOHAMMAD SAJJADUL ISLAM',
        'customer_name'      => 'MOHAMMAD SAJJADUL ISLAM',

        'contact_no_1'       => '8801841155666',
        'mobile_number'      => '8801841155666',

        'email_1'            => 'sajjad10@primebank.com.bd',
        'email_address'      => 'sajjad10@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSBABY',
        'account_number'     => '2119214011341',
        'SIF_Number'         => '150940',

        'account_title'      => 'MS BABY NAJNIN',
        'customer_name'      => 'MS BABY NAJNIN',

        'contact_no_1'       => '8801720158499',
        'mobile_number'      => '8801720158499',

        'email_1'            => 'babynazninkhan@gmail.com',
        'email_address'      => 'babynazninkhan@gmail.com',

        'branch_name'        => 'NARAYANGONJ BRANCH',
        'acc_opening_branch' => 'NARAYANGONJ BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSHADHPRM',
        'account_number'     => '2104215022062',
        'SIF_Number'         => '903002',

        'account_title'      => 'MOHAMMAD SHADAT HOSSAIN',
        'customer_name'      => 'MOHAMMAD SHADAT HOSSAIN',

        'contact_no_1'       => '8801981343019',
        'mobile_number'      => '8801981343019',

        'email_1'            => 'mh080201@primebank.com.bd',
        'email_address'      => 'mh080201@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSHAFIQPB',
        'account_number'     => '2125212013010',
        'SIF_Number'         => '412395',

        'account_title'      => 'MOHAMMAD SHAFIQUL ISLAM',
        'customer_name'      => 'MOHAMMAD SHAFIQUL ISLAM',

        'contact_no_1'       => '8801770834403',
        'mobile_number'      => '8801770834403',

        'email_1'            => 'mshafiq2007@gmail.com',
        'email_address'      => 'mshafiq2007@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSHAFIUPB',
        'account_number'     => '3108218012585',
        'SIF_Number'         => '843877',

        'account_title'      => 'MOHAMMAD SHAFIULLAH',
        'customer_name'      => 'MOHAMMAD SHAFIULLAH',

        'contact_no_1'       => '8801730704147',
        'mobile_number'      => '8801730704147',

        'email_1'            => 'maskrose.shafi@gmail.com',
        'email_address'      => 'maskrose.shafi@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSHAHIDPB',
        'account_number'     => '2104215021957',
        'SIF_Number'         => '1857815',

        'account_title'      => 'MD. SHAHIDUZZAMAN',
        'customer_name'      => 'MD. SHAHIDUZZAMAN',

        'contact_no_1'       => '8801795110110',
        'mobile_number'      => '8801795110110',

        'email_1'            => 'shahiduzzaman@primebank.com.bd',
        'email_address'      => 'shahiduzzaman@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSHAKHAWA',
        'account_number'     => '2119214008644',
        'SIF_Number'         => '149396',

        'account_title'      => 'MR MD SHAKHAWAT HOSSAIN KHAN',
        'customer_name'      => 'MR MD SHAKHAWAT HOSSAIN KHAN',

        'contact_no_1'       => '8801720158499',
        'mobile_number'      => '8801720158499',

        'email_1'            => 'shakhawatskt@gmail.com',
        'email_address'      => 'shakhawatskt@gmail.com',

        'branch_name'        => 'NARAYANGONJ BRANCH',
        'acc_opening_branch' => 'NARAYANGONJ BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSHAMSCHO',
        'account_number'     => '2118218011462',
        'SIF_Number'         => '82722',

        'account_title'      => 'MD. SHAMSUL HAQUE CHOWDHURY',
        'customer_name'      => 'MD. SHAMSUL HAQUE CHOWDHURY',

        'contact_no_1'       => '8801841256549',
        'mobile_number'      => '8801841256549',

        'email_1'            => 'shamsul1952@yahoo.com',
        'email_address'      => 'shamsul1952@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSHARIFPB',
        'account_number'     => '2127212026119',
        'SIF_Number'         => '2598692',

        'account_title'      => 'MD. SHARIF UL ALAM',
        'customer_name'      => 'MD. SHARIF UL ALAM',

        'contact_no_1'       => '8801810186901',
        'mobile_number'      => '8801810186901',

        'email_1'            => 'srpbiz09@gmail.com',
        'email_address'      => 'srpbiz09@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSHARIPRI',
        'account_number'     => '2164217023636',
        'SIF_Number'         => '2004730',

        'account_title'      => 'MD. SHARIFUL ISLAM',
        'customer_name'      => 'MD. SHARIFUL ISLAM',

        'contact_no_1'       => '8801715312261',
        'mobile_number'      => '8801715312261',

        'email_1'            => 'solution.instrument@gmail.com',
        'email_address'      => 'solution.instrument@gmail.com',

        'branch_name'        => 'PALLABI BRANCH',
        'acc_opening_branch' => 'PALLABI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSNAZIFAM',
        'account_number'     => '2171215011550',
        'SIF_Number'         => '2083406',

        'account_title'      => 'MS NAZIFA MAHZABIN',
        'customer_name'      => 'MS NAZIFA MAHZABIN',

        'contact_no_1'       => '8801775174853',
        'mobile_number'      => '8801775174853',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'PROBARTAK MORE BRANCH',
        'acc_opening_branch' => 'PROBARTAK MORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSNISHAT',
        'account_number'     => '2104214015448',
        'SIF_Number'         => '23127',

        'account_title'      => 'MS NISHAT AHAMED',
        'customer_name'      => 'MS NISHAT AHAMED',

        'contact_no_1'       => '8801819261381',
        'mobile_number'      => '8801819261381',

        'email_1'            => 'nishatahmed1958@gmail.com',
        'email_address'      => 'nishatahmed1958@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MSTASLIMA',
        'account_number'     => '2516219005578',
        'SIF_Number'         => '2396587',

        'account_title'      => 'MS TASLIMA AKTER',
        'customer_name'      => 'MS TASLIMA AKTER',

        'contact_no_1'       => '8801828420130',
        'mobile_number'      => '8801828420130',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MADUNAGHAT SME BR/SC',
        'acc_opening_branch' => 'MADUNAGHAT SME BR/SC',
    ],
    [
        'bp_id'              => 'IN137MSTRAHIMA',
        'account_number'     => '2212216009646',
        'SIF_Number'         => '2480177',

        'account_title'      => 'MST. RAHIMA KHATUN',
        'customer_name'      => 'MST. RAHIMA KHATUN',

        'contact_no_1'       => '8801723664817',
        'mobile_number'      => '8801723664817',

        'email_1'            => 'rahimakhatun01893@gmail.com',
        'email_address'      => 'rahimakhatun01893@gmail.com',

        'branch_name'        => 'MAHADEBPUR BRANCH',
        'acc_opening_branch' => 'MAHADEBPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUHITPRIM',
        'account_number'     => '2126213012937',
        'SIF_Number'         => '2425915',

        'account_title'      => 'MOHAMMAD YOUSUF MUHIT',
        'customer_name'      => 'MOHAMMAD YOUSUF MUHIT',

        'contact_no_1'       => '8801921252607',
        'mobile_number'      => '8801921252607',

        'email_1'            => 'md.yousufmuhit@yahoo.com',
        'email_address'      => 'md.yousufmuhit@yahoo.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137MUKULAPB',
        'account_number'     => '2104218020832',
        'SIF_Number'         => '10317',

        'account_title'      => 'MD MUKUL ALI',
        'customer_name'      => 'MD MUKUL ALI',

        'contact_no_1'       => '8801718233234',
        'mobile_number'      => '8801718233234',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUNANDYPB',
        'account_number'     => '2120215005695',
        'SIF_Number'         => '689910',

        'account_title'      => 'MUNNI NANDY',
        'customer_name'      => 'MUNNI NANDY',

        'contact_no_1'       => '8801777753311',
        'mobile_number'      => '8801777753311',

        'email_1'            => 'nandymunni@gmail.com',
        'email_address'      => 'nandymunni@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUNIARFPB',
        'account_number'     => '2192211010924',
        'SIF_Number'         => '2492173',

        'account_title'      => 'MUNIA AHMED RAFIQUE',
        'customer_name'      => 'MUNIA AHMED RAFIQUE',

        'contact_no_1'       => '8801550151163',
        'mobile_number'      => '8801550151163',

        'email_1'            => 'munia.ahmed963@gmail.com',
        'email_address'      => 'munia.ahmed963@gmail.com',

        'branch_name'        => 'BANASREE BRANCH',
        'acc_opening_branch' => 'BANASREE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUNJIBAPB',
        'account_number'     => '2118214018230',
        'SIF_Number'         => '74147',

        'account_title'      => 'MUNJIBA SHAMS',
        'customer_name'      => 'MUNJIBA SHAMS',

        'contact_no_1'       => '8801835090237',
        'mobile_number'      => '8801835090237',

        'email_1'            => 'munjiba@yahoo.com',
        'email_address'      => 'munjiba@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUNSHIPB',
        'account_number'     => '2110218005900',
        'SIF_Number'         => '274335',

        'account_title'      => 'MOHAMMAD KOWSER MUNSHI',
        'customer_name'      => 'MOHAMMAD KOWSER MUNSHI',

        'contact_no_1'       => '8801819260838',
        'mobile_number'      => '8801819260838',

        'email_1'            => 'kowser.munshi@gmail.com',
        'email_address'      => 'kowser.munshi@gmail.com',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUNSHIZAK',
        'account_number'     => '2128212021937',
        'SIF_Number'         => '2615173',

        'account_title'      => 'MUNSHI ABU ZAKARIA',
        'customer_name'      => 'MUNSHI ABU ZAKARIA',

        'contact_no_1'       => '8801715024240',
        'mobile_number'      => '8801715024240',

        'email_1'            => 'zakarianim1993@gmail.com',
        'email_address'      => 'zakarianim1993@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUREZAPB',
        'account_number'     => '2219215006174',
        'SIF_Number'         => '701641',

        'account_title'      => 'MURSHED REZA',
        'customer_name'      => 'MURSHED REZA',

        'contact_no_1'       => '8801713039194',
        'mobile_number'      => '8801713039194',

        'email_1'            => 'm.reza@aegisint.net',
        'email_address'      => 'm.reza@aegisint.net',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MURSHEDPB',
        'account_number'     => '2126211013326',
        'SIF_Number'         => '2510749',

        'account_title'      => 'MD. MURSHEDUL ALAM',
        'customer_name'      => 'MD. MURSHEDUL ALAM',

        'contact_no_1'       => '8801911152588',
        'mobile_number'      => '8801911152588',

        'email_1'            => 'alamengspr@gmail.com',
        'email_address'      => 'alamengspr@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137MURSHIDK',
        'account_number'     => '2165219015803',
        'SIF_Number'         => '1922415',

        'account_title'      => 'MURSHID KULI KHAN',
        'customer_name'      => 'MURSHID KULI KHAN',

        'contact_no_1'       => '8801730009331',
        'mobile_number'      => '8801730009331',

        'email_1'            => 'mkkhanbb1@gmail.com',
        'email_address'      => 'mkkhanbb1@gmail.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MURSMUSPB',
        'account_number'     => '2148216000834',
        'SIF_Number'         => '1575891',

        'account_title'      => 'MOHAMMED MURSHEDUL MUSLIM',
        'customer_name'      => 'MOHAMMED MURSHEDUL MUSLIM',

        'contact_no_1'       => '8801819291407',
        'mobile_number'      => '8801819291407',

        'email_1'            => 'mohammedmurshed2011@gmail.com',
        'email_address'      => 'mohammedmurshed2011@gmail.com',

        'branch_name'        => 'KAKRAIL BRANCH',
        'acc_opening_branch' => 'KAKRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MURSPRIME',
        'account_number'     => '2114215000546',
        'SIF_Number'         => '265651',

        'account_title'      => 'MURSHEDA AKTER',
        'customer_name'      => 'MURSHEDA AKTER',

        'contact_no_1'       => '8801718258893',
        'mobile_number'      => '8801718258893',

        'email_1'            => 'nazrul.rita@gmail.com',
        'email_address'      => 'nazrul.rita@gmail.com',

        'branch_name'        => 'ELEPHANT ROAD BRANCH',
        'acc_opening_branch' => 'ELEPHANT ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUSHFIQPB',
        'account_number'     => '2118215022603',
        'SIF_Number'         => '2025246',

        'account_title'      => 'MD MUSHFIQUR RAHMAN',
        'customer_name'      => 'MD MUSHFIQUR RAHMAN',

        'contact_no_1'       => '8801751702111',
        'mobile_number'      => '8801751702111',

        'email_1'            => 'mushfiq41@yahoo.com',
        'email_address'      => 'mushfiq41@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUSHRAHPB',
        'account_number'     => '2125211016809',
        'SIF_Number'         => '1660834',

        'account_title'      => 'MUSHFIQUR RAHMAN',
        'customer_name'      => 'MUSHFIQUR RAHMAN',

        'contact_no_1'       => '8801730359483',
        'mobile_number'      => '8801730359483',

        'email_1'            => 'gara07@yahoo.com',
        'email_address'      => 'gara07@yahoo.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUSTAFIZ',
        'account_number'     => '2126217014582',
        'SIF_Number'         => '2642233',

        'account_title'      => 'MD.MUSTAFIZUR RAHMAN',
        'customer_name'      => 'MD.MUSTAFIZUR RAHMAN',

        'contact_no_1'       => '8801711987151',
        'mobile_number'      => '8801711987151',

        'email_1'            => 'sparknir@gmail.com',
        'email_address'      => 'sparknir@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137MUSTAFIZU',
        'account_number'     => '2118116017489',
        'SIF_Number'         => '176621',

        'account_title'      => 'MUSTAFIZUR RAHMAN',
        'customer_name'      => 'MUSTAFIZUR RAHMAN',

        'contact_no_1'       => '8801312934732',
        'mobile_number'      => '8801312934732',

        'email_1'            => 'mustafiz@ndebd.com',
        'email_address'      => 'mustafiz@ndebd.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUSTAPRIM',
        'account_number'     => '2219215008394',
        'SIF_Number'         => '2371748',

        'account_title'      => 'MD. MUSTAFA KAMAL BHUIYAN',
        'customer_name'      => 'MD. MUSTAFA KAMAL BHUIYAN',

        'contact_no_1'       => '8801711104352',
        'mobile_number'      => '8801711104352',

        'email_1'            => 'mustafa2025@yahoo.com',
        'email_address'      => 'mustafa2025@yahoo.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUSTAQPB',
        'account_number'     => '3108211039205',
        'SIF_Number'         => '26832',

        'account_title'      => 'MUSTAQ JAMAL',
        'customer_name'      => 'MUSTAQ JAMAL',

        'contact_no_1'       => '8801712006686',
        'mobile_number'      => '8801712006686',

        'email_1'            => 'mj012809@primebank.com.bd',
        'email_address'      => 'mj012809@primebank.com.bd',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUTASIMPB',
        'account_number'     => '2117218008634',
        'SIF_Number'         => '136753',

        'account_title'      => 'MD. MUTASIM BILLAH',
        'customer_name'      => 'MD. MUTASIM BILLAH',

        'contact_no_1'       => '8801731293291',
        'mobile_number'      => '8801731293291',

        'email_1'            => 'billah64@hotmail.com',
        'email_address'      => 'billah64@hotmail.com',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUZAHIDPB',
        'account_number'     => '2123211004187',
        'SIF_Number'         => '779524',

        'account_title'      => 'MD. MUJAHIDUL ISLAM',
        'customer_name'      => 'MD. MUJAHIDUL ISLAM',

        'contact_no_1'       => '8801716172206',
        'mobile_number'      => '8801716172206',

        'email_1'            => 'mujahidul.islam@primebank.com.bd',
        'email_address'      => 'mujahidul.islam@primebank.com.bd',

        'branch_name'        => 'JESSORE BRANCH',
        'acc_opening_branch' => 'JESSORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUZAPRIME',
        'account_number'     => '2132213026228',
        'SIF_Number'         => '2356301',

        'account_title'      => 'MOHAMMAD MUZAHIDUL KARIM',
        'customer_name'      => 'MOHAMMAD MUZAHIDUL KARIM',

        'contact_no_1'       => '8801713011695',
        'mobile_number'      => '8801713011695',

        'email_1'            => 'muzahidulkarim@yahoo.com',
        'email_address'      => 'muzahidulkarim@yahoo.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MUZIBRPRM',
        'account_number'     => '2107216031952',
        'SIF_Number'         => '1118955',

        'account_title'      => 'MD. MUZIBUR RAHMAN',
        'customer_name'      => 'MD. MUZIBUR RAHMAN',

        'contact_no_1'       => '8801711217953',
        'mobile_number'      => '8801711217953',

        'email_1'            => 'tutul889900@gmail.com',
        'email_address'      => 'tutul889900@gmail.com',

        'branch_name'        => 'KHULNA BRANCH',
        'acc_opening_branch' => 'KHULNA BRANCH',
    ],
    [
        'bp_id'              => 'IN137MYSHELIPB',
        'account_number'     => '2104216018499',
        'SIF_Number'         => '21946',

        'account_title'      => 'MAHBUBA YESMIN SHELLI',
        'customer_name'      => 'MAHBUBA YESMIN SHELLI',

        'contact_no_1'       => '8801816304863',
        'mobile_number'      => '8801816304863',

        'email_1'            => 'shelli_mahbuba@yahoo.com',
        'email_address'      => 'shelli_mahbuba@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137MYUNUSAPB',
        'account_number'     => '2179214019473',
        'SIF_Number'         => '2293571',

        'account_title'      => 'MD. YOUNUS ALI',
        'customer_name'      => 'MD. YOUNUS ALI',

        'contact_no_1'       => '8801790124583',
        'mobile_number'      => '8801790124583',

        'email_1'            => 'younus.auc@gmail.com',
        'email_address'      => 'younus.auc@gmail.com',

        'branch_name'        => 'ISHWARDI BRANCH',
        'acc_opening_branch' => 'ISHWARDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137MYUSUFPRM',
        'account_number'     => '2128217000100',
        'SIF_Number'         => '727961',

        'account_title'      => 'MARIAM YUSUF SHOVA',
        'customer_name'      => 'MARIAM YUSUF SHOVA',

        'contact_no_1'       => '8801963149951',
        'mobile_number'      => '8801963149951',

        'email_1'            => 'mariamshova@gmail.com',
        'email_address'      => 'mariamshova@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137MZAHIDIPB',
        'account_number'     => '2169217010323',
        'SIF_Number'         => '1865261',

        'account_title'      => 'MD. ZAHIDUL ISLAM SARKER',
        'customer_name'      => 'MD. ZAHIDUL ISLAM SARKER',

        'contact_no_1'       => '8801716829038',
        'mobile_number'      => '8801716829038',

        'email_1'            => 'zahidwrc60@gmail.com',
        'email_address'      => 'zahidwrc60@gmail.com',

        'branch_name'        => 'DINAJPUR BRANCH',
        'acc_opening_branch' => 'DINAJPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137MZAMIUL',
        'account_number'     => '2104214021370',
        'SIF_Number'         => '1405154',

        'account_title'      => 'MD. ZAMIUL ISLAM',
        'customer_name'      => 'MD. ZAMIUL ISLAM',

        'contact_no_1'       => '8801712999606',
        'mobile_number'      => '8801712999606',

        'email_1'            => 'zamiul@primebank.com.bd',
        'email_address'      => 'zamiul@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137NABILAAH',
        'account_number'     => '2146211014535',
        'SIF_Number'         => '2120872',

        'account_title'      => 'NABILA AHMED',
        'customer_name'      => 'NABILA AHMED',

        'contact_no_1'       => '8801819207459',
        'mobile_number'      => '8801819207459',

        'email_1'            => 'nabilaahmed@gmail.com',
        'email_address'      => 'nabilaahmed@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137NADHAQPRM',
        'account_number'     => '2104212026645',
        'SIF_Number'         => '1287344',

        'account_title'      => 'NADIA HAQUE',
        'customer_name'      => 'NADIA HAQUE',

        'contact_no_1'       => '8801774464554',
        'mobile_number'      => '8801774464554',

        'email_1'            => 'nadia2012123101@gmail.com',
        'email_address'      => 'nadia2012123101@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137NADIAYOU',
        'account_number'     => '2118213033727',
        'SIF_Number'         => '2478702',

        'account_title'      => 'NADIA YOUSUF CHOWDHURY',
        'customer_name'      => 'NADIA YOUSUF CHOWDHURY',

        'contact_no_1'       => '8801713104556',
        'mobile_number'      => '8801713104556',

        'email_1'            => 'shahid_UNISOFT@YAHOO.COM',
        'email_address'      => 'shahid_UNISOFT@YAHOO.COM',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137NADIRAAKR',
        'account_number'     => '2118211028597',
        'SIF_Number'         => '1368001',

        'account_title'      => 'NADIRA AKHTER',
        'customer_name'      => 'NADIRA AKHTER',

        'contact_no_1'       => '8801915692476',
        'mobile_number'      => '8801915692476',

        'email_1'            => 'nadiraakhter59@gmail.com',
        'email_address'      => 'nadiraakhter59@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAFIZNPRM',
        'account_number'     => '2104217039197',
        'SIF_Number'         => '2503546',

        'account_title'      => 'MD. NAFIZ NEWAZ',
        'customer_name'      => 'MD. NAFIZ NEWAZ',

        'contact_no_1'       => '8801725724609',
        'mobile_number'      => '8801725724609',

        'email_1'            => 'nafiz11.newaz@gmail.com',
        'email_address'      => 'nafiz11.newaz@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAHEANKPB',
        'account_number'     => '2126215013271',
        'SIF_Number'         => '1385729',

        'account_title'      => 'NAHEAN AHMED KHAN',
        'customer_name'      => 'NAHEAN AHMED KHAN',

        'contact_no_1'       => '8801911569490',
        'mobile_number'      => '8801911569490',

        'email_1'            => 'nahean.ahmed@gmail.com',
        'email_address'      => 'nahean.ahmed@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137NAHIDSPRM',
        'account_number'     => '2125217021695',
        'SIF_Number'         => '2022600',

        'account_title'      => 'NAHID SULTANA',
        'customer_name'      => 'NAHID SULTANA',

        'contact_no_1'       => '8801723625367',
        'mobile_number'      => '8801723625367',

        'email_1'            => 'nahidsultana356@yahoo.com',
        'email_address'      => 'nahidsultana356@yahoo.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAHREENPB',
        'account_number'     => '2132211028839',
        'SIF_Number'         => '2487213',

        'account_title'      => 'NAHREEN QUAYYUM',
        'customer_name'      => 'NAHREEN QUAYYUM',

        'contact_no_1'       => '8801819263627',
        'mobile_number'      => '8801819263627',

        'email_1'            => 'nahreen.quayyum@primebank.com.bd',
        'email_address'      => 'nahreen.quayyum@primebank.com.bd',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAILATPRM',
        'account_number'     => '2132211012717',
        'SIF_Number'         => '713532',

        'account_title'      => 'NAILA TAMANNA',
        'customer_name'      => 'NAILA TAMANNA',

        'contact_no_1'       => '8801711503100',
        'mobile_number'      => '8801711503100',

        'email_1'            => 'ntamanna@grameenphone.com',
        'email_address'      => 'ntamanna@grameenphone.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAIMAPUPB',
        'account_number'     => '2104212038541',
        'SIF_Number'         => '2436222',

        'account_title'      => 'NAIMUL APU',
        'customer_name'      => 'NAIMUL APU',

        'contact_no_1'       => '8801755557540',
        'mobile_number'      => '8801755557540',

        'email_1'            => 'opunaimul@gmail.com',
        'email_address'      => 'opunaimul@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAIMUPRIM',
        'account_number'     => '2104211027355',
        'SIF_Number'         => '10630',

        'account_title'      => 'MD NAIMUL HASAN',
        'customer_name'      => 'MD NAIMUL HASAN',

        'contact_no_1'       => '8801916100764',
        'mobile_number'      => '8801916100764',

        'email_1'            => 'naimulhasan@yahoo.com',
        'email_address'      => 'naimulhasan@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAKIBULPB',
        'account_number'     => '2110213000476',
        'SIF_Number'         => '1308023',

        'account_title'      => 'K. M . NAKIBUL ISLAM',
        'customer_name'      => 'K. M . NAKIBUL ISLAM',

        'contact_no_1'       => '8801711133970',
        'mobile_number'      => '8801711133970',

        'email_1'            => 'nakib.sk@gmail.com',
        'email_address'      => 'nakib.sk@gmail.com',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137NANDITAPR',
        'account_number'     => '2110211000908',
        'SIF_Number'         => '273340',

        'account_title'      => 'NANDITA MAJUMDER',
        'customer_name'      => 'NANDITA MAJUMDER',

        'contact_no_1'       => '8801711145041',
        'mobile_number'      => '8801711145041',

        'email_1'            => 'ajit@juniv.edu',
        'email_address'      => 'ajit@juniv.edu',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137NARAYANPB',
        'account_number'     => '2209215013099',
        'SIF_Number'         => '1336075',

        'account_title'      => 'NARAYAN CHAKRABORTY',
        'customer_name'      => 'NARAYAN CHAKRABORTY',

        'contact_no_1'       => '8801712551199',
        'mobile_number'      => '8801712551199',

        'email_1'            => 'narayanctg@gmail.com',
        'email_address'      => 'narayanctg@gmail.com',

        'branch_name'        => 'KARNAFULI EPZ BRANCH',
        'acc_opening_branch' => 'KARNAFULI EPZ BRANCH',
    ],
    [
        'bp_id'              => 'IN137NARGISPRM',
        'account_number'     => '2104217036003',
        'SIF_Number'         => '2319558',

        'account_title'      => 'NARGIS AKTER',
        'customer_name'      => 'NARGIS AKTER',

        'contact_no_1'       => '8801819425318',
        'mobile_number'      => '8801819425318',

        'email_1'            => 'saodahnargis@gmail.com',
        'email_address'      => 'saodahnargis@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137NARGISSU',
        'account_number'     => '2165212001560',
        'SIF_Number'         => '106593',

        'account_title'      => 'NARGIS SULTANA',
        'customer_name'      => 'NARGIS SULTANA',

        'contact_no_1'       => '8801811229820',
        'mobile_number'      => '8801811229820',

        'email_1'            => 'ns092801@primebank.com.bd',
        'email_address'      => 'ns092801@primebank.com.bd',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137NASIMAPRM',
        'account_number'     => '2125212006162',
        'SIF_Number'         => '1003038',

        'account_title'      => 'NASIMA AKTER',
        'customer_name'      => 'NASIMA AKTER',

        'contact_no_1'       => '8801845923628',
        'mobile_number'      => '8801845923628',

        'email_1'            => 'mdmohsin@dhaka.net',
        'email_address'      => 'mdmohsin@dhaka.net',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137NASIMHPRM',
        'account_number'     => '2125218019649',
        'SIF_Number'         => '1932869',

        'account_title'      => 'NASIMUL HASIN',
        'customer_name'      => 'NASIMUL HASIN',

        'contact_no_1'       => '8801600365322',
        'mobile_number'      => '8801600365322',

        'email_1'            => 'nasimul@romofashion.com',
        'email_address'      => 'nasimul@romofashion.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137NASIRUDD',
        'account_number'     => '2104219003985',
        'SIF_Number'         => '10755',

        'account_title'      => 'MD. NASIR UDDIN KHAN',
        'customer_name'      => 'MD. NASIR UDDIN KHAN',

        'contact_no_1'       => '8801731802813',
        'mobile_number'      => '8801731802813',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137NASIRUDPB',
        'account_number'     => '2152215030041',
        'SIF_Number'         => '1246883',

        'account_title'      => 'NASIR UDDIN',
        'customer_name'      => 'NASIR UDDIN',

        'contact_no_1'       => '8801751502710',
        'mobile_number'      => '8801751502710',

        'email_1'            => 'nassermirzahyc@gmail.com',
        'email_address'      => 'nassermirzahyc@gmail.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137NASREENPB',
        'account_number'     => '2162211016703',
        'SIF_Number'         => '2422873',

        'account_title'      => 'MST. NASREEN HAQUE',
        'customer_name'      => 'MST. NASREEN HAQUE',

        'contact_no_1'       => '8801730641402',
        'mobile_number'      => '8801730641402',

        'email_1'            => 'shimul92@gmail.com',
        'email_address'      => 'shimul92@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137NASRINAK',
        'account_number'     => '2138211016663',
        'SIF_Number'         => '2088031',

        'account_title'      => 'NASRIN AKTER',
        'customer_name'      => 'NASRIN AKTER',

        'contact_no_1'       => '8801711154442',
        'mobile_number'      => '8801711154442',

        'email_1'            => 'sraboni.nasrin2014@gmail.com',
        'email_address'      => 'sraboni.nasrin2014@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137NASRINAPB',
        'account_number'     => '2118217035702',
        'SIF_Number'         => '2568209',

        'account_title'      => 'NASRIN AFROSE',
        'customer_name'      => 'NASRIN AFROSE',

        'contact_no_1'       => '8801712854754',
        'mobile_number'      => '8801712854754',

        'email_1'            => 'nasrin_afrose@yahoo.com',
        'email_address'      => 'nasrin_afrose@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137NASUDAHPB',
        'account_number'     => '2110213007372',
        'SIF_Number'         => '1385995',

        'account_title'      => 'NASIR UDDIN AHAMED',
        'customer_name'      => 'NASIR UDDIN AHAMED',

        'contact_no_1'       => '8801912531427',
        'mobile_number'      => '8801912531427',

        'email_1'            => 'nasir1427@yahoo.com',
        'email_address'      => 'nasir1427@yahoo.com',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAWAZDOLA',
        'account_number'     => '2152211028023',
        'SIF_Number'         => '2384322',

        'account_title'      => 'SHAHANARA NAWAZ DOLA',
        'customer_name'      => 'SHAHANARA NAWAZ DOLA',

        'contact_no_1'       => '8801624365553',
        'mobile_number'      => '8801624365553',

        'email_1'            => 'shahnawagkurini@gmail.com',
        'email_address'      => 'shahnawagkurini@gmail.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAYANBPBL',
        'account_number'     => '2104211016473',
        'SIF_Number'         => '1843591',

        'account_title'      => 'NAYAN BARUA',
        'customer_name'      => 'NAYAN BARUA',

        'contact_no_1'       => '8801817751235',
        'mobile_number'      => '8801817751235',

        'email_1'            => 'nayanivac@gmail.com',
        'email_address'      => 'nayanivac@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAZALAMPB',
        'account_number'     => '2125211005785',
        'SIF_Number'         => '84416',

        'account_title'      => 'MD NAZMUL ALAM',
        'customer_name'      => 'MD NAZMUL ALAM',

        'contact_no_1'       => '8801732939132',
        'mobile_number'      => '8801732939132',

        'email_1'            => 'nazmul.fin.du@gmail.com',
        'email_address'      => 'nazmul.fin.du@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAZMARAPB',
        'account_number'     => '2146214001927',
        'SIF_Number'         => '1284931',

        'account_title'      => 'NAZMA RAHMAN',
        'customer_name'      => 'NAZMA RAHMAN',

        'contact_no_1'       => '8801716197710',
        'mobile_number'      => '8801716197710',

        'email_1'            => 'nojibur@yahoo.com',
        'email_address'      => 'nojibur@yahoo.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAZMULHAQ',
        'account_number'     => '2208212006055',
        'SIF_Number'         => '1616312',

        'account_title'      => 'MD NAZMUL HAQUE',
        'customer_name'      => 'MD NAZMUL HAQUE',

        'contact_no_1'       => '8801826060728',
        'mobile_number'      => '8801826060728',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'BOALMARI BRANCH',
        'acc_opening_branch' => 'BOALMARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAZMULHPB',
        'account_number'     => '2141214008471',
        'SIF_Number'         => '682176',

        'account_title'      => 'MD. NAZMUL HASAN',
        'customer_name'      => 'MD. NAZMUL HASAN',

        'contact_no_1'       => '8801732293213',
        'mobile_number'      => '8801732293213',

        'email_1'            => 'irinsultana88@gmail.com',
        'email_address'      => 'irinsultana88@gmail.com',

        'branch_name'        => 'PANTHAPATH BRANCH',
        'acc_opening_branch' => 'PANTHAPATH BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAZMUZZPR',
        'account_number'     => '2138216004416',
        'SIF_Number'         => '1283845',

        'account_title'      => 'MOHAMMAD NAZMUZZAMAN BHUIAN',
        'customer_name'      => 'MOHAMMAD NAZMUZZAMAN BHUIAN',

        'contact_no_1'       => '8801819254874',
        'mobile_number'      => '8801819254874',

        'email_1'            => 'nzemon@yahoo.com',
        'email_address'      => 'nzemon@yahoo.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAZNENSPB',
        'account_number'     => '2104212032031',
        'SIF_Number'         => '167134',

        'account_title'      => 'MST. NAZNEEN SULTANA',
        'customer_name'      => 'MST. NAZNEEN SULTANA',

        'contact_no_1'       => '8801711150081',
        'mobile_number'      => '8801711150081',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAZNINAPB',
        'account_number'     => '2187216005207',
        'SIF_Number'         => '1351485',

        'account_title'      => 'NAZNIN AKTHER',
        'customer_name'      => 'NAZNIN AKTHER',

        'contact_no_1'       => '8801687863686',
        'mobile_number'      => '8801687863686',

        'email_1'            => 'chondro4@gmail.com',
        'email_address'      => 'chondro4@gmail.com',

        'branch_name'        => 'WARI BRANCH',
        'acc_opening_branch' => 'WARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137NAZNINPB',
        'account_number'     => '2138212021968',
        'SIF_Number'         => '2636042',

        'account_title'      => 'NAZNIN ISLAM',
        'customer_name'      => 'NAZNIN ISLAM',

        'contact_no_1'       => '8801781866615',
        'mobile_number'      => '8801781866615',

        'email_1'            => 'nazninislam959@gmail.com',
        'email_address'      => 'nazninislam959@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137NEESARAPB',
        'account_number'     => '2138212004824',
        'SIF_Number'         => '437832',

        'account_title'      => 'NEESAR AHMED',
        'customer_name'      => 'NEESAR AHMED',

        'contact_no_1'       => '8801911325363',
        'mobile_number'      => '8801911325363',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137NIAZMARPB',
        'account_number'     => '2140214012961',
        'SIF_Number'         => '102377',

        'account_title'      => 'MD. NIAZ MARSHED',
        'customer_name'      => 'MD. NIAZ MARSHED',

        'contact_no_1'       => '8801555555510',
        'mobile_number'      => '8801555555510',

        'email_1'            => 'niazmarshed@gmail.com',
        'email_address'      => 'niazmarshed@gmail.com',

        'branch_name'        => 'TONGI BRANCH',
        'acc_opening_branch' => 'TONGI BRANCH',
    ],
    [
        'bp_id'              => 'IN137NILADEYPB',
        'account_number'     => '2104218038081',
        'SIF_Number'         => '2404611',

        'account_title'      => 'NILA DEY',
        'customer_name'      => 'NILA DEY',

        'contact_no_1'       => '8801726176105',
        'mobile_number'      => '8801726176105',

        'email_1'            => 'nilaaloke420@gmail.com',
        'email_address'      => 'nilaaloke420@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137NILPAULPB',
        'account_number'     => '2118216033344',
        'SIF_Number'         => '2442324',

        'account_title'      => 'NILANJANA PAUL',
        'customer_name'      => 'NILANJANA PAUL',

        'contact_no_1'       => '8801851506429',
        'mobile_number'      => '8801851506429',

        'email_1'            => 'paulnilanjana8@gmail.com',
        'email_address'      => 'paulnilanjana8@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137NIRENDRPB',
        'account_number'     => '2112213026602',
        'SIF_Number'         => '2506849',

        'account_title'      => 'NIRENDRA NATH MUSTAFI',
        'customer_name'      => 'NIRENDRA NATH MUSTAFI',

        'contact_no_1'       => '8801798596544',
        'mobile_number'      => '8801798596544',

        'email_1'            => 'nnmustafi@gmail.com',
        'email_address'      => 'nnmustafi@gmail.com',

        'branch_name'        => 'RAJSHAHI BRANCH',
        'acc_opening_branch' => 'RAJSHAHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137NISHATSPB',
        'account_number'     => '2118218035396',
        'SIF_Number'         => '2550697',

        'account_title'      => 'NISHAT SULTANA',
        'customer_name'      => 'NISHAT SULTANA',

        'contact_no_1'       => '8801811110522',
        'mobile_number'      => '8801811110522',

        'email_1'            => 'mamuniup7@gmail.com',
        'email_address'      => 'mamuniup7@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137NIVAPB',
        'account_number'     => '2219214006175',
        'SIF_Number'         => '2088324',

        'account_title'      => 'ISRAT REZA NIVA',
        'customer_name'      => 'ISRAT REZA NIVA',

        'contact_no_1'       => '8801819248434',
        'mobile_number'      => '8801819248434',

        'email_1'            => 'israt.reza1966@gmail.com',
        'email_address'      => 'israt.reza1966@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137NJAHANPB',
        'account_number'     => '2104214005746',
        'SIF_Number'         => '861268',

        'account_title'      => 'NUSRAT JAHAN',
        'customer_name'      => 'NUSRAT JAHAN',

        'contact_no_1'       => '8801635372522',
        'mobile_number'      => '8801635372522',

        'email_1'            => 'nusrat16@gmail.com',
        'email_address'      => 'nusrat16@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137NLAILAPB',
        'account_number'     => '2118218033342',
        'SIF_Number'         => '2442321',

        'account_title'      => 'NOSHIN LAILA',
        'customer_name'      => 'NOSHIN LAILA',

        'contact_no_1'       => '8801722047784',
        'mobile_number'      => '8801722047784',

        'email_1'            => 'noshin189@gmail.com',
        'email_address'      => 'noshin189@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137NMEHRANPB',
        'account_number'     => '2219214007504',
        'SIF_Number'         => '1259159',

        'account_title'      => 'NAFIS UDDIN MEHRAN',
        'customer_name'      => 'NAFIS UDDIN MEHRAN',

        'contact_no_1'       => '8801713350208',
        'mobile_number'      => '8801713350208',

        'email_1'            => 'mnafisuddin7@gmail.com',
        'email_address'      => 'mnafisuddin7@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137NOUSHINS',
        'account_number'     => '2164211024375',
        'SIF_Number'         => '2424901',

        'account_title'      => 'NOUSHIN SULTANA',
        'customer_name'      => 'NOUSHIN SULTANA',

        'contact_no_1'       => '8801613051549',
        'mobile_number'      => '8801613051549',

        'email_1'            => 'a.hilalee@yahoo.com',
        'email_address'      => 'a.hilalee@yahoo.com',

        'branch_name'        => 'PALLABI BRANCH',
        'acc_opening_branch' => 'PALLABI BRANCH',
    ],
    [
        'bp_id'              => 'IN137NUBAYRAPB',
        'account_number'     => '2219217006172',
        'SIF_Number'         => '2176323',

        'account_title'      => 'NUBAYRA REZA',
        'customer_name'      => 'NUBAYRA REZA',

        'contact_no_1'       => '8801712959973',
        'mobile_number'      => '8801712959973',

        'email_1'            => 'reza.rubayra@gmail.com',
        'email_address'      => 'reza.rubayra@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137NUMAIBPRM',
        'account_number'     => '2118218000150',
        'SIF_Number'         => '74136',

        'account_title'      => 'NUMA IBN SHAMS',
        'customer_name'      => 'NUMA IBN SHAMS',

        'contact_no_1'       => '8801779985994',
        'mobile_number'      => '8801779985994',

        'email_1'            => 'shams_ni@yahoo.ca',
        'email_address'      => 'shams_ni@yahoo.ca',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137NUMHASPB',
        'account_number'     => '2118218000150',
        'SIF_Number'         => '74136',

        'account_title'      => 'NUMA IBN SHAMS AND HASNAHENA SHAMS',
        'customer_name'      => 'NUMA IBN SHAMS AND HASNAHENA SHAMS',

        'contact_no_1'       => '8801779985994',
        'mobile_number'      => '8801779985994',

        'email_1'            => 'shams_ni@yahoo.ca',
        'email_address'      => 'shams_ni@yahoo.ca',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137NURULAMPB',
        'account_number'     => '2143211008472',
        'SIF_Number'         => '2067235',

        'account_title'      => 'NURUL AMIN CHOWDHURY',
        'customer_name'      => 'NURUL AMIN CHOWDHURY',

        'contact_no_1'       => '8801713332221',
        'mobile_number'      => '8801713332221',

        'email_1'            => 'aminoldnavy@hotmail.com',
        'email_address'      => 'aminoldnavy@hotmail.com',

        'branch_name'        => 'UPASHAHAR BRANCH',
        'acc_opening_branch' => 'UPASHAHAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137NURULPB',
        'account_number'     => '2145213018410',
        'SIF_Number'         => '1883472',

        'account_title'      => 'MOHAMMAD NURUL AMIN',
        'customer_name'      => 'MOHAMMAD NURUL AMIN',

        'contact_no_1'       => '8801819841814',
        'mobile_number'      => '8801819841814',

        'email_1'            => 'mh.nurul@primebank.com.bd',
        'email_address'      => 'mh.nurul@primebank.com.bd',

        'branch_name'        => 'LALDIGHI EAST BRANCH',
        'acc_opening_branch' => 'LALDIGHI EAST BRANCH',
    ],
    [
        'bp_id'              => 'IN137NURUNAHPB',
        'account_number'     => '2113212003452',
        'SIF_Number'         => '728522',

        'account_title'      => 'NURUN NAHAR',
        'customer_name'      => 'NURUN NAHAR',

        'contact_no_1'       => '8801712625282',
        'mobile_number'      => '8801712625282',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137NURUPRIME',
        'account_number'     => '2219216003477',
        'SIF_Number'         => '1920330',

        'account_title'      => 'S.M. NURUZZAMAN',
        'customer_name'      => 'S.M. NURUZZAMAN',

        'contact_no_1'       => '8801716475664',
        'mobile_number'      => '8801716475664',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137NUSHAYAT',
        'account_number'     => '2118212033587',
        'SIF_Number'         => '2473575',

        'account_title'      => 'NUSRAT HAYAT KHAN',
        'customer_name'      => 'NUSRAT HAYAT KHAN',

        'contact_no_1'       => '8801623930240',
        'mobile_number'      => '8801623930240',

        'email_1'            => 'nusraat.hayat@gmail.com',
        'email_address'      => 'nusraat.hayat@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137NUSJAHPB',
        'account_number'     => '2104213027113',
        'SIF_Number'         => '807623',

        'account_title'      => 'NUSRAT JAHAN',
        'customer_name'      => 'NUSRAT JAHAN',

        'contact_no_1'       => '8801970320018',
        'mobile_number'      => '8801970320018',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137NUSRAT',
        'account_number'     => '2126211012764',
        'SIF_Number'         => '2398532',

        'account_title'      => 'NUSRAT JAHAN',
        'customer_name'      => 'NUSRAT JAHAN',

        'contact_no_1'       => '8801701220977',
        'mobile_number'      => '8801701220977',

        'email_1'            => 'nusratmeem@gmail.com',
        'email_address'      => 'nusratmeem@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137NUSRATAMM',
        'account_number'     => '2146215011198',
        'SIF_Number'         => '1668060',

        'account_title'      => 'NUSRAT AMIN MIRZA',
        'customer_name'      => 'NUSRAT AMIN MIRZA',

        'contact_no_1'       => '8801711529150',
        'mobile_number'      => '8801711529150',

        'email_1'            => 'erfan@venturegrp.net',
        'email_address'      => 'erfan@venturegrp.net',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137NUSSRMPRM',
        'account_number'     => '2104212039507',
        'SIF_Number'         => '2519711',

        'account_title'      => 'NUSRAT SARMIN NOVA',
        'customer_name'      => 'NUSRAT SARMIN NOVA',

        'contact_no_1'       => '8801303593797',
        'mobile_number'      => '8801303593797',

        'email_1'            => 'nusratnova666@gmail.com',
        'email_address'      => 'nusratnova666@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137NUZHATMA',
        'account_number'     => '2141211013061',
        'SIF_Number'         => '2182786',

        'account_title'      => 'NUZHAT MANSUR',
        'customer_name'      => 'NUZHAT MANSUR',

        'contact_no_1'       => '8801715351454',
        'mobile_number'      => '8801715351454',

        'email_1'            => 'nuzhat.mansur@gmail.com',
        'email_address'      => 'nuzhat.mansur@gmail.com',

        'branch_name'        => 'PANTHAPATH BRANCH',
        'acc_opening_branch' => 'PANTHAPATH BRANCH',
    ],
    [
        'bp_id'              => 'IN137NUZHATSA',
        'account_number'     => '2127217020422',
        'SIF_Number'         => '2175043',

        'account_title'      => 'NUZHAT SANAM',
        'customer_name'      => 'NUZHAT SANAM',

        'contact_no_1'       => '8801811232343',
        'mobile_number'      => '8801811232343',

        'email_1'            => 'nshr60@gmail.com',
        'email_address'      => 'nshr60@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137OBAIDURPB',
        'account_number'     => '3133215034029',
        'SIF_Number'         => '1670420',

        'account_title'      => 'MD. OBAIDUR RAHMAN',
        'customer_name'      => 'MD. OBAIDUR RAHMAN',

        'contact_no_1'       => '8801711133549',
        'mobile_number'      => '8801711133549',

        'email_1'            => 'obaidur15@primebank.com.bd',
        'email_address'      => 'obaidur15@primebank.com.bd',

        'branch_name'        => 'IBB MIRPUR BRANCH',
        'acc_opening_branch' => 'IBB MIRPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137OHIDUZMPB',
        'account_number'     => '2106212005118',
        'SIF_Number'         => '563011',

        'account_title'      => 'MD. OHIDUZZAMAN',
        'customer_name'      => 'MD. OHIDUZZAMAN',

        'contact_no_1'       => '8801712558687',
        'mobile_number'      => '8801712558687',

        'email_1'            => 'mohid_77@yahoo.com',
        'email_address'      => 'mohid_77@yahoo.com',

        'branch_name'        => 'MOULVIBAZAR BRANCH',
        'acc_opening_branch' => 'MOULVIBAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137OMARPRIME',
        'account_number'     => '2104218002509',
        'SIF_Number'         => '1771365',

        'account_title'      => 'SYED MOHAMMED OMAR TAYUB',
        'customer_name'      => 'SYED MOHAMMED OMAR TAYUB',

        'contact_no_1'       => '8801730006199',
        'mobile_number'      => '8801730006199',

        'email_1'            => 'omartayub@yahoo.com',
        'email_address'      => 'omartayub@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137OSMANPB',
        'account_number'     => '2152215007791',
        'SIF_Number'         => '832358',

        'account_title'      => 'MD. OSMAN ALI',
        'customer_name'      => 'MD. OSMAN ALI',

        'contact_no_1'       => '8801711404814',
        'mobile_number'      => '8801711404814',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137OVIDASPRM',
        'account_number'     => '2145213005417',
        'SIF_Number'         => '1037046',

        'account_title'      => 'OVI DAS',
        'customer_name'      => 'OVI DAS',

        'contact_no_1'       => '8801619619429',
        'mobile_number'      => '8801619619429',

        'email_1'            => 'ovidascu92@gmail.com',
        'email_address'      => 'ovidascu92@gmail.com',

        'branch_name'        => 'LALDIGHI EAST BRANCH',
        'acc_opening_branch' => 'LALDIGHI EAST BRANCH',
    ],
    [
        'bp_id'              => 'IN137PABITRAPB',
        'account_number'     => '2183217008204',
        'SIF_Number'         => '1233600',

        'account_title'      => 'PABITRA',
        'customer_name'      => 'PABITRA',

        'contact_no_1'       => '8801937051523',
        'mobile_number'      => '8801937051523',

        'email_1'            => 'daspabitra4412@gmail.com',
        'email_address'      => 'daspabitra4412@gmail.com',

        'branch_name'        => 'SERAJDIKHAN BRANCH',
        'acc_opening_branch' => 'SERAJDIKHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137PAKTERPRM',
        'account_number'     => '2125219001562',
        'SIF_Number'         => '1031510',

        'account_title'      => 'PARVEN AKTER',
        'customer_name'      => 'PARVEN AKTER',

        'contact_no_1'       => '8801928915015',
        'mobile_number'      => '8801928915015',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137PALASHKPB',
        'account_number'     => '2127211005743',
        'SIF_Number'         => '1494956',

        'account_title'      => 'PALASH KUMAR KUNDU',
        'customer_name'      => 'PALASH KUMAR KUNDU',

        'contact_no_1'       => '8801720502206',
        'mobile_number'      => '8801720502206',

        'email_1'            => 'palashndc@gmail.com',
        'email_address'      => 'palashndc@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137PALASHPB',
        'account_number'     => '2127211005743',
        'SIF_Number'         => '1494956',

        'account_title'      => 'PALASH KUMAR KUNDU',
        'customer_name'      => 'PALASH KUMAR KUNDU',

        'contact_no_1'       => '8801720502206',
        'mobile_number'      => '8801720502206',

        'email_1'            => 'palashndc@gmail.com',
        'email_address'      => 'palashndc@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137PALLABSPB',
        'account_number'     => '2125213005437',
        'SIF_Number'         => '1194752',

        'account_title'      => 'PALLAB SARKAR',
        'customer_name'      => 'PALLAB SARKAR',

        'contact_no_1'       => '8801814285054',
        'mobile_number'      => '8801814285054',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137PARASHAR',
        'account_number'     => '2132212033746',
        'SIF_Number'         => '2667148',

        'account_title'      => 'PARASHAR SAHA',
        'customer_name'      => 'PARASHAR SAHA',

        'contact_no_1'       => '8801746223484',
        'mobile_number'      => '8801746223484',

        'email_1'            => 'parashar.saha.7@gmail.com',
        'email_address'      => 'parashar.saha.7@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137PARTHBSPB',
        'account_number'     => '2126211013383',
        'SIF_Number'         => '2467905',

        'account_title'      => 'PARTHA BISWAS',
        'customer_name'      => 'PARTHA BISWAS',

        'contact_no_1'       => '8801759776977',
        'mobile_number'      => '8801759776977',

        'email_1'            => 'parthabu11@gmail.com',
        'email_address'      => 'parthabu11@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137PARVEENR',
        'account_number'     => '2132219015654',
        'SIF_Number'         => '61352',

        'account_title'      => 'PARVEEN RABBANI',
        'customer_name'      => 'PARVEEN RABBANI',

        'contact_no_1'       => '8801711533398',
        'mobile_number'      => '8801711533398',

        'email_1'            => 'parveen.rabbani9@gmail.com',
        'email_address'      => 'parveen.rabbani9@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137PARVEZPB',
        'account_number'     => '2118216041990',
        'SIF_Number'         => '2344501',

        'account_title'      => 'PARVEZ HASHIM',
        'customer_name'      => 'PARVEZ HASHIM',

        'contact_no_1'       => '8801711568592',
        'mobile_number'      => '8801711568592',

        'email_1'            => 'phashim19@gmail.com',
        'email_address'      => 'phashim19@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137PARVINAKT',
        'account_number'     => '0021060006833',
        'SIF_Number'         => '1055987',

        'account_title'      => 'MRS. PARVIN AKTER',
        'customer_name'      => 'MRS. PARVIN AKTER',

        'contact_no_1'       => '8801632465248',
        'mobile_number'      => '8801632465248',

        'email_1'            => 'nazmul101272@gmail.com',
        'email_address'      => 'nazmul101272@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137PARVNMPRM',
        'account_number'     => '2117211012246',
        'SIF_Number'         => '1898860',

        'account_title'      => 'PARVEEN MOSHARRAF',
        'customer_name'      => 'PARVEEN MOSHARRAF',

        'contact_no_1'       => '8801937015164',
        'mobile_number'      => '8801937015164',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137PARVPRM',
        'account_number'     => '2125219001562',
        'SIF_Number'         => '1031510',

        'account_title'      => 'PARVEN AKTER',
        'customer_name'      => 'PARVEN AKTER',

        'contact_no_1'       => '8801928915015',
        'mobile_number'      => '8801928915015',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137PIJGHOSPB',
        'account_number'     => '2169211002301',
        'SIF_Number'         => '963037',

        'account_title'      => 'PIJUSH GHOSH',
        'customer_name'      => 'PIJUSH GHOSH',

        'contact_no_1'       => '8801717544314',
        'mobile_number'      => '8801717544314',

        'email_1'            => 'pijushpbl82@gmail.com',
        'email_address'      => 'pijushpbl82@gmail.com',

        'branch_name'        => 'DINAJPUR BRANCH',
        'acc_opening_branch' => 'DINAJPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137PINASHPB',
        'account_number'     => '2118211033324',
        'SIF_Number'         => '2442250',

        'account_title'      => 'FARIA KHAN PINASH',
        'customer_name'      => 'FARIA KHAN PINASH',

        'contact_no_1'       => '8801760505649',
        'mobile_number'      => '8801760505649',

        'email_1'            => 'pinashfariakhan@gmail.com',
        'email_address'      => 'pinashfariakhan@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137PKUMARPB',
        'account_number'     => '2129214007917',
        'SIF_Number'         => '383393',

        'account_title'      => 'PALLAB KUMAR DEB',
        'customer_name'      => 'PALLAB KUMAR DEB',

        'contact_no_1'       => '8801718196632',
        'mobile_number'      => '8801718196632',

        'email_1'            => 'pallab_ges@yahoo.com',
        'email_address'      => 'pallab_ges@yahoo.com',

        'branch_name'        => 'BOGURA BRANCH',
        'acc_opening_branch' => 'BOGURA BRANCH',
    ],
    [
        'bp_id'              => 'IN137PRIYAKUPB',
        'account_number'     => '2118211041219',
        'SIF_Number'         => '2793519',

        'account_title'      => 'PRIYA KUNDU',
        'customer_name'      => 'PRIYA KUNDU',

        'contact_no_1'       => '8801748190393',
        'mobile_number'      => '8801748190393',

        'email_1'            => 'priya.kundu0708@gmail.com',
        'email_address'      => 'priya.kundu0708@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137PRKSHAHPB',
        'account_number'     => '2107214031822',
        'SIF_Number'         => '2551321',

        'account_title'      => 'PROHLAD KUMAR SAHA',
        'customer_name'      => 'PROHLAD KUMAR SAHA',

        'contact_no_1'       => '8801926130030',
        'mobile_number'      => '8801926130030',

        'email_1'            => 'pks108@gmail.com',
        'email_address'      => 'pks108@gmail.com',

        'branch_name'        => 'KHULNA BRANCH',
        'acc_opening_branch' => 'KHULNA BRANCH',
    ],
    [
        'bp_id'              => 'IN137PROJIBDAT',
        'account_number'     => '2198216000601',
        'SIF_Number'         => '267291',

        'account_title'      => 'PROJIB BHUSAN DATTA',
        'customer_name'      => 'PROJIB BHUSAN DATTA',

        'contact_no_1'       => '8801711118381',
        'mobile_number'      => '8801711118381',

        'email_1'            => 'pbd-fcma@yahoo.com',
        'email_address'      => 'pbd-fcma@yahoo.com',

        'branch_name'        => 'BASHABO BRANCH',
        'acc_opening_branch' => 'BASHABO BRANCH',
    ],
    [
        'bp_id'              => 'IN137PROTITYPB',
        'account_number'     => '2104211031257',
        'SIF_Number'         => '1997771',

        'account_title'      => 'PROTITY AYESHA',
        'customer_name'      => 'PROTITY AYESHA',

        'contact_no_1'       => '8801754030949',
        'mobile_number'      => '8801754030949',

        'email_1'            => 'protitytridha@gmail.com',
        'email_address'      => 'protitytridha@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137PTANVIRPB',
        'account_number'     => '2104211022497',
        'SIF_Number'         => '1427377',

        'account_title'      => 'PATWARY TANVIR RAHMAN SIDDIQUE',
        'customer_name'      => 'PATWARY TANVIR RAHMAN SIDDIQUE',

        'contact_no_1'       => '8801678078968',
        'mobile_number'      => '8801678078968',

        'email_1'            => 'patwaryhimel@gmail.com',
        'email_address'      => 'patwaryhimel@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137QUAZIAKPB',
        'account_number'     => '2164211008922',
        'SIF_Number'         => '58729',

        'account_title'      => 'QUAZI ABUL KALAM AZAD',
        'customer_name'      => 'QUAZI ABUL KALAM AZAD',

        'contact_no_1'       => '8801711546756',
        'mobile_number'      => '8801711546756',

        'email_1'            => 'azad@maxgarments.com',
        'email_address'      => 'azad@maxgarments.com',

        'branch_name'        => 'PALLABI BRANCH',
        'acc_opening_branch' => 'PALLABI BRANCH',
    ],
    [
        'bp_id'              => 'IN137RABEYAPRM',
        'account_number'     => '2170219002481',
        'SIF_Number'         => '690691',

        'account_title'      => 'RABEYA BEGUM',
        'customer_name'      => 'RABEYA BEGUM',

        'contact_no_1'       => '8801717204771',
        'mobile_number'      => '8801717204771',

        'email_1'            => 'fancybd1979@gmail.com',
        'email_address'      => 'fancybd1979@gmail.com',

        'branch_name'        => 'GARIB-E-NEWAZ AVE. BR',
        'acc_opening_branch' => 'GARIB-E-NEWAZ AVE. BR',
    ],
    [
        'bp_id'              => 'IN137RABIUISPB',
        'account_number'     => '2138212006803',
        'SIF_Number'         => '437463',

        'account_title'      => 'MD RABIUL ISLAM',
        'customer_name'      => 'MD RABIUL ISLAM',

        'contact_no_1'       => '8801818362301',
        'mobile_number'      => '8801818362301',

        'email_1'            => 'rabieco10@yahoo.com',
        'email_address'      => 'rabieco10@yahoo.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137RABIULHA',
        'account_number'     => '2138211000550',
        'SIF_Number'         => '1759654',

        'account_title'      => 'RABIUL HASAN',
        'customer_name'      => 'RABIUL HASAN',

        'contact_no_1'       => '8801713613186',
        'mobile_number'      => '8801713613186',

        'email_1'            => 'robinitp12@gmail.com',
        'email_address'      => 'robinitp12@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137RABIULPB',
        'account_number'     => '2219216006421',
        'SIF_Number'         => '2192677',

        'account_title'      => 'MIAH MOHAMMAD RABIUL HASAN',
        'customer_name'      => 'MIAH MOHAMMAD RABIUL HASAN',

        'contact_no_1'       => '8801729242059',
        'mobile_number'      => '8801729242059',

        'email_1'            => 'rabiul_hasan@yahoo.com',
        'email_address'      => 'rabiul_hasan@yahoo.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137RABPRIMEB',
        'account_number'     => '2146211003604',
        'SIF_Number'         => '1403593',

        'account_title'      => 'RABEYA SULTANA',
        'customer_name'      => 'RABEYA SULTANA',

        'contact_no_1'       => '8801713376509',
        'mobile_number'      => '8801713376509',

        'email_1'            => 'rabeyasultana1614@gmail.com',
        'email_address'      => 'rabeyasultana1614@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAFAPRIME',
        'account_number'     => '2104215011115',
        'SIF_Number'         => '1700288',

        'account_title'      => 'RAFAT KHAN',
        'customer_name'      => 'RAFAT KHAN',

        'contact_no_1'       => '8801713015421',
        'mobile_number'      => '8801713015421',

        'email_1'            => 'rafat.ullah@primebank.com.bd',
        'email_address'      => 'rafat.ullah@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAFIDRYPB',
        'account_number'     => '2146211019717',
        'SIF_Number'         => '2498939',

        'account_title'      => 'RAFID RAIYAN',
        'customer_name'      => 'RAFID RAIYAN',

        'contact_no_1'       => '8801732631606',
        'mobile_number'      => '8801732631606',

        'email_1'            => 'rafidraiyan006@gmail.com',
        'email_address'      => 'rafidraiyan006@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAFIKAPB',
        'account_number'     => '2127218004680',
        'SIF_Number'         => '292015',

        'account_title'      => 'RAFIKA KHATUN',
        'customer_name'      => 'RAFIKA KHATUN',

        'contact_no_1'       => '8801323718583',
        'mobile_number'      => '8801323718583',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAFIQULPB',
        'account_number'     => '2125211029207',
        'SIF_Number'         => '2417131',

        'account_title'      => 'MD RAFIQUL ISLAM',
        'customer_name'      => 'MD RAFIQUL ISLAM',

        'contact_no_1'       => '8801729253111',
        'mobile_number'      => '8801729253111',

        'email_1'            => 'rafiqul.dl@gmail.com',
        'email_address'      => 'rafiqul.dl@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAHIMAA',
        'account_number'     => '2141217005797',
        'SIF_Number'         => '284556',

        'account_title'      => 'RAHIMA AKTER',
        'customer_name'      => 'RAHIMA AKTER',

        'contact_no_1'       => '8801742606565',
        'mobile_number'      => '8801742606565',

        'email_1'            => 'ra080501@primebank.com.bd',
        'email_address'      => 'ra080501@primebank.com.bd',

        'branch_name'        => 'PANTHAPATH BRANCH',
        'acc_opening_branch' => 'PANTHAPATH BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAHIMAKPB',
        'account_number'     => '3108214011250',
        'SIF_Number'         => '2035909',

        'account_title'      => 'RAHIMA AKTER',
        'customer_name'      => 'RAHIMA AKTER',

        'contact_no_1'       => '8801684868044',
        'mobile_number'      => '8801684868044',

        'email_1'            => 'sabinadhaka88@gmail.com',
        'email_address'      => 'sabinadhaka88@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAHINUZPB',
        'account_number'     => '2169213012885',
        'SIF_Number'         => '2896961',

        'account_title'      => 'A.K.M RAHINUZZAMAN',
        'customer_name'      => 'A.K.M RAHINUZZAMAN',

        'contact_no_1'       => '8801925829867',
        'mobile_number'      => '8801925829867',

        'email_1'            => 'rahinuzzaman01@gmail.com',
        'email_address'      => 'rahinuzzaman01@gmail.com',

        'branch_name'        => 'DINAJPUR BRANCH',
        'acc_opening_branch' => 'DINAJPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAHLPRIME',
        'account_number'     => '2104219011293',
        'SIF_Number'         => '1622940',

        'account_title'      => 'RAHEL AHMED',
        'customer_name'      => 'RAHEL AHMED',

        'contact_no_1'       => '8801711568182',
        'mobile_number'      => '8801711568182',

        'email_1'            => 'rahelahmed1971@gmail.com',
        'email_address'      => 'rahelahmed1971@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAHMANNAH',
        'account_number'     => '2132211028391',
        'SIF_Number'         => '2472545',

        'account_title'      => 'RAHMAN NAHIAN',
        'customer_name'      => 'RAHMAN NAHIAN',

        'contact_no_1'       => '8801918180700',
        'mobile_number'      => '8801918180700',

        'email_1'            => 'rnahian34@gmail.com',
        'email_address'      => 'rnahian34@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAJIBBRPB',
        'account_number'     => '2132218000715',
        'SIF_Number'         => '1804334',

        'account_title'      => 'RAJIB BARAN ROY',
        'customer_name'      => 'RAJIB BARAN ROY',

        'contact_no_1'       => '8801604508254',
        'mobile_number'      => '8801604508254',

        'email_1'            => 'rajib.roy@primeasia.edu.bd',
        'email_address'      => 'rajib.roy@primeasia.edu.bd',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAJIBK',
        'account_number'     => '2113211009815',
        'SIF_Number'         => '948058',

        'account_title'      => 'RAJIB KHADEM',
        'customer_name'      => 'RAJIB KHADEM',

        'contact_no_1'       => '8801715341533',
        'mobile_number'      => '8801715341533',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAJIBKRI',
        'account_number'     => '0021010032409',
        'SIF_Number'         => '1267416',

        'account_title'      => 'RAJIB KRISHNA ROY',
        'customer_name'      => 'RAJIB KRISHNA ROY',

        'contact_no_1'       => '8801911515397',
        'mobile_number'      => '8801911515397',

        'email_1'            => 'rajibkrishnaroy@gmail.com',
        'email_address'      => 'rajibkrishnaroy@gmail.com',

        'branch_name'        => 'WARI BRANCH',
        'acc_opening_branch' => 'WARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAJIBKSPB',
        'account_number'     => '2132218024467',
        'SIF_Number'         => '2241298',

        'account_title'      => 'RAJIB KUMAR SAHA',
        'customer_name'      => 'RAJIB KUMAR SAHA',

        'contact_no_1'       => '8801912045031',
        'mobile_number'      => '8801912045031',

        'email_1'            => 'rajibsaha1529@gmail.com',
        'email_address'      => 'rajibsaha1529@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAMADAS',
        'account_number'     => '2104211008396',
        'SIF_Number'         => '696581',

        'account_title'      => 'DR. RAMA DAS',
        'customer_name'      => 'DR. RAMA DAS',

        'contact_no_1'       => '8801879988630',
        'mobile_number'      => '8801879988630',

        'email_1'            => 'rama_thirty@hotmail.com',
        'email_address'      => 'rama_thirty@hotmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAMADPRIM',
        'account_number'     => '2104211008396',
        'SIF_Number'         => '696581',

        'account_title'      => 'DR. RAMA DAS',
        'customer_name'      => 'DR. RAMA DAS',

        'contact_no_1'       => '8801879988630',
        'mobile_number'      => '8801879988630',

        'email_1'            => 'rama_thirty@hotmail.com',
        'email_address'      => 'rama_thirty@hotmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAMZANPB',
        'account_number'     => '2104213022759',
        'SIF_Number'         => '873326',

        'account_title'      => 'MD. RAMZAN ALI',
        'customer_name'      => 'MD. RAMZAN ALI',

        'contact_no_1'       => '8801713660065',
        'mobile_number'      => '8801713660065',

        'email_1'            => 'ma05ramzanfuod@gmail.com',
        'email_address'      => 'ma05ramzanfuod@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAQIBPB',
        'account_number'     => '2118211036756',
        'SIF_Number'         => '2606578',

        'account_title'      => 'RAQIB AL RASHID',
        'customer_name'      => 'RAQIB AL RASHID',

        'contact_no_1'       => '8801816711415',
        'mobile_number'      => '8801816711415',

        'email_1'            => 'ralrashid@yahoo.com',
        'email_address'      => 'ralrashid@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137RASHEDHPR',
        'account_number'     => '2106216001170',
        'SIF_Number'         => '316494',

        'account_title'      => 'RASHEDUL HAQUE',
        'customer_name'      => 'RASHEDUL HAQUE',

        'contact_no_1'       => '8801678341971',
        'mobile_number'      => '8801678341971',

        'email_1'            => 'rhchayan@gmail.com',
        'email_address'      => 'rhchayan@gmail.com',

        'branch_name'        => 'MOULVIBAZAR BRANCH',
        'acc_opening_branch' => 'MOULVIBAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137RASHEDMA',
        'account_number'     => '2114211002520',
        'SIF_Number'         => '263124',

        'account_title'      => 'RASHED MAZUMDER',
        'customer_name'      => 'RASHED MAZUMDER',

        'contact_no_1'       => '8801738541513',
        'mobile_number'      => '8801738541513',

        'email_1'            => 'rakhu345@yahoo.com',
        'email_address'      => 'rakhu345@yahoo.com',

        'branch_name'        => 'ELEPHANT ROAD BRANCH',
        'acc_opening_branch' => 'ELEPHANT ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137RASHEDPAR',
        'account_number'     => '2104212016216',
        'SIF_Number'         => '1459151',

        'account_title'      => 'MD RASHED PARVEZ',
        'customer_name'      => 'MD RASHED PARVEZ',

        'contact_no_1'       => '8801917319534',
        'mobile_number'      => '8801917319534',

        'email_1'            => 'rashed.parvez@primebank.com.bd',
        'email_address'      => 'rashed.parvez@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RASMAHPB',
        'account_number'     => '2104212006068',
        'SIF_Number'         => '1444218',

        'account_title'      => 'RASHED MAHMUD',
        'customer_name'      => 'RASHED MAHMUD',

        'contact_no_1'       => '8801675727808',
        'mobile_number'      => '8801675727808',

        'email_1'            => 'rashed.mahmud@primebank.com.bd',
        'email_address'      => 'rashed.mahmud@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAYHANTPB',
        'account_number'     => '2118217030562',
        'SIF_Number'         => '2348516',

        'account_title'      => 'SYED RAYHAN TARIQUE',
        'customer_name'      => 'SYED RAYHAN TARIQUE',

        'contact_no_1'       => '8801713486695',
        'mobile_number'      => '8801713486695',

        'email_1'            => 'syedrayhan.tarique@gmail.com',
        'email_address'      => 'syedrayhan.tarique@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137RAZAULPB',
        'account_number'     => '2120216011270',
        'SIF_Number'         => '197976',

        'account_title'      => 'MD. RAZAUL KARIM',
        'customer_name'      => 'MD. RAZAUL KARIM',

        'contact_no_1'       => '8801720046873',
        'mobile_number'      => '8801720046873',

        'email_1'            => 'riponmariner@gmail.com',
        'email_address'      => 'riponmariner@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137RDEVIPB',
        'account_number'     => '2184216014616',
        'SIF_Number'         => '1973288',

        'account_title'      => 'RUMI DEVI',
        'customer_name'      => 'RUMI DEVI',

        'contact_no_1'       => '8801632192021',
        'mobile_number'      => '8801632192021',

        'email_1'            => 'rumidvi@gmail.com',
        'email_address'      => 'rumidvi@gmail.com',

        'branch_name'        => 'OXYGEN MORE BRANCH',
        'acc_opening_branch' => 'OXYGEN MORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137REDHWANUR',
        'account_number'     => '2165212020271',
        'SIF_Number'         => '2795761',

        'account_title'      => 'MD. REDHWANUR RHAMAN',
        'customer_name'      => 'MD. REDHWANUR RHAMAN',

        'contact_no_1'       => '8801975559947',
        'mobile_number'      => '8801975559947',

        'email_1'            => 'redhwan777@gmail.com',
        'email_address'      => 'redhwan777@gmail.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137REHANAHPB',
        'account_number'     => '2118216033625',
        'SIF_Number'         => '2475150',

        'account_title'      => 'REHANA HUDA',
        'customer_name'      => 'REHANA HUDA',

        'contact_no_1'       => '8801714495895',
        'mobile_number'      => '8801714495895',

        'email_1'            => 'diptihuda@gmail.com',
        'email_address'      => 'diptihuda@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137REHANULIS',
        'account_number'     => '2118215033840',
        'SIF_Number'         => '2482581',

        'account_title'      => 'REHANUL ISLAM',
        'customer_name'      => 'REHANUL ISLAM',

        'contact_no_1'       => '8801894626537',
        'mobile_number'      => '8801894626537',

        'email_1'            => 'islamrehanul61@gmail.com',
        'email_address'      => 'islamrehanul61@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137RENAISSAN',
        'account_number'     => '2205218000030',
        'SIF_Number'         => '1474988',

        'account_title'      => 'RENAISSANCE HAQUE',
        'customer_name'      => 'RENAISSANCE HAQUE',

        'contact_no_1'       => '8801920571777',
        'mobile_number'      => '8801920571777',

        'email_1'            => 'haquerenaissance@gmail.com',
        'email_address'      => 'haquerenaissance@gmail.com',

        'branch_name'        => 'IBRAHIMPUR BRANCH',
        'acc_opening_branch' => 'IBRAHIMPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137REZAKHPRM',
        'account_number'     => '2198211004384',
        'SIF_Number'         => '1360739',

        'account_title'      => 'REZAUL KHAN',
        'customer_name'      => 'REZAUL KHAN',

        'contact_no_1'       => '8801971976082',
        'mobile_number'      => '8801971976082',

        'email_1'            => 'appolo.khan.ak@gmail.com',
        'email_address'      => 'appolo.khan.ak@gmail.com',

        'branch_name'        => 'BASHABO BRANCH',
        'acc_opening_branch' => 'BASHABO BRANCH',
    ],
    [
        'bp_id'              => 'IN137REZAULKPR',
        'account_number'     => '2162212000771',
        'SIF_Number'         => '857524',

        'account_title'      => 'MD.REZAUL KARIM',
        'customer_name'      => 'MD.REZAUL KARIM',

        'contact_no_1'       => '8801714228872',
        'mobile_number'      => '8801714228872',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137REZAURRA',
        'account_number'     => '2104212039069',
        'SIF_Number'         => '2495291',

        'account_title'      => 'REZAUR RAHMAN NAFIZ',
        'customer_name'      => 'REZAUR RAHMAN NAFIZ',

        'contact_no_1'       => '8801711081535',
        'mobile_number'      => '8801711081535',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137REZIAPRIM',
        'account_number'     => '2159215010343',
        'SIF_Number'         => '1930058',

        'account_title'      => 'REZIA RAHMAT',
        'customer_name'      => 'REZIA RAHMAT',

        'contact_no_1'       => '8801552345623',
        'mobile_number'      => '8801552345623',

        'email_1'            => 'rezia.rhmt@gmail.com',
        'email_address'      => 'rezia.rhmt@gmail.com',

        'branch_name'        => 'BIJOY NAGAR BRANCH',
        'acc_opening_branch' => 'BIJOY NAGAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137REZOANUPB',
        'account_number'     => '2118211024702',
        'SIF_Number'         => '2096834',

        'account_title'      => 'REZOANUM MUNIR',
        'customer_name'      => 'REZOANUM MUNIR',

        'contact_no_1'       => '8801676986650',
        'mobile_number'      => '8801676986650',

        'email_1'            => 'rezoanum@gmail.com',
        'email_address'      => 'rezoanum@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137REZWANAPB',
        'account_number'     => '2118211030064',
        'SIF_Number'         => '2332579',

        'account_title'      => 'KHANDAKER REZWANA MOZAMMEL',
        'customer_name'      => 'KHANDAKER REZWANA MOZAMMEL',

        'contact_no_1'       => '8801830501499',
        'mobile_number'      => '8801830501499',

        'email_1'            => 'rezwana01@gmail.com',
        'email_address'      => 'rezwana01@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137REZWANPB',
        'account_number'     => '2118212037810',
        'SIF_Number'         => '2644266',

        'account_title'      => 'REZWANUL HAQUE',
        'customer_name'      => 'REZWANUL HAQUE',

        'contact_no_1'       => '8801916825921',
        'mobile_number'      => '8801916825921',

        'email_1'            => 'fahimhaque@gmail.com',
        'email_address'      => 'fahimhaque@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137RFORHADPB',
        'account_number'     => '2219211012986',
        'SIF_Number'         => '1901664',

        'account_title'      => 'C.M REZA QURESHI FORHAD',
        'customer_name'      => 'C.M REZA QURESHI FORHAD',

        'contact_no_1'       => '8801819215936',
        'mobile_number'      => '8801819215936',

        'email_1'            => 'forhad.reza@hotmail.com',
        'email_address'      => 'forhad.reza@hotmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137RIASADPB',
        'account_number'     => '2104217029172',
        'SIF_Number'         => '1894135',

        'account_title'      => 'RIASAD SARWAR TONMOY',
        'customer_name'      => 'RIASAD SARWAR TONMOY',

        'contact_no_1'       => '8801913131255',
        'mobile_number'      => '8801913131255',

        'email_1'            => 'riasad.sarwar@primebank.com.bd',
        'email_address'      => 'riasad.sarwar@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RIASENPRM',
        'account_number'     => '2165218017289',
        'SIF_Number'         => '2512084',

        'account_title'      => 'RIA SEN',
        'customer_name'      => 'RIA SEN',

        'contact_no_1'       => '8801886622205',
        'mobile_number'      => '8801886622205',

        'email_1'            => 'riyasen2205@gmail.com',
        'email_address'      => 'riyasen2205@gmail.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137RIFATHOPB',
        'account_number'     => '2205218010773',
        'SIF_Number'         => '2135279',

        'account_title'      => 'MD. RIFAT HOSSAIN',
        'customer_name'      => 'MD. RIFAT HOSSAIN',

        'contact_no_1'       => '8801915500765',
        'mobile_number'      => '8801915500765',

        'email_1'            => 'jitu.rh@gmail.com',
        'email_address'      => 'jitu.rh@gmail.com',

        'branch_name'        => 'IBRAHIMPUR BRANCH',
        'acc_opening_branch' => 'IBRAHIMPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137RIFATULI',
        'account_number'     => '2207211005159',
        'SIF_Number'         => '2121858',

        'account_title'      => 'KHANDAKER RIFAT-UL-ISLAM',
        'customer_name'      => 'KHANDAKER RIFAT-UL-ISLAM',

        'contact_no_1'       => '8801726207828',
        'mobile_number'      => '8801726207828',

        'email_1'            => 'rifat-13qm@gmail.com',
        'email_address'      => 'rifat-13qm@gmail.com',

        'branch_name'        => 'TEJGAON BRANCH',
        'acc_opening_branch' => 'TEJGAON BRANCH',
    ],
    [
        'bp_id'              => 'IN137RITAPRIME',
        'account_number'     => '2155214014548',
        'SIF_Number'         => '2031488',

        'account_title'      => 'RITA RANI BASAK',
        'customer_name'      => 'RITA RANI BASAK',

        'contact_no_1'       => '8801751766885',
        'mobile_number'      => '8801751766885',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'TANGAIL BRANCH',
        'acc_opening_branch' => 'TANGAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RITARNIPB',
        'account_number'     => '2155214014548',
        'SIF_Number'         => '2031488',

        'account_title'      => 'RITA RANI BASAK',
        'customer_name'      => 'RITA RANI BASAK',

        'contact_no_1'       => '8801751766885',
        'mobile_number'      => '8801751766885',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'TANGAIL BRANCH',
        'acc_opening_branch' => 'TANGAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RITUAPRIM',
        'account_number'     => '2118216036065',
        'SIF_Number'         => '1520652',

        'account_title'      => 'RITU AROBI',
        'customer_name'      => 'RITU AROBI',

        'contact_no_1'       => '8801877751252',
        'mobile_number'      => '8801877751252',

        'email_1'            => 'rituarobi7@gmail.com',
        'email_address'      => 'rituarobi7@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ROBAITHPB',
        'account_number'     => '3108215038518',
        'SIF_Number'         => '2227533',

        'account_title'      => 'ROBAITH HASAN KHADEM',
        'customer_name'      => 'ROBAITH HASAN KHADEM',

        'contact_no_1'       => '8801711302775',
        'mobile_number'      => '8801711302775',

        'email_1'            => 'robaith71@gmail.com',
        'email_address'      => 'robaith71@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ROKSANBPB',
        'account_number'     => '2148217003076',
        'SIF_Number'         => '923602',

        'account_title'      => 'ROKSHANA BOBY',
        'customer_name'      => 'ROKSHANA BOBY',

        'contact_no_1'       => '8801677806082',
        'mobile_number'      => '8801677806082',

        'email_1'            => 'rokshanaboby8@gmail.com',
        'email_address'      => 'rokshanaboby8@gmail.com',

        'branch_name'        => 'KAKRAIL BRANCH',
        'acc_opening_branch' => 'KAKRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ROKSAPRM',
        'account_number'     => '2118219041302',
        'SIF_Number'         => '2758966',

        'account_title'      => 'ROKSANA HOSSAIN',
        'customer_name'      => 'ROKSANA HOSSAIN',

        'contact_no_1'       => '8801777712423',
        'mobile_number'      => '8801777712423',

        'email_1'            => 'roksana.hossain543@gmail.com',
        'email_address'      => 'roksana.hossain543@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137RONYCPPB',
        'account_number'     => '2104219025806',
        'SIF_Number'         => '22087',

        'account_title'      => 'RONY CHANDDRO PODDER',
        'customer_name'      => 'RONY CHANDDRO PODDER',

        'contact_no_1'       => '8801911236084',
        'mobile_number'      => '8801911236084',

        'email_1'            => 'rp112609@primebank.com.bd',
        'email_address'      => 'rp112609@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RSHAMIM',
        'account_number'     => '2118212034254',
        'SIF_Number'         => '2496308',

        'account_title'      => 'SHAMIM REZA',
        'customer_name'      => 'SHAMIM REZA',

        'contact_no_1'       => '8801819220303',
        'mobile_number'      => '8801819220303',

        'email_1'            => 'shamim.reza@sc.com',
        'email_address'      => 'shamim.reza@sc.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137RUBAIYZPB',
        'account_number'     => '2128114018131',
        'SIF_Number'         => '1912658',

        'account_title'      => 'RUBAIYA ZARRIN KHAN',
        'customer_name'      => 'RUBAIYA ZARRIN KHAN',

        'contact_no_1'       => '8801708114474',
        'mobile_number'      => '8801708114474',

        'email_1'            => 'arx4@opsonin.net',
        'email_address'      => 'arx4@opsonin.net',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137RUBELMIA',
        'account_number'     => '2127212025590',
        'SIF_Number'         => '2567267',

        'account_title'      => 'MD RUBEL MIA',
        'customer_name'      => 'MD RUBEL MIA',

        'contact_no_1'       => '8801985578134',
        'mobile_number'      => '8801985578134',

        'email_1'            => 'mdrobin.eng@gmail.com',
        'email_address'      => 'mdrobin.eng@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137RUHALMPRM',
        'account_number'     => '2153217006915',
        'SIF_Number'         => '1830158',

        'account_title'      => 'RUHUL ALAM',
        'customer_name'      => 'RUHUL ALAM',

        'contact_no_1'       => '8801711234936',
        'mobile_number'      => '8801711234936',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'NAOGAON BRANCH',
        'acc_opening_branch' => 'NAOGAON BRANCH',
    ],
    [
        'bp_id'              => 'IN137RUHULAMPB',
        'account_number'     => '2138214005282',
        'SIF_Number'         => '437406',

        'account_title'      => 'MD. RUHUL AMIN',
        'customer_name'      => 'MD. RUHUL AMIN',

        'contact_no_1'       => '8801758898742',
        'mobile_number'      => '8801758898742',

        'email_1'            => 'dentist.ruhul@gmail.com',
        'email_address'      => 'dentist.ruhul@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137RUMANAZPB',
        'account_number'     => '2148214009050',
        'SIF_Number'         => '1596503',

        'account_title'      => 'RUMANA ZAMAN',
        'customer_name'      => 'RUMANA ZAMAN',

        'contact_no_1'       => '8801552423249',
        'mobile_number'      => '8801552423249',

        'email_1'            => 'nupur88102@gmail.com',
        'email_address'      => 'nupur88102@gmail.com',

        'branch_name'        => 'KAKRAIL BRANCH',
        'acc_opening_branch' => 'KAKRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RUMNAPRIM',
        'account_number'     => '2104219033594',
        'SIF_Number'         => '1747341',

        'account_title'      => 'RUMNAJ RAHMAN',
        'customer_name'      => 'RUMNAJ RAHMAN',

        'contact_no_1'       => '8801559103538',
        'mobile_number'      => '8801559103538',

        'email_1'            => 'rumnaj.rahman@gmail.com',
        'email_address'      => 'rumnaj.rahman@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RUPAHQPRM',
        'account_number'     => '2114215002286',
        'SIF_Number'         => '1193941',

        'account_title'      => 'RUPA HAQUE',
        'customer_name'      => 'RUPA HAQUE',

        'contact_no_1'       => '8801685013744',
        'mobile_number'      => '8801685013744',

        'email_1'            => 'rupahaque1@gmail.com',
        'email_address'      => 'rupahaque1@gmail.com',

        'branch_name'        => 'ELEPHANT ROAD BRANCH',
        'acc_opening_branch' => 'ELEPHANT ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137RUPALEPAU',
        'account_number'     => '2104218044329',
        'SIF_Number'         => '2850613',

        'account_title'      => 'RUPALE PAUL AND MAUNTU CHANDRA PAUL',
        'customer_name'      => 'RUPALE PAUL AND MAUNTU CHANDRA PAUL',

        'contact_no_1'       => '8801345717880',
        'mobile_number'      => '8801345717880',

        'email_1'            => 'rupalepaul123@gmail.com',
        'email_address'      => 'rupalepaul123@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137RUPAPRIME',
        'account_number'     => '2132218023550',
        'SIF_Number'         => '1871405',

        'account_title'      => 'RUPA ROY',
        'customer_name'      => 'RUPA ROY',

        'contact_no_1'       => '8801748198879',
        'mobile_number'      => '8801748198879',

        'email_1'            => 'ruparoy1885@gmail.com',
        'email_address'      => 'ruparoy1885@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137RUSSEAUPB',
        'account_number'     => '2104217037571',
        'SIF_Number'         => '2384284',

        'account_title'      => 'MD. RAQUIBUL ISLAM RUSSEAU',
        'customer_name'      => 'MD. RAQUIBUL ISLAM RUSSEAU',

        'contact_no_1'       => '8801730714499',
        'mobile_number'      => '8801730714499',

        'email_1'            => 'raquibul.islam@live.com',
        'email_address'      => 'raquibul.islam@live.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAADBINPB',
        'account_number'     => '2110214023121',
        'SIF_Number'         => '2581981',

        'account_title'      => 'MUHAMMAD SAAD BIN SIHAB',
        'customer_name'      => 'MUHAMMAD SAAD BIN SIHAB',

        'contact_no_1'       => '8801955194724',
        'mobile_number'      => '8801955194724',

        'email_1'            => 'saadbinsihab7@gmail.com',
        'email_address'      => 'saadbinsihab7@gmail.com',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAADHAMPB',
        'account_number'     => '2113219014841',
        'SIF_Number'         => '2264932',

        'account_title'      => 'SAAD HAMID',
        'customer_name'      => 'SAAD HAMID',

        'contact_no_1'       => '8801711468327',
        'mobile_number'      => '8801711468327',

        'email_1'            => 'akt.hamid@gmail.com',
        'email_address'      => 'akt.hamid@gmail.com',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAADRUDDI',
        'account_number'     => '2132212011148',
        'SIF_Number'         => '1485686',

        'account_title'      => 'IMTIAZ SAADRUDDIN MAHMOOD',
        'customer_name'      => 'IMTIAZ SAADRUDDIN MAHMOOD',

        'contact_no_1'       => '8801715374905',
        'mobile_number'      => '8801715374905',

        'email_1'            => 'saad_ism@yahoo.com',
        'email_address'      => 'saad_ism@yahoo.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SABBIRPB',
        'account_number'     => '2127211025443',
        'SIF_Number'         => '2561516',

        'account_title'      => 'MD SABBIR HASAN',
        'customer_name'      => 'MD SABBIR HASAN',

        'contact_no_1'       => '8801774309434',
        'mobile_number'      => '8801774309434',

        'email_1'            => 'sabbirhasananik@gmail.com',
        'email_address'      => 'sabbirhasananik@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SABDULMPR',
        'account_number'     => '2118211011220',
        'SIF_Number'         => '1756576',

        'account_title'      => 'SHAMS ABDULLAH MUHAIMIN',
        'customer_name'      => 'SHAMS ABDULLAH MUHAIMIN',

        'contact_no_1'       => '8801708130770',
        'mobile_number'      => '8801708130770',

        'email_1'            => 'shams.muhaimin@gmail.com',
        'email_address'      => 'shams.muhaimin@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SABIHAAKR',
        'account_number'     => '2165212017129',
        'SIF_Number'         => '2431464',

        'account_title'      => 'SABIHA AKTER',
        'customer_name'      => 'SABIHA AKTER',

        'contact_no_1'       => '8801324943062',
        'mobile_number'      => '8801324943062',

        'email_1'            => 'aktersabiha466@gmail.com',
        'email_address'      => 'aktersabiha466@gmail.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SABUJRYPB',
        'account_number'     => '2174219015000',
        'SIF_Number'         => '2606991',

        'account_title'      => 'SABUJ RAY',
        'customer_name'      => 'SABUJ RAY',

        'contact_no_1'       => '8801814800187',
        'mobile_number'      => '8801814800187',

        'email_1'            => 'sabujray187@mail.com',
        'email_address'      => 'sabujray187@mail.com',

        'branch_name'        => 'MYMENSINGH BRANCH',
        'acc_opening_branch' => 'MYMENSINGH BRANCH',
    ],
    [
        'bp_id'              => 'IN137SADAFPB',
        'account_number'     => '2207211005299',
        'SIF_Number'         => '2142456',

        'account_title'      => 'SADAF ISLAM',
        'customer_name'      => 'SADAF ISLAM',

        'contact_no_1'       => '8801711083243',
        'mobile_number'      => '8801711083243',

        'email_1'            => 'sadaf8islam@hotmail.com',
        'email_address'      => 'sadaf8islam@hotmail.com',

        'branch_name'        => 'TEJGAON BRANCH',
        'acc_opening_branch' => 'TEJGAON BRANCH',
    ],
    [
        'bp_id'              => 'IN137SADATAPRM',
        'account_number'     => '2125214034865',
        'SIF_Number'         => '2678276',

        'account_title'      => 'SADAT AMIN KHAN',
        'customer_name'      => 'SADAT AMIN KHAN',

        'contact_no_1'       => '8801845478936',
        'mobile_number'      => '8801845478936',

        'email_1'            => 'arup69@gmail.com',
        'email_address'      => 'arup69@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SADIAFRIN',
        'account_number'     => '2110214006951',
        'SIF_Number'         => '275271',

        'account_title'      => 'SADIA AFRIN',
        'customer_name'      => 'SADIA AFRIN',

        'contact_no_1'       => '8801972572161',
        'mobile_number'      => '8801972572161',

        'email_1'            => 'sadiapipo@gmail.com',
        'email_address'      => 'sadiapipo@gmail.com',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SADIAJPBL',
        'account_number'     => '2200215010656',
        'SIF_Number'         => '2489454',

        'account_title'      => 'SADIA JAHAN',
        'customer_name'      => 'SADIA JAHAN',

        'contact_no_1'       => '8801777613872',
        'mobile_number'      => '8801777613872',

        'email_1'            => 'sadiabristy012@gmail.com',
        'email_address'      => 'sadiabristy012@gmail.com',

        'branch_name'        => 'JAMALPUR BRANCH',
        'acc_opening_branch' => 'JAMALPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137SADIAKTPB',
        'account_number'     => '2118219033325',
        'SIF_Number'         => '2442253',

        'account_title'      => 'SADIA AKTER ASHA',
        'customer_name'      => 'SADIA AKTER ASHA',

        'contact_no_1'       => '8801860774757',
        'mobile_number'      => '8801860774757',

        'email_1'            => 'sadiaasha037@gmail.com',
        'email_address'      => 'sadiaasha037@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SADIYAAF',
        'account_number'     => '2140219011273',
        'SIF_Number'         => '296666',

        'account_title'      => 'SADIYA AFROSE JAHAN',
        'customer_name'      => 'SADIYA AFROSE JAHAN',

        'contact_no_1'       => '8801577401894',
        'mobile_number'      => '8801577401894',

        'email_1'            => 'sadiyaafrose@gmail.com',
        'email_address'      => 'sadiyaafrose@gmail.com',

        'branch_name'        => 'TONGI BRANCH',
        'acc_opening_branch' => 'TONGI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SADSARHOS',
        'account_number'     => '2104212000095',
        'SIF_Number'         => '1316332',

        'account_title'      => 'SADIA SARMIN HOSSAIN',
        'customer_name'      => 'SADIA SARMIN HOSSAIN',

        'contact_no_1'       => '8801818468564',
        'mobile_number'      => '8801818468564',

        'email_1'            => 'sadia_2303@yahoo.com',
        'email_address'      => 'sadia_2303@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAFAYETPB',
        'account_number'     => '2125215006888',
        'SIF_Number'         => '842678',

        'account_title'      => 'MD. SAFAYET IMTIAZ',
        'customer_name'      => 'MD. SAFAYET IMTIAZ',

        'contact_no_1'       => '8801717094846',
        'mobile_number'      => '8801717094846',

        'email_1'            => 'imtiaz_mn@yahoo.com',
        'email_address'      => 'imtiaz_mn@yahoo.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAFROZPRM',
        'account_number'     => '2162211015193',
        'SIF_Number'         => '2255690',

        'account_title'      => 'SADIA AFROZ LEEZA',
        'customer_name'      => 'SADIA AFROZ LEEZA',

        'contact_no_1'       => '8801711506559',
        'mobile_number'      => '8801711506559',

        'email_1'            => 'safroz@grameenphone.com',
        'email_address'      => 'safroz@grameenphone.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAFUNPB',
        'account_number'     => '2104213001829',
        'SIF_Number'         => '1373392',

        'account_title'      => 'SAFUN IMAM',
        'customer_name'      => 'SAFUN IMAM',

        'contact_no_1'       => '8801741363361',
        'mobile_number'      => '8801741363361',

        'email_1'            => 'safunimam@yahoo.com',
        'email_address'      => 'safunimam@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAHABUDPB',
        'account_number'     => '2118215006499',
        'SIF_Number'         => '1818606',

        'account_title'      => 'MD. SAHABUDDIN',
        'customer_name'      => 'MD. SAHABUDDIN',

        'contact_no_1'       => '8801730333044',
        'mobile_number'      => '8801730333044',

        'email_1'            => 'sahabuddin161988@gmail.com',
        'email_address'      => 'sahabuddin161988@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAHISNUPB',
        'account_number'     => '2219211007945',
        'SIF_Number'         => '1082329',

        'account_title'      => 'SAHISNU DUTTA',
        'customer_name'      => 'SAHISNU DUTTA',

        'contact_no_1'       => '8801911122963',
        'mobile_number'      => '8801911122963',

        'email_1'            => 'sa123du@gmail.com',
        'email_address'      => 'sa123du@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAIDUPRM',
        'account_number'     => '2104211011507',
        'SIF_Number'         => '1324157',

        'account_title'      => 'MD SAIDUR RAHMAN DIPU',
        'customer_name'      => 'MD SAIDUR RAHMAN DIPU',

        'contact_no_1'       => '8801919220377',
        'mobile_number'      => '8801919220377',

        'email_1'            => 'sayeed1973@gmail.com',
        'email_address'      => 'sayeed1973@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAIDURAPB',
        'account_number'     => '2125211022609',
        'SIF_Number'         => '1267321',

        'account_title'      => 'MD. SAIDUR RAHMAN',
        'customer_name'      => 'MD. SAIDUR RAHMAN',

        'contact_no_1'       => '8801952228888',
        'mobile_number'      => '8801952228888',

        'email_1'            => 'saidur.btl@gmail.com',
        'email_address'      => 'saidur.btl@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAIFULHPB',
        'account_number'     => '2118212002103',
        'SIF_Number'         => '75537',

        'account_title'      => 'SYED SAIFUL HAQUE',
        'customer_name'      => 'SYED SAIFUL HAQUE',

        'contact_no_1'       => '8801717157116',
        'mobile_number'      => '8801717157116',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAIFULPB',
        'account_number'     => '2118212040822',
        'SIF_Number'         => '2778468',

        'account_title'      => 'MUHAMMED SAIFUL ISLAM',
        'customer_name'      => 'MUHAMMED SAIFUL ISLAM',

        'contact_no_1'       => '8801711083287',
        'mobile_number'      => '8801711083287',

        'email_1'            => 'msaiful2k@yahoo.com',
        'email_address'      => 'msaiful2k@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAIFULPRM',
        'account_number'     => '2118212040822',
        'SIF_Number'         => '2778468',

        'account_title'      => 'MUHAMMED SAIFUL ISLAM',
        'customer_name'      => 'MUHAMMED SAIFUL ISLAM',

        'contact_no_1'       => '8801711083287',
        'mobile_number'      => '8801711083287',

        'email_1'            => 'msaiful2k@yahoo.com',
        'email_address'      => 'msaiful2k@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAIFULSUJ',
        'account_number'     => '2121218013265',
        'SIF_Number'         => '2333367',

        'account_title'      => 'MD. SAIFUL ALAM SUNNY',
        'customer_name'      => 'MD. SAIFUL ALAM SUNNY',

        'contact_no_1'       => '8801737999977',
        'mobile_number'      => '8801737999977',

        'email_1'            => 'saifulsunny1992@gmail.com',
        'email_address'      => 'saifulsunny1992@gmail.com',

        'branch_name'        => 'JUBILEE ROAD BRANCH',
        'acc_opening_branch' => 'JUBILEE ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAIKATBPB',
        'account_number'     => '2171213020454',
        'SIF_Number'         => '2860576',

        'account_title'      => 'SAIKAT BARUA',
        'customer_name'      => 'SAIKAT BARUA',

        'contact_no_1'       => '8801755578806',
        'mobile_number'      => '8801755578806',

        'email_1'            => 'saikat.barua77@gmail.com',
        'email_address'      => 'saikat.barua77@gmail.com',

        'branch_name'        => 'PROBARTAK MORE BRANCH',
        'acc_opening_branch' => 'PROBARTAK MORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAJEDAKA',
        'account_number'     => '0021060021734',
        'SIF_Number'         => '1306406',

        'account_title'      => 'SAJEDA KARIM',
        'customer_name'      => 'SAJEDA KARIM',

        'contact_no_1'       => '8801711845214',
        'mobile_number'      => '8801711845214',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAJIDRPRM',
        'account_number'     => '2118215027420',
        'SIF_Number'         => '2211481',

        'account_title'      => 'SAJID RAHMAN',
        'customer_name'      => 'SAJID RAHMAN',

        'contact_no_1'       => '8801730704633',
        'mobile_number'      => '8801730704633',

        'email_1'            => 'sajid.rahman@gmail.com',
        'email_address'      => 'sajid.rahman@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAKHAWTPB',
        'account_number'     => '2104217020403',
        'SIF_Number'         => '1379511',

        'account_title'      => 'K. M. SAKHAWAT HOSSAIN',
        'customer_name'      => 'K. M. SAKHAWAT HOSSAIN',

        'contact_no_1'       => '8801726228073',
        'mobile_number'      => '8801726228073',

        'email_1'            => 'kmshakhawatripon@gmail.com',
        'email_address'      => 'kmshakhawatripon@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SALAHUDPB',
        'account_number'     => '3108215061130',
        'SIF_Number'         => '2332605',

        'account_title'      => 'SALAH UDDIN AHMED',
        'customer_name'      => 'SALAH UDDIN AHMED',

        'contact_no_1'       => '8801711726275',
        'mobile_number'      => '8801711726275',

        'email_1'            => 'salahuddinmjl@gmail.com',
        'email_address'      => 'salahuddinmjl@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SALEHAHM',
        'account_number'     => '2219213001995',
        'SIF_Number'         => '1802364',

        'account_title'      => 'SALEH AHMED JEWEL',
        'customer_name'      => 'SALEH AHMED JEWEL',

        'contact_no_1'       => '8801924400792',
        'mobile_number'      => '8801924400792',

        'email_1'            => 'jewelsalehahmed@yahoo.com',
        'email_address'      => 'jewelsalehahmed@yahoo.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SALMANPB',
        'account_number'     => '2104212021554',
        'SIF_Number'         => '1663039',

        'account_title'      => 'MD. SALMAN',
        'customer_name'      => 'MD. SALMAN',

        'contact_no_1'       => '8801711014290',
        'mobile_number'      => '8801711014290',

        'email_1'            => 'salman.bd01@gmail.com',
        'email_address'      => 'salman.bd01@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SALSUNYPB',
        'account_number'     => '2121218013265',
        'SIF_Number'         => '2333367',

        'account_title'      => 'MD. SAIFUL ALAM SUNNY',
        'customer_name'      => 'MD. SAIFUL ALAM SUNNY',

        'contact_no_1'       => '8801737999977',
        'mobile_number'      => '8801737999977',

        'email_1'            => 'saifulsunny1992@gmail.com',
        'email_address'      => 'saifulsunny1992@gmail.com',

        'branch_name'        => 'JUBILEE ROAD BRANCH',
        'acc_opening_branch' => 'JUBILEE ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAMAFRNPB',
        'account_number'     => '2162214001561',
        'SIF_Number'         => '273943',

        'account_title'      => 'SAMIA AFRIN',
        'customer_name'      => 'SAMIA AFRIN',

        'contact_no_1'       => '8801715509321',
        'mobile_number'      => '8801715509321',

        'email_1'            => 'afrinsamia300@gmail.com',
        'email_address'      => 'afrinsamia300@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAMANAPB',
        'account_number'     => '2146212010037',
        'SIF_Number'         => '724937',

        'account_title'      => 'SAMANA DAHAR',
        'customer_name'      => 'SAMANA DAHAR',

        'contact_no_1'       => '8801711645585',
        'mobile_number'      => '8801711645585',

        'email_1'            => 'samana20dahar@gmail.com',
        'email_address'      => 'samana20dahar@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAMIAPB',
        'account_number'     => '2118213030624',
        'SIF_Number'         => '2351284',

        'account_title'      => 'SHAHNAZ SHARMIN SAMIA',
        'customer_name'      => 'SHAHNAZ SHARMIN SAMIA',

        'contact_no_1'       => '8801936450003',
        'mobile_number'      => '8801936450003',

        'email_1'            => 'samiachowdhury2117@gmail.com',
        'email_address'      => 'samiachowdhury2117@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAMIATAPB',
        'account_number'     => '2132216015806',
        'SIF_Number'         => '1239684',

        'account_title'      => 'SAMIA TALUKDAR',
        'customer_name'      => 'SAMIA TALUKDAR',

        'contact_no_1'       => '8801817298644',
        'mobile_number'      => '8801817298644',

        'email_1'            => 'samiatalukdar@gmail.com',
        'email_address'      => 'samiatalukdar@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAMIHAPB',
        'account_number'     => '2104218032522',
        'SIF_Number'         => '2076487',

        'account_title'      => 'SAMIHA BINTE MUHAMMAD',
        'customer_name'      => 'SAMIHA BINTE MUHAMMAD',

        'contact_no_1'       => '8801626401819',
        'mobile_number'      => '8801626401819',

        'email_1'            => 'samihabinte.18@gmail.com',
        'email_address'      => 'samihabinte.18@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAMIHKPRM',
        'account_number'     => '2118211005040',
        'SIF_Number'         => '76165',

        'account_title'      => 'SAMIUL H KAISER',
        'customer_name'      => 'SAMIUL H KAISER',

        'contact_no_1'       => '8801720388168',
        'mobile_number'      => '8801720388168',

        'email_1'            => 'samkai@hotmail.com',
        'email_address'      => 'samkai@hotmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAMINKHAN',
        'account_number'     => '2118212033330',
        'SIF_Number'         => '2442265',

        'account_title'      => 'SAMIN ARMAN KHAN',
        'customer_name'      => 'SAMIN ARMAN KHAN',

        'contact_no_1'       => '8801816558307',
        'mobile_number'      => '8801816558307',

        'email_1'            => 'sadhkm1@yahoo.com',
        'email_address'      => 'sadhkm1@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAMIRONPB',
        'account_number'     => '2128219021492',
        'SIF_Number'         => '2574956',

        'account_title'      => 'SAMIRON KUMAR MONDAL',
        'customer_name'      => 'SAMIRON KUMAR MONDAL',

        'contact_no_1'       => '8801711821893',
        'mobile_number'      => '8801711821893',

        'email_1'            => 'drshamiron@yahoo.com',
        'email_address'      => 'drshamiron@yahoo.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAMSEPB',
        'account_number'     => '2118214042776',
        'SIF_Number'         => '2866540',

        'account_title'      => 'MOHAMMAD SAMSE TABRIZ',
        'customer_name'      => 'MOHAMMAD SAMSE TABRIZ',

        'contact_no_1'       => '8801613063534',
        'mobile_number'      => '8801613063534',

        'email_1'            => 'samse.tabriz@gmail.com',
        'email_address'      => 'samse.tabriz@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SANIAAFR',
        'account_number'     => '2104217033430',
        'SIF_Number'         => '304041',

        'account_title'      => 'SANIA AFROZ',
        'customer_name'      => 'SANIA AFROZ',

        'contact_no_1'       => '8801726676490',
        'mobile_number'      => '8801726676490',

        'email_1'            => 'sania.afroz@primebank.com.bd',
        'email_address'      => 'sania.afroz@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SANJIBKAR',
        'account_number'     => '2118219000076',
        'SIF_Number'         => '1325498',

        'account_title'      => 'MR SANJIB SARKAR',
        'customer_name'      => 'MR SANJIB SARKAR',

        'contact_no_1'       => '8801912084430',
        'mobile_number'      => '8801912084430',

        'email_1'            => 'sanjibna@yahoo.com',
        'email_address'      => 'sanjibna@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SANJIDAPB',
        'account_number'     => '2135211033470',
        'SIF_Number'         => '2298464',

        'account_title'      => 'SANJIDA SULTANA',
        'customer_name'      => 'SANJIDA SULTANA',

        'contact_no_1'       => '8801888626607',
        'mobile_number'      => '8801888626607',

        'email_1'            => 'sanjida607@outlook.com',
        'email_address'      => 'sanjida607@outlook.com',

        'branch_name'        => 'PRAGATI SARANI BRANCH',
        'acc_opening_branch' => 'PRAGATI SARANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SANJOYKUS',
        'account_number'     => '2128215012875',
        'SIF_Number'         => '235542',

        'account_title'      => 'SANJOY KUMAR SAHA',
        'customer_name'      => 'SANJOY KUMAR SAHA',

        'contact_no_1'       => '8801712959868',
        'mobile_number'      => '8801712959868',

        'email_1'            => 'sanjoykrsaha@gmail.com',
        'email_address'      => 'sanjoykrsaha@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137SARABBYPB',
        'account_number'     => '2152219023976',
        'SIF_Number'         => '2164006',

        'account_title'      => 'SAJIDUR RAHMAN RABBY',
        'customer_name'      => 'SAJIDUR RAHMAN RABBY',

        'contact_no_1'       => '8801724234567',
        'mobile_number'      => '8801724234567',

        'email_1'            => 'rabbyx7xafc@live.com',
        'email_address'      => 'rabbyx7xafc@live.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137SARIFUZPB',
        'account_number'     => '2219211009958',
        'SIF_Number'         => '2425941',

        'account_title'      => 'S.M. SARIFUZZAMAN',
        'customer_name'      => 'S.M. SARIFUZZAMAN',

        'contact_no_1'       => '8801711167311',
        'mobile_number'      => '8801711167311',

        'email_1'            => 'zamanrns75@gmail.com',
        'email_address'      => 'zamanrns75@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SARMAKTPB',
        'account_number'     => '2187213003064',
        'SIF_Number'         => '814861',

        'account_title'      => 'SARMIN AKTER',
        'customer_name'      => 'SARMIN AKTER',

        'contact_no_1'       => '8801718420145',
        'mobile_number'      => '8801718420145',

        'email_1'            => 'smaktar00@gamil.com',
        'email_address'      => 'smaktar00@gamil.com',

        'branch_name'        => 'WARI BRANCH',
        'acc_opening_branch' => 'WARI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SARMISTPB',
        'account_number'     => '2127219017129',
        'SIF_Number'         => '2025958',

        'account_title'      => 'SARMISTHA PAUL',
        'customer_name'      => 'SARMISTHA PAUL',

        'contact_no_1'       => '8801789139463',
        'mobile_number'      => '8801789139463',

        'email_1'            => 'sarmistha2102@gmail.com',
        'email_address'      => 'sarmistha2102@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SARWARMPB',
        'account_number'     => '2104211024732',
        'SIF_Number'         => '1389638',

        'account_title'      => 'H. M. SARWAR MARUF',
        'customer_name'      => 'H. M. SARWAR MARUF',

        'contact_no_1'       => '8801911293999',
        'mobile_number'      => '8801911293999',

        'email_1'            => 'sarwarmaruf@gmail.com',
        'email_address'      => 'sarwarmaruf@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SARWISLPB',
        'account_number'     => '2128211008704',
        'SIF_Number'         => '237137',

        'account_title'      => 'SAROWAT BINTE ISLAM',
        'customer_name'      => 'SAROWAT BINTE ISLAM',

        'contact_no_1'       => '8801727655153',
        'mobile_number'      => '8801727655153',

        'email_1'            => 'sarowatislam@yahoo.com',
        'email_address'      => 'sarowatislam@yahoo.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137SATTRANPB',
        'account_number'     => '2104211002506',
        'SIF_Number'         => '1550350',

        'account_title'      => 'SATTYA RANJAN CHAKRABORTY',
        'customer_name'      => 'SATTYA RANJAN CHAKRABORTY',

        'contact_no_1'       => '8801755525599',
        'mobile_number'      => '8801755525599',

        'email_1'            => 'ranjan.dattya@gmail.com',
        'email_address'      => 'ranjan.dattya@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SATYSPRIM',
        'account_number'     => '2127217007031',
        'SIF_Number'         => '1094842',

        'account_title'      => 'SATYAJIT SAHA',
        'customer_name'      => 'SATYAJIT SAHA',

        'contact_no_1'       => '8801819504015',
        'mobile_number'      => '8801819504015',

        'email_1'            => 'stsskysneha@gmail.com',
        'email_address'      => 'stsskysneha@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAWASDPRM',
        'account_number'     => '2118218014946',
        'SIF_Number'         => '1719717',

        'account_title'      => 'SAWASDEE KAMAL CHOWDHURY',
        'customer_name'      => 'SAWASDEE KAMAL CHOWDHURY',

        'contact_no_1'       => '8801755502952',
        'mobile_number'      => '8801755502952',

        'email_1'            => 'schowdhury83@gmail.com',
        'email_address'      => 'schowdhury83@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAYEDAAA',
        'account_number'     => '2206215002442',
        'SIF_Number'         => '1174817',

        'account_title'      => 'SAYEDA ASMA ALI',
        'customer_name'      => 'SAYEDA ASMA ALI',

        'contact_no_1'       => '8801985054277',
        'mobile_number'      => '8801985054277',

        'email_1'            => 'asmafsibl85@gmail.com',
        'email_address'      => 'asmafsibl85@gmail.com',

        'branch_name'        => 'KHULSHI BRANCH',
        'acc_opening_branch' => 'KHULSHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAYEDBHPB',
        'account_number'     => '2113211009808',
        'SIF_Number'         => '787263',

        'account_title'      => 'SAYED BEALAL HOSSAIN',
        'customer_name'      => 'SAYED BEALAL HOSSAIN',

        'contact_no_1'       => '8801711435104',
        'mobile_number'      => '8801711435104',

        'email_1'            => 'sb.hossain@yahoo.com',
        'email_address'      => 'sb.hossain@yahoo.com',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAYEDBL',
        'account_number'     => '2132219003726',
        'SIF_Number'         => '383514',

        'account_title'      => 'SAYED MAHSIB HOSSAIN',
        'customer_name'      => 'SAYED MAHSIB HOSSAIN',

        'contact_no_1'       => '8801917095188',
        'mobile_number'      => '8801917095188',

        'email_1'            => 'mahsib2003@gmail.com',
        'email_address'      => 'mahsib2003@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAYEEDPB',
        'account_number'     => '2107213008714',
        'SIF_Number'         => '542991',

        'account_title'      => 'SYED ABU SAYEED',
        'customer_name'      => 'SYED ABU SAYEED',

        'contact_no_1'       => '8801614444171',
        'mobile_number'      => '8801614444171',

        'email_1'            => 'sayeed.dr@gmail.com',
        'email_address'      => 'sayeed.dr@gmail.com',

        'branch_name'        => 'KHULNA BRANCH',
        'acc_opening_branch' => 'KHULNA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAYEFULL',
        'account_number'     => '2118211034207',
        'SIF_Number'         => '2495152',

        'account_title'      => 'MOHAMMAD SAYEF ULLAH',
        'customer_name'      => 'MOHAMMAD SAYEF ULLAH',

        'contact_no_1'       => '8801819144070',
        'mobile_number'      => '8801819144070',

        'email_1'            => 'sayefullah@mindshare-bd.com',
        'email_address'      => 'sayefullah@mindshare-bd.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAYEMPB',
        'account_number'     => '3108219015876',
        'SIF_Number'         => '2379944',

        'account_title'      => 'SAYEM UDDIN AHAMMED',
        'customer_name'      => 'SAYEM UDDIN AHAMMED',

        'contact_no_1'       => '8801712184820',
        'mobile_number'      => '8801712184820',

        'email_1'            => 'sayemahammed444@gmail.com',
        'email_address'      => 'sayemahammed444@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAYMAMOIN',
        'account_number'     => '2120211002670',
        'SIF_Number'         => '1568918',

        'account_title'      => 'SAYMA MOIN',
        'customer_name'      => 'SAYMA MOIN',

        'contact_no_1'       => '8801817211313',
        'mobile_number'      => '8801817211313',

        'email_1'            => 'saymajamshed.bd@gmail.com',
        'email_address'      => 'saymajamshed.bd@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAZZADHO',
        'account_number'     => '2104216029728',
        'SIF_Number'         => '873996',

        'account_title'      => 'MR. MD. SAZZAD HOSSAIN',
        'customer_name'      => 'MR. MD. SAZZAD HOSSAIN',

        'contact_no_1'       => '8801717013345',
        'mobile_number'      => '8801717013345',

        'email_1'            => 'mh053003@primebank.com.bd',
        'email_address'      => 'mh053003@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SAZZHOSPB',
        'account_number'     => '2104216010512',
        'SIF_Number'         => '873996',

        'account_title'      => 'MD. SAZZAD HOSSAIN',
        'customer_name'      => 'MD. SAZZAD HOSSAIN',

        'contact_no_1'       => '8801717013345',
        'mobile_number'      => '8801717013345',

        'email_1'            => 'mh053003@primebank.com.bd',
        'email_address'      => 'mh053003@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SEKANDERH',
        'account_number'     => '2118218034910',
        'SIF_Number'         => '1951388',

        'account_title'      => 'SEKANDER HAYAT KHAN',
        'customer_name'      => 'SEKANDER HAYAT KHAN',

        'contact_no_1'       => '8801713008424',
        'mobile_number'      => '8801713008424',

        'email_1'            => 'abchayat@gmail.com',
        'email_address'      => 'abchayat@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SELIMABPB',
        'account_number'     => '2170212027097',
        'SIF_Number'         => '2585926',

        'account_title'      => 'SELIMA BEGUM',
        'customer_name'      => 'SELIMA BEGUM',

        'contact_no_1'       => '8801713452627',
        'mobile_number'      => '8801713452627',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'GARIB-E-NEWAZ AVE. BR',
        'acc_opening_branch' => 'GARIB-E-NEWAZ AVE. BR',
    ],
    [
        'bp_id'              => 'IN137SELINAPB',
        'account_number'     => '2219214008080',
        'SIF_Number'         => '2345000',

        'account_title'      => 'SELINA HUSNA BANU',
        'customer_name'      => 'SELINA HUSNA BANU',

        'contact_no_1'       => '8801713012738',
        'mobile_number'      => '8801713012738',

        'email_1'            => 'selinabanu17@gmail.com',
        'email_address'      => 'selinabanu17@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SERAJURPB',
        'account_number'     => '3108214035135',
        'SIF_Number'         => '2642000',

        'account_title'      => 'ABU AHAMMED SERAJUR RAHMAN',
        'customer_name'      => 'ABU AHAMMED SERAJUR RAHMAN',

        'contact_no_1'       => '8801714004670',
        'mobile_number'      => '8801714004670',

        'email_1'            => 'serajurrahman@gmail.com',
        'email_address'      => 'serajurrahman@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SFATEMAPB',
        'account_number'     => '2104214040107',
        'SIF_Number'         => '2566029',

        'account_title'      => 'SYEDA FATEMA KANIZ',
        'customer_name'      => 'SYEDA FATEMA KANIZ',

        'contact_no_1'       => '8801711361405',
        'mobile_number'      => '8801711361405',

        'email_1'            => 'hritizone@yahoo.com',
        'email_address'      => 'hritizone@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHABIHAPB',
        'account_number'     => '2129211013596',
        'SIF_Number'         => '2225716',

        'account_title'      => 'SHABIHA KHAN',
        'customer_name'      => 'SHABIHA KHAN',

        'contact_no_1'       => '8801711313807',
        'mobile_number'      => '8801711313807',

        'email_1'            => 'shabihakhan2019@gmail.com',
        'email_address'      => 'shabihakhan2019@gmail.com',

        'branch_name'        => 'BOGURA BRANCH',
        'acc_opening_branch' => 'BOGURA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHADHON',
        'account_number'     => '0021060013692',
        'SIF_Number'         => '392156',

        'account_title'      => 'SHADHON KUMAR RAY',
        'customer_name'      => 'SHADHON KUMAR RAY',

        'contact_no_1'       => '8801711468601',
        'mobile_number'      => '8801711468601',

        'email_1'            => 'ektukhanishad_ray@yahoo.com',
        'email_address'      => 'ektukhanishad_ray@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAFINRPB',
        'account_number'     => '2118212036069',
        'SIF_Number'         => '2586339',

        'account_title'      => 'MD. SHAFINUR RAHMAN',
        'customer_name'      => 'MD. SHAFINUR RAHMAN',

        'contact_no_1'       => '8801715961248',
        'mobile_number'      => '8801715961248',

        'email_1'            => 'shafinurbd@gmail.com',
        'email_address'      => 'shafinurbd@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAFIPRIM',
        'account_number'     => '2152217029470',
        'SIF_Number'         => '2480556',

        'account_title'      => 'SHAFIQUR RAHMAN',
        'customer_name'      => 'SHAFIQUR RAHMAN',

        'contact_no_1'       => '8801711112455',
        'mobile_number'      => '8801711112455',

        'email_1'            => 'shafiqur.rahmanucep@gmail.com',
        'email_address'      => 'shafiqur.rahmanucep@gmail.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAFIQMPB',
        'account_number'     => '2125211008517',
        'SIF_Number'         => '1142824',

        'account_title'      => 'MD. MAHBUB ALAM SHAFIQ',
        'customer_name'      => 'MD. MAHBUB ALAM SHAFIQ',

        'contact_no_1'       => '8801886017244',
        'mobile_number'      => '8801886017244',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAFIQURP',
        'account_number'     => '2118216038061',
        'SIF_Number'         => '2659523',

        'account_title'      => 'SHAFIQUR RAHMAN',
        'customer_name'      => 'SHAFIQUR RAHMAN',

        'contact_no_1'       => '8801612892285',
        'mobile_number'      => '8801612892285',

        'email_1'            => 'kamrulshohelazi@gmail.com',
        'email_address'      => 'kamrulshohelazi@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAHADAPB',
        'account_number'     => '2111216025206',
        'SIF_Number'         => '2657653',

        'account_title'      => 'SHAHADAT HOSSAIN',
        'customer_name'      => 'SHAHADAT HOSSAIN',

        'contact_no_1'       => '8801764847947',
        'mobile_number'      => '8801764847947',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'BARISAL BRANCH',
        'acc_opening_branch' => 'BARISAL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAHADATH',
        'account_number'     => '2146216014126',
        'SIF_Number'         => '2097236',

        'account_title'      => 'MD. SHAHADAT HOSSAIN',
        'customer_name'      => 'MD. SHAHADAT HOSSAIN',

        'contact_no_1'       => '8801671985360',
        'mobile_number'      => '8801671985360',

        'email_1'            => 'shossain170184@gmail.com',
        'email_address'      => 'shossain170184@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAHAJADI',
        'account_number'     => '2104217044585',
        'SIF_Number'         => '2872864',

        'account_title'      => 'SHAHAJADI',
        'customer_name'      => 'SHAHAJADI',

        'contact_no_1'       => '8801781366380',
        'mobile_number'      => '8801781366380',

        'email_1'            => 'shahajadi360@gmail.com',
        'email_address'      => 'shahajadi360@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAHALPRM',
        'account_number'     => '2104116038762',
        'SIF_Number'         => '62879',

        'account_title'      => 'MD SHAHIN ALAM',
        'customer_name'      => 'MD SHAHIN ALAM',

        'contact_no_1'       => '8801713080386',
        'mobile_number'      => '8801713080386',

        'email_1'            => 'ms_alam27@yahoo.com',
        'email_address'      => 'ms_alam27@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAHANAPB',
        'account_number'     => '2118213040797',
        'SIF_Number'         => '2777406',

        'account_title'      => 'SHAHANA PARVEEN',
        'customer_name'      => 'SHAHANA PARVEEN',

        'contact_no_1'       => '8801934923188',
        'mobile_number'      => '8801934923188',

        'email_1'            => 'syedashamimaparveen@gmail.com',
        'email_address'      => 'syedashamimaparveen@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAHANPB',
        'account_number'     => '2104213000367',
        'SIF_Number'         => '13472',

        'account_title'      => 'MD. SHAHAN KARIM',
        'customer_name'      => 'MD. SHAHAN KARIM',

        'contact_no_1'       => '8801703723525',
        'mobile_number'      => '8801703723525',

        'email_1'            => 'mk120102@primebank.com.bd',
        'email_address'      => 'mk120102@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAHARPB',
        'account_number'     => '2104214010746',
        'SIF_Number'         => '988503',

        'account_title'      => 'MD. SHAHARIYER ALAMGIR',
        'customer_name'      => 'MD. SHAHARIYER ALAMGIR',

        'contact_no_1'       => '8801911764156',
        'mobile_number'      => '8801911764156',

        'email_1'            => 'shahariyer764@gmail.com',
        'email_address'      => 'shahariyer764@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAHELAPB',
        'account_number'     => '2118219037896',
        'SIF_Number'         => '2649044',

        'account_title'      => 'SYEDA SHAHELA PARVIN',
        'customer_name'      => 'SYEDA SHAHELA PARVIN',

        'contact_no_1'       => '8801817631068',
        'mobile_number'      => '8801817631068',

        'email_1'            => 'shaheladko@gmail.com',
        'email_address'      => 'shaheladko@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAHIDUL',
        'account_number'     => '2170219006219',
        'SIF_Number'         => '791264',

        'account_title'      => 'MUHAMMAD SHAHIDULLAH MUNSHI',
        'customer_name'      => 'MUHAMMAD SHAHIDULLAH MUNSHI',

        'contact_no_1'       => '8801914908354',
        'mobile_number'      => '8801914908354',

        'email_1'            => 'sm031440@primebank.com.bd',
        'email_address'      => 'sm031440@primebank.com.bd',

        'branch_name'        => 'GARIB-E-NEWAZ AVE. BR',
        'acc_opening_branch' => 'GARIB-E-NEWAZ AVE. BR',
    ],
    [
        'bp_id'              => 'IN137SHAHJAHPB',
        'account_number'     => '2106211006217',
        'SIF_Number'         => '316923',

        'account_title'      => 'MD. SHAHJAHAN',
        'customer_name'      => 'MD. SHAHJAHAN',

        'contact_no_1'       => '8801719400920',
        'mobile_number'      => '8801719400920',

        'email_1'            => 'ms062902@primebank.com.bd',
        'email_address'      => 'ms062902@primebank.com.bd',

        'branch_name'        => 'MOULVIBAZAR BRANCH',
        'acc_opening_branch' => 'MOULVIBAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAHNAJPB',
        'account_number'     => '2104215034018',
        'SIF_Number'         => '2161097',

        'account_title'      => 'SHAHNAZ BEGUM',
        'customer_name'      => 'SHAHNAZ BEGUM',

        'contact_no_1'       => '8801722000972',
        'mobile_number'      => '8801722000972',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAHPRIME',
        'account_number'     => '2161319000104',
        'SIF_Number'         => '38621',

        'account_title'      => 'Md Shahadat Hossain',
        'customer_name'      => 'Md Shahadat Hossain',

        'contact_no_1'       => '8801713200701',
        'mobile_number'      => '8801713200701',

        'email_1'            => 'shahadatgroupinfo@gmail.com',
        'email_address'      => 'shahadatgroupinfo@gmail.com',

        'branch_name'        => 'RANGPUR BRANCH',
        'acc_opening_branch' => 'RANGPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAHRIARF',
        'account_number'     => '2219218010223',
        'SIF_Number'         => '1883088',

        'account_title'      => 'SHAHRIAR RAHMAN RAFI',
        'customer_name'      => 'SHAHRIAR RAHMAN RAFI',

        'contact_no_1'       => '8801715407433',
        'mobile_number'      => '8801715407433',

        'email_1'            => 'rafi-rahman@hotmail.com',
        'email_address'      => 'rafi-rahman@hotmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAIKATB',
        'account_number'     => '2104212005045',
        'SIF_Number'         => '1668596',

        'account_title'      => 'SHAIKAT BARUA',
        'customer_name'      => 'SHAIKAT BARUA',

        'contact_no_1'       => '8801964843104',
        'mobile_number'      => '8801964843104',

        'email_1'            => 'sbarua842@gmail.com',
        'email_address'      => 'sbarua842@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAIKHNUR',
        'account_number'     => '2104214028599',
        'SIF_Number'         => '1443151',

        'account_title'      => 'SHAIKH NUR ALAM',
        'customer_name'      => 'SHAIKH NUR ALAM',

        'contact_no_1'       => '8801714626389',
        'mobile_number'      => '8801714626389',

        'email_1'            => 'shaikhraju055@gmail.com',
        'email_address'      => 'shaikhraju055@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAILABED',
        'account_number'     => '2219216002370',
        'SIF_Number'         => '1843283',

        'account_title'      => 'SHAILA ABEDIN',
        'customer_name'      => 'SHAILA ABEDIN',

        'contact_no_1'       => '8801713063470',
        'mobile_number'      => '8801713063470',

        'email_1'            => 'arikarikarika@yahoo.com',
        'email_address'      => 'arikarikarika@yahoo.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAILAKPB',
        'account_number'     => '3108216002810',
        'SIF_Number'         => '166855',

        'account_title'      => 'SHAILA AKHTER',
        'customer_name'      => 'SHAILA AKHTER',

        'contact_no_1'       => '8801674042448',
        'mobile_number'      => '8801674042448',

        'email_1'            => 'shailaakhter1981@gmail.com',
        'email_address'      => 'shailaakhter1981@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAILASH',
        'account_number'     => '2127218022138',
        'SIF_Number'         => '2310497',

        'account_title'      => 'SHAILA SHARMIN',
        'customer_name'      => 'SHAILA SHARMIN',

        'contact_no_1'       => '8801811447396',
        'mobile_number'      => '8801811447396',

        'email_1'            => 'shaila.sharmin.ovi@gmail.com',
        'email_address'      => 'shaila.sharmin.ovi@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAJIAPB',
        'account_number'     => '2219211005577',
        'SIF_Number'         => '304733',

        'account_title'      => 'SHAJIA AFREEN',
        'customer_name'      => 'SHAJIA AFREEN',

        'contact_no_1'       => '8801713201835',
        'mobile_number'      => '8801713201835',

        'email_1'            => 'afreen.shajia2017@gmail.com',
        'email_address'      => 'afreen.shajia2017@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAJMERIP',
        'account_number'     => '2162219016002',
        'SIF_Number'         => '440286',

        'account_title'      => 'SHAHJAT MERINA',
        'customer_name'      => 'SHAHJAT MERINA',

        'contact_no_1'       => '8801797341560',
        'mobile_number'      => '8801797341560',

        'email_1'            => 'llshafiq@gmail.com',
        'email_address'      => 'llshafiq@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAKHOSS',
        'account_number'     => '2120719001806',
        'SIF_Number'         => '197915',

        'account_title'      => 'MOHAMMAD SHAKHAWAT HOSSAIN',
        'customer_name'      => 'MOHAMMAD SHAKHAWAT HOSSAIN',

        'contact_no_1'       => '8801817210880',
        'mobile_number'      => '8801817210880',

        'email_1'            => 'mshuzzal5@gmail.com',
        'email_address'      => 'mshuzzal5@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAKILAPB',
        'account_number'     => '2104216007385',
        'SIF_Number'         => '158540',

        'account_title'      => 'SHAKILUDDIN AHMED',
        'customer_name'      => 'SHAKILUDDIN AHMED',

        'contact_no_1'       => '8801916832321',
        'mobile_number'      => '8801916832321',

        'email_1'            => 'sa051801@primebank.com.bd',
        'email_address'      => 'sa051801@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAKILPB',
        'account_number'     => '3108211031103',
        'SIF_Number'         => '2603587',

        'account_title'      => 'SHAKIL AHMAD',
        'customer_name'      => 'SHAKIL AHMAD',

        'contact_no_1'       => '8801727548072',
        'mobile_number'      => '8801727548072',

        'email_1'            => 'asoka@hotmail.com',
        'email_address'      => 'asoka@hotmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAMAALAM',
        'account_number'     => '2118214042917',
        'SIF_Number'         => '2873290',

        'account_title'      => 'SHAMA RUKH ALAM',
        'customer_name'      => 'SHAMA RUKH ALAM',

        'contact_no_1'       => '8801713084494',
        'mobile_number'      => '8801713084494',

        'email_1'            => 'alamsrukh@gmail.com',
        'email_address'      => 'alamsrukh@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAMAILPB',
        'account_number'     => '2219213008701',
        'SIF_Number'         => '2397517',

        'account_title'      => 'SHAMAILA MAHBUB',
        'customer_name'      => 'SHAMAILA MAHBUB',

        'contact_no_1'       => '8801713063863',
        'mobile_number'      => '8801713063863',

        'email_1'            => 'smahbub04@gmail.com',
        'email_address'      => 'smahbub04@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAMIMAPB',
        'account_number'     => '2104215004433',
        'SIF_Number'         => '13635',

        'account_title'      => 'SHAMIMA SHARMIN',
        'customer_name'      => 'SHAMIMA SHARMIN',

        'contact_no_1'       => '8801813036197',
        'mobile_number'      => '8801813036197',

        'email_1'            => 'sharminnina@yahoo.com',
        'email_address'      => 'sharminnina@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAMISUPB',
        'account_number'     => '2146214001505',
        'SIF_Number'         => '846196',

        'account_title'      => 'MOST. SHAMIMA SULTANA',
        'customer_name'      => 'MOST. SHAMIMA SULTANA',

        'contact_no_1'       => '8801918580832',
        'mobile_number'      => '8801918580832',

        'email_1'            => 'shamima.bncu@gmail.com',
        'email_address'      => 'shamima.bncu@gmail.com',

        'branch_name'        => 'DHANMONDI BRANCH',
        'acc_opening_branch' => 'DHANMONDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAMMUPB',
        'account_number'     => '2219213007075',
        'SIF_Number'         => '1704648',

        'account_title'      => 'FARHANA HOSSAIN SHAMMU',
        'customer_name'      => 'FARHANA HOSSAIN SHAMMU',

        'contact_no_1'       => '8801730312516',
        'mobile_number'      => '8801730312516',

        'email_1'            => 'amzad.hossain@primebank.com.bd',
        'email_address'      => 'amzad.hossain@primebank.com.bd',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAMNASPB',
        'account_number'     => '2128217013061',
        'SIF_Number'         => '919673',

        'account_title'      => 'SHAMIMA NASRIN',
        'customer_name'      => 'SHAMIMA NASRIN',

        'contact_no_1'       => '8801715290546',
        'mobile_number'      => '8801715290546',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAMOLPRI',
        'account_number'     => '2118219033556',
        'SIF_Number'         => '2472656',

        'account_title'      => 'SHAMOL KUMAR PATRO',
        'customer_name'      => 'SHAMOL KUMAR PATRO',

        'contact_no_1'       => '8801720558706',
        'mobile_number'      => '8801720558706',

        'email_1'            => 'sheti.elementary@gmail.com',
        'email_address'      => 'sheti.elementary@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAMRAHPB',
        'account_number'     => '2126217014863',
        'SIF_Number'         => '2691246',

        'account_title'      => 'MD. SHAMSUR RAHMAN',
        'customer_name'      => 'MD. SHAMSUR RAHMAN',

        'contact_no_1'       => '8801716900358',
        'mobile_number'      => '8801716900358',

        'email_1'            => 'abu.shams@bb.org.bd',
        'email_address'      => 'abu.shams@bb.org.bd',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137SHAMSABDU',
        'account_number'     => '2118211011220',
        'SIF_Number'         => '1756576',

        'account_title'      => 'SHAMS ABDULLAH MUHAIMIN',
        'customer_name'      => 'SHAMS ABDULLAH MUHAIMIN',

        'contact_no_1'       => '8801708130770',
        'mobile_number'      => '8801708130770',

        'email_1'            => 'shams.muhaimin@gmail.com',
        'email_address'      => 'shams.muhaimin@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAMSCPRM',
        'account_number'     => '2113214010125',
        'SIF_Number'         => '310028',

        'account_title'      => 'SHAMSHAD CHOWDHURY',
        'customer_name'      => 'SHAMSHAD CHOWDHURY',

        'contact_no_1'       => '8801715819268',
        'mobile_number'      => '8801715819268',

        'email_1'            => 'wahedwdb@gmail.com',
        'email_address'      => 'wahedwdb@gmail.com',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHANJANAP',
        'account_number'     => '2114211019335',
        'SIF_Number'         => '2590912',

        'account_title'      => 'SHANJANA KAMAL',
        'customer_name'      => 'SHANJANA KAMAL',

        'contact_no_1'       => '8801670759361',
        'mobile_number'      => '8801670759361',

        'email_1'            => 'sanjana.yaara@gmail.com',
        'email_address'      => 'sanjana.yaara@gmail.com',

        'branch_name'        => 'ELEPHANT ROAD BRANCH',
        'acc_opening_branch' => 'ELEPHANT ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAONPRIM',
        'account_number'     => '2118219042045',
        'SIF_Number'         => '2830580',

        'account_title'      => 'MOHAMMAD ASADUZZAMAN SHAON',
        'customer_name'      => 'MOHAMMAD ASADUZZAMAN SHAON',

        'contact_no_1'       => '8801730712469',
        'mobile_number'      => '8801730712469',

        'email_1'            => 'a.shaon@gmail.com',
        'email_address'      => 'a.shaon@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHARAKPRM',
        'account_number'     => '2104211033353',
        'SIF_Number'         => '1083812',

        'account_title'      => 'SHARMIN AKTHER',
        'customer_name'      => 'SHARMIN AKTHER',

        'contact_no_1'       => '8801727291377',
        'mobile_number'      => '8801727291377',

        'email_1'            => 'sakther72@yahoo.com',
        'email_address'      => 'sakther72@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHARFUDIN',
        'account_number'     => '2104219030517',
        'SIF_Number'         => '1956714',

        'account_title'      => 'SHARFUDDIN KOBBAD HUSSAIN',
        'customer_name'      => 'SHARFUDDIN KOBBAD HUSSAIN',

        'contact_no_1'       => '8801708520383',
        'mobile_number'      => '8801708520383',

        'email_1'            => 'sharfuddin.kobbad@meghnabank.com.bd',
        'email_address'      => 'sharfuddin.kobbad@meghnabank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHARIFPBL',
        'account_number'     => '2164216025525',
        'SIF_Number'         => '2502444',

        'account_title'      => 'SHARIFA ABBAS',
        'customer_name'      => 'SHARIFA ABBAS',

        'contact_no_1'       => '8801683222156',
        'mobile_number'      => '8801683222156',

        'email_1'            => 'a.hilalee@yahoo.com',
        'email_address'      => 'a.hilalee@yahoo.com',

        'branch_name'        => 'PALLABI BRANCH',
        'acc_opening_branch' => 'PALLABI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHARIFPRM',
        'account_number'     => '2126216007132',
        'SIF_Number'         => '1024186',

        'account_title'      => 'SHARIF UDDIN AHMED',
        'customer_name'      => 'SHARIF UDDIN AHMED',

        'contact_no_1'       => '8801713107134',
        'mobile_number'      => '8801713107134',

        'email_1'            => 'sharifjanuary@yahoo.com',
        'email_address'      => 'sharifjanuary@yahoo.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137SHARIFUZZ',
        'account_number'     => '2112217009455',
        'SIF_Number'         => '576158',

        'account_title'      => 'MD. SHARIFUZZAMAN',
        'customer_name'      => 'MD. SHARIFUZZAMAN',

        'contact_no_1'       => '8801768945378',
        'mobile_number'      => '8801768945378',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'RAJSHAHI BRANCH',
        'acc_opening_branch' => 'RAJSHAHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHARMINPB',
        'account_number'     => '2198218000096',
        'SIF_Number'         => '1645215',

        'account_title'      => 'SHARMIN HOSSAIN',
        'customer_name'      => 'SHARMIN HOSSAIN',

        'contact_no_1'       => '8801818929800',
        'mobile_number'      => '8801818929800',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'BASHABO BRANCH',
        'acc_opening_branch' => 'BASHABO BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHARMINTK',
        'account_number'     => '0021050018154',
        'SIF_Number'         => '1285419',

        'account_title'      => 'SHARMIN TOWFIK',
        'customer_name'      => 'SHARMIN TOWFIK',

        'contact_no_1'       => '8801712605735',
        'mobile_number'      => '8801712605735',

        'email_1'            => 'towfik.globalfashion@gmail.com',
        'email_address'      => 'towfik.globalfashion@gmail.com',

        'branch_name'        => 'SUBID BAZAR BRANCH',
        'acc_opening_branch' => 'SUBID BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHARMSTPB',
        'account_number'     => '2128211020385',
        'SIF_Number'         => '171291',

        'account_title'      => 'SHARMISTHA ROY',
        'customer_name'      => 'SHARMISTHA ROY',

        'contact_no_1'       => '8801715012833',
        'mobile_number'      => '8801715012833',

        'email_1'            => 'sharmistha.doctor@gmail.com',
        'email_address'      => 'sharmistha.doctor@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHARSULPB',
        'account_number'     => '2152211010658',
        'SIF_Number'         => '114739',

        'account_title'      => 'SHARMIN SULTANA',
        'customer_name'      => 'SHARMIN SULTANA',

        'contact_no_1'       => '8801792220001',
        'mobile_number'      => '8801792220001',

        'email_1'            => 'ss050803@primebank.com.bd',
        'email_address'      => 'ss050803@primebank.com.bd',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHATBDPB',
        'account_number'     => '2126213014412',
        'SIF_Number'         => '2625998',

        'account_title'      => 'SHATABDI CHOWDHURY',
        'customer_name'      => 'SHATABDI CHOWDHURY',

        'contact_no_1'       => '8801926511824',
        'mobile_number'      => '8801926511824',

        'email_1'            => 'shatabdidu@gmail.com',
        'email_address'      => 'shatabdidu@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137SHAWKATPB',
        'account_number'     => '2104215004136',
        'SIF_Number'         => '10605',

        'account_title'      => 'MD. SHAWKAT ALI',
        'customer_name'      => 'MD. SHAWKAT ALI',

        'contact_no_1'       => '8801711563448',
        'mobile_number'      => '8801711563448',

        'email_1'            => 'shawkat.ali@primebank.com.bd',
        'email_address'      => 'shawkat.ali@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHAYPAPBK',
        'account_number'     => '2127215023758',
        'SIF_Number'         => '1213544',

        'account_title'      => 'SHAYLA PARVEEN',
        'customer_name'      => 'SHAYLA PARVEEN',

        'contact_no_1'       => '8801619315531',
        'mobile_number'      => '8801619315531',

        'email_1'            => 'shilpi2102@gmail.com',
        'email_address'      => 'shilpi2102@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHETUPB',
        'account_number'     => '2118219033317',
        'SIF_Number'         => '2442237',

        'account_title'      => 'SHARMIN AKTER SHETU',
        'customer_name'      => 'SHARMIN AKTER SHETU',

        'contact_no_1'       => '8801706537735',
        'mobile_number'      => '8801706537735',

        'email_1'            => 'sharmin.shetu10@gmail.com',
        'email_address'      => 'sharmin.shetu10@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHIMULHPB',
        'account_number'     => '2126215010806',
        'SIF_Number'         => '2096329',

        'account_title'      => 'MD. SHIMUL HOSSAIN',
        'customer_name'      => 'MD. SHIMUL HOSSAIN',

        'contact_no_1'       => '8801937232274',
        'mobile_number'      => '8801937232274',

        'email_1'            => 'shimul90pa@gmail.com',
        'email_address'      => 'shimul90pa@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137SHIPRADPB',
        'account_number'     => '2161217014315',
        'SIF_Number'         => '778972',

        'account_title'      => 'SHIPRA DAS',
        'customer_name'      => 'SHIPRA DAS',

        'contact_no_1'       => '8801893200793',
        'mobile_number'      => '8801893200793',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'RANGPUR BRANCH',
        'acc_opening_branch' => 'RANGPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHIPRAGPB',
        'account_number'     => '3108211023473',
        'SIF_Number'         => '2532147',

        'account_title'      => 'SHIPRA RANI GHOSH',
        'customer_name'      => 'SHIPRA RANI GHOSH',

        'contact_no_1'       => '8801680073477',
        'mobile_number'      => '8801680073477',

        'email_1'            => 'badhonghosh120@gmail.com',
        'email_address'      => 'badhonghosh120@gmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHIRINHOS',
        'account_number'     => '2127211006492',
        'SIF_Number'         => '175781',

        'account_title'      => 'SHIRIN HOSSAIN',
        'customer_name'      => 'SHIRIN HOSSAIN',

        'contact_no_1'       => '8801817522126',
        'mobile_number'      => '8801817522126',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHIRSAPRM',
        'account_number'     => '2138214014656',
        'SIF_Number'         => '1027810',

        'account_title'      => 'SHIRIN SHAHANA AKHTER',
        'customer_name'      => 'SHIRIN SHAHANA AKHTER',

        'contact_no_1'       => '8801715105574',
        'mobile_number'      => '8801715105574',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHMAHINPB',
        'account_number'     => '2118215038153',
        'SIF_Number'         => '2664397',

        'account_title'      => 'SHAH MD. AL- MAHIN',
        'customer_name'      => 'SHAH MD. AL- MAHIN',

        'contact_no_1'       => '8801781465492',
        'mobile_number'      => '8801781465492',

        'email_1'            => 'almahinsaad06@gmail.com',
        'email_address'      => 'almahinsaad06@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHNTHIMPB',
        'account_number'     => '2104219005816',
        'SIF_Number'         => '13681',

        'account_title'      => 'SHANTHI MAHJABEEN',
        'customer_name'      => 'SHANTHI MAHJABEEN',

        'contact_no_1'       => '8801556343176',
        'mobile_number'      => '8801556343176',

        'email_1'            => 'smahjabeen1980@gmail.com',
        'email_address'      => 'smahjabeen1980@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHOHAGPB',
        'account_number'     => '2104214025835',
        'SIF_Number'         => '1445027',

        'account_title'      => 'KHANDAKER SHOHAG',
        'customer_name'      => 'KHANDAKER SHOHAG',

        'contact_no_1'       => '8801732703433',
        'mobile_number'      => '8801732703433',

        'email_1'            => 'khandakershohag99@gmail.com',
        'email_address'      => 'khandakershohag99@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHOMAILA',
        'account_number'     => '2118218034118',
        'SIF_Number'         => '2491926',

        'account_title'      => 'SHOMAIL ATAHER',
        'customer_name'      => 'SHOMAIL ATAHER',

        'contact_no_1'       => '8801711507928',
        'mobile_number'      => '8801711507928',

        'email_1'            => 'shomail13@gmail.com',
        'email_address'      => 'shomail13@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHORIATPB',
        'account_number'     => '2104213016603',
        'SIF_Number'         => '14211',

        'account_title'      => 'SHORIAT UDDIN AHAMMED',
        'customer_name'      => 'SHORIAT UDDIN AHAMMED',

        'contact_no_1'       => '8801709658491',
        'mobile_number'      => '8801709658491',

        'email_1'            => 'sa082102@primebank.com.bd',
        'email_address'      => 'sa082102@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHOSSAINP',
        'account_number'     => '2114211019334',
        'SIF_Number'         => '2590902',

        'account_title'      => 'SHAFAYET HOSSAIN',
        'customer_name'      => 'SHAFAYET HOSSAIN',

        'contact_no_1'       => '8801615399095',
        'mobile_number'      => '8801615399095',

        'email_1'            => 'shafs_07@yahoo.com',
        'email_address'      => 'shafs_07@yahoo.com',

        'branch_name'        => 'ELEPHANT ROAD BRANCH',
        'acc_opening_branch' => 'ELEPHANT ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHPTPRIME',
        'account_number'     => '2114212001141',
        'SIF_Number'         => '260179',

        'account_title'      => 'Shaheda Pervin Trisha',
        'customer_name'      => 'Shaheda Pervin Trisha',

        'contact_no_1'       => '8801715428587',
        'mobile_number'      => '8801715428587',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'ELEPHANT ROAD BRANCH',
        'acc_opening_branch' => 'ELEPHANT ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHRMNSLPB',
        'account_number'     => '2117216001342',
        'SIF_Number'         => '835370',

        'account_title'      => 'SHARMIN SULTANA',
        'customer_name'      => 'SHARMIN SULTANA',

        'contact_no_1'       => '8801670180865',
        'mobile_number'      => '8801670180865',

        'email_1'            => 'sharmin2010soni@gmail.com',
        'email_address'      => 'sharmin2010soni@gmail.com',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHUBHABIS',
        'account_number'     => '2118216022602',
        'SIF_Number'         => '2025219',

        'account_title'      => 'SHUBHA BISWAS',
        'customer_name'      => 'SHUBHA BISWAS',

        'contact_no_1'       => '8801622879981',
        'mobile_number'      => '8801622879981',

        'email_1'            => 'shubha.biswas94@gmail.com',
        'email_address'      => 'shubha.biswas94@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SHUMAROPB',
        'account_number'     => '2106217001708',
        'SIF_Number'         => '177451',

        'account_title'      => 'SHUMA ROY',
        'customer_name'      => 'SHUMA ROY',

        'contact_no_1'       => '8801720963178',
        'mobile_number'      => '8801720963178',

        'email_1'            => 'shumaroy@yahoo.com',
        'email_address'      => 'shumaroy@yahoo.com',

        'branch_name'        => 'MOULVIBAZAR BRANCH',
        'acc_opening_branch' => 'MOULVIBAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137SIRAJULHQ',
        'account_number'     => '2120211002556',
        'SIF_Number'         => '195737',

        'account_title'      => 'SIRAJUL HOQUE',
        'customer_name'      => 'SIRAJUL HOQUE',

        'contact_no_1'       => '8801911116474',
        'mobile_number'      => '8801911116474',

        'email_1'            => 'sirajulhoque65@gmail.com',
        'email_address'      => 'sirajulhoque65@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SJAGLULPB',
        'account_number'     => '2113219014973',
        'SIF_Number'         => '2278373',

        'account_title'      => 'SYED JAGLUL PASHA',
        'customer_name'      => 'SYED JAGLUL PASHA',

        'contact_no_1'       => '8801819219479',
        'mobile_number'      => '8801819219479',

        'email_1'            => 'syedjpasha@gmail.com',
        'email_address'      => 'syedjpasha@gmail.com',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137SJKUMARPB',
        'account_number'     => '2126211013029',
        'SIF_Number'         => '2445182',

        'account_title'      => 'SUJAL KUMAR BASAK',
        'customer_name'      => 'SUJAL KUMAR BASAK',

        'contact_no_1'       => '8801737151715',
        'mobile_number'      => '8801737151715',

        'email_1'            => 'sujalbasak554@gmail.com',
        'email_address'      => 'sujalbasak554@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137SKAMRULPB',
        'account_number'     => '2104217008713',
        'SIF_Number'         => '14893',

        'account_title'      => 'SYED KAMRUL ISLAM',
        'customer_name'      => 'SYED KAMRUL ISLAM',

        'contact_no_1'       => '8801710908849',
        'mobile_number'      => '8801710908849',

        'email_1'            => 'rawnak742@yahoo.com',
        'email_address'      => 'rawnak742@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SKHAIRAPB',
        'account_number'     => '2123212009465',
        'SIF_Number'         => '1619707',

        'account_title'      => 'SHAIKH KHAIRUL ALAM',
        'customer_name'      => 'SHAIKH KHAIRUL ALAM',

        'contact_no_1'       => '8801924400903',
        'mobile_number'      => '8801924400903',

        'email_1'            => 'khairul.kln@gmail.com',
        'email_address'      => 'khairul.kln@gmail.com',

        'branch_name'        => 'JESSORE BRANCH',
        'acc_opening_branch' => 'JESSORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137SKMATIURR',
        'account_number'     => '2104213022528',
        'SIF_Number'         => '14712',

        'account_title'      => 'SK MATIUR RAHMAN',
        'customer_name'      => 'SK MATIUR RAHMAN',

        'contact_no_1'       => '8801713047709',
        'mobile_number'      => '8801713047709',

        'email_1'            => 'skmatiurrahman62@gmail.com',
        'email_address'      => 'skmatiurrahman62@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SKMMONIPB',
        'account_number'     => '2125217026207',
        'SIF_Number'         => '2235896',

        'account_title'      => 'SHEIKH MOMENA MONI',
        'customer_name'      => 'SHEIKH MOMENA MONI',

        'contact_no_1'       => '8801712205975',
        'mobile_number'      => '8801712205975',

        'email_1'            => 'momenamoni@gmail.com',
        'email_address'      => 'momenamoni@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SKMUAZHAD',
        'account_number'     => '2123214003078',
        'SIF_Number'         => '563577',

        'account_title'      => 'SK MUAZ HURAYRA MARSHAD',
        'customer_name'      => 'SK MUAZ HURAYRA MARSHAD',

        'contact_no_1'       => '8801711000112',
        'mobile_number'      => '8801711000112',

        'email_1'            => 'sm080708@primebank.com.bd',
        'email_address'      => 'sm080708@primebank.com.bd',

        'branch_name'        => 'JESSORE BRANCH',
        'acc_opening_branch' => 'JESSORE BRANCH',
    ],
    [
        'bp_id'              => 'IN137SKSARKRPB',
        'account_number'     => '2118215000575',
        'SIF_Number'         => '1214569',

        'account_title'      => 'SUDARSHAN KUMAR SARKER',
        'customer_name'      => 'SUDARSHAN KUMAR SARKER',

        'contact_no_1'       => '8801718460126',
        'mobile_number'      => '8801718460126',

        'email_1'            => 'biman-sarker@yahoo.com',
        'email_address'      => 'biman-sarker@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SMAHBUBPB',
        'account_number'     => '2192211001048',
        'SIF_Number'         => '1049315',

        'account_title'      => 'ADVOCATE SYED MAHBUB HOSSAIN',
        'customer_name'      => 'ADVOCATE SYED MAHBUB HOSSAIN',

        'contact_no_1'       => '8801707071793',
        'mobile_number'      => '8801707071793',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'BANASREE BRANCH',
        'acc_opening_branch' => 'BANASREE BRANCH',
    ],
    [
        'bp_id'              => 'IN137SMFARUQPB',
        'account_number'     => '2129219009306',
        'SIF_Number'         => '1422985',

        'account_title'      => 'SYED MOHAMMAD FARUQ MUSTAHID',
        'customer_name'      => 'SYED MOHAMMAD FARUQ MUSTAHID',

        'contact_no_1'       => '8801711316289',
        'mobile_number'      => '8801711316289',

        'email_1'            => 'syedmohammadfaruk.mustahid@aramco.com',
        'email_address'      => 'syedmohammadfaruk.mustahid@aramco.com',

        'branch_name'        => 'BOGURA BRANCH',
        'acc_opening_branch' => 'BOGURA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SMGOLAMPB',
        'account_number'     => '2198213016162',
        'SIF_Number'         => '2335649',

        'account_title'      => 'S. M. GOLAM MORTUZA',
        'customer_name'      => 'S. M. GOLAM MORTUZA',

        'contact_no_1'       => '8801712004921',
        'mobile_number'      => '8801712004921',

        'email_1'            => 'jahanara271066@gmail.com',
        'email_address'      => 'jahanara271066@gmail.com',

        'branch_name'        => 'BASHABO BRANCH',
        'acc_opening_branch' => 'BASHABO BRANCH',
    ],
    [
        'bp_id'              => 'IN137SMITPRIME',
        'account_number'     => '2121218016037',
        'SIF_Number'         => '2261856',

        'account_title'      => 'SOUMEN MITRA',
        'customer_name'      => 'SOUMEN MITRA',

        'contact_no_1'       => '8801962424165',
        'mobile_number'      => '8801962424165',

        'email_1'            => 'soumenmitra44@gmail.com',
        'email_address'      => 'soumenmitra44@gmail.com',

        'branch_name'        => 'JUBILEE ROAD BRANCH',
        'acc_opening_branch' => 'JUBILEE ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SMMOKAMPB',
        'account_number'     => '2111217012179',
        'SIF_Number'         => '1330880',

        'account_title'      => 'S. M. MOKAMMEL HAQUE',
        'customer_name'      => 'S. M. MOKAMMEL HAQUE',

        'contact_no_1'       => '8801718597307',
        'mobile_number'      => '8801718597307',

        'email_1'            => 'smmh08@gmail.com',
        'email_address'      => 'smmh08@gmail.com',

        'branch_name'        => 'BARISAL BRANCH',
        'acc_opening_branch' => 'BARISAL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SMMONIRZN',
        'account_number'     => '2118211030197',
        'SIF_Number'         => '2337187',

        'account_title'      => 'S M MONIRUZZAMAN',
        'customer_name'      => 'S M MONIRUZZAMAN',

        'contact_no_1'       => '8801730098879',
        'mobile_number'      => '8801730098879',

        'email_1'            => 'sm.moniruzzamanbb@gmail.com',
        'email_address'      => 'sm.moniruzzamanbb@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SMSAJJDPB',
        'account_number'     => '2135213029476',
        'SIF_Number'         => '2202996',

        'account_title'      => 'S.M. SAJJADUR ALAM RASEL',
        'customer_name'      => 'S.M. SAJJADUR ALAM RASEL',

        'contact_no_1'       => '8801716163041',
        'mobile_number'      => '8801716163041',

        'email_1'            => 'rasarder2@gmail.com',
        'email_address'      => 'rasarder2@gmail.com',

        'branch_name'        => 'PRAGATI SARANI BRANCH',
        'acc_opening_branch' => 'PRAGATI SARANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SMSALA',
        'account_number'     => '2121216003755',
        'SIF_Number'         => '1519958',

        'account_title'      => 'S M SALAUDDIN',
        'customer_name'      => 'S M SALAUDDIN',

        'contact_no_1'       => '8801819635118',
        'mobile_number'      => '8801819635118',

        'email_1'            => 'sain635118@gmail.com',
        'email_address'      => 'sain635118@gmail.com',

        'branch_name'        => 'JUBILEE ROAD BRANCH',
        'acc_opening_branch' => 'JUBILEE ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SMUQSIT',
        'account_number'     => '2118211032952',
        'SIF_Number'         => '2426411',

        'account_title'      => 'SYED MUQSIT RAZEE',
        'customer_name'      => 'SYED MUQSIT RAZEE',

        'contact_no_1'       => '8801708146356',
        'mobile_number'      => '8801708146356',

        'email_1'            => 'syed.m.razee@gmail.com',
        'email_address'      => 'syed.m.razee@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SMZAHIDRN',
        'account_number'     => '2104218022606',
        'SIF_Number'         => '54726',

        'account_title'      => 'SHAH MOHAMMAD ZAHIDUR RAHMAN',
        'customer_name'      => 'SHAH MOHAMMAD ZAHIDUR RAHMAN',

        'contact_no_1'       => '8801712520818',
        'mobile_number'      => '8801712520818',

        'email_1'            => 'sr013116@primebank.com.bd',
        'email_address'      => 'sr013116@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SNARGIPRM',
        'account_number'     => '2125217015689',
        'SIF_Number'         => '1348843',

        'account_title'      => 'SHAMIMA NARGIS',
        'customer_name'      => 'SHAMIMA NARGIS',

        'contact_no_1'       => '8801917300499',
        'mobile_number'      => '8801917300499',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SNGDHRPRM',
        'account_number'     => '2192219010561',
        'SIF_Number'         => '1943909',

        'account_title'      => 'SNIGDHA RANI AICH',
        'customer_name'      => 'SNIGDHA RANI AICH',

        'contact_no_1'       => '8801937051523',
        'mobile_number'      => '8801937051523',

        'email_1'            => 'snigdhadas795@gmail.com',
        'email_address'      => 'snigdhadas795@gmail.com',

        'branch_name'        => 'BANASREE BRANCH',
        'acc_opening_branch' => 'BANASREE BRANCH',
    ],
    [
        'bp_id'              => 'IN137SOHEBPB',
        'account_number'     => '2104211024500',
        'SIF_Number'         => '14905',

        'account_title'      => 'MD.SOHEB AHMED',
        'customer_name'      => 'MD.SOHEB AHMED',

        'contact_no_1'       => '8801720032321',
        'mobile_number'      => '8801720032321',

        'email_1'            => 'soheb707@yahoo.com',
        'email_address'      => 'soheb707@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SOHELRPRM',
        'account_number'     => '2138218009066',
        'SIF_Number'         => '438720',

        'account_title'      => 'SOHEL RANA',
        'customer_name'      => 'SOHEL RANA',

        'contact_no_1'       => '8801711904462',
        'mobile_number'      => '8801711904462',

        'email_1'            => 'sohel.atl@gmail.com',
        'email_address'      => 'sohel.atl@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137SONIAMPB',
        'account_number'     => '2132215032034',
        'SIF_Number'         => '2601332',

        'account_title'      => 'SONIA MAHMUD',
        'customer_name'      => 'SONIA MAHMUD',

        'contact_no_1'       => '8801755594113',
        'mobile_number'      => '8801755594113',

        'email_1'            => 'sonia.mahmud@yahoo.com',
        'email_address'      => 'sonia.mahmud@yahoo.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SONJITROY',
        'account_number'     => '2126211013028',
        'SIF_Number'         => '1445054',

        'account_title'      => 'SONJIT ROY',
        'customer_name'      => 'SONJIT ROY',

        'contact_no_1'       => '8801927067188',
        'mobile_number'      => '8801927067188',

        'email_1'            => 'sonjitroy.bb@gmail.com',
        'email_address'      => 'sonjitroy.bb@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137SONSINKUM',
        'account_number'     => '2104218025732',
        'SIF_Number'         => '172057',

        'account_title'      => 'SONJIB KUMER SINGHA',
        'customer_name'      => 'SONJIB KUMER SINGHA',

        'contact_no_1'       => '8801913328040',
        'mobile_number'      => '8801913328040',

        'email_1'            => 'kumer.sonjib@gmail.com',
        'email_address'      => 'kumer.sonjib@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SRABNIRPB',
        'account_number'     => '2126219013319',
        'SIF_Number'         => '2510018',

        'account_title'      => 'SRABONI RAHMAN',
        'customer_name'      => 'SRABONI RAHMAN',

        'contact_no_1'       => '8801673777542',
        'mobile_number'      => '8801673777542',

        'email_1'            => 'rahmansraboni09@gmail.com',
        'email_address'      => 'rahmansraboni09@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137SRAKIBPRM',
        'account_number'     => '2179219011640',
        'SIF_Number'         => '1919274',

        'account_title'      => 'SYED NUR-E-RAKIB',
        'customer_name'      => 'SYED NUR-E-RAKIB',

        'contact_no_1'       => '8801718237064',
        'mobile_number'      => '8801718237064',

        'email_1'            => 'rakib.jkkniu@gmail.com',
        'email_address'      => 'rakib.jkkniu@gmail.com',

        'branch_name'        => 'ISHWARDI BRANCH',
        'acc_opening_branch' => 'ISHWARDI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SRRIMIPRM',
        'account_number'     => '3133211000918',
        'SIF_Number'         => '437851',

        'account_title'      => 'MD SYDUR RAHMAN & RIMI CHOWDHURY',
        'customer_name'      => 'MD SYDUR RAHMAN & RIMI CHOWDHURY',

        'contact_no_1'       => '8801713196320',
        'mobile_number'      => '8801713196320',

        'email_1'            => 'rahman_sydur@hotmail.com',
        'email_address'      => 'rahman_sydur@hotmail.com',

        'branch_name'        => 'IBB MIRPUR BRANCH',
        'acc_opening_branch' => 'IBB MIRPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137SSABINA',
        'account_number'     => '2118215033014',
        'SIF_Number'         => '2428757',

        'account_title'      => 'SYEDA SABINA PARVEEN',
        'customer_name'      => 'SYEDA SABINA PARVEEN',

        'contact_no_1'       => '8801817212170',
        'mobile_number'      => '8801817212170',

        'email_1'            => 'ssparveen11@gmail.com',
        'email_address'      => 'ssparveen11@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SSAKPRIME',
        'account_number'     => '2219216000796',
        'SIF_Number'         => '1782039',

        'account_title'      => 'SYED SADAT ALMAS KABIR',
        'customer_name'      => 'SYED SADAT ALMAS KABIR',

        'contact_no_1'       => '8801711521964',
        'mobile_number'      => '8801711521964',

        'email_1'            => 'almas@metro.net.bd',
        'email_address'      => 'almas@metro.net.bd',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SSHAIDERC',
        'account_number'     => '2118215026570',
        'SIF_Number'         => '2171718',

        'account_title'      => 'SYED SAZZAD HAIDER CHOWDHURY',
        'customer_name'      => 'SYED SAZZAD HAIDER CHOWDHURY',

        'contact_no_1'       => '8801715024384',
        'mobile_number'      => '8801715024384',

        'email_1'            => 'sazzadc3070@gmail.com',
        'email_address'      => 'sazzadc3070@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137STANIAHPB',
        'account_number'     => '2104214039802',
        'SIF_Number'         => '786001',

        'account_title'      => 'SYEDA TANIA HOSSAIN RITI',
        'customer_name'      => 'SYEDA TANIA HOSSAIN RITI',

        'contact_no_1'       => '8801716418846',
        'mobile_number'      => '8801716418846',

        'email_1'            => 'hritizone@yahoo.com',
        'email_address'      => 'hritizone@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137STANVIRPB',
        'account_number'     => '2117214016607',
        'SIF_Number'         => '2228600',

        'account_title'      => 'S TANVIR AHMED RATUL',
        'customer_name'      => 'S TANVIR AHMED RATUL',

        'contact_no_1'       => '00447414050115',
        'mobile_number'      => '00447414050115',

        'email_1'            => 'editor@lastbench.org',
        'email_address'      => 'editor@lastbench.org',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUBHASDPB',
        'account_number'     => '2117217000178',
        'SIF_Number'         => '136874',

        'account_title'      => 'SUBHASISH DEY',
        'customer_name'      => 'SUBHASISH DEY',

        'contact_no_1'       => '8801989029466',
        'mobile_number'      => '8801989029466',

        'email_1'            => 'deysubhashish@rocketmail.com',
        'email_address'      => 'deysubhashish@rocketmail.com',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUDIPCHPB',
        'account_number'     => '2120212016042',
        'SIF_Number'         => '1887357',

        'account_title'      => 'SUDIP CHOWDHURY',
        'customer_name'      => 'SUDIP CHOWDHURY',

        'contact_no_1'       => '8801869532303',
        'mobile_number'      => '8801869532303',

        'email_1'            => 'sudipabc@gmail.com',
        'email_address'      => 'sudipabc@gmail.com',

        'branch_name'        => 'AGRABAD BRANCH',
        'acc_opening_branch' => 'AGRABAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUDIPKSPB',
        'account_number'     => '2118217034762',
        'SIF_Number'         => '2513686',

        'account_title'      => 'SUDIP KUMAR SAHA',
        'customer_name'      => 'SUDIP KUMAR SAHA',

        'contact_no_1'       => '8801884487492',
        'mobile_number'      => '8801884487492',

        'email_1'            => 'sudip1978saha@gmail.com',
        'email_address'      => 'sudip1978saha@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUDIPTAPB',
        'account_number'     => '3108214039343',
        'SIF_Number'         => '2442240',

        'account_title'      => 'SUDIPTA DAS DYUTI',
        'customer_name'      => 'SUDIPTA DAS DYUTI',

        'contact_no_1'       => '8801553139434',
        'mobile_number'      => '8801553139434',

        'email_1'            => 'sudipta.dyuti@primebank.com.bd',
        'email_address'      => 'sudipta.dyuti@primebank.com.bd',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUFIABEPB',
        'account_number'     => '2112213009459',
        'SIF_Number'         => '576099',

        'account_title'      => 'MOST. SUFIA BEGUM',
        'customer_name'      => 'MOST. SUFIA BEGUM',

        'contact_no_1'       => '8801712101233',
        'mobile_number'      => '8801712101233',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'RAJSHAHI BRANCH',
        'acc_opening_branch' => 'RAJSHAHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUFIABPRM',
        'account_number'     => '2113216016799',
        'SIF_Number'         => '2548523',

        'account_title'      => 'SUFIA BEGUM',
        'customer_name'      => 'SUFIA BEGUM',

        'contact_no_1'       => '8801796231835',
        'mobile_number'      => '8801796231835',

        'email_1'            => 'sufia.nbl@gmail.com',
        'email_address'      => 'sufia.nbl@gmail.com',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUFIAKTPB',
        'account_number'     => '2104216018333',
        'SIF_Number'         => '679787',

        'account_title'      => 'SUFIA AKTHER LIPE',
        'customer_name'      => 'SUFIA AKTHER LIPE',

        'contact_no_1'       => '8801913091966',
        'mobile_number'      => '8801913091966',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUHAILPB',
        'account_number'     => '2118211037986',
        'SIF_Number'         => '1230477',

        'account_title'      => 'SUHAIL AHMED',
        'customer_name'      => 'SUHAIL AHMED',

        'contact_no_1'       => '8801713464474',
        'mobile_number'      => '8801713464474',

        'email_1'            => 'mithu23@gmail.com',
        'email_address'      => 'mithu23@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUJAN',
        'account_number'     => '2118211033356',
        'SIF_Number'         => '2308374',

        'account_title'      => 'SUJAN KUMAR SAHA',
        'customer_name'      => 'SUJAN KUMAR SAHA',

        'contact_no_1'       => '8801707500555',
        'mobile_number'      => '8801707500555',

        'email_1'            => 'sujansaha.rrh@gmail.com',
        'email_address'      => 'sujansaha.rrh@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SULTANALA',
        'account_number'     => '2104214041345',
        'SIF_Number'         => '2469416',

        'account_title'      => 'SULTAN RIZBI ALAM',
        'customer_name'      => 'SULTAN RIZBI ALAM',

        'contact_no_1'       => '8801745577078',
        'mobile_number'      => '8801745577078',

        'email_1'            => 'rizbi.ict@gmail.com',
        'email_address'      => 'rizbi.ict@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SULTANAPB',
        'account_number'     => '2165219000656',
        'SIF_Number'         => '410007',

        'account_title'      => 'SALMA SULTANA',
        'customer_name'      => 'SALMA SULTANA',

        'contact_no_1'       => '8801713015397',
        'mobile_number'      => '8801713015397',

        'email_1'            => 'salma.sultana.limit@gmail.com',
        'email_address'      => 'salma.sultana.limit@gmail.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137SULTANPRM',
        'account_number'     => '2127213019907',
        'SIF_Number'         => '2143186',

        'account_title'      => 'SULTANA SHABNAM HASAN',
        'customer_name'      => 'SULTANA SHABNAM HASAN',

        'contact_no_1'       => '8801552336769',
        'mobile_number'      => '8801552336769',

        'email_1'            => 'sultanashabnam18@gmail.com',
        'email_address'      => 'sultanashabnam18@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUMAIYAPR',
        'account_number'     => '2104212032502',
        'SIF_Number'         => '2076334',

        'account_title'      => 'SUMAIYA TAHASIN ALI',
        'customer_name'      => 'SUMAIYA TAHASIN ALI',

        'contact_no_1'       => '8801950679199',
        'mobile_number'      => '8801950679199',

        'email_1'            => 'tahsinsumaiya2@gmail.com',
        'email_address'      => 'tahsinsumaiya2@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUMANSPB',
        'account_number'     => '2118211035419',
        'SIF_Number'         => '2551499',

        'account_title'      => 'SUMAN SAHA',
        'customer_name'      => 'SUMAN SAHA',

        'contact_no_1'       => '8801730701791',
        'mobile_number'      => '8801730701791',

        'email_1'            => 'sumu056@gmail.com',
        'email_address'      => 'sumu056@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUMONBARP',
        'account_number'     => '2135219045567',
        'SIF_Number'         => '2813081',

        'account_title'      => 'SUMON BARUA',
        'customer_name'      => 'SUMON BARUA',

        'contact_no_1'       => '8801819295971',
        'mobile_number'      => '8801819295971',

        'email_1'            => 'sumanbarua37@gmail.com',
        'email_address'      => 'sumanbarua37@gmail.com',

        'branch_name'        => 'PRAGATI SARANI BRANCH',
        'acc_opening_branch' => 'PRAGATI SARANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUMONCDPB',
        'account_number'     => '2126213013182',
        'SIF_Number'         => '2492759',

        'account_title'      => 'SUMON CHANDRA DAS',
        'customer_name'      => 'SUMON CHANDRA DAS',

        'contact_no_1'       => '8801612629256',
        'mobile_number'      => '8801612629256',

        'email_1'            => 'sumandas09@gmail.com',
        'email_address'      => 'sumandas09@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137SUMROPRIM',
        'account_number'     => '2118219031287',
        'SIF_Number'         => '1192676',

        'account_title'      => 'SUMIT ROY',
        'customer_name'      => 'SUMIT ROY',

        'contact_no_1'       => '8801912062727',
        'mobile_number'      => '8801912062727',

        'email_1'            => 'sumit392003.05@gmail.com',
        'email_address'      => 'sumit392003.05@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUNYBHPRM',
        'account_number'     => '2118213034709',
        'SIF_Number'         => '2511733',

        'account_title'      => 'SUNNY BHUIYA',
        'customer_name'      => 'SUNNY BHUIYA',

        'contact_no_1'       => '8801840174086',
        'mobile_number'      => '8801840174086',

        'email_1'            => 'bhuiyasunny@gmail.com',
        'email_address'      => 'bhuiyasunny@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SURMOHPRM',
        'account_number'     => '2198215012524',
        'SIF_Number'         => '2281066',

        'account_title'      => 'SURAIYA MOHSIN',
        'customer_name'      => 'SURAIYA MOHSIN',

        'contact_no_1'       => '8801673986208',
        'mobile_number'      => '8801673986208',

        'email_1'            => 'mushfiq.043@gmail.com',
        'email_address'      => 'mushfiq.043@gmail.com',

        'branch_name'        => 'BASHABO BRANCH',
        'acc_opening_branch' => 'BASHABO BRANCH',
    ],
    [
        'bp_id'              => 'IN137SUSHANTPB',
        'account_number'     => '2118217016892',
        'SIF_Number'         => '76339',

        'account_title'      => 'SUSHANTA SARKER',
        'customer_name'      => 'SUSHANTA SARKER',

        'contact_no_1'       => '8801745758414',
        'mobile_number'      => '8801745758414',

        'email_1'            => 'sushantasarker78@gmail.com',
        'email_address'      => 'sushantasarker78@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SYEDABDUL',
        'account_number'     => '2127212000007',
        'SIF_Number'         => '291703',

        'account_title'      => 'SYED ABDULLAH JABIR MOSHARRAF',
        'customer_name'      => 'SYED ABDULLAH JABIR MOSHARRAF',

        'contact_no_1'       => '8801817522126',
        'mobile_number'      => '8801817522126',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137SYEDAMOU',
        'account_number'     => '2132214028926',
        'SIF_Number'         => '2489207',

        'account_title'      => 'SYEDA MOUMITA HOQUE',
        'customer_name'      => 'SYEDA MOUMITA HOQUE',

        'contact_no_1'       => '8801795353842',
        'mobile_number'      => '8801795353842',

        'email_1'            => 'moumita.hoq07@gmail.com',
        'email_address'      => 'moumita.hoq07@gmail.com',

        'branch_name'        => 'BANANI BRANCH',
        'acc_opening_branch' => 'BANANI BRANCH',
    ],
    [
        'bp_id'              => 'IN137SYEDASAPB',
        'account_number'     => '2118217041411',
        'SIF_Number'         => '2649042',

        'account_title'      => 'SYEDA SANJIDA PARVEEN',
        'customer_name'      => 'SYEDA SANJIDA PARVEEN',

        'contact_no_1'       => '8801930504090',
        'mobile_number'      => '8801930504090',

        'email_1'            => 'sanjidaparveen092@gmail.com',
        'email_address'      => 'sanjidaparveen092@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137SYEDBELPB',
        'account_number'     => '2104219041647',
        'SIF_Number'         => '2654947',

        'account_title'      => 'SYED BELLAL HOSSAIN',
        'customer_name'      => 'SYED BELLAL HOSSAIN',

        'contact_no_1'       => '8801915355013',
        'mobile_number'      => '8801915355013',

        'email_1'            => 'bellal1975@yahoo.com',
        'email_address'      => 'bellal1975@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137SYEDMOSH',
        'account_number'     => '2127212010253',
        'SIF_Number'         => '292877',

        'account_title'      => 'SYED MOSHARRAF HOSSAIN',
        'customer_name'      => 'SYED MOSHARRAF HOSSAIN',

        'contact_no_1'       => '8801817522126',
        'mobile_number'      => '8801817522126',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137TABIENKA',
        'account_number'     => '2159213016334',
        'SIF_Number'         => '2493452',

        'account_title'      => 'TABIEN KAMAL',
        'customer_name'      => 'TABIEN KAMAL',

        'contact_no_1'       => '8801711506009',
        'mobile_number'      => '8801711506009',

        'email_1'            => 'tabien.kamal@dhl.com',
        'email_address'      => 'tabien.kamal@dhl.com',

        'branch_name'        => 'BIJOY NAGAR BRANCH',
        'acc_opening_branch' => 'BIJOY NAGAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAHAPRIME',
        'account_number'     => '2104216003400',
        'SIF_Number'         => '1924',

        'account_title'      => 'TAHMINA AKTER',
        'customer_name'      => 'TAHMINA AKTER',

        'contact_no_1'       => '8801713277707',
        'mobile_number'      => '8801713277707',

        'email_1'            => 'ta090101@primebank.com.bd',
        'email_address'      => 'ta090101@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAHERAKHA',
        'account_number'     => '2121217003291',
        'SIF_Number'         => '885656',

        'account_title'      => 'TAHERA KHANAM',
        'customer_name'      => 'TAHERA KHANAM',

        'contact_no_1'       => '8801731081540',
        'mobile_number'      => '8801731081540',

        'email_1'            => 'solaimanmohammad@yahoo.com',
        'email_address'      => 'solaimanmohammad@yahoo.com',

        'branch_name'        => 'JUBILEE ROAD BRANCH',
        'acc_opening_branch' => 'JUBILEE ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAHJESPBK',
        'account_number'     => '2118211010348',
        'SIF_Number'         => '793722',

        'account_title'      => 'TAHERA JESMINE',
        'customer_name'      => 'TAHERA JESMINE',

        'contact_no_1'       => '8801732997691',
        'mobile_number'      => '8801732997691',

        'email_1'            => 'j.tahera@hotmail.com',
        'email_address'      => 'j.tahera@hotmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAHSHEPRM',
        'account_number'     => '2104214038754',
        'SIF_Number'         => '2457474',

        'account_title'      => 'TAHAMENA SHEMA',
        'customer_name'      => 'TAHAMENA SHEMA',

        'contact_no_1'       => '8801818042779',
        'mobile_number'      => '8801818042779',

        'email_1'            => 'shematahamena@gmail.com',
        'email_address'      => 'shematahamena@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAHSINUR',
        'account_number'     => '2126211009414',
        'SIF_Number'         => '1918719',

        'account_title'      => 'TAHSIN UR RAHMAN',
        'customer_name'      => 'TAHSIN UR RAHMAN',

        'contact_no_1'       => '8801715258363',
        'mobile_number'      => '8801715258363',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137TAHSTARPB',
        'account_number'     => '2138213017999',
        'SIF_Number'         => '2188877',

        'account_title'      => 'TAHSIN TAREQ AHMAD',
        'customer_name'      => 'TAHSIN TAREQ AHMAD',

        'contact_no_1'       => '8801711507833',
        'mobile_number'      => '8801711507833',

        'email_1'            => 'iamspandan@gmail.com',
        'email_address'      => 'iamspandan@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAKIYANPB',
        'account_number'     => '2104216014829',
        'SIF_Number'         => '1613524',

        'account_title'      => 'TAKIYAN CHOWDHURY',
        'customer_name'      => 'TAKIYAN CHOWDHURY',

        'contact_no_1'       => '8801766684116',
        'mobile_number'      => '8801766684116',

        'email_1'            => 'takiyan_chow@hotmail.com',
        'email_address'      => 'takiyan_chow@hotmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAMALTAHA',
        'account_number'     => '2104217041367',
        'SIF_Number'         => '2631816',

        'account_title'      => 'MD. TAMAL TAHASIN KHAN',
        'customer_name'      => 'MD. TAMAL TAHASIN KHAN',

        'contact_no_1'       => '8801717167494',
        'mobile_number'      => '8801717167494',

        'email_1'            => 't.tahasin@gmail.com',
        'email_address'      => 't.tahasin@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAMINULPB',
        'account_number'     => '2219211010550',
        'SIF_Number'         => '2489784',

        'account_title'      => 'GAZI MD.TAMINUL ISLAM',
        'customer_name'      => 'GAZI MD.TAMINUL ISLAM',

        'contact_no_1'       => '8801711083948',
        'mobile_number'      => '8801711083948',

        'email_1'            => 'tamin@live.com',
        'email_address'      => 'tamin@live.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAMJPRIME',
        'account_number'     => '2114219009899',
        'SIF_Number'         => '261794',

        'account_title'      => 'SM Tamjid',
        'customer_name'      => 'SM Tamjid',

        'contact_no_1'       => '8801746208404',
        'mobile_number'      => '8801746208404',

        'email_1'            => 'tamjidsm@gmail.com',
        'email_address'      => 'tamjidsm@gmail.com',

        'branch_name'        => 'ELEPHANT ROAD BRANCH',
        'acc_opening_branch' => 'ELEPHANT ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137TANBIRAPR',
        'account_number'     => '2141215011300',
        'SIF_Number'         => '2043352',

        'account_title'      => 'TANBIRA HOSSAIN',
        'customer_name'      => 'TANBIRA HOSSAIN',

        'contact_no_1'       => '8801805420781',
        'mobile_number'      => '8801805420781',

        'email_1'            => 'tanbira.hossain@gmail.com',
        'email_address'      => 'tanbira.hossain@gmail.com',

        'branch_name'        => 'PANTHAPATH BRANCH',
        'acc_opening_branch' => 'PANTHAPATH BRANCH',
    ],
    [
        'bp_id'              => 'IN137TANIMRNPB',
        'account_number'     => '2206215000107',
        'SIF_Number'         => '1199434',

        'account_title'      => 'TANIM RAIHAN',
        'customer_name'      => 'TANIM RAIHAN',

        'contact_no_1'       => '8801827101423',
        'mobile_number'      => '8801827101423',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'KHULSHI BRANCH',
        'acc_opening_branch' => 'KHULSHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137TANOOR',
        'account_number'     => '3134211013141',
        'SIF_Number'         => '937012',

        'account_title'      => 'TANIA NOOR',
        'customer_name'      => 'TANIA NOOR',

        'contact_no_1'       => '8801724422413',
        'mobile_number'      => '8801724422413',

        'email_1'            => 'taniatima@gmail.com',
        'email_address'      => 'taniatima@gmail.com',

        'branch_name'        => 'IBB O.R. NIZAM ROAD BR',
        'acc_opening_branch' => 'IBB O.R. NIZAM ROAD BR',
    ],
    [
        'bp_id'              => 'IN137TANRAHAPB',
        'account_number'     => '2127214025416',
        'SIF_Number'         => '2561128',

        'account_title'      => 'MD. TANVIR RAHAMAN',
        'customer_name'      => 'MD. TANVIR RAHAMAN',

        'contact_no_1'       => '8801750385587',
        'mobile_number'      => '8801750385587',

        'email_1'            => 'tanvir.rahman6455@gmail.com',
        'email_address'      => 'tanvir.rahman6455@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137TANVAHMPB',
        'account_number'     => '2104213014905',
        'SIF_Number'         => '1660211',

        'account_title'      => 'TANVIR AHMED MAHBOOB',
        'customer_name'      => 'TANVIR AHMED MAHBOOB',

        'contact_no_1'       => '8801713279700',
        'mobile_number'      => '8801713279700',

        'email_1'            => 'tanvirmahboob@primebank.com.bd',
        'email_address'      => 'tanvirmahboob@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAPANCMPB',
        'account_number'     => '2117212018647',
        'SIF_Number'         => '2442025',

        'account_title'      => 'TAPAN CHANDRA MAZUMDER',
        'customer_name'      => 'TAPAN CHANDRA MAZUMDER',

        'contact_no_1'       => '8801711222691',
        'mobile_number'      => '8801711222691',

        'email_1'            => 'tapanmazumder0402@gmail.com',
        'email_address'      => 'tapanmazumder0402@gmail.com',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAPANKBPB',
        'account_number'     => '2104219018207',
        'SIF_Number'         => '751762',

        'account_title'      => 'TAPAN KUMAR BANIK',
        'customer_name'      => 'TAPAN KUMAR BANIK',

        'contact_no_1'       => '8801730329061',
        'mobile_number'      => '8801730329061',

        'email_1'            => 'tb083002@primebank.com.bd',
        'email_address'      => 'tb083002@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137TARANALIM',
        'account_number'     => '2127216030141',
        'SIF_Number'         => '2776503',

        'account_title'      => 'TARANNA ANSAR AND MOHAMMAD FERDOUS ALIM',
        'customer_name'      => 'TARANNA ANSAR AND MOHAMMAD FERDOUS ALIM',

        'contact_no_1'       => '8801910086339',
        'mobile_number'      => '8801910086339',

        'email_1'            => 'taranna.ferdous@gmail.com',
        'email_address'      => 'taranna.ferdous@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAREMPB',
        'account_number'     => '2110212008363',
        'SIF_Number'         => '1539204',

        'account_title'      => 'TAREM AHMED',
        'customer_name'      => 'TAREM AHMED',

        'contact_no_1'       => '8801732822599',
        'mobile_number'      => '8801732822599',

        'email_1'            => 'taremahmed@yahoo.com',
        'email_address'      => 'taremahmed@yahoo.com',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAREQHASS',
        'account_number'     => '2151219020379',
        'SIF_Number'         => '2070886',

        'account_title'      => 'TAREQ HASSAN MAHMUD',
        'customer_name'      => 'TAREQ HASSAN MAHMUD',

        'contact_no_1'       => '8801722073174',
        'mobile_number'      => '8801722073174',

        'email_1'            => 'tareq.asif.768@gmail.com',
        'email_address'      => 'tareq.asif.768@gmail.com',

        'branch_name'        => 'JOYDEVPUR BRANCH',
        'acc_opening_branch' => 'JOYDEVPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAREQPRIM',
        'account_number'     => '2170211019260',
        'SIF_Number'         => '2129632',

        'account_title'      => 'K.M. TAREQ RAHMAN',
        'customer_name'      => 'K.M. TAREQ RAHMAN',

        'contact_no_1'       => '8801752009803',
        'mobile_number'      => '8801752009803',

        'email_1'            => 'tareq.icma@gmail.com',
        'email_address'      => 'tareq.icma@gmail.com',

        'branch_name'        => 'GARIB-E-NEWAZ AVE. BR',
        'acc_opening_branch' => 'GARIB-E-NEWAZ AVE. BR',
    ],
    [
        'bp_id'              => 'IN137TARIQULPB',
        'account_number'     => '2138219008273',
        'SIF_Number'         => '1812993',

        'account_title'      => 'MD. TARIQUL ISLAM',
        'customer_name'      => 'MD. TARIQUL ISLAM',

        'contact_no_1'       => '8801715026457',
        'mobile_number'      => '8801715026457',

        'email_1'            => 'tarik.forest@gmail.com',
        'email_address'      => 'tarik.forest@gmail.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137TASHREEFP',
        'account_number'     => '2118217037500',
        'SIF_Number'         => '2631069',

        'account_title'      => 'MUHAMMAD TASHREEF REZA',
        'customer_name'      => 'MUHAMMAD TASHREEF REZA',

        'contact_no_1'       => '8801713067817',
        'mobile_number'      => '8801713067817',

        'email_1'            => 'tashreefreza@gmail.com',
        'email_address'      => 'tashreefreza@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137TASIDQPRM',
        'account_number'     => '2113214006552',
        'SIF_Number'         => '1385522',

        'account_title'      => 'TANVIR AHMED SIDDIQUI',
        'customer_name'      => 'TANVIR AHMED SIDDIQUI',

        'contact_no_1'       => '8801911027910',
        'mobile_number'      => '8801911027910',

        'email_1'            => 'tasiddiqui@gmail.com',
        'email_address'      => 'tasiddiqui@gmail.com',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137TASLIMAPB',
        'account_number'     => '2138211004528',
        'SIF_Number'         => '680764',

        'account_title'      => 'DR. TASLIMA SULTANA',
        'customer_name'      => 'DR. TASLIMA SULTANA',

        'contact_no_1'       => '8801732792773',
        'mobile_number'      => '8801732792773',

        'email_1'            => 'drrimi29@yahoo.com',
        'email_address'      => 'drrimi29@yahoo.com',

        'branch_name'        => 'ASADGATE BRANCH',
        'acc_opening_branch' => 'ASADGATE BRANCH',
    ],
    [
        'bp_id'              => 'IN137TASLIPRM',
        'account_number'     => '2158214017167',
        'SIF_Number'         => '2482157',

        'account_title'      => 'TASLIMA AKTER',
        'customer_name'      => 'TASLIMA AKTER',

        'contact_no_1'       => '8801680226080',
        'mobile_number'      => '8801680226080',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'COMILLA BRANCH',
        'acc_opening_branch' => 'COMILLA BRANCH',
    ],
    [
        'bp_id'              => 'IN137TASMIA',
        'account_number'     => '2127219017822',
        'SIF_Number'         => '293965',

        'account_title'      => 'TASMIA TAHMID',
        'customer_name'      => 'TASMIA TAHMID',

        'contact_no_1'       => '8801674665191',
        'mobile_number'      => '8801674665191',

        'email_1'            => 'ttmisti@gmail.com',
        'email_address'      => 'ttmisti@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137TASNUFPRM',
        'account_number'     => '2104217033075',
        'SIF_Number'         => '2106355',

        'account_title'      => 'TASNUVA FARUQ MISHU',
        'customer_name'      => 'TASNUVA FARUQ MISHU',

        'contact_no_1'       => '8801755594970',
        'mobile_number'      => '8801755594970',

        'email_1'            => 'tfmishu@gmail.com',
        'email_address'      => 'tfmishu@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137TASNUVAPB',
        'account_number'     => '2112214029720',
        'SIF_Number'         => '2642352',

        'account_title'      => 'TASNUVA PARVIN',
        'customer_name'      => 'TASNUVA PARVIN',

        'contact_no_1'       => '8801773750961',
        'mobile_number'      => '8801773750961',

        'email_1'            => 'tasnuvaparvin620@gmail.com',
        'email_address'      => 'tasnuvaparvin620@gmail.com',

        'branch_name'        => 'RAJSHAHI BRANCH',
        'acc_opening_branch' => 'RAJSHAHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAYEBSUPB',
        'account_number'     => '2152212033857',
        'SIF_Number'         => '2718579',

        'account_title'      => 'MOHAMMAD TAYEB SULAIMAN',
        'customer_name'      => 'MOHAMMAD TAYEB SULAIMAN',

        'contact_no_1'       => '8801711080312',
        'mobile_number'      => '8801711080312',

        'email_1'            => 'mdtayebsulaiman@gmail.com',
        'email_address'      => 'mdtayebsulaiman@gmail.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137TAZINKPRM',
        'account_number'     => '2219211003143',
        'SIF_Number'         => '1884405',

        'account_title'      => 'TAZIN KHAN',
        'customer_name'      => 'TAZIN KHAN',

        'contact_no_1'       => '8801770204349',
        'mobile_number'      => '8801770204349',

        'email_1'            => 'tazinkhan8@gmail.com',
        'email_address'      => 'tazinkhan8@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137TFMISHUPB',
        'account_number'     => '2104217033075',
        'SIF_Number'         => '2106355',

        'account_title'      => 'TASNUVA FARUQ MISHU',
        'customer_name'      => 'TASNUVA FARUQ MISHU',

        'contact_no_1'       => '8801755594970',
        'mobile_number'      => '8801755594970',

        'email_1'            => 'tfmishu@gmail.com',
        'email_address'      => 'tfmishu@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137TIBRIZEPB',
        'account_number'     => '2198214005958',
        'SIF_Number'         => '2144386',

        'account_title'      => 'KAZI MD. SHAMS TIBRIZE',
        'customer_name'      => 'KAZI MD. SHAMS TIBRIZE',

        'contact_no_1'       => '8801610013777',
        'mobile_number'      => '8801610013777',

        'email_1'            => 'tibrize@gmail.com',
        'email_address'      => 'tibrize@gmail.com',

        'branch_name'        => 'BASHABO BRANCH',
        'acc_opening_branch' => 'BASHABO BRANCH',
    ],
    [
        'bp_id'              => 'IN137TMRIYADPB',
        'account_number'     => '2205211013239',
        'SIF_Number'         => '1142829',

        'account_title'      => 'TANVEER MAHMOOD RIYADH',
        'customer_name'      => 'TANVEER MAHMOOD RIYADH',

        'contact_no_1'       => '8801817146278',
        'mobile_number'      => '8801817146278',

        'email_1'            => 'triyadh@hotmail.com',
        'email_address'      => 'triyadh@hotmail.com',

        'branch_name'        => 'IBRAHIMPUR BRANCH',
        'acc_opening_branch' => 'IBRAHIMPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137TOFAILPB',
        'account_number'     => '2104216012724',
        'SIF_Number'         => '24290',

        'account_title'      => 'TOFAIL AHMED',
        'customer_name'      => 'TOFAIL AHMED',

        'contact_no_1'       => '8801730326802',
        'mobile_number'      => '8801730326802',

        'email_1'            => 'ta030119@primebank.com.bd',
        'email_address'      => 'ta030119@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137TOMADAPRM',
        'account_number'     => '2125217016828',
        'SIF_Number'         => '1772067',

        'account_title'      => 'TILOTTOMA DAS',
        'customer_name'      => 'TILOTTOMA DAS',

        'contact_no_1'       => '8801962553990',
        'mobile_number'      => '8801962553990',

        'email_1'            => 'tomadasbd@gmail.com',
        'email_address'      => 'tomadasbd@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137TONIPRM',
        'account_number'     => '2118214041976',
        'SIF_Number'         => '2694433',

        'account_title'      => 'TONIMA HOSSAIN',
        'customer_name'      => 'TONIMA HOSSAIN',

        'contact_no_1'       => '8801713069877',
        'mobile_number'      => '8801713069877',

        'email_1'            => 'tonimahossain@yahoo.com',
        'email_address'      => 'tonimahossain@yahoo.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137TOUHIDAPB',
        'account_number'     => '2118212032308',
        'SIF_Number'         => '2407226',

        'account_title'      => 'TOUHIDA AKTER',
        'customer_name'      => 'TOUHIDA AKTER',

        'contact_no_1'       => '8801643299099',
        'mobile_number'      => '8801643299099',

        'email_1'            => 'touhidaakter36@gmail.com',
        'email_address'      => 'touhidaakter36@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137TRASHIDPB',
        'account_number'     => '2104218018091',
        'SIF_Number'         => '25638',

        'account_title'      => 'TANVEER RASHID',
        'customer_name'      => 'TANVEER RASHID',

        'contact_no_1'       => '8801817503555',
        'mobile_number'      => '8801817503555',

        'email_1'            => 'trashid@primebank.com.bd',
        'email_address'      => 'trashid@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137TUSHARPRM',
        'account_number'     => '2104212012686',
        'SIF_Number'         => '702265',

        'account_title'      => 'TUSHAR KANTI KAR',
        'customer_name'      => 'TUSHAR KANTI KAR',

        'contact_no_1'       => '8801716709611',
        'mobile_number'      => '8801716709611',

        'email_1'            => 'tkk_2006@yahoo.com',
        'email_address'      => 'tkk_2006@yahoo.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137UBAERHPRM',
        'account_number'     => '2104211039152',
        'SIF_Number'         => '2500429',

        'account_title'      => 'MD UBAER HOSSEN',
        'customer_name'      => 'MD UBAER HOSSEN',

        'contact_no_1'       => '8801777920370',
        'mobile_number'      => '8801777920370',

        'email_1'            => 'hzubayer050@gmail.com',
        'email_address'      => 'hzubayer050@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137UMMESLMPB',
        'account_number'     => '2152215018145',
        'SIF_Number'         => '360065',

        'account_title'      => 'UMME SALMA',
        'customer_name'      => 'UMME SALMA',

        'contact_no_1'       => '8801741465939',
        'mobile_number'      => '8801741465939',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137UMMETSFPB',
        'account_number'     => '2152215026429',
        'SIF_Number'         => '2303023',

        'account_title'      => 'UMME TASFIA RAHMAN',
        'customer_name'      => 'UMME TASFIA RAHMAN',

        'contact_no_1'       => '8801318053805',
        'mobile_number'      => '8801318053805',

        'email_1'            => 'tasfialamisha@gmail.com',
        'email_address'      => 'tasfialamisha@gmail.com',

        'branch_name'        => 'MIRPUR-1 BRANCH',
        'acc_opening_branch' => 'MIRPUR-1 BRANCH',
    ],
    [
        'bp_id'              => 'IN137UMZAKIAPB',
        'account_number'     => '2128218011809',
        'SIF_Number'         => '233778',

        'account_title'      => 'UMMEY ZAKIA',
        'customer_name'      => 'UMMEY ZAKIA',

        'contact_no_1'       => '8801819902152',
        'mobile_number'      => '8801819902152',

        'email_1'            => 'ummeyzakia1979@gmail.com',
        'email_address'      => 'ummeyzakia1979@gmail.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137UTDHWABPB',
        'account_number'     => '2111212013743',
        'SIF_Number'         => '811519',

        'account_title'      => 'UTDHWAB BAIRAGI',
        'customer_name'      => 'UTDHWAB BAIRAGI',

        'contact_no_1'       => '8801724369697',
        'mobile_number'      => '8801724369697',

        'email_1'            => 'UTDHWABBAIRAGI@GMAIL.COM',
        'email_address'      => 'UTDHWABBAIRAGI@GMAIL.COM',

        'branch_name'        => 'BARISAL BRANCH',
        'acc_opening_branch' => 'BARISAL BRANCH',
    ],
    [
        'bp_id'              => 'IN137UTPALKOME',
        'account_number'     => '2508212004719',
        'SIF_Number'         => '2007201',

        'account_title'      => 'UTPAL KOMER PRASADI',
        'customer_name'      => 'UTPAL KOMER PRASADI',

        'contact_no_1'       => '8801755538335',
        'mobile_number'      => '8801755538335',

        'email_1'            => 'utpal_chem@seelbd.com',
        'email_address'      => 'utpal_chem@seelbd.com',

        'branch_name'        => 'SONARGAON SME BR/SC',
        'acc_opening_branch' => 'SONARGAON SME BR/SC',
    ],
    [
        'bp_id'              => 'IN137WAHEDCPRM',
        'account_number'     => '2165211015744',
        'SIF_Number'         => '310028',

        'account_title'      => 'A K M WAHEDUDDIN CHOWDHURY',
        'customer_name'      => 'A K M WAHEDUDDIN CHOWDHURY',

        'contact_no_1'       => '8801715819268',
        'mobile_number'      => '8801715819268',

        'email_1'            => 'wahedwdb@gmail.com',
        'email_address'      => 'wahedwdb@gmail.com',

        'branch_name'        => 'BASHUNDHARA BRANCH',
        'acc_opening_branch' => 'BASHUNDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137WAHPPRIME',
        'account_number'     => '2104211026829',
        'SIF_Number'         => '1211720',

        'account_title'      => 'MS WAHIDA PRODHAN',
        'customer_name'      => 'MS WAHIDA PRODHAN',

        'contact_no_1'       => '8801714047354',
        'mobile_number'      => '8801714047354',

        'email_1'            => 'wahida.prodhan@primebank.com.bd',
        'email_address'      => 'wahida.prodhan@primebank.com.bd',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137WAHSIDQPB',
        'account_number'     => '2127119011314',
        'SIF_Number'         => '290450',

        'account_title'      => 'WAHIDUL HAQUE SIDDIQUI',
        'customer_name'      => 'WAHIDUL HAQUE SIDDIQUI',

        'contact_no_1'       => '8801711564888',
        'mobile_number'      => '8801711564888',

        'email_1'            => 'norban@bdcom.com',
        'email_address'      => 'norban@bdcom.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137WAZEDPRIM',
        'account_number'     => '3133219021550',
        'SIF_Number'         => '2484168',

        'account_title'      => 'MOHAMMAD WAZEDUR RAHMAN',
        'customer_name'      => 'MOHAMMAD WAZEDUR RAHMAN',

        'contact_no_1'       => '8801971774710',
        'mobile_number'      => '8801971774710',

        'email_1'            => 'akshorrahman71@gmail.com',
        'email_address'      => 'akshorrahman71@gmail.com',

        'branch_name'        => 'IBB MIRPUR BRANCH',
        'acc_opening_branch' => 'IBB MIRPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137YAHSANPB',
        'account_number'     => '2219212005146',
        'SIF_Number'         => '2092588',

        'account_title'      => 'MD YAHSAN HASIN MAJUMDER',
        'customer_name'      => 'MD YAHSAN HASIN MAJUMDER',

        'contact_no_1'       => '8801712175076',
        'mobile_number'      => '8801712175076',

        'email_1'            => 'yahsan.hasin@hotmail.com',
        'email_address'      => 'yahsan.hasin@hotmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137YASMEENPB',
        'account_number'     => '2128214015846',
        'SIF_Number'         => '2013942',

        'account_title'      => 'YASMEEN ALAM',
        'customer_name'      => 'YASMEEN ALAM',

        'contact_no_1'       => '8801713005214',
        'mobile_number'      => '8801713005214',

        'email_1'            => 'Yalam@yahoo.com',
        'email_address'      => 'Yalam@yahoo.com',

        'branch_name'        => 'NEW ESKATON BRANCH',
        'acc_opening_branch' => 'NEW ESKATON BRANCH',
    ],
    [
        'bp_id'              => 'IN137YASMEHOPB',
        'account_number'     => '2127218004003',
        'SIF_Number'         => '290327',

        'account_title'      => 'YASMEEN HOSSAIN',
        'customer_name'      => 'YASMEEN HOSSAIN',

        'contact_no_1'       => '8801711621553',
        'mobile_number'      => '8801711621553',

        'email_1'            => 'hossainyasmeen@yahoo.com',
        'email_address'      => 'hossainyasmeen@yahoo.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137YASMINPRM',
        'account_number'     => '2148211010730',
        'SIF_Number'         => '2482550',

        'account_title'      => 'MST YASMIN SULTANA',
        'customer_name'      => 'MST YASMIN SULTANA',

        'contact_no_1'       => '8801914720494',
        'mobile_number'      => '8801914720494',

        'email_1'            => 'sahid.rahman221@gmail.com',
        'email_address'      => 'sahid.rahman221@gmail.com',

        'branch_name'        => 'KAKRAIL BRANCH',
        'acc_opening_branch' => 'KAKRAIL BRANCH',
    ],
    [
        'bp_id'              => 'IN137YASMINSPB',
        'account_number'     => '2104213037559',
        'SIF_Number'         => '2383894',

        'account_title'      => 'YASMIN SULTANA',
        'customer_name'      => 'YASMIN SULTANA',

        'contact_no_1'       => '8801920911144',
        'mobile_number'      => '8801920911144',

        'email_1'            => 'ys6187433@gmail.com',
        'email_address'      => 'ys6187433@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137YOUSUFAPB',
        'account_number'     => '2127218013046',
        'SIF_Number'         => '296700',

        'account_title'      => 'MD. YOUSUF ALI',
        'customer_name'      => 'MD. YOUSUF ALI',

        'contact_no_1'       => '8801841048832',
        'mobile_number'      => '8801841048832',

        'email_1'            => 'trtyousuf@gmail.com',
        'email_address'      => 'trtyousuf@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137YOUSUFPB',
        'account_number'     => '2162216006204',
        'SIF_Number'         => '1639340',

        'account_title'      => 'MD. YOUSUF REZA',
        'customer_name'      => 'MD. YOUSUF REZA',

        'contact_no_1'       => '8801911405964',
        'mobile_number'      => '8801911405964',

        'email_1'            => 'yrezabd@gmail.com',
        'email_address'      => 'yrezabd@gmail.com',

        'branch_name'        => 'RING ROAD BRANCH',
        'acc_opening_branch' => 'RING ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137YUSUFPRBM',
        'account_number'     => '2125211027854',
        'SIF_Number'         => '2342253',

        'account_title'      => 'MD YUSUF FARUQ',
        'customer_name'      => 'MD YUSUF FARUQ',

        'contact_no_1'       => '8801952375002',
        'mobile_number'      => '8801952375002',

        'email_1'            => 'yfaruq114@gmail.com',
        'email_address'      => 'yfaruq114@gmail.com',

        'branch_name'        => 'UTTARA BRANCH',
        'acc_opening_branch' => 'UTTARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZAHIDULPB',
        'account_number'     => '2118212031938',
        'SIF_Number'         => '2396252',

        'account_title'      => 'MOHAMMAD ZAHIDUL ISLAM',
        'customer_name'      => 'MOHAMMAD ZAHIDUL ISLAM',

        'contact_no_1'       => '8801711904790',
        'mobile_number'      => '8801711904790',

        'email_1'            => 'm.zahidul@primebank.com.bd',
        'email_address'      => 'm.zahidul@primebank.com.bd',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZAIMANPB',
        'account_number'     => '2127217017220',
        'SIF_Number'         => '2030094',

        'account_title'      => 'ZAIMA NUZHAT',
        'customer_name'      => 'ZAIMA NUZHAT',

        'contact_no_1'       => '8801751692385',
        'mobile_number'      => '8801751692385',

        'email_1'            => 'zaima.nuzhat@gmail.com',
        'email_address'      => 'zaima.nuzhat@gmail.com',

        'branch_name'        => 'SAT MASJID ROAD BRANCH',
        'acc_opening_branch' => 'SAT MASJID ROAD BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZAKAHADPB',
        'account_number'     => '2104214039943',
        'SIF_Number'         => '2546883',

        'account_title'      => 'ZAKARIYA AHAD',
        'customer_name'      => 'ZAKARIYA AHAD',

        'contact_no_1'       => '8801715401999',
        'mobile_number'      => '8801715401999',

        'email_1'            => 'zakstar1@gmail.com',
        'email_address'      => 'zakstar1@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZAKARIAH',
        'account_number'     => '2104211035449',
        'SIF_Number'         => '2264257',

        'account_title'      => 'MD. ZAKARIA HOSSAIN',
        'customer_name'      => 'MD. ZAKARIA HOSSAIN',

        'contact_no_1'       => '8801731637991',
        'mobile_number'      => '8801731637991',

        'email_1'            => 'zakaria.bb14@gmail.com',
        'email_address'      => 'zakaria.bb14@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZAKARIAPB',
        'account_number'     => '2219216007619',
        'SIF_Number'         => '1769581',

        'account_title'      => 'HOSSAIN MOHAMMAD ZAKARIA',
        'customer_name'      => 'HOSSAIN MOHAMMAD ZAKARIA',

        'contact_no_1'       => '8801755596195',
        'mobile_number'      => '8801755596195',

        'email_1'            => 'zakaria.bbl@gmail.com',
        'email_address'      => 'zakaria.bbl@gmail.com',

        'branch_name'        => 'BARIDHARA BRANCH',
        'acc_opening_branch' => 'BARIDHARA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZAKIQULPB',
        'account_number'     => '2126218014888',
        'SIF_Number'         => '2464667',

        'account_title'      => 'MUHAMMAD ZAKIQUL ISLAM',
        'customer_name'      => 'MUHAMMAD ZAKIQUL ISLAM',

        'contact_no_1'       => '8801712853761',
        'mobile_number'      => '8801712853761',

        'email_1'            => 'zakiqul3@gmail.com',
        'email_address'      => 'zakiqul3@gmail.com',

        'branch_name'        => 'FOREIGN EXCHANGE BR',
        'acc_opening_branch' => 'FOREIGN EXCHANGE BR',
    ],
    [
        'bp_id'              => 'IN137ZAKIRHOPB',
        'account_number'     => '2104211019245',
        'SIF_Number'         => '109',

        'account_title'      => 'MD. ZAKIR HOSSAIN',
        'customer_name'      => 'MD. ZAKIR HOSSAIN',

        'contact_no_1'       => '8801819908267',
        'mobile_number'      => '8801819908267',

        'email_1'            => 'uzhossain@gmail.com',
        'email_address'      => 'uzhossain@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZANNATPB',
        'account_number'     => '2117215011235',
        'SIF_Number'         => '136866',

        'account_title'      => 'SYEDA ZANNAT ARA',
        'customer_name'      => 'SYEDA ZANNAT ARA',

        'contact_no_1'       => '8801751366716',
        'mobile_number'      => '8801751366716',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZEENATJHN',
        'account_number'     => '2205215005552',
        'SIF_Number'         => '1284450',

        'account_title'      => 'ZEENAT JAHAN',
        'customer_name'      => 'ZEENAT JAHAN',

        'contact_no_1'       => '8801755086426',
        'mobile_number'      => '8801755086426',

        'email_1'            => 'drzeenatjahan7@gmail.com',
        'email_address'      => 'drzeenatjahan7@gmail.com',

        'branch_name'        => 'IBRAHIMPUR BRANCH',
        'acc_opening_branch' => 'IBRAHIMPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZIAPRIME',
        'account_number'     => '2118211026541',
        'SIF_Number'         => '2169210',

        'account_title'      => 'MD. ZIAUR RAHMAN',
        'customer_name'      => 'MD. ZIAUR RAHMAN',

        'contact_no_1'       => '8801730001146',
        'mobile_number'      => '8801730001146',

        'email_1'            => 'zia.sit@gmail.com',
        'email_address'      => 'zia.sit@gmail.com',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZIAUDCHWP',
        'account_number'     => '2113212018526',
        'SIF_Number'         => '2766091',

        'account_title'      => 'MD. ZIA UDDIN CHOWDHURY',
        'customer_name'      => 'MD. ZIA UDDIN CHOWDHURY',

        'contact_no_1'       => '8801711930226',
        'mobile_number'      => '8801711930226',

        'email_1'            => 'masud.zia26@gmail.com',
        'email_address'      => 'masud.zia26@gmail.com',

        'branch_name'        => 'KAWRAN BAZAR BRANCH',
        'acc_opening_branch' => 'KAWRAN BAZAR BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZIAULAMPB',
        'account_number'     => '2161211010302',
        'SIF_Number'         => '364634',

        'account_title'      => 'MD ZIAUL AMIN',
        'customer_name'      => 'MD ZIAUL AMIN',

        'contact_no_1'       => '8801711771581',
        'mobile_number'      => '8801711771581',

        'email_1'            => 'shozib_rnp@yahoo.com',
        'email_address'      => 'shozib_rnp@yahoo.com',

        'branch_name'        => 'RANGPUR BRANCH',
        'acc_opening_branch' => 'RANGPUR BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZIAULHAPB',
        'account_number'     => '2117213007169',
        'SIF_Number'         => '894489',

        'account_title'      => 'ZIAUL HASAN CHOWDHURY',
        'customer_name'      => 'ZIAUL HASAN CHOWDHURY',

        'contact_no_1'       => '8801935805378',
        'mobile_number'      => '8801935805378',

        'email_1'            => 'prcinvestment@yahoo.com',
        'email_address'      => 'prcinvestment@yahoo.com',

        'branch_name'        => 'MOUCHAK BRANCH',
        'acc_opening_branch' => 'MOUCHAK BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZINNATUN',
        'account_number'     => '2110211012820',
        'SIF_Number'         => '275587',

        'account_title'      => 'ZINNATUN NAHAR',
        'customer_name'      => 'ZINNATUN NAHAR',

        'contact_no_1'       => '8801711568145',
        'mobile_number'      => '8801711568145',

        'email_1'            => 'nahar@meditexbd.com',
        'email_address'      => 'nahar@meditexbd.com',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZLRNIGTPB',
        'account_number'     => '2112213015233',
        'SIF_Number'         => '1061505',

        'account_title'      => 'ZILLUR RAHMAN &AMP; NIGHAT NAHARIN RAHMAN',
        'customer_name'      => 'ZILLUR RAHMAN &AMP; NIGHAT NAHARIN RAHMAN',

        'contact_no_1'       => '8801713764286',
        'mobile_number'      => '8801713764286',

        'email_1'            => 'zillur67@gmail.com',
        'email_address'      => 'zillur67@gmail.com',

        'branch_name'        => 'RAJSHAHI BRANCH',
        'acc_opening_branch' => 'RAJSHAHI BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZOHURHPRM',
        'account_number'     => '2104215039199',
        'SIF_Number'         => '2503549',

        'account_title'      => 'MD. ZOHURUL HAQUE',
        'customer_name'      => 'MD. ZOHURUL HAQUE',

        'contact_no_1'       => '8801826926957',
        'mobile_number'      => '8801826926957',

        'email_1'            => 'zohurulhaque.adust@gmail.com',
        'email_address'      => 'zohurulhaque.adust@gmail.com',

        'branch_name'        => 'MOTIJHEEL BRANCH',
        'acc_opening_branch' => 'MOTIJHEEL BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZRAHPRIME',
        'account_number'     => '3108213040011',
        'SIF_Number'         => '1638057',

        'account_title'      => 'ZIAUR RAHMAN',
        'customer_name'      => 'ZIAUR RAHMAN',

        'contact_no_1'       => '8801713065732',
        'mobile_number'      => '8801713065732',

        'email_1'            => 'rahman.ziaur@hotmail.com',
        'email_address'      => 'rahman.ziaur@hotmail.com',

        'branch_name'        => 'IBB DILKUSHA BRANCH',
        'acc_opening_branch' => 'IBB DILKUSHA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZREBEKAPB',
        'account_number'     => '2129219019610',
        'SIF_Number'         => '2764212',

        'account_title'      => 'MST. ZESMIN AKTAR REBEKA',
        'customer_name'      => 'MST. ZESMIN AKTAR REBEKA',

        'contact_no_1'       => '8801345251795',
        'mobile_number'      => '8801345251795',

        'email_1'            => 'ams100100100@gmail.com',
        'email_address'      => 'ams100100100@gmail.com',

        'branch_name'        => 'BOGURA BRANCH',
        'acc_opening_branch' => 'BOGURA BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZUBDATAPB',
        'account_number'     => '2118211031939',
        'SIF_Number'         => '2396261',

        'account_title'      => 'ZUBDAT ANSARI',
        'customer_name'      => 'ZUBDAT ANSARI',

        'contact_no_1'       => '8801913474228',
        'mobile_number'      => '8801913474228',

        'email_1'            => '',
        'email_address'      => '',

        'branch_name'        => 'GULSHAN BRANCH',
        'acc_opening_branch' => 'GULSHAN BRANCH',
    ],
    [
        'bp_id'              => 'IN137ZULKARPB',
        'account_number'     => '2110212023628',
        'SIF_Number'         => '2627610',

        'account_title'      => 'MD. ZULKARNAIN ISLAM',
        'customer_name'      => 'MD. ZULKARNAIN ISLAM',

        'contact_no_1'       => '8801797576382',
        'mobile_number'      => '8801797576382',

        'email_1'            => 'zulkar9881@gmail.com',
        'email_address'      => 'zulkar9881@gmail.com',

        'branch_name'        => 'MOHAKHALI BRANCH',
        'acc_opening_branch' => 'MOHAKHALI BRANCH',
    ],
];

$successCount = 0;
$failedCount  = 0;
$failedRows   = [];

// স্টেটমেন্ট প্রিপেয়ার
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
            $w_form_type,
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
