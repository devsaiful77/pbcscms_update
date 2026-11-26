<?php
ini_set("always_populate_raw_post_data", "On");
header("Content-Type: text/plain; charset=ISO-8859-1\r\n");
date_default_timezone_set("Asia/Dhaka");
echo "The time is " . date("Y-m-d h:i:sa");
echo "\n\nStarting Consumer.....\n";

//Local Connection
$hostname='127.0.0.1';
$username='root';
$password='';
$dbname='dbvocmtb';
$port='port=3306';

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

$dbh = null;

try {
    $dbh = new PDO("mysql:host=$hostname;$port;dbname=$dbname",$username,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    //echo $e->getMessage();
}
		
sendEscalationMail($dbh);	
//$dbh->query('KILL CONNECTION_ID()');
$dbh = null;


function sendEscalationMail($dbh)
{	
	global $client;
	set_time_limit(1080);
	echo "\nTrying to send SMS to Customer...\n";	
	$callTime = time();
	//echo 'Continue';
	$result = getOutgoingMessages($dbh);
	// echo '<pre>';
	// print_r($result); die;
	if(is_array($result)&& count($result)>0)
	{	
		foreach($result as $info)
		{
			//echo "<pre>";
			print_r($info); 

			if (!empty($info)) {
                
                $headers_get[] = 'Content-Type: application/x-www-form-urlencoded';
                //$curl_URL_Param = "http://sss.bkkb.gov.bd/sendAPIdata?apiKey=".$token."&mobile_no=".$request->mobile_no; 

                //$info['cmnt_time']=(!empty($info['cmnt_time']))?$info['cmnt_time']:$info['ref_date'];
                //$info['cmnt_ref']=(!empty($info['cmnt_ref']))?$info['cmnt_ref']:$info['ref_ref'];
                
                // $curl_URL_Param = "https://voc.mutualtrustbank.com/voc/sendEscalationEmail?user=escmailusr&passwd=Pwd@!123&ref_ref=".$info['ref_ref']; //New Live URL
                //$curl_URL_Param = "http://10.45.6.58:5306/cscms/sendEscalationEmail?user=escmailusr&passwd=Pwd@!123&ref_ref=".$info['ref_ref']; //Live URL
                $curl_URL_Param = "http://192.168.45.22:5306/uatvoc/sendEscalationEmail?user=escmailusr&passwd=Pwd@!123&ref_ref=".$info['ref_ref']; //Test URL                         
                $curl_get = curl_init();
                curl_setopt($curl_get, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_get, CURLOPT_CONNECTTIMEOUT, 10);
        		curl_setopt($curl_get, CURLOPT_TIMEOUT, 10);
                curl_setopt($curl_get, CURLOPT_POST, false);
                curl_setopt($curl_get, CURLOPT_URL, $curl_URL_Param);       
                //curl_setopt($curl_get, CURLOPT_HTTPHEADER, $headers_get);
                curl_setopt($curl_get, CURLOPT_FOLLOWLOCATION, true); 
                curl_setopt( $curl_get, CURLOPT_SSL_VERIFYPEER, false);         
                curl_setopt($curl_get, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );          
                $curl_get_response = curl_exec($curl_get);      
                curl_close($curl_get);
                //echo $curl_URL_Param;

                print_r($curl_get_response)."<br/><br/>";
                //$json = json_decode($curl_get_response, true);
                //prd($json);
                //echo 'phone ---- '.$json['phone']; die;                                  
                
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

function getOutgoingMessages($dbh)
{
	//$ts = time();		
	$query = "SELECT ref.reference_number AS ref_ref FROM `references` ref where (ref.reference_number like 'S%' or ref.reference_number like 'C%') and ref.id > 10 and ref.callback_status <> 1 and ref.form_status not in (-1,-2,11,12)";

	$result = array();
	
	foreach ($dbh->query($query) as $row)
    {
		$result[] = array('ref_ref' => $row[0]);
    }
	
	return $result;
}

?>
