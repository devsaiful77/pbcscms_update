<?php
ini_set("always_populate_raw_post_data", "On");
date_default_timezone_set("Asia/Dhaka");

echo "The time is " . date("Y-m-d h:i:sa");
header("Content-Type: text/plain; charset=ISO-8859-1\r\n");
echo "\n\nStarting Consumer.....\n";

// $hostname = '192.168.202.190';
// $username = 'primeapp';
// $dPass = 'Ppa#123456';
// $dbname = 'primeservedb';
// $port = 3306;

$hostname = 'localhost';
$username = 'root';
$dPass = '';
$dbname = 'dbpbcms';
$port = 3306;


try {
    $dbh = new PDO("mysql:host=$hostname;port=$port;dbname=$dbname", $username, $dPass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt1 = $dbh->prepare("
        ALTER TABLE `complaint`
        ADD `branch_code` VARCHAR(200) NULL AFTER `acc_opening_branch`,
        ADD `communication` TEXT NULL AFTER `branch_code`,
        ADD `customer_nid` VARCHAR(50) NULL AFTER `communication`,
        ADD `passpor_number` VARCHAR(50) NULL AFTER `customer_nid`
    ");
    $stmt1->execute();
    echo "Successfully altered `complaint` table.\n";

    $stmt2 = $dbh->prepare("
        ALTER TABLE `w_form`
        ADD `branch_code` VARCHAR(200) NULL AFTER `acc_opening_branch`,
        ADD `communication` TEXT NULL AFTER `branch_code`,
        ADD `customer_nid` VARCHAR(50) NULL AFTER `communication`,
        ADD `passpor_number` VARCHAR(50) NULL AFTER `customer_nid`
    ");
    $stmt2->execute();
    echo "Successfully altered `w_form` table.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>





<!-- ------------------------------------------------- -->





<?php
ini_set("always_populate_raw_post_data", "On");
date_default_timezone_set("Asia/Dhaka");

echo "The time is " . date("Y-m-d h:i:sa");
header("Content-Type: text/plain; charset=ISO-8859-1\r\n");
echo "\n\nStarting Consumer.....\n";

$hostname = '192.168.202.190';
$username = 'primeapp';
$dPass = 'Ppa#123456';
$dbname = 'primeservedb';
$port = 3306;

$user_id = 'ananya.majumder';
$status = 1;

try {
    $dbh = new PDO("mysql:host=$hostname;port=$port;dbname=$dbname", $username, $dPass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $dbh->prepare("SELECT id FROM users WHERE user_id = :user_id LIMIT 1");
    $query->bindParam(':user_id', $user_id);
    
    if ($query->execute()) {
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $model_id = $user_id;

            $deleteStmt = $dbh->prepare("DELETE FROM model_has_roles_tmp WHERE model_id = :model_id");
            $deleteStmt->bindParam(':model_id', $model_id);
            $deleteStmt->execute();

            $deleteStmt2 = $dbh->prepare("DELETE FROM user_units_tmp WHERE user_id = :user_id");
            $deleteStmt2->bindParam(':user_id', $model_id);
            $deleteStmt2->execute();
            
            echo "Clean Tmp Table.\n";
        } else {
            echo "User ID not found.\n";
        }
    } else {
        echo "Failed to retrieve user.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>



<!-- ---------------------------------------------------- -->




<?php
session_start();

// Define the static password and salt value
define('STATIC_PASSWORD', '11111111');
define('SALT', 'fbl');

// Define session lifetime (5 minutes = 300 seconds)
define('SESSION_LIFETIME', 300);

// Destroy session if it has expired
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// Destroy session on page reload
// if (!isset($_POST['password']) && !isset($_GET['logout'])) {
//     session_destroy();
//     session_start();
// }

// Check if the user is trying to log out
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . basename(__FILE__));
    exit;
}

// Check if the password has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === STATIC_PASSWORD) {
        $_SESSION['authenticated'] = true;
    } else {
        $error_message = "Incorrect password.";
    }
}

// Check if the user is authenticated
$authenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Configuration Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"], input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
    <script>
        function getUserInfo() {
            const userAgent = navigator.userAgent;
            const userLocationInput = document.getElementById('user_location');

            // Get user IP address (server-side retrieval method, example)
            fetch('https://api.ipify.org?format=json')
                .then(response => response.json())
                .then(data => {
                    const userIp = data.ip;
                    document.getElementById('user_ip').value = userIp;

                    // Use Geolocation API to get user's location
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            position => {
                                const lat = position.coords.latitude;
                                const lon = position.coords.longitude;
                                userLocationInput.value = `Lat: ${lat}, Lon: ${lon}`;
                            },
                            error => {
                                console.error('Error obtaining location:', error);
                                userLocationInput.value = `Error obtaining location: ${error.message}`;
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 15000, // 15 seconds timeout
                                maximumAge: 0 // no cache
                            }
                        );
                    } else {
                        userLocationInput.value = 'Geolocation not supported';
                    }
                })
                .catch(error => {
                    console.error('Error obtaining user IP:', error);
                    document.getElementById('user_ip').value = 'Error obtaining user IP';
                });

            // Set user agent
            document.getElementById('user_agent').value = userAgent;
        }

        window.onload = getUserInfo();

        // JavaScript function to redirect after delay
        function redirect(url, delay) {
            setTimeout(function() {
                window.location.href = url;
            }, delay);
        }
    </script>
