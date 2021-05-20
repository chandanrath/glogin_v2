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
 
	 $sql_adver="select cmpid,cname,cmpname,mobile,cmpemail,company_url,address,telephone from ri_company	
	where status=1  order by cmpname desc";


	$result= mysqli_query($conn,$sql_adver);			
	$num_data = mysqli_num_rows($result);
	$header = "";
	$header ="<table border='1' style='border-collapse:collapse;'>
					<tr><td colspan='7' style='text-align:center;'><bold> Company Details </bold></td></tr>
					<tr><th>Sr No.</th><th>Company Name</th><th>Company Url</th><th>Contact Person</th><th>Email</th><th>Mobile</th><th>Address</th></tr>";
	
	$i=1;			
	while($fetch_data = mysqli_fetch_array($result))
	{
		
		
		$header .="<tr><td>".$i."</td><td>".$fetch_data["cmpname"]."</td><td>".$fetch_data["company_url"]."</td><td>".$fetch_data["cname"]."</td><td>".$fetch_data["cmpemail"]."</td><td>".$fetch_data["mobile"].' / '.$fetch_data["telephone"]."</td><td>".$fetch_data["address"]."</td></tr>";
		
		$i++;		
	}
	 $header .="</table>";
	
	header('Content-Type: application/force-download');
	header('Content-disposition: attachment; filename=Company-Details-'.date('Y-m-d').'.xls');			
	header("Pragma: ");
	header("Cache-Control: ");			
	echo $header;
	




?>
