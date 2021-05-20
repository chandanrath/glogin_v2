
<?php

ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);

include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

unset($_SESSION['errmsg']);

//echo"post==<pre>";print_r($_POST);

//echo"get==<pre>";print_r($_SESSION);



$timestamp = (isset($_GET['tstmpid'])?$_GET['tstmpid']:"");

$senderId = (isset($_GET['senderid'])?$_GET['senderid']:"0");	//check userid//

if($senderId==$_SESSION['user_id'])
{
	$ipAddress = getIPaddress();// get user ip address //	
		
	
	
	if ( isset($_GET['tstmpid']) ){
		
		 $qry_backlog ="SELECT id,username,fileid,filename,authkey,backup_date FROM ri_account_backup WHERE status=1 and uploadtimestamp='".$timestamp."'";
		$reslog = mysqli_query($conn,$qry_backlog);
		$numLog = mysqli_num_rows($reslog);	
		$logVal = mysqli_fetch_array($reslog);
		
		
		$type= "downloadbackup";
		 $comment = "download file ".$logVal['username'] ." on date : ".date('Y-m-d',strtotime($logVal['backup_date'])) ;
		
		if(isset($_SESSION['username'])){
			$userLog = UserClickLog($conn,'','',$logVal['username'],$_SESSION['username'],$senderId,$type,$ipAddress,'','',$comment);		
		}
		
		// chk authentication //
       $chkAuthKey = ChkAuthetication($conn,$timestamp,$logVal['authkey']);
       
       if($chkAuthKey > 0)
		{
       
		    $fileUrl = "https://f000.backblazeb2.com/file/rath-backup/".$logVal['filename'];  //?Authorization=".$logVal['authkey']
		    
		    
		     header("Location: ".$fileUrl ,true,301);
		     
		    
		}
		
	}
	else
	{
		
		$_SESSION['errmsg'] = "File not found!";
		
	}
}
else{
	
	$_SESSION['errmsg'] = "User not authenticated!";
}


function ChkAuthetication($conn,$timestmp,$auth)
{
	
	 $chkAuth ="SELECT authkey FROM ri_account_backup WHERE status=1 and uploadtimestamp='".$timestmp."' and authkey='".$auth."' ";
	$resAuth= mysqli_query($conn,$chkAuth);
	$numLogview = mysqli_num_rows($resAuth);
	
	return $numLogview;
}
?>
