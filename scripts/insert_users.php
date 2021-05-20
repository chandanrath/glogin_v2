<?php

ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);

include_once '../includes/connect.php';
include_once '../includes/define.php';

include_once '../includes/functions.php';


date_default_timezone_set('Asia/Calcutta'); 


unset($_SESSION['error']);




$created_date	= date('Y-m-d H:i:s');
$users_id		= (!empty($_POST['users_id'])?$_POST['users_id']:"");
$name			= (!empty(trim($_POST['name']))?trim($_POST['name']):"");
$email			= trim($_POST['email']);
$ractive		= trim($_POST['ractive']);
$role			= trim($_POST['role']);
$crmactive		= trim($_POST['crmactive']);
$created_by		= $_SESSION['ref_id'];
$operation		= $_POST['operation'];

//echo"<pre>post==";print_r($_POST);//exit;

if($operation=='add_new')
{	

	$chk_email = is_duplicate($conn,$email,"ri_users"); 

	if($chk_email > 0)
	{
		$_SESSION['error'] = "duplicate";
		header('location:'.BASE_URL.'dashboard.php?action=users');	
		exit;
	}


	$query ="insert into ri_users(name,email,role,crm,status,created_date)
	values('".$name."','".$email."','".$role."','".$crmactive."','1','".$created_date."')";
	
	mysqli_query($conn,$query);			 
	$insert_id = mysqli_insert_id();
	
	header('location:'.BASE_URL.'dashboard.php?action=users_list');	
	
}
else if($_REQUEST['mode']=='delete')
{	
	$ID	=	substr(decode($_REQUEST['d']),2);	
	$update = "update "._PREFIX."users set status='2',modified_date='".$created_date."' where id='".$ID."'";
	mysqli_query($conn, $update);
	
	header('location:'.BASE_URL.'dashboard.php?action=users_list');		
}
else if($operation=='edit')
{	
	 $update = "update "._PREFIX."users set name='".$name."', email='".$email."', role='".$role."',role_active='".$ractive."',crm='".$crmactive."',modified_date='".	$created_date."' where id='".substr(decode($users_id),2)."'";
	mysqli_query($conn, $update);	
	
	header('location:'.BASE_URL.'dashboard.php?action=users_list');	
	//exit;
}
else if($_REQUEST['mode']=='cookiedelete')
{
	
	$expId = explode(",",$_REQUEST['d']);
	$countID = count($expId);
	
	for($i=0; $i<$countID; $i++)
	{
		$ID	=	substr(decode($expId[$i]),2);		
		$update = "update ri_setuser_cookie set status='2',is_delete='1',modified_date='".$created_date."' where id='".$ID."'";
		mysqli_query($conn, $update);	
	}
	
	header('location:'.BASE_URL.'dashboard.php?action=cookies_set_users');	
	
}
else if($_REQUEST['mode']=='cookieouterdelete')
{
	
	$expId = explode(",",$_REQUEST['d']);
	$countID = count($expId);
	
	for($i=0; $i<$countID; $i++)
	{
		$ID	=	substr(decode($expId[$i]),2);		
		$update = "update ri_setuser_cookie set status='2',is_delete='1',modified_date='".$created_date."' where id='".$ID."' and outers=1";
		mysqli_query($conn, $update);	
	}
	
	header('location:'.BASE_URL.'dashboard.php?action=cookies_set_outerusers');	
	
}
else if($operation=='usercheck')
{
	
	if(isset($_POST['checkin']))
	{
		//echo"<pre>ss==";print_r($_SESSION);
		
		// $sql="select chkflag from ri_users_checkin where userid='".$_SESSION['user_id']."' and DATE_FORMAT(checkin_date,'%Y-%m-%d')='".date//('Y-m-d')."'";	
		//$chkQury = mysqli_query($conn,$sql);
		//$chkRows = mysqli_num_rows($chkQury);
		
		//if($chkRows==0)
		//{
			$query ="insert into ri_users_checkin(userid,empcode,name,email,status,checkin_date,checkin_timestamp,chkflag)
			values('".$_SESSION['user_id']."','".$_SESSION['empcode']."','".$_SESSION['name']."','".$_SESSION['email']."','1','".$created_date."','".strtotime($created_date)."',1)";

			mysqli_query($conn,$query);	
		
			$_SESSION['chkmsg'] = "You are successfully Check-in!";
			
		//}
		
	}
	if(isset($_POST['checkout']))
	{
		//echo"checkout";
		
		 $sql="select max(id) uid from ri_users_checkin where userid='".$_SESSION['user_id']."' and DATE_FORMAT(checkin_date,'%Y-%m-%d')='".date('Y-m-d')."'";	
		$chkQury = mysqli_query($conn,$sql);
		$chkRows = mysqli_num_rows($chkQury);
		$Userdata=mysqli_fetch_array($chkQury);
		$userId = $Userdata['uid'];
		
		 $update = "update ri_users_checkin set chkflag='2',checkout_date='".$created_date."',chkout_timestamp='".strtotime($created_date)."' where userid='".$_SESSION['user_id']."' and id='".$userId."'";
		mysqli_query($conn, $update);
		
		header('location:'.BASE_URL.'index.php');	
		exit;
	}
	
	header('location:'.BASE_URL.'dashboard.php?action=');	
}

else
{
	header('location:'.BASE_URL.'dashboard.php?action=users_list');	
}


//exit;


 function is_duplicate($conn,$email,$table)
 {	
	$sql="select email from $table where email='".$email."'";	
	$chkQury = mysqli_query($conn,$sql);
	$chkRows = mysqli_num_rows($chkQury);
	return 	$chkRows;
	
 }

?>
