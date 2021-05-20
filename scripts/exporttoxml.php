<?php

ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);
//header("Content-type: text/xml charset=utf-8"); 

include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

date_default_timezone_set('Asia/Calcutta'); 


$created_date = date('Y-m-d H:i:s');

//echo"<pre>date==";print_r($_REQUEST);exit;
 
	$ipAddress = getIPaddress();// get user ip address //	
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$agent_details = IPDetails($ipAddress);	// get ipdetails
	//$ISPurl = ISPDetails($ipAddress); 
	$ISP = ""; //(!empty($ISPurl['isp'])?$ISPurl['isp']:"") ;	
	$type= "ftp";
	if(isset($_SESSION['username'])){
		
		if(!empty($_REQUEST['cat']))
		{
	$userLog = UserClickLog($conn,$_REQUEST['cat'],$_REQUEST['server'],$_REQUEST['user'],$_SESSION['username'],$_SESSION['user_id'],$type,$ipAddress,$agent_details,$ISP,$_REQUEST['comment']);		
		}
	}

	 $getSql="select passwrd from ri_domain where status=1 and server='".$_REQUEST['server']."' and username='".$_REQUEST['user']."' and domain='".$_REQUEST['domain']."' ";

	$result= mysqli_query($conn,$getSql);			
	$num_data = mysqli_num_rows($result);
 	

	
	if($num_data > 0)
	{
		$header .='<?xml version="1.0" encoding="UTF-8"?>
		<FileZilla3 version="3.37.1" platform="windows">
		<Servers>';
		
					
		while($fetch_data = mysqli_fetch_array($result))
		{		
			$passwd = $fetch_data['passwrd'];			
			$header .='<Server>
						<Host>'.$_REQUEST['domain'].'</Host>
						<Port>21</Port>
						<Protocol>0</Protocol>
						<Type>0</Type>
						<User>'.$_REQUEST['user'].'</User>
						<Pass encoding="base64">'.$passwd.'</Pass>
						<Logontype>1</Logontype>
						<TimezoneOffset>0</TimezoneOffset>
						<PasvMode>MODE_DEFAULT</PasvMode>
						<MaximumMultipleConnections>0</MaximumMultipleConnections>
						<EncodingType>Auto</EncodingType>
						<BypassProxy>0</BypassProxy>
						<Name>'.$_REQUEST['user'].'</Name>
						<Comments />
						<Colour>0</Colour>
						<LocalDir /><RemoteDir />
						<SyncBrowsing>0</SyncBrowsing>
						<DirectoryComparison>0</DirectoryComparison>
					</Server>';
				
			}
		  $header .='</Servers>
		  </FileZilla3>';
		
		header('Content-type: application/xml');
		header('Content-Disposition: attachment; filename='.$_REQUEST['user'].'.xml');	
		
		readfile($_REQUEST['user'].'.xml'); // or otherwise print your xml to the response stream
		
			
		echo $header;
	}
	else
	{
		
		header('location:'.BASE_URL.'dashboard.php?action=cpanel_list');	
	}
	




?>
