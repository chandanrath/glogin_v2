<?php
ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);


include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

date_default_timezone_set('Asia/Calcutta'); 

$created_date			= date('Y-m-d H:i:s');

unset($_SESSION['messg']);

//echo"<pre>sess==";print_r($_SESSION);	exit;								
	
	// upload company data // 
	
	if(isset($_POST["import"]))
	{
		
		//$CntryCode = CompanyCountryCode();	// get country iso /isd code
		
		$filename = $_FILES["file"]["name"];
		$source = $_FILES["file"]["tmp_name"];
		$type = $_FILES["file"]["type"];
		
		//echo"siz=".$_FILES["file"]["size"];
				
		if($_FILES["file"]["size"] > 0)
		{
			
			$name = explode(".", $filename);
			$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
			foreach($accepted_types as $mime_type) {
				if($mime_type == $type) {
					$okay = true;
					break;
				} 
			}
			
			
			$target_path = "../htmlupload/".$filename;  // change this to the correct site path
			
			if(move_uploaded_file($source, $target_path))
			{
				$zip = new ZipArchive(); 
				
				// extract zip file //
				
				$x = $zip->open($target_path);
				if ($x === true) {
					$zip->extractTo("../htmlupload/"); // change this to the correct site path
					$zip->close();			
					
					unlink($target_path);
				}
				$message = "Your .zip file was uploaded and unpacked.";
			} else {	
				$message = "There was a problem with the upload. Please try again.";
			}
			
			$_SESSION['messg'] = $message;
			
			// open total file//
			
			$path = "../htmlupload/".trim($name[0]);
			$files = scandir($path);
			
			//echo"<pre>file==";print_r($files);
			
			$sqlChk="SELECT id FROM ri_html_folder WHERE  foldername='".trim($name[0])."' and status=1";
			$qryChk = mysqli_query($conn,$sqlChk);								
			$arrChk = mysqli_num_rows($qry_sqlChk);
			if($arrChk > 0)					
			{
				$_SESSION['messg'] = "Directory Already Existed!";
				header('location:'.BASE_URL.'dashboard.php?action=html_file_editor');	
			}
			else
			{
								
				$query ="insert into ri_html_folder(foldername,upload_by,upload_date,status) values('".trim($name[0])."','".$_SESSION['user_id']."','".$created_date."',1)"; 
				mysqli_query($conn,$query);				
				$id = mysqli_insert_id($conn);
				
			
						
						
				foreach ($files as &$value)
				{			
					$fname = explode(".", $value);
					
					if(!empty($fname[1]) && ($fname[1]=="html")) 
					{			
						echo"<li>22=".	$query1 ="insert into ri_htmlupload(folderid,htmlfile,upload_date,status) values('".$id."','".$value."','".$created_date."',1)"; 
						mysqli_query($conn,$query1);
						$htmlId = mysqli_insert_id($conn);
						
						echo"<li>33=".	$queryEdit ="insert into ri_html_edited(folderid,htmlid,assign_to,assign_on,submit_date,status,assign_status) values('".$id."','".$htmlId."','".$_SESSION['user_id']."','".$created_date."','".$created_date."',1,1)"; 
						mysqli_query($conn,$queryEdit);
					
					}
				}
			}
	
			
			
		}
		
		$_SESSION['messg'] = "Upload Successful!";
		header('location:'.BASE_URL.'dashboard.php?action=html_file_editor');	
		exit;
		
	}
	
	
	function CompanyCountryCode()
	{
		$sql = "SELECT id,name,iso_code from "._PREFIX."country where status = 1";
		$result= mysqli_query($conn,$sql);
		
		while($country=mysqli_fetch_array($result))
		{
		 	$iso_code	= $country["iso_code"];
			$name	= $country["name"];
			$isoCode[] = $iso_code;
		}
		return $isoCode;
	}
	

	
?>