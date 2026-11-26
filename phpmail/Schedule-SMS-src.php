<?php
ini_set("always_populate_raw_post_data", "On");
header("Content-Type: text/plain; charset=ISO-8859-1\r\n");

echo "\n\nStarting Consumer.....\n";

//Local Connection
/*$hostname='localhost';
$username='root';
$password='';
$dbname='dbcscmsbbluat';
$port='port=3306';*/

//Test Connection 
/*$hostname='172.25.15.121';
$dbname='dbcsrncms';
$username='bblcsrncms';
$password='DDefcms.2345#';
$port='port=3489';*/

//Live Connection
$hostname='10.5.31.74';
$dbname='dbcsrncms';
$username='bblcsrncms';
$password='DDefcms.2345#';
$port='port=3360';

$dbh = null;

try {
    $dbh = new PDO("mysql:host=$hostname;$port;dbname=$dbname",$username,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    //echo $e->getMessage();
}

$GLOBALS['SMS_API_URL'] = "";
$query = 'select SMS_API_URL from api_credential limit 1';
foreach ($dbh->query($query) as $row)
{
	$GLOBALS['SMS_API_URL'] = $row[0];
}

//print_r($GLOBALS['SMS_API_URL']);die;

// Start API Credential

/*
$GLOBALS['channel_id'] = "";
$GLOBALS['user_name'] = "";
$GLOBALS['user_password'] = "";
$GLOBALS['unique_id'] = "";
$GLOBALS['sms_type_code'] = "";
$GLOBALS['language'] = "";

$query = 'select channel_id, user_name, user_password, unique_id, sms_type_code, language from api_credential limit 1';
	
	foreach ($dbh->query($query) as $row)
    {
		$GLOBALS['channel_id'] = $row[0];
		$GLOBALS['user_name'] = $row[1];
		$GLOBALS['user_password'] = $row[2];
		$GLOBALS['unique_id'] = $row[3];
		$GLOBALS['sms_type_code'] = $row[4];
		$GLOBALS['language'] = $row[5];
    }
*/

//echo $GLOBALS['user_password']; die;

//Close API crede

// while(1)
// {		
// 	try 
// 	{		
		sendSMSToCustomer($dbh);	
		sendEmailToCustomer($dbh);
		//$dbh->query('KILL CONNECTION_ID()');
		$dbh = null;
//	} 
// 		catch (Exception $e) 
// 	{}	
// 		sleep(3);	
// }


function sendSMSToCustomer($dbh)
{	
	global $client;
	echo "\nTrying to send SMS to Customer...\n";	
	$callTime = time();
	//echo 'Continue';
	$result = getOutgoingMessages($dbh);
	//echo '<pre>';
	//print_r($result); die;
	if(is_array($result)&& count($result)>0)
	{	
		foreach($result as $info)
		{
			//echo "<pre>";
			//print_r($info); die;    
			if(_sendSMS($info, $dbh))
			{                           
					   sleep(1);
					   continue;
			}
			
		}		
		echo "Found ".count($result)." SMS...\n";		
	}
	else
	{
		echo "Not found any SMS to Send...\n";
	}
	echo "...End...\n";
}

//Send SMS to CMP

function _sendSMS($info, $dbh)
{
	//echo '<pre>';
	//print_r($info); die;
	//date_default_timezone_set('Asia/Dhaka');
	$cTime = date("G:i:s", time());
	$sTime = strtotime("6:00:00");
	$eTime = strtotime("23:30:00");
	
	$data['To'] = $info['mobileNo'];			
	$data['Message'] = $info['message'];

	print_r($data);
		
	/*$sql = 'select id from outgoingemailtable where status = 3 and reference_number = "'.$info['reference_number'].'" limit 1';

	$emailID = "";
	foreach ($dbh->query($sql) as $row)
    {
		$emailID = $row[0];
    }

    //echo $emailID;

    if(!empty($emailID)){
	$sql = 'update outgoingsmstable set status = 5 where reference_number = "'.$info['reference_number'].'" and id = '.$info['id'];
	$dbh->query($sql);
	
	}else{*/		

	$smsId = "";
	$mobile_number = $info['mobileNo'];
	$mnumber1 = formatMobileNumber($mobile_number);
	if($mnumber1 != "")
	{
		//echo $mnumber1.'***    ';	die;			
		if(is_numeric($mnumber1) && strlen($mnumber1) == 14)
			{
				$mnumber = str_replace("+88", "", $mnumber1);
				$dataForApi = '{"mobileNo":"'.$mnumber.'","message":"'.$data['Message'].'","component":"CSRCMS","eventCode":"12","msgType":"TEXT"}';
				//echo $dataForApi; die;
				//$ch = curl_init('http://172.25.0.111:20001/api/sms/v2/send'); // Test URL
				//$ch = curl_init('http://10.5.0.29:20001/api/sms/v2/send'); // Live URL
				$ch = curl_init('http://smsapi.bracbank.com:20001/api/sms/v2/send'); //Live URL
				// print_r($GLOBALS['SMS_API_URL']);die;
				curl_setopt($ch, CURLOPT_POST, true);
			    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataForApi);
			    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
			    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			    curl_setopt($ch, CURLOPT_TIMEOUT, 10);  
				$response = curl_exec($ch);
				curl_close($ch);
					
				$fresult = json_decode($response);
				$smsId = (!empty($fresult->responseCode))? $fresult->responseCode : 0;
					
			}			
			
		}
		
		if($smsId == '200')
		{		
			echo "Success...\n";
			$info['id'] = $info['id'];
			$info['sentTime'] = time();
			$info['cmpMsgId'] = $smsId;
			$info['status'] = 1;
			updateStatus($info, $dbh);			
		}	
		else
		{	
			echo "Failed...\n";
			$info['id'] = $info['id'];			
			$info['status'] = 3;
			updateFailedStatus($info, $dbh);
		}

	//}
				
 	return true;

}

