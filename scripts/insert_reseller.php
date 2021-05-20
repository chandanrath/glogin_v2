<?php
//phpinfo();
ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);


include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

date_default_timezone_set('Asia/Calcutta'); 

$created_date	= date('Y/m/d H:i:s');

$operation		= $_POST['operation'];


if($operation=="add_new")
{
	echo"<pre>AD==";print_r($_POST);exit;
	
	if(isset($_POST['regserver1']) && ($_POST['regserver1']!=""))
	{
		$appNs1 = "&ns=".$_POST['regserver1'];	
	}
	if(isset($_POST['regserver2']) && ($_POST['regserver2']!=""))
	{
		$appNs2 = "&ns=".$_POST['regserver2'];	
	}
	echo "https://httpapi.com/api/domains/register.xml?auth-userid=594871&api-key=9Tq425RlWSE21EbuEulrnfCRXbH4IFH5&domain-name=".trim($_POST['domain'])."&years=1".$appNs1.$appNs2."&customer-id=12755426&reg-contact-id=43175603&admin-contact-id=43175603&tech-contact-id=43175603&billing-contact-id=43175603&invoice-option=NoInvoice";
	//exit;
	$ch = curl_init();
	// for add domain //
	
	curl_setopt($ch, CURLOPT_URL,"https://httpapi.com/api/domains/register.xml");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,
	"auth-userid=594871&api-key=9Tq425RlWSE21EbuEulrnfCRXbH4IFH5&domain-name=".trim($_POST['domain'])."&years=1".$appNs1.$appNs2."&customer-id=12755426&admin-contact-id=43175603&reg-contact-id=43175603&tech-contact-id=43175603&billing-contact-id=43175603&invoice-option=NoInvoice"); 
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$server_output = curl_exec ($ch);	
	
	echo"<pre>op==";print_r($server_output);
	
	curl_close($ch);	
	
	// user log //
	$ipAddress = getIPaddress();		// get user ip address //	
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$agent_details = IPDetails($ipAddress);	
	$ISPurl = ISPDetails($ipAddress);
	$ISP = (!empty($ISPurl['isp'])?$ISPurl['isp']:"") ;
	$type= "domain-reg";
	$category = "";
	$host = "";
	$userLog = UserClickLog($conn,$category,$host,$_POST['domain'],$_SESSION['username'],$type,$ipAddress,$agent_details,$ISP);
	
	header('location:'.BASE_URL.'dashboard.php?action=reseller_register&regmsg=1');	
 
}
else if($operation=="edit")
{
	echo"<pre>edit==";print_r($_POST); 
	
	$AppNs1 = (!empty($_POST['server1'])?"&ns=".$_POST['server1']:"");
	$AppNs2 = (!empty($_POST['server2'])?"&ns=".$_POST['server2']:"");
	$AppNs3 = (!empty($_POST['server3'])?"&ns=".$_POST['server3']:"");
	$AppNs4 = (!empty($_POST['server4'])?"&ns=".$_POST['server4']:"");	
	
	echo"id==". $orderId = GetOrderId($_POST['domain']);	
	
	echo"<li>path==". $path = "https://httpapi.com/api/domains/modify-ns.json?auth-userid=594871&api-key=9Tq425RlWSE21EbuEulrnfCRXbH4IFH5&domain-name=".trim($_POST['domain']).$AppNs1.$AppNs2.$AppNs3.$AppNs4;		//"&order-id=".trim($orderId).
	
	
	//exit;
	
	$ch = curl_init();
	// for add domain  &order-id=43175603//
	
	curl_setopt($ch, CURLOPT_URL,"https://httpapi.com/api/domains/modify-ns.json");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,
	"auth-userid=594871&api-key=9Tq425RlWSE21EbuEulrnfCRXbH4IFH5&domain-name=".trim($_POST['domain'])."&order-id=".trim($orderId).$AppNs1 .$AppNs2 .$AppNs3 .$AppNs4);
	
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$server_output = curl_exec ($ch);
	
	$json = json_decode($server_output,true); 
	
	echo"<pre>server_output==";print_r($server_output);
	echo"<pre>json==";print_r($json);
	
	curl_close($ch);   
	
	// user log //
	$ipAddress = getIPaddress();		// get user ip address //	
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$agent_details = IPDetails($ipAddress);	
	$ISPurl = ISPDetails($ipAddress);
	$ISP = (!empty($ISPurl['isp'])?$ISPurl['isp']:"") ;
	$type= "domain-upd";
	$category = "";
	$host = "";
	$userLog = UserClickLog($conn,$category,$host,$_POST['domain'],$_SESSION['username'],$type,$ipAddress,$agent_details,$ISP);
	
	if($json['status']=="Success")
	{
		$msg=1;	
	}
	else
	{
		$msg=0;	
	}
	echo"msg==". $msg;
	
	//header('location:'.BASE_URL.'dashboard.php?action=reseller_update&domain='.$_POST['domain'].'&updmsg='.$msg);	
	
	
  
 }



function FetchNameServer($domain)
{

	 $url = "https://www.whoisxmlapi.com/whoisserver/WhoisService?apiKey=at_GtwH8Ss4vynMZicYr6X0eMgG4xkTw&outputFormat=json&domainName=".$domain;	
	
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url ); //Url together with parameters
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Return data instead printing directly in Browser
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 7); //Timeout after 7 seconds
	curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	
	$result = curl_exec($ch);
	curl_close($ch);
	
	if(curl_errno($ch))  //catch if curl error exists and show it
	{
	echo 'curl-error';
	}
	else
	{
	$json = json_decode($result,true); 
	}
	$arrData = $json['WhoisRecord']['nameServers']['hostNames'];
	return $arrData;
	//echo"<pre>FETCH==";print_r($json['WhoisRecord']['nameServers']['hostNames']);
	
	//"<li>host===".$json['WhoisRecord']['nameServers']['hostNames'];
}

function GetOrderId($domain)
{
	$url = 'https://httpapi.com/api/domains/details-by-name.json';

    $data = array (
        'auth-userid' => '594871',
		'api-key' => '9Tq425RlWSE21EbuEulrnfCRXbH4IFH5',
		'domain-name' => trim($domain),
		'options' => 'OrderDetails',
        );
        
	$params = '';
	
	foreach($data as $key=>$value)
	{
		$params .= $key.'='.$value.'&';
	}
	 
	$params = trim($params, '&');
	
	echo"url==".$url.'?'.$params;
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url.'?'.$params ); //Url together with parameters
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Return data instead printing directly in Browser
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 7); //Timeout after 7 seconds
	curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	
	$result = curl_exec($ch);
	curl_close($ch);
	
	if(curl_errno($ch))  //catch if curl error exists and show it
	{
	echo 'Curl error: ' . curl_error($ch);
	}
	else
	{
	$json = json_decode($result,true); 
	}
	
	echo"<pre>result@==";print_r($result);
	
	return $orderId = $json['orderid'];
  
}

?>
