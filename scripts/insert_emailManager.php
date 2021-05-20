<?php
ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);


include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';
include_once '../classes/Class_Database.php';
include_once '../classes/Class_encrypt.php';

$database=new Database;
$convert=new Encryption;

date_default_timezone_set('Asia/Calcutta'); 

$created_date			= date('Y/m/d H:i:s');

$manager_id	=$_POST['manager_id'];

//echo"<pre>";print_r($_POST);

 $subject				=trim($_POST['subjects']);
$code					=trim($_POST['code']);
$email_manager_desc		= stripslashes($_POST['email_manager_desc']); 

$created_by				=$_SESSION['ref_id'];
$operation				=$_POST['operation'];


if($operation=='add_new')
{
	$database->insert("ubm_email_manager",array($subject,$code,$email_manager_desc,1,$created_date),"subject,code,description,status,created_date");

	 $insert_id = mysql_insert_id();
	
}
else if($_REQUEST['mode']=='delete')
{
	$ID	=	substr($convert->decode($_REQUEST['d']),2);
	$created_by		=$_SESSION['ref_id'];
	
	$database->update("ubm_email_manager",array('status'=>'2','updated_date'=>$created_date)," id='".$ID."'");
}
else if($_REQUEST['mode']=='active')
{
	$ID	=	substr($convert->decode($_REQUEST['d']),2);
	$created_by		=$_SESSION['ref_id'];
	
	$database->update("ubm_email_manager",array('status'=>'1','updated_date'=>$created_date)," id='".$ID."'");
}
else
{
	 $database->update("ubm_email_manager",array('subject'=>$subject,'code'=>$code,'description'=>$email_manager_desc,'updated_date'=>$created_date)," id='".substr($convert->decode($manager_id),2)."'");
}

header('location:'.BASE_URL.'dashboard.php?action=emailManager_list');	

exit;

?>
