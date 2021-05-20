
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

//echo"<pre>cat==".$_GET['category'];

if(!empty($_GET['category']))
{
	 $qry_server ="SELECT id,category,hostname,username,token FROM "._PREFIX."cat_server WHERE status=1 and category='".$_GET['category']."'";
	$resServer = mysqli_query($conn,$qry_server);
	$numServer = mysqli_num_rows($resServer);	
	$arrServer = mysqli_fetch_array($resServer);
	
	$host 			= $arrServer['hostname'];		
	$whmuser 		= $arrServer['username'];		// server username
	$token 			= $arrServer['token'];	;	// api token to access login without passwd //
}
	
	
	//echo $query = "https://" . $host . "/json-api/accountsummary?api.version=1"; //&user=$cpanel_user
		
	$query = "https://" . $host . "/json-api/create_user_session?api.version=1&user=$whmuser&service=whostmgrd";  //&user=rathinfo&service=cpaneld
	
	$curl = curl_init();                                     // Create Curl Object.
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);       // Allow self-signed certificates...
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);       // and certificates that don't match the hostname.
	curl_setopt($curl, CURLOPT_HEADER, false);               // Do not include header in output
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);        // Return contents of transfer on curl_exec.
	
	
	$header[0] = "Authorization: WHM $whmuser:" . preg_replace( "'(\r|\n)'", '', $token );

	//echo"qur==". $query;
	
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);         // Set the username and password.
	curl_setopt($curl, CURLOPT_URL, $query);                 // Execute the query.
	$result = curl_exec($curl);

	//echo"<pre>result==";print_r($result); exit;
	
	if ($result == false) {
		error_log("curl_exec threw error \"" . curl_error($curl) . "\" for $query");
														// log error if curl exec fails
	}	
	
	$decoded_response = json_decode( $result, true );
	
	//echo"<pre>url==";print_r($decoded_response); //exit;
	
	$targetURL = $decoded_response['data']['url'];	
	// add click by user logs //
	
	header('Location: '.$targetURL);

 
?>
