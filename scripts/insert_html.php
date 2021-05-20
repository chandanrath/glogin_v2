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

//echo"<pre>post3==";print_r($_POST);
//exit;
 

if($_POST['operation']=="step1")
{
	if(isset($_POST['submit']))
	{
		$fldrId = explode('@',$_POST['hidfld']);
		$fileId = explode('@',$_POST['hidfile']);
		
		if(!empty($_POST['srno']))
		{		
					
			foreach($_POST['srno'] as $key=> $sval)	
			{
			
				if(!empty($_POST['name'][$key]))
				{
					$query ="insert into ri_html_content(folderid,htmlid,srno,name,content,submit_date,status)
					values('".$fldrId[1]."','".$fileId[1]."','".$sval."','".$_POST['name'][$key]."','".$_POST['content'][$key]."','".$created_date."','1')";
					
					mysqli_query($conn,$query);			 
					$insert_id = mysqli_insert_id($conn);
					
					
					
				}
			}
			
				 $update = "update "._PREFIX."htmlupload set htmlcontent='".$_POST['htmlcontent']."',edited=1 where id='".$fileId[1]."' and folderid='".$fldrId[1]."'";
				mysqli_query($conn, $update);	
		}
	}
	header('location:'.BASE_URL.'dashboard.php?action=openhtml-1&file='.$fileId[0].'&fld='.$fldrId[0]);	
}
else if($_POST['operation']=="step2")
{
	$fldrId = explode('@',$_POST['hidfld']);
	$fileId = explode('@',$_POST['hidfile']);
	
	header('location:'.BASE_URL.'dashboard.php?action=openhtml-2&file='.$fileId[0].'&fld='.$fldrId[0]);		
}
else if($_POST['operation']=="step3")
{
	

	$fldrId = explode('@',$_POST['hidfld']);
	$fileId = explode('@',$_POST['hidfile']);
		
	
	
	if(!empty($_POST['srno']))
	{
		foreach($_POST['srno'] as $key=> $sval)	
		{		
			if(!empty($_POST['name'][$key]))
			{
				if(!empty($_POST['content'][$key]))
				{
				 $updateContnt = "update "._PREFIX."html_content set content_new='".$_POST['content'][$key]."',edited=1 where srno='".$sval."' and htmlid='".$fileId[1]."' and folderid='".$fldrId[1]."'";
					mysqli_query($conn, $updateContnt);
				}
		
				$title[] = '@'.$_POST['name'][$key].'@';
				$content[] = $_POST['content'][$key];				
			}
			
		}
		
		$htmlcontent = str_replace($title,$content,$_POST['htmlcontent']);
		
		
		 $update = "update "._PREFIX."htmlupload set htmlcontent_new='".$htmlcontent."',edited=1 where id='".$fileId[1]."' and folderid='".$fldrId[1]."'";
		mysqli_query($conn, $update);	
		
	}

	
	$newFileName = "../htmlupload/".$fldrId[0]."/".$fileId[0];
	$newFileContent = $htmlcontent;
	
	if (file_put_contents($newFileName, $newFileContent) !== false) {
		echo "File created (" . basename($newFileName) . ")";
	} else {
		echo "Cannot create file (" . basename($newFileName) . ")";
	}
	
	header('location:'.BASE_URL.'dashboard.php?action=openhtml-3&file='.$fileId[0].'&fld='.$fldrId[0]);		
}
else if($_POST['operation']=="assignto")
{
	//echo"<pre>post3==";print_r($_POST);

	$fldrId = explode('@',$_POST['hidfld']);
	$fileId = explode('@',$_POST['hidfile']);
	
	$updAssgn = "update "._PREFIX."html_edited set assign_status='2' where folderid='".$fldrId[1]."' and htmlid='".$fileId[1]."' ";
	mysqli_query($conn, $updAssgn);	
	
	$queryEdit ="insert into ri_html_edited(folderid,htmlid,assign_to,assign_on,submit_date,status,assign_status)
	values('".$fldrId[1]."','".$fileId[1]."','".$_POST['user']."','".$created_date."','".$created_date."','1',1)";					
	mysqli_query($conn,$queryEdit);	
	
	header('location:'.BASE_URL.'dashboard.php?action=html_file_editor');		
}
else if($_POST['operation']=="addHtml")
{
	//echo"<pre>post34==";print_r($_POST);	
	
	$filename = $_POST['filename'].'.html';
	
	$query1 ="insert into ri_htmlupload(folderid,htmlfile,added_by,upload_date,status)
	values('1','".$filename."','".$_SESSION['user_id']."','".$created_date."',1)";					
	mysqli_query($conn,$query1);
		 
	$insertId = mysqli_insert_id($conn);
	
	$htmlContent = str_replace("'","\"",$_POST['htmlcontent']);										
	
	
	$update = "update "._PREFIX."htmlupload set htmlcontent='".$htmlContent."',htmlcontent_new='".$htmlContent."',edited=1 where id='".$insertId."' and folderid='".$_POST['hidfld']."'";
	mysqli_query($conn, $update);	
	
	 $queryEdit ="insert into ri_html_edited(folderid,htmlid,assign_to,assign_on,submit_date,status,assign_status)
	values('".$_POST['hidfld']."','".$insertId."','".$_SESSION['user_id']."','".$created_date."','".$created_date."','1',1)";					
	mysqli_query($conn,$queryEdit);
	
	
	$sqlFld="SELECT foldername FROM ri_html_folder WHERE  id='".$_POST['hidfld']."' and status=1";
	$qryFld = mysqli_query($conn,$sqlFld);								
	$arrFld = mysqli_fetch_array($qryFld);
	
	$newFileName = "../htmlupload/".$arrFld['foldername']."/".$filename;
	$newFileContent = $htmlContent;
	
	if (file_put_contents($newFileName, $newFileContent) !== false) {
		echo "File created (" . basename($newFileName) . ")";
	} else {
		echo "Cannot create file (" . basename($newFileName) . ")";
	}
}