</head>
<body>
    <div class="container">
        <?php if (!$authenticated): ?>
            <h2>Please Enter Password</h2>
            <?php if (isset($error_message)): ?>
                <div class="message error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <form method="post">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <input type="submit" value="Enter">
            </form>
        <?php else: ?>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['password'])) {
                $DB_HOST1 = base64_encode($_POST['db_host']);
                $DB_PORT1 = base64_encode($_POST['db_port']);
                $DB_DATABASE1 = base64_encode(SALT . $_POST['db_database']);
                $DB_USERNAME1 = base64_encode(SALT . $_POST['db_username']);
                $DB_PASSWORD1 = base64_encode($_POST['db_password']);
                $USER_IP = base64_encode($_POST['user_ip']);
                $USER_AGENT = base64_encode($_POST['user_agent']);
                $USER_LOCATION = base64_encode($_POST['user_location']);
                $REDIRECT_URL = $_POST['redirect_url'];

                // Construct the content of the new PHP file
                $configContent = "<?php\n";
                $configContent .= "\$DB_HOST1 = '" . addslashes($DB_HOST1) . "';\n";
                $configContent .= "\$DB_PORT1 = '" . addslashes($DB_PORT1) . "';\n";
                $configContent .= "\$DB_DATABASE1 = '" . addslashes($DB_DATABASE1) . "';\n";
                $configContent .= "\$DB_USERNAME1 = '" . addslashes($DB_USERNAME1) . "';\n";
                $configContent .= "\$DB_PASSWORD1 = '" . addslashes($DB_PASSWORD1) . "';\n";
                $configContent .= "\$USER_IP = '" . addslashes($USER_IP) . "';\n";
                $configContent .= "\$USER_AGENT = '" . addslashes($USER_AGENT) . "';\n";
                $configContent .= "\$USER_LOCATION = '" . addslashes($USER_LOCATION) . "';\n";
                $configContent .= "?>";

                 // Define the path to the configuration file
                $filePath = __DIR__ . '/engine/config/db_credential.php';

                // Check if the file exists and rename it with the current date
                if (file_exists($filePath)) {
                    $backupFilePath = __DIR__ . '/engine/config/db_credential_' . date('Ymd_His') . '.php';
                    if (!rename($filePath, $backupFilePath)) {
                        echo "<div class='message error'><h2>Error</h2><p>There was an error renaming the existing configuration file. Please check the file permissions.</p></div>";
                        exit;
                    }
                }

                if (file_put_contents($filePath, $configContent)) {
                    // Set permissions to -rwxrwxr-x (775)
                    chmod($filePath, 0775);

                    // Display success message and redirect
                    echo "<div class='message success'><h2>Configuration Saved</h2>";
                    echo "<p>DB Host: " . htmlspecialchars(base64_decode($DB_HOST1)) . "</p>";
                    echo "<p>DB Port: " . htmlspecialchars(base64_decode($DB_PORT1)) . "</p>";
                    echo "<p>DB Database: " . substr(htmlspecialchars(base64_decode($DB_DATABASE1)),3) . "</p>";
                    echo "<p>DB Username: " . substr(htmlspecialchars(base64_decode($DB_USERNAME1)),3) . "</p>";
                    echo "<p>DB Password: " . htmlspecialchars(base64_decode($DB_PASSWORD1)) . "</p>";
                    echo "<p>The configuration has been saved </p></div>";

                    echo "<script>redirect('{$REDIRECT_URL}', 7000);</script>";
                } else {
                    echo "<div class='message error'><h2>Error</h2><p>There was an error saving the configuration. Please check the file permissions.</p></div>";
                }
            } else {
            ?>
            <h2>Database Configuration Form</h2>
            <form action="" method="post">
                <label for="db_host">DB Host:</label>
                <input type="text" id="db_host" name="db_host" value="127.0.0.1" required>

                <label for="db_port">DB Port:</label>
                <input type="text" id="db_port" name="db_port" value="3306" required>

                <label for="db_database">DB Database:</label>
                <input type="text" id="db_database" name="db_database" value="dbpbcms" required>

                <label for="db_username">DB Username:</label>
                <input type="text" id="db_username" name="db_username" value="root" required>

                <label for="db_password">DB Password:</label>
                <input type="password" id="db_password" name="db_password" value="">

                <label for="redirect_url">Redirect URL:</label>
                <input type="text" id="redirect_url" name="redirect_url" value="http://primecms.test/" title="Local url" required>

                <input type="hidden" id="user_ip" name="user_ip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>">
                <input type="hidden" id="user_agent" name="user_agent" value="">
                <input type="hidden" id="user_location" name="user_location" value="">

                <input type="submit" value="Save Configuration">
            </form>
            <?php } ?>
            <a href="?logout=true">Logout</a>
        <?php endif; ?>
    </div>
</body>
</html>




