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
	// echo $username ."==" .$hostname . "==" .$dbname. "==" .$port. "==" .$password ;
} else {
	echo "File not found: " . $filePath;
}


//Test Connection 
// $hostname='localhost';
// $dbname='dbmtbcscms_190723';
// $username='root';
// $password='abc123';
// $port='port=3306';

//DR Connection
// $hostname='10.46.10.125';
// // $hostname='drcscmdb.mutualtrustbank.com';
// $dbname='dbmtbcscms';
// $username='DRTest';
// $password='Abcd.1234#';
// $port='port=3360';

//Live Connection
// $hostname='10.45.10.120';
// $dbname='dbmtbcscms';
// $username='mtbcscms';
// $password='DDefcms.2345#';
// $port='port=3360';

// Escalation URL: $ESCALATION_URL (db_credential.php) > APP_URL (engine/.env) > local default
$escalationBaseUrl = getEscalationBaseUrl();
echo "Escalation URL: " . $escalationBaseUrl . "\n";

// Prevent overlapping runs (duplicate breach/email)
$lockHandle = fopen(__DIR__ . '/engine/storage/schedule-escalation.lock', 'c');
if ($lockHandle === false || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
	echo "Another escalation run is in progress. Exiting...\n";
	exit;
}

$dbh = null;

try {
    $dbh = new PDO("mysql:host=$hostname;$port;dbname=$dbname",$username,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    echo "DB Connection Error: " . $e->getMessage() . "\n";
    error_log("Schedule-Escalation DB Connection Error: " . $e->getMessage());
}

if ($dbh !== null) {
	sendEscalationMail($dbh, $escalationBaseUrl);
}
//$dbh->query('KILL CONNECTION_ID()');
$dbh = null;

flock($lockHandle, LOCK_UN);
fclose($lockHandle);

function getEscalationBaseUrl()
{
	global $ESCALATION_URL;
	if (!empty($ESCALATION_URL)) {
		return rtrim($ESCALATION_URL, '/');
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

	return 'http://primecms.test';
}


function sendEscalationMail($dbh, $escalationBaseUrl)
{
	// print("Mail");
	// die();
	set_time_limit(0);
	echo "\nTrying to send Escalation Email...\n";
	$callTime = time();
	$successCount = 0;
	$failedCount = 0;
	//echo 'Continue';
	$result = getOutgoingMessages($dbh);
	// echo '<pre>';
	// print_r($result); die;
	if(is_array($result)&& count($result)>0)
	{
		foreach($result as $info)
		{
			if (!empty($info)) {

                //$curl_URL_Param = "http://sss.bkkb.gov.bd/sendAPIdata?apiKey=".$token."&mobile_no=".$request->mobile_no; 
				
                //$info['cmnt_time']=(!empty($info['cmnt_time']))?$info['cmnt_time']:$info['ref_date'];
                //$info['cmnt_ref']=(!empty($info['cmnt_ref']))?$info['cmnt_ref']:$info['ref_ref'];
                
                // $curl_URL_Param = "https://voc.mutualtrustbank.com/voc/sendEscalationEmail?user=escmailusr&passwd=Pwd@!123&ref_ref=".$info['ref_ref']; //New Live URL
                //$curl_URL_Param = "http://10.45.6.58:5306/cscms/sendEscalationEmail?user=escmailusr&passwd=Pwd@!123&ref_ref=".$info['ref_ref']; //Live URL
                // $curl_URL_Param = "http://192.168.45.22:5306/uatvoc/sendEscalationEmail?user=escmailusr&passwd=Pwd@!123&ref_ref=".$info['ref_ref']; //Test URL                         
				$ref_ref = isset($info['ref_ref']) ? $info['ref_ref'] : '';
				$curl_URL_Param = $escalationBaseUrl . "/sendEscalationEmail?user=escmailusr&passwd=" . urlencode('Pwd@!123') . "&ref_ref=" . urlencode($ref_ref);
				
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
					$failedCount++;
					echo $ref_ref . " => cURL Error: " . curl_error($curl_get) . "\n";
					curl_close($curl_get);
					continue;
				}

				curl_close($curl_get);
				$successCount++;
				echo $ref_ref . " => " . trim($curl_get_response) . "\n";
           }
		}
		echo "Found ".count($result)." Reference... Processed: ".$successCount.", cURL Failed: ".$failedCount."\n";
		echo "Time taken: ".(time() - $callTime)." sec\n";
	}
	else
	{
		echo "Not found any Reference to Escalate...\n";
	}
	echo "...End...\n";
}

function getOutgoingMessages($dbh)
{
	//$ts = time();
	$query = "SELECT ref.reference_number AS ref_ref FROM `reference` ref where (ref.reference_number like 'S%' or ref.reference_number like 'C%') and ref.id > 10 and ref.form_status not in (-1,-2,11,12)";

	$result = array();
	
	foreach ($dbh->query($query) as $row)
    {
		$result[] = array('ref_ref' => $row[0]);
    }
	
	return $result;
}

?>
