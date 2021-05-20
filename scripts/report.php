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
 
if(isset($_POST) && $_POST['hid_date']!="")
{
	$dtype = 	$_POST['hid_date'];
}

	if($dtype=='t')
	{
		$dateName = "Today";
		$append_date = 	" and DATE_FORMAT(assign_date, '%Y-%m-%d')= DATE_FORMAT(CURDATE(),'%Y-%m-%d')";
	}
	else if($dtype=='w')
	{
		$dateName = "Weekly";
		$append_date = " and assign_date >= NOW() - INTERVAL 7 DAY and assign_date  < NOW() + INTERVAL 7 DAY";
	}
	else if($dtype=='m')
	{
		$dateName = "Monthly";
		$append_date = " and assign_date >= NOW() - INTERVAL 31 DAY and assign_date  < NOW() + INTERVAL 31 DAY";						
	}
	else
	{
		$dateName = "Monthly";
		$append_date = "";
	}
	
	
	if(!empty($_POST['fromdate']) && (!empty($_POST['todate'])))
	{
		$frmdate = $_POST['fromdate'];
		$todate =  $_POST['todate'];
		$app_frmdate = 	" and DATE_FORMAT(rcw.assign_date, '%Y-%m-%d') BETWEEN '".$_POST['fromdate']."' and '".$_POST['todate']."'";	
	}
	else
	{
		$frmdate = "";
		$todate = "";
		$app_frmdate = "";	
	}	
	if(!empty($_POST['status']))
	{
		
		$appStatus = 	" and stage= '".$_POST['status']."'";	
	}
	else
	{		
		$appStatus = "";	
	}		

	 $sql_adver="select rcw.id,rcw.cmpid,rcw.userid,rcw.website,rcw.surlstatus,rcw.SURL,rcw.`status`,rcw.assigned,rcw.stage,
	rc.cname,rc.cmpname,rc.mobile,ru.name,ru.email,rc.company_url,rcw.assign_date,rcw.complete_date
	from ri_company_website rcw
	join ri_company rc on(rc.cmpid=rcw.cmpid) 
	join ri_users ru on(ru.id=rcw.userid) 
	where rcw.status=1 and stage<>0 ".$append_date. $app_frmdate.$appStatus." order by rcw.id desc";

//exit;

	$result= mysqli_query($conn,$sql_adver);			
	$num_data = mysqli_num_rows($result);
	$header = "";
	$header ="<table border='1' style='border-collapse:collapse;'>
					<tr><td colspan='9' style='text-align:center;'><bold> '".$dateName."' Submission Details </bold></td></tr>
					<tr><th>Sr No.</th><th>Company Name</th><th>Company Url</th><th>User Name</th><th>Website</th><th>Email</th><th>Assigned Date</th><th>SURL</th><th>Complete Date</th><th>status</th></tr>";
	
	$i=1;			
	while($fetch_data = mysqli_fetch_array($result))
	{
		$stage	= $fetch_data["stage"];
		if($stage==1){$stype = "Assigned";}if($stage==2){$stype ="SURL Pending";}if($stage==3){$stype ="Completed";}
		
		$header .="<tr><td>".$i."</td><td>".$fetch_data["cmpname"]."</td><td>".$fetch_data["company_url"]."</td><td>".$fetch_data["cname"]."</td><td>".$fetch_data["website"]."</td><td>".$fetch_data["email"]."</td><td>".$fetch_data["assign_date"]."</td><td>".$fetch_data["SURL"]."</td><td>".$fetch_data["complete_date"]."</td><td>".$stype."</td></tr>";
		
		$i++;		
	}
	  $header .="</table>";
	 //exit;
	
	header('Content-Type: application/force-download');
	header('Content-disposition: attachment; filename='.$dateName.'-report-'.date('Y-m-d').'.xls');			
	header("Pragma: ");
	header("Cache-Control: ");			
	echo $header;
	//header('location:'.BASE_URL.'dashboard.php?action=report');






?>
