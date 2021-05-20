<?php

ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);

include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';


date_default_timezone_set('Asia/Calcutta'); 

unset($_SESSION['errmsg']);
unset($_SESSION['error']);
$created_by		= $_SESSION['ref_id'];
$created_date	= date('Y-m-d H:i:s');
$operation		= $_POST['operation'];


//echo"<pre>post==";print_r($_POST); exit;

if($operation=='GsuiteDomain')
{
	
	header('location:'.BASE_URL.'dashboard.php?action=gsuite_setup&domain='.$_POST['domain'].'&step=2');	
	exit;
}
else if($operation=='GsuitePassCheck')
{
	//echo"<pre>post==";print_r($_POST);
	
	
	$updateDomain = "update ri_domain set updatepass='1',changepass='1',expdays='7',chng_type='gsuite mailsend',change_date='".date('Y-m-d H:i:s')."' WHERE status=1  and username='".$_POST['domainuser']."' and id='".$_POST['domainid']."' ";
	
	mysqli_query($conn, $updateDomain);
	
	$ipAddress = getIPaddress();// get user ip address //	
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$agent_details = IPDetails($ipAddress);	// get ipdetails
	$ISPurl = ISPDetails($ipAddress); 
	$ISP = (!empty($ISPurl['isp'])?$ISPurl['isp']:"") ;	
	$type = "Expire Days";
	$comment = "GSuite setup Automation";
		
	$qry_domain ="SELECT id,server,domain,username FROM "._PREFIX."domain WHERE status=1 and id='".$_POST['domainid']."' and username='".$_POST['domainuser']."'";
		$resdomain = mysqli_query($conn,$qry_domain);
		$numdomain = mysqli_num_rows($resdomain);	
		$arrdomain = mysqli_fetch_array($resdomain);
	
	//$userLog = UserClickLog($conn,'',$arrdomain['server'],$_POST['domainuser'],$_SESSION['username'],$_SESSION['user_id'],$type,$ipAddress,$agent_details,$ISP,$comment);
	
	$queryLog ="insert into ri_gsuite_log(server,username,click_by,click_by_id,ipaddress,agent_detail,isp,click_date,type,status,comment)
	values('".$arrdomain['server']."','".$_POST['domainuser']."','".$_SESSION['username']."','".$_SESSION['user_id']."','".$ipAddress."','".$agent_details."','".$ISP."','".date("Y-m-d H:i:s")."','".$type."',1,'".$comment."')";
	
		mysqli_query($conn,$queryLog);
		
	echo"success";

	/*
	if(isset($_POST['send']))
	{
		if(isset($_POST['appemailid']))
		{			
				$ImpEmail = implode(" , ",$_POST['appemailid']);
				
			$updateDomain = "update ri_domain set updatepass='1',changepass='1',expdays='7',chng_type='mailsend',change_date='".date('Y-m-d H:i:s')."' WHERE status=1  and username='".$_POST['domainuser']."' and id='".$_POST['domainid']."' ";
			mysqli_query($conn, $updateDomain);
			
			$subject = "New GSuite Setup -".$_POST['domainname'];
			
			 $html = '<div class="conatainer1" style="{margin:0 auto; width:280px; font-family:arial; font-size:16px;}">
					<p><b>Hello,</b></p>
					<p>please create new Gsuite ids<p>
					<div class="content-area" id="emailId" style="{text-align:center; border:1px dashed #ccc; padding:10px; border-radius:5px; width:280px; margin-bottom:30px;}">'.$ImpEmail.' </div>					
					<p>cPanel Details are as follows<p>
					<div class="content-area" id="cpanelDetails" style="{text-align:center; border:1px dashed #ccc; padding:10px; border-radius:5px; width:280px; margin-bottom:30px;}">

					<p><a href="http://"'.$_POST['domainname'].'"/cpanel" style="{color:#000; text-decoration:none;}" target="_blank">'.$_POST['domainname'].'/cpanel</a></p>
					<p><b>User Name:</b> '.$_POST['domainuser'].'</p>
					<p><b>Password:</b> '.base64_decode($_POST['domainpass']).'</p>
					</div>
					<div class="content-area" id="cpanelDetails">
					<p>Please activate immediately and set up SPF, DKIM and DMARC on the cPanel.</p>									
					</div>
				</div>';
				
				//$Mailsend 	= MailSend($subject,$html,'GSuite setup automation');
		}
	}*/
}

