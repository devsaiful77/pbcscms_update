<?php
session_start();

define('STATIC_PASSWORD', '11111111');
define('SALT', 'fbl');

define('SESSION_LIFETIME', 300);


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


if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . basename(__FILE__));
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === STATIC_PASSWORD) {
        $_SESSION['authenticated'] = true;
    } else {
        $error_message = "Incorrect password.";
    }
}

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

        input[type="text"],
        input[type="password"] {
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
                            }, {
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

            document.getElementById('user_agent').value = userAgent;
        }

        window.onload = getUserInfo();

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
                $DB_HOST1 = $_POST['db_host'];
                $DB_PORT1 = $_POST['db_port'];
                $DB_DATABASE1 = $_POST['db_database'];
                $DB_USERNAME1 = $_POST['db_username'];
                $DB_PASSWORD1 = base64_encode($_POST['db_password']);
                $USER_IP = base64_encode($_POST['user_ip']);
                $USER_AGENT = base64_encode($_POST['user_agent']);
                $USER_LOCATION = base64_encode($_POST['user_location']);
                $REDIRECT_URL = $_POST['redirect_url'];

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

                $filePath = __DIR__ . '/engine/config/db_credential.php';

                if (file_exists($filePath)) {
                    $backupFilePath = __DIR__ . '/engine/config/db_credential_' . date('Ymd_His') . '.php';
                    if (!rename($filePath, $backupFilePath)) {
                        echo "<div class='message error'><h2>Error</h2><p>There was an error renaming the existing configuration file. Please check the file permissions.</p></div>";
                        exit;
                    }
                }

                if (file_put_contents($filePath, $configContent)) {
                    chmod($filePath, 0775);


                    $output = null;
                    $status = null;
                    exec('php artisan optimize:clear', $output, $status);

                    if ($status === 0) {
                        echo "<div class='message success'><p>Application cache cleared successfully.</p></div>";
                    } else {
                        echo "<div class='message error'><p>Failed to clear Application cache. If you need Please run 'php artisan optimize:clear' manually.</p></div>";
                    }

                    echo "<div class='message success'><h2>Configuration Saved</h2>";
                    echo "<p>DB Host: " . htmlspecialchars($DB_HOST1) . "</p>";
                    echo "<p>DB Port: " . htmlspecialchars($DB_PORT1) . "</p>";
                    echo "<p>DB Name: " . htmlspecialchars($DB_DATABASE1) . "</p>";
                    echo "<p>DB Username: " . htmlspecialchars($DB_USERNAME1) . "</p>";
                    echo "<p>DB Password: " . htmlspecialchars(base64_decode($DB_PASSWORD1)) . "</p>";
                    echo "<p>The configuration has been saved </p></div>";

                    echo "<script>redirect('{$REDIRECT_URL}', 7000);</script>";
                } else {
                    echo "<div class='message error'><h2>Error</h2><p>There was an error saving the configuration. Please check the file permissions.</p></div>";
                }
            } else {
            ?>
                <h2>Database Configuration Form</h2>

                <?php
                $filePath = __DIR__ . '/engine/config/db_credential.php';

                if (file_exists($filePath)) {
                    require $filePath;
                    $hostname = $DB_HOST1;
                    $port =  $DB_PORT1;
                    $dbname = $DB_DATABASE1;
                    $username = $DB_USERNAME1;
                    $password = $DB_PASSWORD1;
                } else {
                    echo "File not found: " . $filePath;
                }

                ?>
                <form action="" method="post">
                    <label for="db_host">DB Host:</label>
                    <input type="text" id="db_host" name="db_host" value="<?= $hostname ?? '127.0.0.1'; ?>" required>

                    <label for="db_port">DB Port:</label>
                    <input type="text" id="db_port" name="db_port" value="<?= $port ?? '3306'; ?>" required>

                    <label for="db_database">DB Name:</label>
                    <input type="text" id="db_database" name="db_database" value="<?= $dbname ?? 'dbpbcms'; ?>" required>

                    <label for="db_username">DB Username:</label>
                    <input type="text" id="db_username" name="db_username" value="<?= $username ?? 'root'; ?>" required>

                    <label for="db_password">DB Password:</label>
                    <input type="password" id="db_password" name="db_password" value="">

                    <label for="redirect_url">Redirect URL:</label>
                    <input type="text" id="redirect_url" name="redirect_url" value="http://primecms.test/prime-session-clear" title="Local url" required>

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