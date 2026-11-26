<?php
ini_set("always_populate_raw_post_data", "On");
header("Content-Type: text/plain; charset=ISO-8859-1\r\n");
date_default_timezone_set("Asia/Dhaka");
echo "The time is " . date("Y-m-d h:i:sa");
echo "\n\nStarting Consumer.....\n";

$filePath = __DIR__ . '/engine/config/db_credential.php';

if (file_exists($filePath)) {
    require $filePath;
    $hostname = $DB_HOST1;
    $port = 'port=' . $DB_PORT1;
    $dbname = $DB_DATABASE1;
    $username = $DB_USERNAME1;
    $password = base64_decode($DB_PASSWORD1);
} else {
    echo "File not found: " . $filePath;
}

// Vendor mail URL: $APP_BASE_URL (db_credential.php) > current host (browser) > APP_URL (engine/.env) > local default
$appBaseUrl = getAppBaseUrl();
echo "Vendor Mail URL: " . $appBaseUrl . "\n";

$dbh = null;

try {
    $dbh = new PDO("mysql:host=$hostname;$port;dbname=$dbname",$username,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    echo "DB Connection Error: " . $e->getMessage() . "\n";
    error_log("Vendor-Email DB Connection Error: " . $e->getMessage());
}

if ($dbh !== null) {
    sendVendorMail($dbh, $appBaseUrl);
}
$dbh = null;

function getAppBaseUrl()
{
    global $APP_BASE_URL;
    if (!empty($APP_BASE_URL)) {
        return rtrim($APP_BASE_URL, '/');
    }

    // Browser theke run hole current host + script folder (Laravel root)
    if (!empty($_SERVER['HTTP_HOST'])) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        return $scheme . '://' . $_SERVER['HTTP_HOST'] . $basePath;
    }

    $envPath = __DIR__ . '/engine/.env';
    if (file_exists($envPath)) {
        foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (strpos($line, 'APP_URL=') === 0) {
                $appUrl = trim(substr($line, strlen('APP_URL=')), " \t\"'");
                if (!empty($appUrl)) {
                    return rtrim($appUrl, '/');
                }
            }
        }
    }

    return 'http://pbl.test';
}

function sendVendorMail($dbh, $appBaseUrl)
{
    set_time_limit(1080);
    echo "\nTrying to send Email to Vendor...\n";
    $callTime = time();

    $result = getOutgoingMessages($dbh);
	
    if(is_array($result) && count($result)>0) {
        foreach($result as $id) {
			// $headers_get[] = 'Content-Type: application/x-www-form-urlencoded';

			$curl_URL_Param = $appBaseUrl . "/send-vendor-email?user=VendormailUsr&passwd=" . urlencode('PwdVendor@!123') . "&id=" . urlencode($id);

			$curl_get = curl_init();
			curl_setopt($curl_get, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_get, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($curl_get, CURLOPT_TIMEOUT, 20);
			curl_setopt($curl_get, CURLOPT_POST, false);
			curl_setopt($curl_get, CURLOPT_URL, $curl_URL_Param);
			curl_setopt($curl_get, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl_get, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl_get, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl_get, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

			$curl_get_response = curl_exec($curl_get);
			if ($curl_get_response === false) {
				echo "cURL Error: " . curl_error($curl_get) . "\n";
			} else {
				echo "Response for ID $id: " . $curl_get_response . "\n";
			}

			curl_close($curl_get);
		}

        echo "Found ".count($result)." requisition...\n";
    } else {
        echo "Not found any requisition to Send...\n";
    }
    echo "...End...\n";
}

function getOutgoingMessages($dbh)
{
    $query = "SELECT ref.id AS ref_ref FROM outgoing_vendor_email ref WHERE ref.status NOT IN (7) LIMIT 50";

    $result = array();

    foreach ($dbh->query($query) as $row)
    {
        $result[] =  $row[0];
    }
	
    return $result;
}

?>