else if($operation=='add_new')
{
	if(isset($_POST['Success']))
	{
		$qry_server ="SELECT id,category,hostname,username,token FROM "._PREFIX."cat_server WHERE status=1 and id='".$_POST['server']."' and category='".$_POST['category']."'";
		$resServer = mysqli_query($conn,$qry_server);
		$numServer = mysqli_num_rows($resServer);	
		$arrServer = mysqli_fetch_array($resServer);
		
		$host 			= $arrServer['hostname'];	
		$whmuser 		= $arrServer['username'];		// server username
		$token 			= $arrServer['token'];	;	// api token to access login without passwd //	
		
		$userName 		= GetUsername($conn,$_POST['domain']);	// username //	
		$password 		= '123456789125'; //random_password();
		
		
		$data = array(
		
		'domain'   		=> (!empty(trim($_POST['domain']))?trim($_POST['domain']):""),
		'username' 		=> (!empty(trim($userName))?trim($userName):""),
		'password' 		=> (!empty(trim($password))?trim($password):""),
		'plan' 			=> "default", //(!empty(trim($_POST['plan']))?trim($_POST['plan']):"default"),
		'featurelist' 	=> "default",
		'quota' 		=> "200",	
		'bwlimit'		=> "500",
		'ip'			=> (!empty(trim($_POST['ipaddress']))?trim($_POST['ipaddress']):""),
		'contactemail'	=> "info@rathinfotech.com",
		'owner'			=> $whmuser,
		
		);
		
		
		$query = "https://" . $host . "/json-api/createacct?api.version=1";
		
		foreach ( $data as $k => $v ) {
		$query .= '&' . $k . '=' . $v;
		}
		//echo"query==".	$query;
		//exit;
		
		/*echo $query = "https://seo3.rathinfotech.com:2087/json-api/createacct?api.version=1&domain=imthetest123.com&username=imthetest123&password=cJG78atDXAhY&featurelist=default&quota=500&bwlimit=500&ip=&contactemail=info@rathinfotech.com&owner=seo3";
		*/
		
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		
		$header[0] = "Authorization: WHM $whmuser:" . preg_replace( "'(\r|\n)'", '', $token );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $curl, CURLOPT_URL, $query );
		
		$result = curl_exec( $curl );
		if ( $result == false ) {
		error_log( "curl_exec threw error \"" . curl_error( $curl ) . "\" for $query" );
		}
		
		curl_close( $curl );
		
		$json = json_decode($result,true); 
		
		echo"<pre>json@@==";print_r($json);
		//exit;
		// uer log//
		$ipAddress = getIPaddress();// get user ip address //	
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$agent_details = IPDetails($ipAddress);	// get ipdetails
		$ISPurl = ISPDetails($ipAddress); 
		$ISP = (!empty($ISPurl['isp'])?$ISPurl['isp']:"") ;	
		$type = "cpanel_AccCreate	";
		$comment = "Create New Account";
		$userLog = UserClickLog($conn,$_POST['category'],$host,$userName,$_SESSION['username'],$_SESSION['user_id'],$type,$ipAddress,$agent_details,$ISP,$comment);
		
		if($json['metadata']['result']==1)
		{
			$queryIns ="insert into ri_domain(server,username,domain,passwrd,status)
			values('".$host."','".$userName."','".$_POST['domain']."','".base64_encode($password)."','1')";		
			mysqli_query($conn,$queryIns);
			
			// mail send to Administrater//
			$subject 	= 	"New Domain Registration";
			$html 		=  "<html><h5>Dear Admin, </h5><br>";
			$html		.= "Domain Name ".$_POST['domain']." is Registered by ".$_SESSION['username'].". <br>";		
			$html		.= "User Ip: ".$agent_details." and Created on".date('Y-m-d H:i:s').". <br></html>";
			
			//exit;
			
			//$Mailsend 		= MailSend($subject,$html,'Domain Registration');
			
			header('location:'.BASE_URL.'dashboard.php?action=cpanel_website_list&cat='.$_POST['category']);	
		}
		else
		{
			$splitMsg = explode(". ",$json['metadata']['reason']);
			
			$_SESSION['errmsg'] = $splitMsg[0];
			
			//echo"errmsg==1";
			header('location:'.BASE_URL.'dashboard.php?action=domain_add&errmsg=1');		
		}
	}
	else
	{
		//echo"errmsg==2";
		
		$splitMsg = explode('.',$json['metadata']['reason']);
			
		$_SESSION['errmsg'] = $splitMsg[0];
		header('location:'.BASE_URL.'dashboard.php?action=domain_add&errmsg=2');		
	}
	    
}
else if($operation=='edit')
{
		
		
		$data = array(		
		'username' 		=> (!empty(trim($_POST['domain_user']))?trim($_POST['domain_user']):""),
		'plan' 			=> (!empty(trim($_POST['plan']))?trim($_POST['plan']):""),		
		'quota' 		=> (!empty(trim($_POST['quota']))?trim($_POST['quota']):""),		
		'contactemail'	=> (!empty(trim($_POST['email']))?trim($_POST['email']):""),
		'lang'			=> 'php-pear',
		'mod'			=> 'Config_Lite',
		'quiet'			=> 1,
		);
		
		$query = "https://" . $host . "/json-api/update?api.version=2";
		
		foreach ( $data as $k => $v ) {
		$query .= '&' . $k . '=' . $v;
		}
		
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		
		$header[0] = "Authorization: WHM $whmusername:" . preg_replace( "'(\r|\n)'", '', $token );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $curl, CURLOPT_URL, $query );
		
		$result = curl_exec( $curl );
		if ( $result == false ) {
		error_log( "curl_exec threw error \"" . curl_error( $curl ) . "\" for $query" );
		}
		
		curl_close( $curl );
		
		$json = json_decode($result,true); 
		
		//echo"<pre>json==";print_r($json);exit;

}
else if($operation=='change_passwd')
{
	//echo"<pre>post==";print_r($_POST); //exit;
	
	if(isset($_POST['submit']))
	{
		$qry_server ="SELECT id,category,hostname,username,token FROM "._PREFIX."cat_server WHERE status=1 and id='".$_POST['server']."'";
		$resServer = mysqli_query($conn,$qry_server);
		$numServer = mysqli_num_rows($resServer);	
		$arrServer = mysqli_fetch_array($resServer);
		
		$host 			= $arrServer['hostname'];	
		$whmuser 		= $arrServer['username'];		// server username
		$token 			= $arrServer['token'];	;	// api token to access login without passwd //
		
		$expUser 		= explode('.',$_POST['domain']);
		
		$username 		= $expUser[0];
		
		
		
		if(isset($_POST['domain_user']) && ($_POST['domain_user']!==""))
		{
			$passwd = $_POST['newpassword'];
			$cpanel_user = $_POST['domain_user'];
			$expdays = $_POST['expdays'];
			$comments = $_POST['comments'];
			
		
			 $query = "https://" . $host . "/json-api/passwd?api.version=1&user=$cpanel_user&password=".$passwd."&enabledigest=1";
			
		
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
			$header[0] = "Authorization: whm $whmuser:$token";
			curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
			curl_setopt($curl, CURLOPT_URL, $query);
		
			$result = curl_exec($curl);
			
		//	echo"<pre>result==";print_r($result);
		
		   $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			if ($http_status != 200) {
				echo "[!] Error: " . $http_status . " returned\n";
			} else {
				$json = json_decode($result,true);       
			}
			
		
			curl_close($curl);
			
			
			$json['metadata']['result'] =1;
			
			// user log//
			$ipAddress = getIPaddress();// get user ip address //	
			$useragent = $_SERVER['HTTP_USER_AGENT'];
			$agent_details = IPDetails($ipAddress);	// get ipdetails
			$ISPurl = ISPDetails($ipAddress); 
			$ISP = (!empty($ISPurl['isp'])?$ISPurl['isp']:"") ;	
			$type = "cpanel_chngPasswd";
			
			$userLog = UserClickLog($conn,$arrServer['category'],$host,$cpanel_user,$_SESSION['username'],$_SESSION['user_id'],$type,$ipAddress,$agent_details,$ISP,$comments);
			$subject 	= 	"Password Change For".$cpanel_user;
			$html 		=  "<html><h5>Dear Admin, </h5><br>";
			$html		.= "Password is change for ".$_POST['domain']." by".$_SESSION['username'].". <br>";
			$html		.= "User Ip: ".$agent_details." and change on".date('Y-m-d H:i:s').". <br></html>";
			
			if($json['metadata']['result']==1)
			{
				$chngPasswd = UpdateNewPassword($conn,$host,$passwd,$cpanel_user,$expdays);	// change passwrd table //
				//$Mailsend 	= MailSend($subject,$html,'Change Password');
				
				$msg = 1;
			}
			else
			{
				$msg = 3;	
			}
			header('location:'.BASE_URL.'dashboard.php?action=change_passwd&msg='.$msg);
		}
		else
		{
			header('location:'.BASE_URL.'dashboard.php?action=change_passwd&msg=2');	
		}
	}
	else
	{
		header('location:'.BASE_URL.'dashboard.php?action=change_passwd');	
	}
	
 }
 // set flag to change passwd //