//sendSMStoCMP(array('mobileNo' => '+8801841777707', 'message' => 'Good Day'));

function OtherSMS($info, $dbh)
{		
			
	echo 'Success';
	$info['id'] = $info['id'];
	$info['sentTime'] = time();
	$info['cmpMsgId'] = 11;
	$info['status'] = 1;
	updateStatus($info, $dbh);

}

function getOutgoingMessages($dbh)
{
	//$ts = time();		
	$query = 'select id, mobileNo, message, repeat_time, reference_number from outgoingsmstable where status = 3 limit 100';
	//$mysqli = new mysqli("localhost","root","","wbms_db_live");
	//$data = mysqli_query($mysqli, $query);
	$result = array();
	
	foreach ($dbh->query($query) as $row)
    {
		$result[] = array('id' => $row[0], 'mobileNo' => $row[1], 'message' => $row[2], 'repeat_time' => $row[3], 'reference_number' => $row[4]);
    }
	
	return $result;
}

function updateStatus($info, $dbh)
{
	
	if(is_array($info)&& count($info)>0)
	{		
		
		$sql = "update outgoingsmstable set status = 1, senttime = '".date("Y-m-d H:i:s")."', sentSMSid = '".$info['cmpMsgId']."' where id = ".$info['id'];
		$dbh->query($sql);
		//$mysqli = new mysqli("localhost","root","","wbms_db_live");
		//mysqli_query($mysqli, $sql);
		//mysqli_close($mysqli);
		
	}
}

function updateFailedStatus($info, $dbh)
{	
	if(is_array($info)&& count($info)>0)
	{			
		//print_r($info);
		if($info['repeat_time'] == 0){
			$sql = 'update outgoingsmstable set status = 3, repeat_time = 1 where id = '.$info['id'];		
		}elseif($info['repeat_time'] == 1){
			$sql = 'update outgoingsmstable set status = 3, repeat_time = 2 where id = '.$info['id'];		
		}elseif($info['repeat_time'] == 2){
			$sql = 'update outgoingsmstable set status = 4, repeat_time = 3 where id = '.$info['id'];		
		}else{
			$sql = 'update outgoingsmstable set status = 4 where id = '.$info['id'];
		}

		$dbh->query($sql);
		//$mysqli = new mysqli("localhost","root","","wbms_db_live");
		//mysqli_query($mysqli, $sql);
		//mysqli_close($mysqli);		
		
	}	
}


function sendEmailToCustomer($dbh)
{	
	global $client;
	echo "\nTrying to send Email to Customer...\n";	
	$callTime = time();
	//echo 'Continue';
	$result = getOutgoingEmails($dbh);
	//echo "<pre>";
	//print_r($result); die;
	if(is_array($result)&& count($result)>0)
	{	
		foreach($result as $info)
		{
            //echo "<pre>";
			//print_r($info); die;    
			if(_sendEmail($info, $dbh))
			{                           
					   sleep(1);
					   continue;
			}
			
		}		
		echo "Found ".count($result)." Email...\n";		
	}
	else
	{
		echo "Not found any Email...\n";
	}
	echo "...End...\n";
}

function _sendEmail($info, $dbh)
{
	//print_r($info);
	include_once('phpmail/php-mailer.php');

	echo "Trying to send mail to # ".$info['email_address']."\n";
	
	try{

		$is_send = send($info['email_address'], $info['subject'], $info['body']);
		
		// print_r($is_send);
		// echo $is_send;
	
		if($is_send){
			echo "Success...\n";
			updateEmailStatus($info, $dbh);
			
		}else{
			echo "Failed...\n";
			updateEmailFailedStatus($info, $dbh);
		}
	
	}catch(Exception $e){
		
		print_r($e);
		
	}
}


