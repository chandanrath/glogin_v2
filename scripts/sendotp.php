<?php

session_start();
date_default_timezone_set('Asia/Kolkata');
ini_set('max_execution_time', 0);

include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

$currDateTime 	= date("Y-m-d H:i:s");



$ipaddress = getIPaddress();		// get user ip address GIEGMPL//
	
$useragent = $_SERVER['HTTP_USER_AGENT'];
$agent_detail = IPDetails($ipaddress);
$ISPurl = ISPDetails($ipaddress);
$ISP = (!empty($ISPurl['isp'])?$ISPurl['isp']:"") ;

echo"<pre>";print_r($_POST); //exit;


if($_POST['hidtype']=="sendOTP")
{
	// check set user's cookies //
		
	$chkCookiesTime = chkCookiesTime($conn,$_POST['loginemail']);	
	
	
	if(($chkCookiesTime <= 3) && ($chkCookiesTime!="error"))
	{
		
		$errorog ="insert into ri_error_log(email,remote_addr,types,pages,message,log_date)
		values('".$_POST['loginemail']."','".$ipaddress."','Keywords Login','Keywords Generator Login','OTP Already Verified','".date("Y-m-d H:i:s")."')";					
		mysqli_query($conn,$errorog);
		

		$_SESSION['email']			= $_POST['loginemail'];	
		
		$_SESSION['role_active'] 	= 1;	
		
		header('location:'.BASE_URL.'keyword/dashboard.php?action=keywords');
			
	}
	else
	{
		// get manager mobile //
		$getMobile = GetManagerMobile($conn,$_POST['loginemail']);
		
		$mobileAuth = MOBILE_AUTH;
		
		$mobile="91".$getMobile;
		
		//$mobile="918286625132";
		
		//$mobile="919967355303";
		
		$otp= rand(1000,9999);  //Generate random number as OTP					
		
					
		$msg="Your OTP is ".$otp." for emailid  ".$_POST['loginemail']." Login. Powered by RATH Infotech.";   //Your Custom Message
		//echo"https://control.msg91.com/api/sendotp.php?authkey=".$mobileAuth."&mobile=".$mobile."&message=".$msg."&sender=RATHAPP&otp=".$otp;
		
		//exit;
		
		//$postUrl = htmlspecialchars_decode("https://control.msg91.com/api/sendotp.php?authkey=".$mobileAuth."&mobile=".$mobile."&message=".$msg."&sender=RATHAPP&otp=".$otp,ENT_NOQUOTES);
		
	
		
		$ch = curl_init();
		$curlConfig = array(
			CURLOPT_URL            => "https://control.msg91.com/api/sendotp.php?authkey=".$mobileAuth."&mobile=".$mobile."&message=".$msg."&sender=RATHAPP&otp=".$otp,
			CURLOPT_POST           => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
		   // CURLOPT_POSTFIELDS     => $post_array
		);
		curl_setopt_array($ch, $curlConfig);

		$result = curl_exec($ch);
		curl_close($ch);

		$response = json_decode($result,true);
		
			if($response['type']=="success")
			{
				
				$_SESSION["emailid"] = $_POST['loginemail'];	
				
				header('location:'.BASE_URL.'login.php?otp=sent&email='.$_POST['loginemail']);
			}
			else
			{
				//$_SESSION["otpmsg"] = "OTP sent Fail";	
				header('location:'.BASE_URL.'login.php?otp=fail');	
			}
		
	}
	
	

}

