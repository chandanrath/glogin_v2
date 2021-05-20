<?php
include_once 'connect.php';
include_once 'define.php';


include_once '../phpmailer/class.phpmailer.php';


date_default_timezone_set('Asia/Calcutta');



function encodes($value){ 
	if(!$value){return false;}
	$text = $value;
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
	return trim($this->safe_b64encode($crypttext)); 
}

function decodes($value){
	if(!$value){return false;}
	$crypttext = $this->safe_b64decode($value); 
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	$decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
	return trim($decrypttext);
}
function encode($data)
{
	$data = "#@".$data;
	return base64_encode(base64_encode(base64_encode($data)));
}
function decode($data)
{
	//echo"<li>dsw==".$data = substr($data,2);
	return base64_decode(base64_decode(base64_decode($data)));
}


function delete_directory($dirname)
{
   if (is_dir($dirname))
      $dir_handle = opendir($dirname);
   if (!$dir_handle)
      return false;
   while($file = readdir($dir_handle)) {
     if ($file != "." && $file != "..") {
        if (!is_dir($dirname."/".$file))
            unlink($dirname."/".$file);
         else
             delete_directory($dirname.'/'.$file);    
      }
   }
   closedir($dir_handle);
   rmdir($dirname);
   return true;
}



function SEND_MAIL($to, $from, $subject, $text)
{
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";			
	$headers .= "From:" .$from;
	$headers .= "<br>To:" .$to;
	$send = mail($to,$subject,$text,$headers);
}

function TIME_TO_SEC($time) 
{
    $hours = substr($time, 0, -6);
    $minutes = substr($time, -5, 2);
    $seconds = substr($time, -2);

    return $hours * 3600 + $minutes * 60 + $seconds;
}

function SEC_TO_TIME($seconds) 
{
    $hours = floor($seconds / 3600);
    $minutes = floor($seconds % 3600 / 60);
    $seconds = $seconds % 60;

    return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
} 

function send_sms($mobile_no,$sms_content)
{
		$request="http://hapi.smsapi.org/SendSMS.aspx?UserName=".SMS_USERNAME."&password=".SMS_PASSWORD."&MobileNo=91".$mobile_no."&SenderID=".SMS_SENDER_ID."&CDMAHeader=".SMS_CDMA_HEADER."&Message=".urlencode($sms_content);
		$response = explode(' ',file_get_contents($request));
		if($response[0]=='MessageSent')
		{
			echo 'Message Sent Successfully';	
		}
}

function get_num_days($month)
	{
		switch($month)
		{
			case "1";
					return "31";
			break;
			case "2";
					return "28";
			break;

			case "3";
					return "31";
			break;

			case "4";
					return "30";
			break;

			case "5";
					return "31";
			break;
			case "6";
					return "30";
			break;
			case "7";
					return "31";
			break;
			case "8";
					return "31";
			break;
			case "9";
					return "30";
			break;
			case "10";
					return "31";
			break;
			case "11";
					return "30";
			break;
			case "12";
					return "31";
			break;
		}
	}
function date_cmp($dt1,$dt2)
{
		$date1 = $dt1; 
		$date2 = $dt2; 	
		$mfg_date = strtotime($date1); 
		$dispatch_date = strtotime($date2); 
		if ($dispatch_date > $mfg_date) 
		{ 
			return "YES"; 
		} 
		else
		{ 
			return "NO"; 
		} 	
}
// css for mailers


function resize_image1($file,$ext, $w, $h, $path) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
	if($ext=="jpg" || $ext=="JPG" || $ext=="jpeg" || $ext=="JPEG")
	{
		$src = imagecreatefromjpeg($file);
		
	}
	else if($ext=="gif" || $ext=="GIF")
	{
		$src = imagecreatefromgif($file);
		
	}
	else if($ext=="png" || $ext=="PNG")
	{
		$src = imagecreatefrompng($file);
		
	}
	else
	{
		echo "Incorrect file extentstion";	
	}
	$newwidth = $w;
	$newheight = $h;
    $dst = imagecreatetruecolor($newwidth, $newheight);
	imagecolortransparent($dst, $white);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	if($ext=="jpg" || $ext=="JPG" || $ext=="jpeg" || $ext=="JPEG")
	{
		imagejpeg($dst,$path);
		
	}
	else if($ext=="gif" || $ext=="GIF")
	{
		imagegif($dst,$path);
		
	}
	else if($ext=="png" || $ext=="PNG")
	{
		
		imagepng($dst,$path);
		
	}
	else
	{
		echo "Incorrect file extentstion";	
	}
    //return $dst;
	return 1;
}


