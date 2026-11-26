<?php
$hostname = '192.168.202.190';
$username = 'primeapp';
$dPass    = 'Ppa#123456';
$dbname   = 'primeservedb';
$port     = 3306;

try {
    $dbh = new PDO("mysql:host=$hostname;port=$port;dbname=$dbname;charset=utf8mb4", $username, $dPass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tables = [
        'group_info_tmps',
        'group_info',
        'subgroup_info_tmps',
        'subgroup_info'
    ];

    foreach ($tables as $table) {
        $sql = "ALTER TABLE `{$table}` 
                ADD `address` VARCHAR(255) COLLATE utf8mb4_general_ci NULL AFTER `description`";
        $dbh->exec($sql);
    }

    echo "Table Columns created successfully.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>