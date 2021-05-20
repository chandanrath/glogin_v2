
<?php
ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);


include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

date_default_timezone_set('Asia/Calcutta'); 

$created_date		= date('Y/m/d H:i:s');

//echo"<pre>post==";print_r($_POST);

$cpanel_user 	= (!empty($_GET['userName'])?$_GET['userName']:""); //under reseller
$server 		= $_GET['serv']; //server name //
$category 		= $_GET['cat']; //category //


	$qry_server ="SELECT id,category,hostname,username,token FROM "._PREFIX."cat_server WHERE status=1 and id='".$server."' and category='".$category."'";
	$resServer = mysqli_query($conn,$qry_server);
	$numServer = mysqli_num_rows($resServer);	
	$arrServer = mysqli_fetch_array($resServer);
	
	
	$host 			= $arrServer['hostname'];	
	$whmuser 		= $arrServer['username'];		// server username
	$token 			= $arrServer['token'];	;	// api token to access login without passwd //
	
	// user click log //
	
	$ipAddress = getIPaddress();// get user ip address //	
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$agent_details = IPDetails($ipAddress);	// get ipdetails
	$ISPurl = ISPDetails($ipAddress); 
	$ISP = (!empty($ISPurl['isp'])?$ISPurl['isp']:"") ;	
	$type = "cpanel-removeAcc";
	$userLog = UserClickLog($conn,$category,$host,$cpanel_user,$_SESSION['username'],$type,$ipAddress,$agent_details,$ISP);
	
	
	$subject 	= 	"Remove Account of".$cpanel_user;
	$html 		=  "<html><h5>Dear Admin, </h5><br>";
	$html		.= "Remove Account for ".$cpanel_user." by".$_SESSION['username'].". <br>";
	$html		.= "User Ip: ".$agent_details." and change on".date('Y-m-d H:i:s').". <br></html>";
	
	// The user on whose behalf the API call runs.
	
	
	$query = "https://" . $host . "/json-api/removeacct?api.version=1&user=$cpanel_user";	
	
	$curl = curl_init();                                     // Create Curl Object.
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);       // Allow self-signed certificates...
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);       // and certificates that don't match the hostname.
	curl_setopt($curl, CURLOPT_HEADER, false);               // Do not include header in output
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);        // Return contents of transfer on curl_exec.	
	$header[0] = "Authorization: WHM $whmuser:" . preg_replace( "'(\r|\n)'", '', $token );	
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);         // Set the username and password.
	curl_setopt($curl, CURLOPT_URL, $query);                 // Execute the query.
	$result = curl_exec($curl);
	
	if ($result == false) {
		error_log("curl_exec threw error \"" . curl_error($curl) . "\" for $query");
														// log error if curl exec fails
	}
	
	
	$decoded_response = json_decode( $result, true );
	curl_close($curl);
	
	//echo"<pre>url==";print_r($decoded_response);
	
	if($decoded_response['metadata']['result']==1)
	{
		// Account remove //
		$updateDomain = "update ri_domain set status='2' WHERE server='".$host."' and username='".$cpanel_user."'";
		mysqli_query($conn, $updateDomain);
		
		$Mailsend 	= MailSend($subject,$html,'');
	}

	header('location:'.BASE_URL.'dashboard.php?action=cpanel_website_list');	
exit;
 
?>