function encrypt_data($data)
{
	$data = "#@".$data;
	return base64_encode(base64_encode(base64_encode($data)));
}
function decrypt_data($data)
{
	return base64_decode(base64_decode(base64_decode($data)));
}

	
function sendEmail($data,$template)
{
	
		$template_data = getEmailContent($template);

		$setting = getEmailSetting();
				
		if($template_data){

			$subject = html_entity_decode($template_data['subject'], ENT_QUOTES, 'UTF-8');
			$content = html_entity_decode($template_data['description'], ENT_QUOTES, 'UTF-8');
			
			foreach($data as $key => $value){

				
				
				$subject = str_replace("[$key]", $value, $subject);
				$content = str_replace("[$key]", $value, $content);
			}
		}

	
		$mail = new PHPMailer();
		//$mail->IsSMTP();
		//$mail->SMTPAuth		 = true; 
		$mail->IsHTML(true);	
		$mail->Host			= $setting['hostname']; 
		$mail->SMTPDebug	= 2;                    
		$mail->Port			= $setting['port']; 
		$mail->timeout		=	5;
		$mail->Username		= $setting['smtp_username'];
		$mail->Password		= $setting['smtp_password'];
		$mail->Subject		=  $subject;
		$mail->From			= $setting['sender_email'];
		$mail->FromName		= $setting['sender_name'];
		$mail->AddReplyTo($setting['sender_email'], $setting['sender_name']);
		$sent_to			= $data['username'];
		$mail->AddAddress($sent_to, $data['name']);
		$mail->AddAddress("chandancse58@gmail.com","chadnan kumar");	
		
		//echo"bb==".$content;
		$mail->MsgHTML($content);

		
		

		if(!$mail->Send())
		{
			$msg =  "fail";
			
		}else{
			$msg = "success";
		}
		return $msg;
}


function getIPaddress()
{
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    	$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}	
	
	$ip = explode(',',$ip);
	return $ip[0];
}
function IPDetails($ipaddress)
{
	
	$agentContent = htmlspecialchars_decode("https://www.iplocate.io/api/lookup/".$ipaddress,ENT_NOQUOTES); //,ENT_NOQUOTES
	//$agentContent = htmlspecialchars_decode("http://api.ipstack.com/".$ipaddress."?access_key=04c5dd1d2dff879a0f9341155d406ec6",ENT_NOQUOTES); 
	return file_get_contents($agentContent); 
}
function ISPDetails($ipaddress)
{
	return $query = unserialize(file_get_contents('http://ip-api.com/php/'.$ipaddress));
	
	
	//$agentContent = htmlspecialchars_decode("https://www.iplocate.io/api/lookup/".$ipaddress,ENT_NOQUOTES); //,ENT_NOQUOTES
	//$agentContent = htmlspecialchars_decode("http://ip-api.com/php/".$ipaddress,ENT_NOQUOTES); 
	//return file_get_contents($agentContent); 
}

function basedomain( $str = '' )
{
    // $str must be passed WITH protocol. ex: http://domain.com
    $url = @parse_url( $str );
    if ( empty( $url['host'] ) ) return;
    $parts = explode( '.', $url['host'] );
    $slice = ( strlen( reset( array_slice( $parts, -2, 1 ) ) ) == 2 ) && ( count( $parts ) > 2 ) ? 3 : 2;
    return implode( '.', array_slice( $parts, ( 0 - $slice ), $slice ) );
}
function makeurl($text)
{
  // replace non letter or digits by -
  $text = preg_replace('#[^\\pL\d]+#u', '-', $text);

  // trim
  $text = trim($text, '-');

  // transliterate
  if (function_exists('iconv'))
  {
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  }

  // lowercase
  $text = strtolower($text);

  // remove unwanted characters
  $text = preg_replace('#[^-\w]+#', '', $text);

  if (empty($text))
  {
    return '';
  }

  return $text;
 }
 
 function getAuth($conn,$role,$perm)
 {
	$sql1="select id from ri_role_perm where rolecode='".strtolower($role)."' and permcode='".$perm."' and status=1";	
	$chkQury = mysqli_query($conn,$sql1);
	$numRows = mysqli_num_rows($chkQury);
	if($numRows > 0)
	{
		return true;	
	}
	else
	{
		return false;	
	}
	
 }
 
 function random_password( $length = 12 ) {
    $chars = "*abcdefghijklmn(;%OPQRSTUVWXYZ012345_+)opqrstuvwxyz?@#67ABCDEFGHIJKLMN89";
    $password = substr( str_shuffle( $chars ), 0, $length );
    return $password;
}