function updateEmailStatus($info, $dbh)
{
	
	if(is_array($info)&& count($info)>0)
	{		
		$sql = "update outgoingemailtable set status = 1, senttime = '".date("Y-m-d H:i:s")."' where id = ".$info['id'];
		$dbh->query($sql);
		//$mysqli = new mysqli("localhost","root","","wbms_db_live");
		//mysqli_query($mysqli, $sql);
		//mysqli_close($mysqli);
		
	}
}

function updateEmailFailedStatus($info, $dbh)
{	
	if(is_array($info)&& count($info)>0)
	{			
		//print_r($info);
		if($info['repeat_time'] == 0){
			$sql = 'update outgoingemailtable set status = 3, repeat_time = 1 where id = '.$info['id'];		
		}elseif($info['repeat_time'] == 1){
			$sql = 'update outgoingemailtable set status = 3, repeat_time = 2 where id = '.$info['id'];		
		}elseif($info['repeat_time'] == 2){
			$sql = 'update outgoingemailtable set status = 4, repeat_time = 3 where id = '.$info['id'];		
		}else{
			$sql = 'update outgoingemailtable set status = 4 where id = '.$info['id'];
		}

		$dbh->query($sql);
		//$mysqli = new mysqli("localhost","root","","wbms_db_live");
		//mysqli_query($mysqli, $sql);
		//mysqli_close($mysqli);		
		
	}	
}

function getOutgoingEmails($dbh)
{
	$result = array();	
	$query = 'select id, subject, body, email_address, repeat_time from outgoingemailtable where status = 3 limit 100';
	foreach ($dbh->query($query) as $row)
    {
		$result[] = array('id' => $row[0], 'subject' => $row[1], 'body' => $row[2], 'email_address' => $row[3], 'repeat_time' => $row[4]);
    }
	/*
	$mysqli = new mysqli("localhost","root","","wbms_db_live");
	$data = mysqli_query($mysqli, $query);
	
	while ($row = $data->fetch_row()) {
		$result[] = array('id' => $row[0], 'subject' => $row[1], 'body' => $row[2], 'email_address' => $row[3]);
	}	
	mysqli_close($mysqli);
	*/
	return $result;
}

function formatMobileNumber($mnumber)
{
	$tmp = '';
	if(preg_match("/^\+/", trim($mnumber)))
	{
		$tmp = $mnumber;
	}
	else if(preg_match("/^00/", trim($mnumber)))
	{
		$tmp = preg_replace("/^0088/", "+88", $mnumber);
	}
	else if(preg_match("/^17/", trim($mnumber)))
	{
		$tmp = '+880'.$mnumber;
	}
	else if(preg_match("/^18/", trim($mnumber)))
	{
		$tmp = '+880'.$mnumber;
	}
	else if(preg_match("/^19/", trim($mnumber)))
	{
		$tmp = '+880'.$mnumber;
	}
	else if(preg_match("/^16/", trim($mnumber)))
	{
		$tmp = '+880'.$mnumber;
	}
	else if(preg_match("/^15/", trim($mnumber)))
	{
		$tmp = '+880'.$mnumber;
	}
	else if(preg_match("/^11/", trim($mnumber)))
	{
		$tmp = '+880'.$mnumber;
	}
	
	else if(preg_match("/^017/", trim($mnumber)))
	{
		$tmp = '+88'.$mnumber;
	}
	else if(preg_match("/^018/", trim($mnumber)))
	{
		$tmp = '+88'.$mnumber;
	}
	else if(preg_match("/^019/", trim($mnumber)))
	{
		$tmp = '+88'.$mnumber;
	}
	else if(preg_match("/^016/", trim($mnumber)))
	{
		$tmp = '+88'.$mnumber;
	}
	else if(preg_match("/^015/", trim($mnumber)))
	{
		$tmp = '+88'.$mnumber;
	}
	else if(preg_match("/^011/", trim($mnumber)))
	{
		$tmp = '+88'.$mnumber;
	}
	else
	{
		return "";
	}
	
	return $tmp;
}

function convertDateStrToNumber($str)
{
	$cl = preg_split("/[ ]+/", $str);
	$mdy = explode("/", $cl[0]);
	$hms = explode(":", $cl[1]);
	$ap = strtolower($cl[2]);
	
	if($hms[0] < 12 && $ap == 'pm')
	{
		$hms[0] += 12;
	}	
	$datetime = mktime((int)$hms[0], (int)$hms[1], (int)$hms[2], (int)$mdy[0], (int)$mdy[1], (int)$mdy[2]);
	return $datetime;
}

?>