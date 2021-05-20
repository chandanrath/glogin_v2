
 <?php 

ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);

include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

	$created_date	= date('Y-m-d H:i:s');
	$otpval 		= $_POST['otpval'];
	
	//echo"<pre>session==";print_r($_SESSION);
	
	
	
	
	//exit;
	if(isset($_POST) && $_POST['otpval']!="" )
	{
		$countcode	=$_POST['hiddial_code'];		// Mobile Country Code
		$number		=$_POST['hidmob'];  			// Mobile Number
		
		$otp		=$_POST['otpval']; 				//OTP ENTERED BY USER. Mobile no should be with country code.
	   
		$mobile		=$countcode.$number;  			//Concatenate mobile number and country code.
		
		$mobileAuth = MOBILE_AUTH;
	  
	   //HIT THE API
	  
		$postData = array(
				'authkey' => $mobileAuth,
				'mobiles' => trim($mobile),
				'otp' => $otp,
				
			);
			
		//echo"https://control.msg91.com/api/verifyRequestOTP.php?authkey=".$mobileAuth."&mobile=".trim($mobile)."&otp=".$otp;
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://control.msg91.com/api/verifyRequestOTP.php?authkey=".$mobileAuth."&mobile=".trim($mobile)."&otp=".$otp,
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
		//echo"type==	".$result['type'];
		//exit;
		if($result['type']=="success")
		{
			$cmpArr = array();
			$userid = $_SESSION['user_id'];
			$cookiesId = md5('rathinfo#'.$_SESSION['user_id']);
			$userLogId = $_SESSION['logid'];
			$ipaddress = $_SESSION['ipaddress'];
			
			setcookie('userid',$_SESSION['user_id'], time() + 6*24*60*60,'/');
			setcookie('name',$_SESSION['name'], time() + 6*24*60*60,'/');
			setcookie('email',$_SESSION['email'], time() + 6*24*60*60,'/');
			setcookie('role',$_SESSION['role'], time() + 6*24*60*60,'/');
			
			// insert for cookie details//
			$query ="insert into ri_setuser_cookie(userid,cookieid,ipaddress,userlog_id,status,role,login_date)
					values('".$_SESSION['user_id']."','".$cookiesId."','".$ipaddress."','".$userLogId."','1','".$_SESSION['role']."','".date("Y-m-d H:i:s")."')";
			mysqli_query($conn,$query);	
				
			
			if($_SESSION['role']=='dataentry')				
			{
				$sql = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and assigned =1 and userid='".$userid."' ORDER BY RAND() limit 0,1";
								
				$result = mysqli_query($conn,$sql);
				$web_rows = mysqli_num_rows($result);
				$cmpRows = mysqli_fetch_array($result);
				
				if($cmpRows=="" || $cmpRows==0)
				{
					 $sql1 = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and assigned =0 and userid=0 ORDER BY RAND() limit 0,1";	
					$result1= mysqli_query($conn,$sql1);
					$web_rows1 = mysqli_num_rows($result1);
					$cmpRows1=mysqli_fetch_array($result1);
					
					$webid1	= $cmpRows1["id"];		
					$cmpid1	= $cmpRows1["cmpid"];
				
				
					if($web_rows1 > 0)		
					{
						// Assign user to  company website//				
						
						 $update = "update "._PREFIX."company_website set userid='".$userid."',assigned=1,stage=1,assign_date='".date('Y-m-d H:i:s')."' where cmpid='".$cmpid1."' and id='".$webid1."'";
						$sender = mysqli_query($conn, $update);	
					}
				}	
			}
			?>
        
			<script> 			
                window.location.href ='<?=BASE_URL?>dashboard.php?action=';				
            </script>
            <?php 
            
			// mail Config //
			/*		
			$mailconfig =  array('Host' => $this->config->config['Host'],
					'Port' => $this->config->config['Port'],					
					'Username' => $this->config->config['Username'],
					'Password' => $this->config->config['Password'],
					'cmpname' => $cmpDetails,	
					'mobile' => $number				
					);
					
					*/
			
		
			//$this->session->set_flashdata('SUCCESSMMMSG', "Mobile number Has been verified!");
			
		}
		else
		{			
		?>
	
		<script> 			
			window.location.href ='<?=BASE_URL?>verifyotp.php?error=2';             
		</script>
		<?php
			  			 
		}
	
	}
	else
	{
		?>
		<script> 			
			window.location.href ='<?=BASE_URL?>verifyotp.php?error=1';				
		</script>
<?php 	} ?>
	
	