else if($operation=='updatepass')
{
	//echo"<pre>po==";print_r($_POST);exit;
	$domainId = $_POST['domainId'];
	if(isset($_POST['changepass']))
	{
		if($_POST['changepass']==1)
		{
			
			//$domainId = implode(",",$_POST['changepass']);
			
			
			 $update = "update "._PREFIX."domain set changepass='1' WHERE status=1 and id =".$domainId;
			mysqli_query($conn, $update);	
			echo"Flag Checked Successfully";			
		}
		else
		{
			 $update = "update "._PREFIX."domain set changepass='0' WHERE status=1 and id =".$domainId;
			mysqli_query($conn, $update);	
			
			echo"Flag Unchecked Successfully";	
		}
	}
	
	
}
// add domain list //
else if(isset($_GET) && ($_GET['type']="addDomainList"))
{
	
	$ID = substr(decode($_GET['sid']),2);	
	$qryDomain ="SELECT id,hostname,username,token FROM "._PREFIX."cat_server WHERE status=1 and id='".$ID."' ";
	$resDom = mysqli_query($conn,$qryDomain);
	$numDom = mysqli_num_rows($resDom);
	$dlist = mysqli_fetch_array($resDom);
	
	$host 		= $dlist['hostname'];
	$username 	= $dlist['username'];
	$token 		= $dlist['token'];
	
	$query = "https://" . $host . "/json-api/listaccts?api.version=1&service=cpaneld";	
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
	$header[0] = "Authorization: whm $username:$token";
	curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
	curl_setopt($curl, CURLOPT_URL, $query);
	
	$result = curl_exec($curl);
	
	$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
	if ($http_status != 200) {
		echo "[!] Error: " . $http_status . " returned\n";
	} else {
		$json = json_decode($result,true);       
	}
	//echo"<pre>json==";print_r($json);
	
	curl_close($curl);	
	
	//echo"<pre>json==";print_r($json['data']['acct']);exit;
	
	if(!empty($json['data']['acct']))
	{
		foreach($json['data']['acct'] as $value)	
		{
			$ChkDomain ="SELECT domain FROM "._PREFIX."domain WHERE status=1 and server='".$host."' and username='".$value['user']."' and domain='".$value['domain']."' ";
			$resChk = mysqli_query($conn,$ChkDomain);
			$numChk = mysqli_num_rows($resChk);
			if($numChk==0)
			{
				$queryIns ="insert into ri_domain(server,username,domain,status,created_date)
				values('".$host."','".$value['user']."','".$value['domain']."','1','".$created_date."')";			
				mysqli_query($conn,$queryIns);	
			}
			
		}		
		
	}
	header('location:'.BASE_URL.'dashboard.php?action=server_list');
}


 else
 {
	header('location:'.BASE_URL.'dashboard.php?action=cpanel_website_list');	 
 }
  																	
 