else if($_POST['hidtype']=="verifyOTP")
{
	unset($_SESSION['otpmsg']);
	

	if(isset($_POST['verifyotp']) && $_POST['verifyotp'] != "")
	{
		// get manager mobile //
		$getMobile = GetManagerMobile($conn,$_POST['loginemail']);
		
		//$mobileno="918286625132";
		
	    $mobile="91".$getMobile;
		
		$mobileAuth 	= MOBILE_AUTH;
		$recieved_otp 	= $_POST['verifyotp'];
		
		$postData1 = array(
				'authkey' => $mobileAuth,
				'mobile' => trim($mobile),
				'otp' => $recieved_otp,
				
			);
			
		//echo"<pre>postData1==";print_r($postData1); 
		
		
		echo"vet==".$verifyOTPMsg 	= MobileVerifyOTP($postData1);		// mobile num verification//
		
		//exit;
		
		
		if($verifyOTPMsg=="success")
		{
			
			$errorog ="insert into ri_error_log(email,remote_addr,types,pages,message,log_date)
			values('".$_POST['loginemail']."','".$ipaddress."','OTP Verification','Keywords Generator Login','OTP Verified successfully','".date("Y-m-d H:i:s")."')";					
			mysqli_query($conn,$errorog);
				
			$_SESSION['email'] 	= $_POST['loginemail'];	//user email ;
			
			//$_SESSION['role'] 		= $UserRow['role'];	//user role ;
			$_SESSION['role_active'] 	= 1;	//$UserRow['role_active'];	//user role ;
			
			
			$cookiesId = md5($_POST['loginemail'].'#');
			
			setcookie('name',$_SESSION['email'], time() + 3*24*60*60,'/');
			setcookie('email',$_SESSION['email'], time() + 3*24*60*60,'/');
			//setcookie('role',$_SESSION['role'], time() + 12*60*60,'/');
			setcookie('role_active',$_SESSION['role_active'], time() + 3*24*60*60,'/');
			
			$query ="insert into ri_setuser_cookie(email,cookieid,ipaddress,status,outers,login_date)
				values('".$_SESSION['email']."','".$cookiesId."','".$ipaddress."','1',1,'".date("Y-m-d H:i:s")."')";
			mysqli_query($conn,$query);	
				
			
			header('location:'.BASE_URL.'keyword/dashboard.php?action=keywords');
							
				
		}
		else
		{			
			 $_SESSION['otpmsg'] = "Please enter Correct OTP ";
			
			$errorog ="insert into ri_error_log(email,remote_addr,types,pages,message,log_date)
			values('".$_POST['loginemail']."','".$ipaddress."','OTP Verification','Keywords Generator Login OTP','Please enter Correct OTP','".date("Y-m-d H:i:s")."')";					
			mysqli_query($conn,$errorog);
			header('location:'.BASE_URL.'login.php?otp=fail&email='.$_POST['loginemail']);	
			exit;
		}
	}
	else
	{
			$_SESSION['otpmsg'] = "Please enter Correct OTP ";
			
			$errorog ="insert into ri_error_log(email,remote_addr,types,pages,message,log_date)
			values('".$_POST['loginemail']."','".$ipaddress."','OTP Verification','Keywords Generator Login OTP','Please enter Correct OTP','".date("Y-m-d H:i:s")."')";					
			mysqli_query($conn,$errorog);
			header('location:'.BASE_URL.'login.php?otp=fail&email='.$_POST['loginemail']);																																																																																				
			exit;
	}
}

function GetManagerMobile($conn,$emailid)
{
	$ChkSql = "SELECT id,email,
	(SELECT mobile AS onmobile FROM ri_users u2 WHERE u2.id=u1.report_to AND u2.status=1) AS mobile
	from ri_users u1
	where status = 1 AND email='".trim($emailid)."' ";
	$res1 = mysqli_query($conn,$ChkSql);
	$Num = mysqli_num_rows($res1);
	if($Num > 0)
	{
		$data = mysqli_fetch_array($res1);
		$mobile = $data['mobile'];
	}
	else{
		$mobile = '9967355303';
	}
	return $mobile;
}


function MobileVerifyOTP($postData)
{	

	$curl = curl_init();
	//echo "https://control.msg91.com/api/verifyRequestOTP.php?authkey=".$postData['authkey']."&mobile=".trim($postData['mobile'])."&otp=".$postData['otp'];

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://control.msg91.com/api/verifyRequestOTP.php?authkey=".$postData['authkey']."&mobile=".trim($postData['mobile'])."&otp=".$postData['otp'],
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => $postData,				 
	  CURLOPT_HTTPHEADER => array(
		"content-type: application/x-www-form-urlencoded"
	  ),
	));
	
	$response = curl_exec($curl);
	$err = curl_error($curl);
	
	curl_close($curl);
	
	$result=json_decode($response,true);

	//echo"<pre>res==";print_r($result);
	return $result['type'];
}

function chkCookiesTime($conn,$emailid)
{
	 $ChkSql = "SELECT id,login_date from "._PREFIX."setuser_cookie where status = 1 and outers=1 AND  email='".trim($emailid)."' order by id desc limit 1";		
			
	$res1 = mysqli_query($conn,$ChkSql);
	$Num = mysqli_num_rows($res1);
	if($Num > 0)
	{
		$data = mysqli_fetch_array($res1);
		
			
		$currDate = date("Y-m-d H:i:s");		// date-1
		$loginDate = $data['login_date'];		// date-2	
		
		
		//$seconds = strtotime($currDate) - strtotime($loginDate);
		//$hours = round($seconds / 60 / 60);
		
		$earlier = new DateTime($currDate);
		$later = new DateTime($loginDate);

		$DateDiff = $later->diff($earlier)->format("%a");
		
		$hours= $DateDiff;
		
		if($hours==0)
		{
			$hours=1;
		}
		
	}
	else{
		$hours = "error";
	}
	
	return $hours;

	
}
exit;


?>