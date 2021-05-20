<?php
ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);


include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

date_default_timezone_set('Asia/Calcutta'); 

$created_date			= date('Y/m/d H:i:s');

$operation		= $_POST['operation'];


 $type = (!empty($_POST['type'])?$_POST['type']:"");
 
 //echo"<pre>post==";print_r($_POST);


	if($type=="roleperm")
	{
		
		if($operation=='add_new')
		{
			if(!empty($_POST['roleperm']))
			{
				$sqlChk="select id from ri_role_perm where  rolecode='".$_POST['role']."' and status=1";	
				$chkQury = mysqli_query($conn,$sqlChk);
				$chkRows = mysqli_num_rows($chkQury);
					
				if($chkRows > 0)
				{
					$delete = "delete from ri_role_perm where rolecode='".$_POST['role']."' and status=1";
					$delQury = mysqli_query($conn,$delete);
				}
				
				foreach($_POST['roleperm'] as $roleperm)
				{
					
					$query ="insert into ri_role_perm(rolecode,permcode,status)
					values('".$_POST['role']."','".$roleperm."','1')";
					
					mysqli_query($conn,$query);			 
					$insert_id = mysqli_insert_id();
					
					
				}
				
				header('location:'.BASE_URL.'dashboard.php?action=role_permission');	
			}	
			else
			{
				header('location:'.BASE_URL.'dashboard.php?action=role_permission&permerror=1');
			}
		}
		else
		{
			header('location:'.BASE_URL.'dashboard.php?action=role_permission');	
		}
		
	}
	else if($type=="rolemenu")
	{
		//echo"<pre>post==";print_r($_POST);exit;
		
		if(!empty($_POST['level1']) || !empty($_POST['level2']))
		{
			$deleteRole = "delete from ri_role_menu where userid='".$_POST['username']."' and status=1";
			$delQury = mysqli_query($conn,$deleteRole);
			
			foreach($_POST['level1'] as $key1=>$lav1)
			{
				//echo"<pre>post==";print_r($_POST['level2'][$lav1]);
				
				//echo"<li>lav11==".$lav1;
				
				 $query_lav1 ="insert into ri_role_menu(userid,menuid,status)
					values('".$_POST['username']."','".$lav1."','1')";
					
					mysqli_query($conn,$query_lav1);			
				
				if(!empty($_POST['level2'][$lav1]))
				{
					foreach($_POST['level2'][$lav1] as $key2=>$lav2)
					{
						//echo"<li>lav2==".$level2data = 	$lav1.'-'.$lav2;
						
						//$query_lav2 ="insert into ri_role_menu(roleid,menuid,submenu,status)
						//values('".$_POST['role']."','".$lav1."','".$lav2."','1')";
						
						$query_lav2 ="insert into ri_role_menu(userid,menuid,submenu,status)
						values('".$_POST['username']."','".$lav1."','".$lav2."','1')";
					
						mysqli_query($conn,$query_lav2);	
					}
				}
			}	
		}
		
		header('location:'.BASE_URL.'dashboard.php?action=role_menu');
	}
	else if($type=="serveradd")
	{
		//echo"<pre>post==";print_r($_POST);	
		
		$server_id		= (!empty($_POST['server_id'])?$_POST['server_id']:"");		
		$token = (!empty($_POST['token'])?$_POST['token']:"");	
		
		if($operation=='add_new')
		{
			if(isset($_POST['submit']))	
			{
				$serverChk="select id from ri_cat_server where  category='".$_POST['category']."' and hostname='".$_POST['server']."' and status=1";	
				$chkQury = mysqli_query($conn,$serverChk);
				$chkRows = mysqli_num_rows($chkQury);	
				if($chkRows <= 0)
				{
					$queryIns ="insert into ri_cat_server(category,hostname,username,token,status)
					values('".$_POST['category']."','".$_POST['server']."','".$_POST['username']."','".$token."','1')";
					
					mysqli_query($conn,$queryIns);	
					
					$id = mysqli_insert_id($conn);
									
					
					header('location:'.BASE_URL.'dashboard.php?action=server_list');	 	
				}
				else
				{
					header('location:'.BASE_URL.'dashboard.php?action=server_add&msg=1');	
				}
			}			
				
		}
		else if($operation=='edit')
		{
			//echo"<pre>post==";print_r($_POST);		
			if(isset($_POST['submit']))	
			{
				 $update = "update "._PREFIX."cat_server set category='".$_POST['category']."', hostname='".$_POST['server']."', username='".$_POST['username']."' where id='".substr(decode($server_id),2)."'";
				mysqli_query($conn, $update);	
				
				header('location:'.BASE_URL.'dashboard.php?action=server_list');	 	
				
			}		
		}
		else 
		{	
			header('location:'.BASE_URL.'dashboard.php?action=server_list');		
		}
	}
	else if($type=="packageadd")
	{
		//echo"<pre>post==";print_r($_POST);	exit;
		
		$pckg_id		= (!empty($_POST['pckg_id'])?$_POST['pckg_id']:"");		
		
		if($operation=='add_new')
		{
			if(isset($_POST['submit']))	
			{
				$packageChk="select id from ri_cat_package where  category='".strtolower($_POST['category'])."' and serverid='".$_POST['server']."' and pckname='".$_POST['package']."' and status=1";	
				$chkQury = mysqli_query($conn,$packageChk);
				$chkRows = mysqli_num_rows($chkQury);	
				if($chkRows <= 0)
				{
					$queryIns ="insert into ri_cat_package(category,serverid,pckname,status)
					values('".$_POST['category']."','".$_POST['server']."','".$_POST['package']."','1')";
					
					mysqli_query($conn,$queryIns);		
					
					header('location:'.BASE_URL.'dashboard.php?action=package_list');	 	
				}
				else
				{
					header('location:'.BASE_URL.'dashboard.php?action=package_add&msg=1');	
				}
			}			
				
		}
		else if($operation=='edit')
		{
			//echo"<pre>post==";print_r($_POST);		
			if(isset($_POST['submit']))	
			{
				 $update = "update "._PREFIX."cat_package set category='".$_POST['category']."', serverid='".$_POST['server']."', pckname='".$_POST['package']."' where id='".substr(decode($pckg_id),2)."'";
				mysqli_query($conn, $update);	
				
				header('location:'.BASE_URL.'dashboard.php?action=package_list');	 	
				
			}		
		}
		else 
		{	
			header('location:'.BASE_URL.'dashboard.php?action=package_list');		
		}
	}
	else if($type=="menuadd")
	{
		//echo"<pre>postadd==";print_r($_POST);
		
		$code = str_replace(" ","_",strtolower($_POST['menuname']));
		$showmenu = (!empty($_POST['show_menu'])?$_POST['show_menu']:0);
		
		//exit;
		
		if($_POST['is_parent']==1)
		{
			$urlPath ="";
		}
		else
		{
			$urlPath = "dashboard.php?action=".str_replace(' ','_',strtolower($_POST['menuname']));
		}
		if($operation=='add_new')
		{
			
			if(isset($_POST['submit']))	
			{
				
				
				$menuChk="select id from ri_menu where  name='".strtolower($_POST['menuname'])."' and status=1 and child=1";	
				$chkQury = mysqli_query($conn,$menuChk);
				$chkRows = mysqli_num_rows($chkQury);	
				if($chkRows <= 0)
				{
					$parent = (!empty($_POST['is_parent'])?$_POST['is_parent']:0);
					$child =  0;
					
					
				 	$queryIns ="insert into ri_menu(name,code,path,parent,child,sequence,icon,show_menu,status)
					values('".$_POST['menuname']."','".$code."','".$urlPath."','".$parent."','".$child."','".$_POST['sequence']."','".$_POST['icon']."','".$showmenu."','1')";
					
					mysqli_query($conn,$queryIns);	
					$id = mysqli_insert_id($conn);
					
					if($_POST['is_parent']==0)
					{
						$update = "update "._PREFIX."menu set child='".$_POST['parentmenu']."' where id='".$id."'";
						mysqli_query($conn, $update);	
					}
					
					header('location:'.BASE_URL.'dashboard.php?action=menu');	 	
				}
				else
				{
					header('location:'.BASE_URL.'dashboard.php?action=menu_add&msg=1');	
				}
			}			
				
		}
		else if($operation=='edit')
		{
			//echo"<pre>post==";print_r($_POST);		exit;
			$menu_id		= (!empty($_POST['menu_id'])?$_POST['menu_id']:"");	
			
			if(isset($_POST['submit']))	
			{
				$parent = (!empty($_POST['is_parent'])?$_POST['is_parent']:0);
				$child =  ($_POST['is_parent']==0)?$_POST['parentmenu']:0;
					
				  $update = "update "._PREFIX."menu set name='".$_POST['menuname']."',code='".$code."', path='".$urlPath."', parent='".$parent."', child='".$child."',sequence='".$_POST['sequence']."',icon='".$_POST['icon']."',show_menu='".$showmenu."' where id='".substr(decode($menu_id),2)."'";
				mysqli_query($conn, $update);	
				
				header('location:'.BASE_URL.'dashboard.php?action=menu');	 	
				
			}		
		}
		else 
		{	
			header('location:'.BASE_URL.'dashboard.php?action=menu');		
		}	
	}
	else if($type=="checklist")
	{
		//echo"<pre>post==";print_r($_POST);exit;
		if($operation=='add_new')
		{
			if(isset($_POST['submit']))
			{
				if(!empty($_POST['chkname']))
				{
					foreach($_POST['chkname'] as $postval)
					{
						$sqlChk="select id from ri_domain_checklist where checklist ='".$postval."' and stage='".$_POST['stage']."' and status=1";	
						$chkQury = mysqli_query($conn,$sqlChk);
						$chkRows = mysqli_num_rows($chkQury);	
						if($chkRows > 0)
						{
							
							header('location:'.BASE_URL.'dashboard.php?action=checklist&duplicate=1');	
							die;
						}
						else
						{
							$query ="insert into "._PREFIX."domain_checklist(domain,stageid,checklist,status,created_date)
							values('".$_POST['webname']."','".$_POST['stage']."','".$postval."','1','".date('Y-m-d H:i:s')."')";
							
							mysqli_query($conn,$query);			 
							$insert_id = mysqli_insert_id();	
						}
					}
					
						header('location:'.BASE_URL.'dashboard.php?action=check_list');	
				}
			}
		}
		else if($operation=='edit')
		{
			//echo"<pre>post==";print_r($_POST);
			$chklist_id		= (!empty($_POST['chklist_id'])?$_POST['chklist_id']:"");		
			
			if(isset($_POST['submit']))	
			{
				  $update = "update "._PREFIX."checklist set stage='".$_POST['stage']."', chkname='".$_POST['chkname'][0]."' where id='".substr(decode($chklist_id),2)."'";
				mysqli_query($conn, $update);
				
				header('location:'.BASE_URL.'dashboard.php?action=check_list');				
			}		
		}
	}
	else if($type=="website_checklist")
	{
		//echo"<pre>post==";print_r($_POST); //exit;
		
		$userName = $_SESSION['username'];
		$userId = $_SESSION['user_id'];
		
		
		if(isset($_POST['submit']) && ($_POST['submit']=="Update"))
		{
			if(!empty($_POST['webname']))
			{
				if(!empty($_POST['checklist']))
				{
					
					foreach($_POST['checklist'] as $key=> $checklist)
					{
						//echo"<pre>checklist==";print_r($checklist);
						
						$chkStage = explode('-',$checklist);
						$stage = $chkStage[0];
						$checklistId = $chkStage[1];
						
						$comments = $_POST['comment'][$checklistId];	// comment on checklist //
					
						
						  $update = "update "._PREFIX."domain_checklist set checkedid='1', checked_by='".$userName."', userid='".$userId."',comment='".trim($comments)."',checked_date='".date('Y-m-d H:i:s')."' where id='".$checklistId."' and stageid='".$stage."'";
						mysqli_query($conn, $update);
						
					}
				}
			}
		}
		
		header('location:'.BASE_URL.'dashboard.php?action=domain_checklist');
		die;
		
	}
	
	else if($type=="AddChecklist")
	{
		//echo"<pre>post==";print_r($_POST); 
		
		$userName = $_SESSION['username'];
		$userId = $_SESSION['user_id'];	
		$webname = trim($_POST['webname']);
		
		if(isset($_POST['submit']) && ($_POST['submit']=="Update"))
		{
			if($operation=="add_new")
			{
				
				
				if(!empty($_POST['checklist']))
				{
					 
					
					foreach($_POST['checklist'] as $key=> $checklist)
					{
						$sqlDel = "delete from "._PREFIX."domain_checklist where domain='".$webname."' and stageid='".$key."'";
						mysqli_query($conn,$sqlDel);	
						
						foreach($checklist as $chkValue)
						{
							if(!empty($chkValue))
							{
								$query ="insert into "._PREFIX."domain_checklist(domain,stageid,checklist,status,created_date)
								values('".$webname."','".$key."','".trim($chkValue)."','1','".date('Y-m-d H:i:s')."')";
								
								mysqli_query($conn,$query);		
							}
						}
					}
				}
			}
			else if($operation=="edit")
			{
				if(!empty($_POST['checklist']))
				{				
					
					foreach($_POST['checklist'] as $key=> $checklist)
					{
						$sqlDel = "delete from "._PREFIX."domain_checklist where domain='".$webname."' and stageid='".$key."' and checkedid=0";
						mysqli_query($conn,$sqlDel);	
						
						foreach($checklist as $chkValue)
						{
							if(!empty($chkValue))
							{
								
							  $query ="insert into "._PREFIX."domain_checklist(domain,stageid,checklist,status,created_date)
							 values('".trim($_POST['webname'])."','".$key."','".trim($chkValue)."','1','".date('Y-m-d H:i:s')."')";
							
							 mysqli_query($conn,$query);		
								
							}
						}
					}
				}
			}
			
			header('location:'.BASE_URL.'dashboard.php?action=add_checklist');
			die;
		}
	}
	
	
	else if($type=="Addbgbanner")
	{
		if(isset($_POST['submit']))
		{
			 echo"<pre>post==";print_r($_POST);
			 echo"<pre>file==";print_r($_FILES); //exit;

			if(!empty($_FILES['file']['name']))
			{
				$allowed = array("jpg" => "image/jpg","JPG" => "image/jpg", "jpeg" => "image/jpeg","JPEG" => "image/jpeg", "gif" => "image/gif","GIF" => "image/gif", "png" => "image/png","PNG" => "image/png");
				
				$upload_dir = "../img/banner/";
			
				if(!is_dir($upload_dir)) {
			
					mkdir($upload_dir);
				}
				
				$file_tmpname = $_FILES['file']['tmp_name']; 
				$file_name = $_FILES['file']['name']; 
				$file_type = $_FILES['file']['type']; 
				$file_size = $_FILES['file']['size']; 
				
				$ext = pathinfo($file_name, PATHINFO_EXTENSION);
				
				$image_info = getimagesize($file_tmpname); 
			
				$imgWidth = $image_info[0];
				$imgHeight = $image_info[1];
				
				$filepath = $upload_dir.$file_name; 
				
				if(!array_key_exists($ext, $allowed)) 
				{
					$desclog = $file_name." is not valid file format for banner";
					
					$Insquery ='insert into '._PREFIX.'error_log(userid,name,type,flag,message,log_date)
					values("'.$_SESSION['userid'].'","'.$_SESSION['name'].'","banner upload",2,"'.$desclog.'","'.date('Y-m-d H:i:s').'")';	
					mysqli_query($conn, $Insquery);
					
				}
				else
				{
					
					if( move_uploaded_file($file_tmpname, $filepath))               
					{
						$queryImg ="insert into "._PREFIX."bg_banner(bgdate,imgname,imgpath,status,active,addedby,width,height,create_date)
						values('".trim($_POST['dov'])."','".trim(addslashes($file_name))."','".trim($filepath)."','1',1,'".trim($_SESSION['user_id'])."','".trim($imgWidth)."','".trim($imgHeight)."','".$created_date."')";
						
						mysqli_query($conn,$queryImg);
					}
					
				}
			}
		}
		
		header('location:'.BASE_URL.'dashboard.php?action=background_banner');
			die;
	}
	else if($type=="editbgbanner")
	{
		if(isset($_POST['submit']))
		{
			 //echo"<pre>post==";print_r($_POST);
			// echo"<pre>file==";print_r($_FILES); //exit;
			 
			 $bgid	=	substr(decode($_POST['prodid']),2);	

			if(!empty($_FILES['file']['name']))
			{
				$allowed = array("jpg" => "image/jpg","JPG" => "image/jpg", "jpeg" => "image/jpeg","JPEG" => "image/jpeg", "gif" => "image/gif","GIF" => "image/gif", "png" => "image/png","PNG" => "image/png");
				
				$upload_dir = "../img/banner/";
			
				if(!is_dir($upload_dir)) {
			
					mkdir($upload_dir);
				}
				
				$file_tmpname = $_FILES['file']['tmp_name']; 
				$file_name = $_FILES['file']['name']; 
				$file_type = $_FILES['file']['type']; 
				$file_size = $_FILES['file']['size']; 
				
				$ext = pathinfo($file_name, PATHINFO_EXTENSION);
				
				$filepath = $upload_dir.$file_name; 
				
				if(!array_key_exists($ext, $allowed)) 
				{
					$desclog = $file_name." is not valid file format for banner";
					
					$Insquery ='insert into '._PREFIX.'error_log(userid,name,type,flag,message,log_date)
					values("'.$_SESSION['userid'].'","'.$_SESSION['name'].'","banner upload",2,"'.$desclog.'","'.date('Y-m-d H:i:s').'")';	
					mysqli_query($conn, $Insquery);
					
				}
				else{
					if( move_uploaded_file($file_tmpname, $filepath))               
					{
						echo $queryImg ="insert into "._PREFIX."bg_banner(bgdate,imgname,imgpath,status,active,create_date)
						values('".trim($_POST['dov'])."','".trim(addslashes($file_name))."','".trim($filepath)."','1',1,'".$created_date."')";

						mysqli_query($conn,$queryImg);
					}
					
				}
			}
		}
		header('location:'.BASE_URL.'dashboard.php?action=background_banner');
			die;
	}
	
	else if($_REQUEST['type']=='deletebgbanner')
	{	
		
		$ID	=	substr(decode($_REQUEST['d']),2);	
		$update = "update ri_bg_banner set status='2', active=2 where id='".$ID."'";
		mysqli_query($conn, $update);
		
		header('location:'.BASE_URL.'dashboard.php?action=background_banner');		
	}
	
	

?>
