 <?php 

 ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);

include_once '../includes/connect.php';
include_once '../includes/define.php';



	// for error log mail send //

	$html = '';	
	$table = 'ri_error_log';
	$subject = "Error Log By Today ".date('Y-m-d')." - Internal Application";
	
	$SQL="SELECT log_id,userid,oauth_uid,name,email,remote_addr,types,pages,message,log_date,flag FROM ri_error_log WHERE  flag=0";	
	$result= mysqli_query($conn,$SQL);
	$count = mysqli_num_rows($result);
	
	if($count > 0)
	{			
		$html .='<div class="widget-content"><table class="table table-bordered " border="1"><thead><tr>
		<th>User Id</th>
		<th>IP Address</th>
		<th>Name</th>
		<th>Email</th>		
		<th>Message </th>
		<th>Date </th>
		</tr></thead><tbody>';
		$i=1;
		while($fetch_data=mysqli_fetch_array($result))
		{
			
		$html .='<tr><td>'.$fetch_data["oauth_uid"].'</td><td>'.$fetch_data["remote_addr"].'</td><td>'.$fetch_data["name"].'</td><td>'.$fetch_data["email"].'</td><td>'.$fetch_data["message"].'</td><td>'.$fetch_data["log_date"].'</td>';
			$i++;
		}
		$html .='</tbody></table></div>';	
		
		MailSend($table,$conn,$subject,$html);
		
	}
	else
	{
		echo"No Record Found in Error Log!";	
	}
	
	
	// mail send for users login on daily basis //
	
	$html1 = '';	
	$table1 = 'ri_users_log';
	$subject1 = "Logged in By Today ".date('Y-m-d')." - Internal Application";
	
	$SQL_log="SELECT id,userid,oauth_uid,first_name,last_name,email,ipaddress,isp,agent_details,login_date,status FROM ri_users_log where flag=0";	
	$result1= mysqli_query($conn,$SQL_log);
	$count1 = mysqli_num_rows($result1);
	
	if($count1 > 0)
	{			
		$html1 .='<div class="widget-content"><table class="table table-bordered " border="1"><thead><tr>
		
		<th>Name</th>
		<th>Email</th>	
		<th>IP Address</th>	
		<th>User Details </th>
		<th>ISP </th>
		<th>Login Date </th>
		</tr></thead><tbody>';
		$k=1;
		while($fetch_logdata=mysqli_fetch_array($result1))
		{
			$userDetail = json_decode($fetch_logdata["agent_details"],true);
			
		$html1 .='<tr><td>'.$fetch_logdata["first_name"].' '.$fetch_logdata["last_name"].'</td><td>'.$fetch_logdata["email"].'</td><td>'.$fetch_logdata["ipaddress"].'</td><td>'.$userDetail["country"].'/'.$userDetail["city"].'</td><td>'.$fetch_logdata["isp"].'</td><td>'.$fetch_logdata["login_date"].'</td>';
			$k++;
		}
		$html1 .='</tbody></table></div>';	
		
		MailSend($table1,$conn,$subject1,$html1);
		
	}
	else
	{
		echo"No Record Found To Logged In!";	
	}
	
	//echo $html1;
	
	
	
	function MailSend($table,$conn,$subject,$html)
	{
		include_once '../PHPMailer/class.phpmailer.php';
		
		$mail = new PHPMailer();
		$mail->IsSMTP();				
		$mail->SMTPDebug  = 0;  
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Host       = "smtp.sendgrid.net"; // SMTP server
		$mail->Port       = 25; ;                    // set the SMTP port for the GMAIL server
		$mail->Username   ="apikey"; // SMTP account username
		$mail->Password   = "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";            // GMAIL password
		
		
		$mail->Subject    = $subject ;
		$mail->WordWrap = 150;
	
		$mail->MsgHTML($html);
	
		$mail->SetFrom('no-reply@pipingmart.com','Internal Application');
		$mail->AddAddress("sumeet@rathinfotech.com", "");
		$mail->AddCC("chandan@rathinfotech.com", "");
		
		
				
		if(!$mail->Send()) {
				 echo "Mail send fail";
			
			} else {
				
				$update = "update ".$table." set flag='1' where flag='0'";
				$sender = mysqli_query($conn, $update);	
						
				echo "Mail Send successfully";
			 
			}
		
	}
	

?>
