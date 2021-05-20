<?php

ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);

include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

date_default_timezone_set('Asia/Calcutta'); 


$created_date = date('Y-m-d H:i:s');

//echo"<pre>date==";print_r($_POST);exit;
if(isset($_GET))
{
	if(!empty($_GET['user']))
	{
		$appUser=" and ut.userid=".$_GET['user'];		
	}
	else
	{
		$appUser="";						
	}
	if(!empty($_GET['status']))
	{
		$fstatus = $_GET['status'];
		$appComplet = " and ut.complete=".$_GET['status'];	
	}
	else
	{
		$appComplet="";						
	}
	if(!empty($_GET['frmdate']))
	{
		$frmdate = $_GET['frmdate'];
		$appdate = " and DATE_FORMAT(ut.created_date, '%Y-%m-%d')=DATE_FORMAT('".$_GET['frmdate']."','%Y-%m-%d')";		
	}
	else
	{
		$frmdate = "";
		$appdate=" and ut.created_date < DATE_ADD(NOW(), INTERVAL -1 DAY)";						
	}
}
 	
	 $sql_adver="SELECT ut.id,ut.userid,ut.email,ut.task,ut.complete,ut.created_date,u.name
				FROM ri_user_task ut
				join ri_users u on(u.id=ut.userid)
				where ut.status=1 ".$appUser. $appdate. $appComplet."
				ORDER BY ut.id desc ";


	$result= mysqli_query($conn,$sql_adver);			
	$num_data = mysqli_num_rows($result);
	$date = (!empty($frmdate)?$frmdate:date('Y-m-d'));
	
	$header = "";
	$header ="<table border='1' style='border-collapse:collapse;'>
					<tr><td colspan='7' style='text-align:center;'><bold> Users Task Status </bold></td></tr>
					<tr><th>Sr No.</th><th>Name</th><th>Email</th><th>Task</th><th>Status</th><th>Task Date</th></tr>";
	
	$i=1;			
	while($fetch_data = mysqli_fetch_array($result))
	{
		$complete			= ($fetch_data["complete"])==1?"Complete":"Pending";
		
		$header .="<tr><td>".$i."</td><td>".$fetch_data["name"]."</td><td>".$fetch_data["email"]."</td><td>".$fetch_data["task"]."</td><td>".$complete."</td><td>".$fetch_data["created_date"]."</td></tr>";
		
		$i++;		
	}
	 $header .="</table>";
	
	header('Content-Type: application/force-download');
	header('Content-disposition: attachment; filename=Work-Status-'.$date.'.xls');			
	header("Pragma: ");
	header("Cache-Control: ");			
	echo $header;
	




?>