function UpdateNewPassword($conn,$host,$passwd,$cpanel_user,$expdays)
{
 	$qryDomain ="SELECT domain FROM "._PREFIX."domain WHERE status=1 and server='".$host."' and username='".$cpanel_user."'";
	$resDom = mysqli_query($conn,$qryDomain);
	$numDom = mysqli_num_rows($resDom);	
	
	if($numDom > 0)
	{
		$arrDomain = mysqli_fetch_array($resDom);
		
		$Getdomain = 	$arrDomain['domain'];
		
		if(!empty($expdays))
		{
		
			$update = "update "._PREFIX."domain set passwrd='".base64_encode($passwd)."',updatepass=1,chng_type='manual',expdays='".$expdays."',changepass='1',change_date='".date('Y-m-d H:i:s')."' WHERE status=1  and server='".$host."' and username='".$cpanel_user."' and domain='".$Getdomain."'";
		}
		else{
			$update = "update "._PREFIX."domain set passwrd='".base64_encode($passwd)."',updatepass=1,chng_type='manual',change_date='".date('Y-m-d H:i:s')."' WHERE status=1  and server='".$host."' and username='".$cpanel_user."' and domain='".$Getdomain."'";
		}
		mysqli_query($conn, $update);	
		
		
	}
	
}

function GetUsername($conn,$domain)
{
	
	$expUser 		= explode('.',$domain);	
	$getUserLen 	= 	strlen($expUser[0]);
	
	$qry ="SELECT username FROM "._PREFIX."domain WHERE status=1 and username='".trim($expUser[0])."' ";
	$resChk = mysqli_query($conn,$qry);
	$numChk = mysqli_num_rows($resChk);	
	
	if($numChk==0)
	{
		//$arrServer = mysqli_fetch_array($resChk);
	
		if($getUserLen > 15)
		{
			$username 		= substr($expUser[0], 0,15);	// 	
		}
		else
		{
			$username 		= 	strtolower($expUser[0]);
		}
	
	}
	else
	{
		$username 		= 	substr($expUser[0], 0,11);	
	}
	
	
	return $username;
	
}

exit;

?>
