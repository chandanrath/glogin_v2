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

$date			= date('Y-m-d H:i:s');
$operation		= $_POST['operation'];



$created_date	= date('Y-m-d H:i:s');


$task	= array();


//echo"<pre>post==";print_r($_POST);exit;
//echo"<pre>_SESSION==";print_r($_SESSION);exit;

if(empty($_SESSION['user_id']))
{
	session_destroy();
	header('location:glogin/login.php');	
	exit;
}

if($operation=='add_new')
{	

	if(isset($_POST['submit']))
	{
		$task = $_POST['task'];
		if(!empty($_POST['task']))
		{
			foreach($task as $tvalue)
			{
				if(!empty($tvalue))
				{
					 $query ='insert into ri_user_task(userid,email,task,status,complete,created_date)
					values("'.$_SESSION['user_id'].'","'.$_SESSION['email'].'","'.$tvalue.'",1,2,"'.$created_date.'")';			
					mysqli_query($conn,$query);	
				}
			}
		}
		
	}
	
	header('location:'.BASE_URL.'dashboard.php?action=today_work');	
	
}
else if($operation=='tskcomplete')
{	
	$tskID	=	$_POST['taskid'];	
	$update = "update "._PREFIX."user_task set complete='1',completed_date='".$created_date."' where id='".$tskID."'";
	mysqli_query($conn, $update);
	
	//echo"done";
		
}
else if($operation=='tskGrpComplete')
{
	if(isset($_POST['submit']))	
	{
		if(!empty($_POST['chktask']))	
		{
			foreach($_POST['chktask'] as $chktask)
			{				
				 $update = "update "._PREFIX."user_task set complete='1',completed_date='".$created_date."' where id='".$chktask."'";
				 mysqli_query($conn, $update);
			}
		}
	}	
	
	header('location:'.BASE_URL.'dashboard.php?action=today_work');	
}

else if($operation=='edit')
{	

	$ID	=	substr(decode($_REQUEST['task_id']),2);	
	$update = 'update '._PREFIX.'user_task set task="'.$_POST['task'][0].'",comment="'.$_POST['comment'].'"  where id="'.$ID.'"';
	mysqli_query($conn, $update);	
	
	header('location:'.BASE_URL.'dashboard.php?action=today_work');	
	//exit;
}
else if($operation=='formcount')
{
	if(isset($_POST['submit']))
	{
		$formcnt = $_POST['formcount'];
		
		 $query1 ="insert into ri_user_formsubmit(userid,email,formcount,status,completed_date)
		values('".$_SESSION['user_id']."','".$_SESSION['email']."','".$formcnt."','1','".$created_date."')";			
		mysqli_query($conn,$query1);		
		
	}
	
	header('location:'.BASE_URL.'dashboard.php?action=today_work&e=1');	
}
else if($operation=='updateActivity')
{
	
	if(isset($_POST['submit']))
	{
		if(isset($_POST['mode']) && ($_POST['mode']==""))
		{
			$query ="insert into ri_activity(activity,status,created_date)
			values('".$_POST['activity']."','".$_POST['status']."','".$created_date."')";			
			mysqli_query($conn,$query);
			
			$insId = mysqli_insert_id($conn);
			
			// insert activity comment for history //
			
			$query1 ="insert into ri_activity_comments(activityid,comments,update_by,userid,status,update_date,selected)
			values('".$insId."','".$_POST['comment']."','".$_SESSION['username']."','".$_SESSION['user_id']."','".$_POST['status']."','".$created_date."',1)";			
			mysqli_query($conn,$query1);
			
		}
		else
		{
			$ID	=	substr(decode($_REQUEST['activity_id']),2);	
			
			$update = 'update '._PREFIX.'activity_comments set selected=0  where activityid="'.$ID.'"';
			mysqli_query($conn, $update);
			
			 $query1 ="insert into ri_activity_comments(activityid,comments,update_by,userid,status,update_date,selected)
			values('".$ID."','".$_POST['comment']."','".$_SESSION['username']."','".$_SESSION['user_id']."','".$_POST['status']."','".$created_date."',1)";			
			mysqli_query($conn,$query1);
			
			
			$update = 'update '._PREFIX.'activity set status="'.$_POST['status'].'"  where id="'.$ID.'"';
			mysqli_query($conn, $update);
			
		}
		
	}
	//exit;
	header('location:'.BASE_URL.'dashboard.php?action=update_activity');	
}

else
{
	header('location:'.BASE_URL.'dashboard.php?action=today_work');	
}


?>
