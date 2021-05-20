<?php
ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);


include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';
//include_once '../classes/Class_Database.php';
//include_once '../classes/Class_encrypt.php';
//include_once '../classes/Class_emailManager.php';


$database=new Database;
$convert=new Encryption;
//$emailManager=new EmailManager;

date_default_timezone_set('Asia/Calcutta'); 


if($_POST['type']=="mcheck")
{
	//echo"<li>cpde=";print_r($_POST);
	 $SQL_QUERY = "SELECT username,decypt_pwd from "._PREFIX."users where username = '".$_POST['email']."' and status=1";
	$database->select($SQL_QUERY);
	$arr=$database->result;
	 $count = count($arr);


	if($count > 0)
	{
		if($_SESSION['captcha_code']==$_POST['code'])
		{
			
			$data = array(					
			'username' => $arr[0]['username'],
			'password' => $arr[0]['decypt_pwd'],
			);

			
			
			include_once '../phpmailer/class.phpmailer.php';

			$mail = new PHPMailer();
			$mail->IsSMTP();
			//$mail->SMTPAuth   = true; 
			$mail->IsHTML(true);	
			$mail->Host       = "ssl://bh-33.webhostbox.net"; 
			$mail->SMTPDebug  = 2;                    
			$mail->Port       = 465; 
			$mail->timeout	=	5;
			$mail->Username   = "enquiry@innovacconsulting.com"; 
			$mail->Password   = "c6ngc0c8li";
			//$mail->Subject =  $subject;
			$mail->Subject =  "this si test";
			$mail->From = "info@innovacconsulting.com";
			$mail->FromName = "Osource Bugtracker";
			$mail->AddReplyTo("chandancse58@gmail.com", "Osource Bugtracker");
			$sent_to = "chandancse58@gmail.com";
			$mail->AddAddress($sent_to, $sent_to);
			$mail->AddAddress("chandancse58@gmail.com","chadnan kumar");	
			//$mail->MsgHTML($content);
			//echo"<pre>mail==".$content;
			$body = "<body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:12px; color:#666666;\">\n<table width=\"100%\" height=\"auto\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" ><td></td></tr><tr><td><p style='text-align:justify;'>
				Dear chandan ,
				</p><p>
				Below is the summary of Today's Due Tasks. Please Co-ordinate with Concern Team member and Complete the Task by EOD.   
				</p>						
				</td></tr></table><br><br> </body></html></html>\n";
	//echo"vo==".$body;

	$mail->MsgHTML($body);
				
			if(!$mail->Send()) {
			  echo $mail->ErrorInfo;
			} else {
			  echo $mail->ErrorInfo;;
			}

			 echo"success";

		} else {
			echo"notmatch";
		}
	}else{
		echo"null";
	}
	

	//if($emailManager->sendEmail($data,'new_user')) // email
	
	//{
		
	 
	// }
	
	
}
if($_POST['type']=="acheck")
{
	 $SQL_QUERY = "SELECT email from "._PREFIX."advertiser where email = '".trim($_POST['user_email'])."' and status=1";
	$result= mysql_query($SQL_QUERY);

	if(GET_NUM_ROWS($SQL_QUERY)>0)
	{
		echo"1";

	}else{
		echo"0";
	}

}

	



?>