function MailSend($subject,$html,$title)
	{
		//include_once '../PHPMailer/class.phpmailer.php';
		
		$mail = new PHPMailer();
		$mail->IsSMTP();				
		$mail->SMTPDebug  = 0;  
		$mail->SMTPAuth   = true;                  		// enable SMTP authentication
		$mail->Host       = "smtp.sendgrid.net"; 		// SMTP server
		$mail->Port       = 25; ;                    	// set the SMTP port for the GMAIL server
		$mail->Username   ="apikey"; 					// SMTP account username		
		$mail->Password   = "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";            // GMAIL password
		
		
		$mail->Subject    = $subject ;
		$mail->WordWrap = 150;
	
		$mail->MsgHTML($html);
	
		$mail->SetFrom('app@rathinfotech.com','Rathinfotech App');
		//$mail->AddAddress("sumeet@rathinfotech.com", $title);
		$mail->AddAddress("chandan@rathinfotech.com", "Chandan");
		
		
				
		if(!$mail->Send()) {
				 echo "Mail send fail";
			
			} else {				
				
				echo "Mail Send successfully";			 
			}
		
	}
	
	function MailSendToSumeet($subject,$html,$title)
	{
		//include_once '../PHPMailer/class.phpmailer.php';
		
		$mail = new PHPMailer();
		$mail->IsSMTP();				
		$mail->SMTPDebug  = 0;  
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Host       = "smtp.sendgrid.net"; // SMTP server
		$mail->Port       = 25; ;                    // set the SMTP port for the GMAIL server
		$mail->Username   ="apikey"; // SMTP account username
		//$mail->Password   = "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";            // GMAIL password
		$mail->Password   = "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";            // GMAIL password
		
		
		$mail->Subject    = $subject ;
		$mail->WordWrap = 150;
	
		$mail->MsgHTML($html);
	
		//$mail->SetFrom('info@rathinfotech.com',$title);
		$mail->AddAddress("sumeet@rathinfotech.com", "Sumeet");
		$mail->AddAddress("chandan@rathinfotech.com", "Chandan");
		
		
				
		if(!$mail->Send()) {
				 echo "Mail send fail";
			
			} else {				
				
				echo "Mail Send successfully";			 
			}
		
	}
	
	function MailSendToOther($subject,$html,$title,$email)
	{
		//include_once '../PHPMailer/class.phpmailer.php';
		
		$mail = new PHPMailer();
		$mail->IsSMTP();				
		$mail->SMTPDebug  = 0;  
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Host       = "smtp.sendgrid.net"; // SMTP server
		$mail->Port       = 25; ;                    // set the SMTP port for the GMAIL server
		$mail->Username   ="apikey"; // SMTP account username
		//$mail->Password   = "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";            // GMAIL password
		$mail->Password   = "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";            // GMAIL password
		
		
		$mail->Subject    = $subject ;
		$mail->WordWrap = 150;
	
		$mail->MsgHTML($html);
	
		//$mail->SetFrom('info@rathinfotech.com',$title);
		$mail->AddAddress($email);
		$mail->AddAddress("chandan@rathinfotech.com", "Chandan");
		
		//echo"<pre>mail==";print_r($mail);
		//exit;
				
		if(!$mail->Send()) {
				 echo "Mail send fail";
			
			} else {				
				
				echo "Mail Send successfully";			 
			}
		
	}

	
	
	function UserClickLog($conn,$category,$host,$cpanel_user,$username,$userid,$type,$ipAddress,$agent_details,$ISP,$comment)
	{
		 $queryLog ="insert into ri_cpanle_view(category,server,username,click_by,click_by_id,ipaddress,agent_detail,isp,click_date,type,status,comment)
	values('".$category."','".$host."','".$cpanel_user."','".$username."','".$userid."','".$ipAddress."','".$agent_details."','".$ISP."','".date("Y-m-d H:i:s")."','".$type."',1,'".$comment."')";
	
		mysqli_query($conn,$queryLog);
		
		
		 $queryBckup ="insert into ri_account_click(username,click_by,click_date,status) values('".$cpanel_user."','".$username."','".date("Y-m-d H:i:s")."',1)";
		 
		 mysqli_query($conn,$queryBckup);
		
		
	}
	
	function ChkMenuPermission($conn,$userid,$menuid)
	{
		 $sql="select id,userid from ri_role_menu where  submenu='".$menuid."' and status=1";	
		$chkQury = mysqli_query($conn,$sql);
		$numRows = mysqli_num_rows($chkQury);	
		
		if($numRows > 0)
		{
			while($data = mysqli_fetch_array($chkQury)) {
				$arruser[] = $data['userid'];
			}
		}
		
		if(in_array($userid,$arruser))
		{
			return "yesauth";	
		}
		else
		{
			return "noauth";		
		}
	}
	
	function GetMenuClickLog($conn,$userid,$action,$ipAddress,$agent_details,$isp)
	{
		$queryLog ="insert into ri_menuclick_log(userid,pages,ipaddress,agent_details,isp,log_date)
	values('".$userid."','".$action."','".$ipAddress."','".$agent_details."','".$isp."','".date("Y-m-d H:i:s")."')";
	
		mysqli_query($conn,$queryLog);
		
	}
	
	function getDOB($conn,$uid)
	{
		$sql="select dob from ri_users where  id='".$uid."' and status=1";	
		$chkQury = mysqli_query($conn,$sql);
		$numRows = mysqli_num_rows($chkQury);	
		$data = mysqli_fetch_array($chkQury);
		//$day = date('Y-m-d',strtotime($data['dob']));
		$day = $data['dob'];
		return 	$day;
	}
	