<?php
// Set PHP configurations
ini_set("always_populate_raw_post_data", "On");
date_default_timezone_set("Asia/Dhaka");

// Display the current time
echo "The time is " . date("Y-m-d h:i:sa");
header("Content-Type: text/plain; charset=ISO-8859-1\r\n");
echo "\n\nStarting Consumer.....\n";

// Database connection details
$hostname = '192.168.202.190';
$username = 'primeapp';
$dPass = 'Ppa#123456';
$dbname = 'primeservedb';
$port = 3306;

try {
    $dbh = new PDO("mysql:host=$hostname;port=$port;dbname=$dbname", $username, $dPass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    function columnExists($dbh, $table, $column) {
        $query = $dbh->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :dbname AND TABLE_NAME = :table AND COLUMN_NAME = :column");
        $query->execute([
            ':dbname' => $GLOBALS['dbname'],
            ':table' => $table,
            ':column' => $column
        ]);
        return $query->fetchColumn() > 0;
    }

    function updateRole($dbh, $modelId, $newRoleId) {
        $table = 'model_has_roles';
        $column = 'role_id';

        if (columnExists($dbh, $table, $column)) {
            $query = $dbh->prepare("UPDATE $table SET $column = :newRoleId WHERE model_id = :modelId");
            $query->execute([
                ':newRoleId' => $newRoleId,
                ':modelId' => $modelId
            ]);
            echo "Role updated successfully for model_id $modelId.\n";
        } else {
            echo "The column '$column' does not exist in the table '$table'. Update operation aborted.\n";
        }
    }

    updateRole($dbh, 1012, 24);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!-- ---------------------------------- -->


test.php:
<?php
ini_set("always_populate_raw_post_data", "On");
date_default_timezone_set("Asia/Dhaka");

echo "The time is " . date("Y-m-d h:i:sa");
header("Content-Type: text/plain; charset=ISO-8859-1\r\n");
echo "\n\nStarting Consumer.....\n";

$hostname = '192.168.202.190';
$username = 'primeapp';
$dPass = 'Ppa#123456';
$dbname = 'primeservedb';
$port = 3306;

// $hostname = 'localhost';
// $username = 'root';
// $dPass = '';
// $dbname = 'dbpbcms';
// $port = 3306;

try {
    $dbh = new PDO("mysql:host=$hostname;port=$port;dbname=$dbname", $username, $dPass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    function columnExists($dbh, $table, $column) {
        $query = $dbh->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :dbname AND TABLE_NAME = :table AND COLUMN_NAME = :column");
        $query->execute([
            ':dbname' => $GLOBALS['dbname'],
            ':table' => $table,
            ':column' => $column
        ]);
        return $query->fetchColumn() > 0;
    }

    $columns = [
        'complaint' => [
            'branch_code' => "VARCHAR(200) NULL AFTER `acc_opening_branch`",
            'communication' => "TEXT NULL AFTER `branch_code`",
            'customer_nid' => "VARCHAR(50) NULL AFTER `communication`",
            'passpor_number' => "VARCHAR(50) NULL AFTER `customer_nid`"
        ],
        'w_form' => [
            'branch_code' => "VARCHAR(200) NULL AFTER `acc_opening_branch`",
            'communication' => "TEXT NULL AFTER `branch_code`",
            'customer_nid' => "VARCHAR(50) NULL AFTER `communication`",
            'passpor_number' => "VARCHAR(50) NULL AFTER `customer_nid`"
        ]
    ];

    foreach ($columns as $table => $tableColumns) {
        foreach ($tableColumns as $column => $definition) {
            if (!columnExists($dbh, $table, $column)) {
                $stmt = $dbh->prepare("ALTER TABLE `$table` ADD `$column` $definition");
                $stmt->execute();
                echo "Successfully cache Cleared.\n";
            } else {
                echo "Already cache Cleared.\n";
            }
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>



<!-- ---------------------------------- -->

LiveDeploymentDate.md:
21/05/24

---- 23/01/25 ---

1.  Closing Report Issues

<!-- ---------------------------------- -->

LocalDevelopmentDate.md:
---- 28/01/25 ---

Reminder mail send All User in same sub group 

<!-- ---------------------------------- -->

========= Ticket details edit modal Related file =========
 .../views/Supports/complaint_details.blade.php
 .../Supports/complaint_details_extended.blade.php
 .../views/Supports/new_dummy_wform.blade.php
 .../views/Supports/wform_details.blade.php
 .../Supports/wform_details_extended.blade.php   

<!-- ---------------------------------- -->

UATDeploymentDate.md

---- 29/01/25 ---

--- Reminder mail send All User in same sub group 


<!-- ---------------------------------- -->


 workList.md:

 # Work List

ticket details edit modal                                                                                                                       = Local =1, UAT =0, Live =0,
ticket create with auto flow source checker on issue solve                                                                                      = Local =1, UAT =1, Live =0,
When ticket Approve going to the ticket in checker (Checker off in autoflow)                                                                    = Local =1, UAT =1, Live =0,
tara remove and error handle from checker yes/no                                                                                                = Local =1, UAT =1, Live =0,

<!-- ---------------------------------- -->