else if($_POST['operation']=="outdoorClient")
{
	 $query ="insert into ri_outdoor_design(userid,clientid,comment,status,created_date)
	values('".$_SESSION['user_id']."','".trim($_POST['clientname'])."','".trim($_POST['message'])."',1,'".$created_date."')";					
	mysqli_query($conn,$query);
	 
	$insertId = mysqli_insert_id($conn);
	
	header('location:'.BASE_URL.'dashboard.php?action=outdoor_design_list');		
}
else if($_POST['operation']=="outdoorClientEdit")
{
	$ODCID	=	substr(decode($_POST['odcid']),2);
	
	 $update = "update "._PREFIX."outdoor_design set clientid='".trim($_POST['clientname'])."',comment='".trim($_POST['message'])."' where id='".$ODCID."' and userid='".$_SESSION['user_id']."'";
	mysqli_query($conn, $update);
	 
	
	header('location:'.BASE_URL.'dashboard.php?action=outdoor_design_list');		
}
else if($_POST['operation']=='outdoorcompleted')
{	
	$designid	=	$_POST['designid'];	
	$update = "update "._PREFIX."outdoor_design set complete='1',completed_date='".$created_date."' where id='".$designid."'";
	mysqli_query($conn, $update);
	
}
else if($_POST['operation']=="AddOutdoorClient")
{
	//echo"<pre>post3==";print_r($_POST);exit;
	$chkClient = is_duplicate($conn,$_POST['clientname'],"ri_outdoor_client"); 
	
	if($chkClient > 0)
	{
		$_SESSION['error'] = "duplicate";
		header('location:'.BASE_URL.'dashboard.php?action=outdoor_client');	
		exit;
	}
	else
	{	
		$query ="insert into ri_outdoor_client(userid,client_name,ftpdetails,passwrd,status,created_date)
		values('".$_SESSION['user_id']."','".trim($_POST['clientname'])."','".trim($_POST['clientftp'])."','".trim($_POST['ftppasswd'])."',1,'".$created_date."')";					
		mysqli_query($conn,$query);
		
		$insertId = mysqli_insert_id($conn);
		
		header('location:'.BASE_URL.'dashboard.php?action=outdoor_client_list');
	}
}
	

function is_duplicate($conn,$clientname,$table)
 {	
	$sql="select client_name from $table where client_name='".trim($clientname)."' and status=1";	
	$chkQury = mysqli_query($conn,$sql);
	$chkRows = mysqli_num_rows($chkQury);
	return 	$chkRows;
	
 }
?>
