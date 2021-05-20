
 <?php 

ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);

include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

	$created_date	= date('Y-m-d H:i:s');
	//$otpval 		= $_POST['otpval'];
	
	//echo"<pre>post==";print_r($_POST);
	
	//exit;
	$mobileAuth = MOBILE_AUTH;
	
	$mobile='919967355303'; //Concatenate mobile number and country code.
	
	$otp= rand(1000,9999);  //Generate random number as OTP
	
	 
	 $OTPmsg = $otp." is OTP for login to RATH app for ".$email;
	 $OTPmsg .=" IP: http://ip-api.com/line/".$ipadd;
		
	//Hit the API
	 $postUrl = htmlspecialchars_decode("https://control.msg91.com/api/sendotp.php?authkey=".$mobileAuth."&mobile=".$mobile."&message=".$OTPmsg."&sender=RATHINFO&otp=".$otp,ENT_NOQUOTES);
	 
	
	
	$contents = file_get_contents($postUrl);    
	
	$result=json_decode($contents,true);		  
	
	
	  if($result['type']=="success")
	  {
		  echo "OTP Resent on Registered Mobile";
		
	  }
	  else
	  {
		  echo "Something Went Wrong.Please Resend Again!";
		
	  }
	
    ?>
