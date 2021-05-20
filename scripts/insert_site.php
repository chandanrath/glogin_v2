<?php

ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);

include_once '../includes/connect.php';
include_once '../includes/define.php';

include_once '../includes/functions.php';


date_default_timezone_set('Asia/Calcutta'); 


unset($_SESSION['url']);	
unset($_SESSION['ipaddress']);
unset($_SESSION['error']);

$created_date = date('Y-m-d H:i:s');
 


if($_POST['type']=="add_new")
{
	$url			= (!empty(trim($_POST['url']))?trim($_POST['url']):"");
	$ipaddress		= trim($_POST['ipaddress']);
	$pa				= trim($_POST['pa']);
	$da				= trim($_POST['da']);
	$comment		= trim($_POST['comment']);
	
	 $query ="insert into ri_website(url,ipaddress,pa,da,comment,status,created)
	values('".$url."','".$ipaddress."','".$pa."','".$da."','".$comment."','1','".$created_date."')";
	
	mysqli_query($conn,$query);			 
	$insert_id = mysqli_insert_id();
	
	header('location:'.BASE_URL.'dashboard.php?action=website_list');	
	//exit;
}
else if($_GET['type']=="mapp")
{
	$cmp = array();
	$query = array();
	$SQL_QUERY="SELECT cmpid FROM ri_company WHERE  status=1";	
	$result= mysqli_query($conn,$SQL_QUERY);
	$arrCmp = mysqli_num_rows($result);
	
	if($arrCmp > 0)
	{
		while($arr = mysqli_fetch_array($result))
		{
			//echo"<pre>url==";print_r($arr);exit;
			$cmpid = $arr['cmpid'];
			
			
			 $sqlWeb="SELECT id,url FROM ri_website WHERE  status=1";
			
			$resWeb = mysqli_query($conn,$sqlWeb);
			$rowsWeb = mysqli_num_rows($resWeb);
			
			
			
			if($rowsWeb > 0)
			{
			
				while($arrWeb = mysqli_fetch_array($resWeb))
				{				
					
					$webid = $arrWeb['id'];
					$website = $arrWeb['url'];
					
					$sqlChk="SELECT id,website FROM ri_company_website WHERE  cmpid='".$cmpid."' and webid='".$webid."' and status=1";
					$qry_sqlChk = mysqli_query($conn,$sqlChk);
					$arrRows = mysqli_num_rows($qry_sqlChk);
					
					if($arrRows==0)
					{
						$query[] = "insert into ri_company_website(cmpid,webid,website,status,created_date)
	values('".$cmpid."','".$webid."','".$website."','1','".$created_date."')";
						
					}
					
				}
			}
			
		
		}
		
	}	
		shuffle($query);
		//echo"<pre>query==";print_r($query);exit;
		foreach($query as $add)
		{
			mysqli_query($conn,$add);		
		}
		
		header('location:'.BASE_URL.'dashboard.php?action=company_website_map');
 }
 else if($_REQUEST['type']=='delete')
{	
	$ID	=	substr(decode($_REQUEST['d']),2);	
	$update = "update "._PREFIX."website set status='2' where id='".$ID."'";
	mysqli_query($conn, $update);	
	$update1 = "update "._PREFIX."company_website set status='2',modified_date='".$created_date."' where webid='".$ID."'";
	mysqli_query($conn, $update1);	
	
	header('location:'.BASE_URL.'dashboard.php?action=website_list');
}
else if($_REQUEST['type']=='assignTo')
{	
	$ID	=	$_POST['subid'];	
	$update = "update "._PREFIX."company_website set userid='".$_POST['assignto']."',assign_date='".$created_date."' where id='".$ID."'";
	mysqli_query($conn, $update);	
	echo"Assign To Update Successfully!.";
	//header('location:'.BASE_URL.'dashboard.php?action=company_website_map');
}
 
 
	



?>
