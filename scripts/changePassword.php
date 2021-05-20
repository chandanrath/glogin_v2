
<?php
ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);


include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';
//include_once '../classes/Class_Database.php';
//include_once '../classes/Class_encrypt.php';

	
//$database = new Database;
//$convert=new Encryption;

$created_date			= date('Y/m/d H:i:s');

$password = encode($_POST['newPasswd']);


$update = "update "._PREFIX."users set password='".$password."',decypt_pwd='".$_POST['newPasswd']."',updated_date='".$created_date."' where username='".$_SESSION['ref_name']."' and user_id='".$_SESSION['ref_id']."'";
	mysqli_query($conn, $update);	

 $data = array(	
		
		'username' => $_SESSION['ref_name'],
	
	);
	$send = sendEmail($data,'ChangePasswd');

header('location:'.BASE_URL.'dashboard.php?action=userProfile&res=success');	
	  
exit;
	
	
?>