<?php

include_once 'config.php';
require_once('PHPMailer/class.phpmailer.php');



	function mailSend($subject,$semail,$sname,$message,$remail,$file_path,$sdid)
	{
		$mail             = new PHPMailer();
		$body			= $message;
							   
							   
		$mail->IsSMTP(); 							// telling the class to use SMTP
		
		$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
												   // 1 = errors and messages
												   // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Host       = HOST; 					// sets the SMTP server
		$mail->Port       = PORT;                   // set the SMTP port for the GMAIL server
		$mail->Username   = USERNAME; 				// SMTP account username
		$mail->Password   = PASSWORD;       		 // SMTP account password
		
		
		
		$mail->SetFrom($semail,$sname);
		
		$mail->AddReplyTo($semail,$sname);
	
		$mail->Subject    = $subject;
		
		//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		
		$mail->MsgHTML($body);
		
			
			foreach ($remail as $email1)
			{		
				$mail->AddAddress($email1);					
			}
		
			//$mail->ClearAttachments();
		
		
			$mail->ClearAttachments();
			if(isset($file_path) && !empty($file_path))
			{		
				foreach ($file_path as $addfile)
				{				
					if(file_exists($addfile))
					{
						$mail->AddAttachment($addfile); 
					}				 			
				}
			}
		
			if (!$mail->Send()) {
				
				//$errmsg =  "Mailer Error for " . $email1 . " ". $mail->ErrorInfo;
				
				$errmsg1 = "fail";			
					
			} else {
				//$errmsg = "Message sent to ".$email1;
			
				$errmsg1 = "success";
			}
			
		
		return $errmsg1;
	}

?>