function cleanText($str){

$str = str_replace("Ñ" ,"N", $str);
$str = str_replace("ñ" ,"n", $str);
$str = str_replace("ñ" ,"n", $str);
$str = str_replace("Á","A", $str);
$str = str_replace("á","a", $str);
$str = str_replace("É","E", $str);
$str = str_replace("é","e", $str);
$str = str_replace("ú","u", $str);
$str = str_replace("ù","u", $str);
$str = str_replace("Í","I", $str);
$str = str_replace("í","i", $str);
$str = str_replace("Ó","O", $str);
$str = str_replace("ó","o", $str);
$str = str_replace("“","", $str);
$str = str_replace("”","", $str);
$str = str_replace("& Nbsp;","&nbsp;", $str);
$str = str_replace("＆NBSP;","&nbsp;", $str);
$str = str_replace("& nbsp;","&nbsp;", $str);
$str = str_replace("& nbsp;","&nbsp;", $str);
$str = str_replace("& amp;","&amp;", $str);
$str = str_replace("y toro;","&bull;", $str);
$str = str_replace(" y toro;","&bull;", $str);
$str = str_replace(" y amp;"," &amp;", $str);
$str = str_replace("ووالثور؛","&bull;", $str);
$str = str_replace("& نبسب؛","&nbsp;", $str);
$str = str_replace(" & نبسب؛ ","&nbsp;", $str);
$str = str_replace("& quot;","&quot;", $str);
$str = str_replace("& Quot;","&quot;", $str);

$str = str_replace("‘","", $str);
$str = str_replace("’","", $str);
$str = str_replace("—","-", $str);

$str = str_replace("–","-", $str);
$str = str_replace("™","", $str);
$str = str_replace("ü","u", $str);
$str = str_replace("Ü","U", $str);
$str = str_replace("Ê","E", $str);
$str = str_replace("ê","e", $str);
$str = str_replace("Ç","C", $str);
$str = str_replace("ç","c", $str);
$str = str_replace("È","E", $str);
$str = str_replace("è","e", $str);
$str = str_replace("•","*" , $str);
$str = str_replace("°","&deg;" , $str);	// degree
$str = str_replace("¼","&#188;" , $str);
$str = str_replace("½","&#189;" , $str);
$str = str_replace("¾","&#190;" , $str);
$str = str_replace("½","&#189;" , $str);

return $str;

}



?>
