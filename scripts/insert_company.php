<?php
ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);
//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 

include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

date_default_timezone_set('Asia/Calcutta'); 

$created_date			= date('Y-m-d H:i:s');

unset($_SESSION['messg']);

	
	
	// upload company data // 
	
	if(isset($_POST["Import"]))
	{
		
		//$CntryCode = CompanyCountryCode();	// get country iso /isd code
		
		$filename=$_FILES["file"]["tmp_name"];
				
		if($_FILES["file"]["size"] > 0)
		{
			$file = fopen($filename, "r");
			$i=0;
			$cnt = 0;
			
			while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
			{			
				if($i>0)
				{			
					//echo"<pre>getData=";print_r($getData);		
					$mailtype = (!empty($getData[5])?$getData[5]:"");
					$othertype = (!empty($getData[6])?$getData[6]:"");
					
					$query ="insert into ri_company(cmpname,cname,company_url,cmpemail,submitemail,mailtype,othertype,web_passwd,mobile,telephone,address,country_code,about_company,product,status,created_date) values('".trim($getData[0])."','".trim($getData[1])."','".trim($getData[2])."','".trim($getData[3])."','".trim($getData[4])."','".trim($mailtype)."','".trim($othertype)."','".trim($getData[7])."','".trim($getData[8])."','".trim($getData[9])."','".trim($getData[10])."','".trim($getData[11])."','".trim($getData[12])."','".trim($getData[13])."','1','".$created_date."')";
					mysqli_query($conn,$query);	
				}
				
				$i++;
			}
			fclose($file);
			
			$query1 ="insert into ri_upload_file(upload_name,upload_file,upload_date) values('Company','".$_FILES["file"]["name"]."','".$created_date."')"; 
			mysqli_query($conn,$query1);	
			
		}
		
		$_SESSION['messg'] = "Upload Successful!";
		header('location:'.BASE_URL.'dashboard.php?action=company_upload');	
		exit;
		
	}
	else if(isset($_POST["Export"]))
	{	
		
		header('Content-Type: text/csv; charset=utf-8');  
		header('Content-Disposition: attachment; filename=Company.csv');  
		$output = fopen("php://output", "w");  
		fputcsv($output, array('Company Name','Contact Person','Company URL', 'EmailId','Submit Email','Email Type','Other Type','Password','Mobile','Telephone','Address','Country','About Company','Product'));   
		$query = "SELECT cmpname,cname,company_url,cmpemail,submitemail,mailtype,othertype,web_passwd,mobile,telephone,address,country_code,about_company,product from ri_company ORDER BY cmpid DESC limit 0,10 ";  
		$result = mysqli_query($conn, $query);  
		while($row = mysqli_fetch_assoc($result))  
		{  
			
			fputcsv($output, $row);  
		}
		fclose($output);  
		//header('location:'.BASE_URL.'dashboard.php?action=company_upload');	
		//exit;
	}
	else
	{
		// delete recoreds //
		if($_REQUEST['mode']=='delete')
		{
			$ID	=	substr(decode($_REQUEST['d']),2);
			
			$company = "update "._PREFIX."company set status='2',update_date='".$created_date."' where cmpid='".$ID."'";
			mysqli_query($conn, $company);	
			$companyWeb = "update "._PREFIX."company_website set status='2',modified_date='".$created_date."' where cmpid='".$ID."'";
			mysqli_query($conn, $companyWeb);	
			
			header('location:'.BASE_URL.'dashboard.php?action=company_list');	
			exit;
			
		}
		
		// company added  by admin //
		
		//echo"<pre>post";print_r($_POST);exit;
	
		if(($_POST['submit']=="AdminSubmit") && ($_POST['submittype']=='company'))
		{
			if(($_POST['cname']!="") && ($_POST['cmpemail']!="") )
			{
			
				if(!empty($_POST['cmpid']))
				{	
					$cmpid = substr(decode($_POST['cmpid']),2);
					$update = "update "._PREFIX."company set cname='".trim($_POST['cname'])."', cmpname='".trim($_POST['cmpname'])."', cmpemail='".trim($_POST['cmpemail'])."', submitemail='".trim($_POST['submitemail'])."', mailtype='".trim($_POST['mailtype'])."',othertype='".trim($_POST['othertype'])."',web_passwd='".trim($_POST['web_passwd'])."',mobile='".trim($_POST['mobile'])."',telephone='".trim($_POST['telephone'])."',address='".trim($_POST['address'])."',country_code='".trim($_POST['country_code'])."',about_company='".trim($_POST['about_company'])."' ,product='".trim($_POST['product'])."',company_url='".trim($_POST['cmpurl'])."' where cmpid='".$cmpid."'";
					mysqli_query($conn, $update);
					
				}
				else	
				{		
					try
					{						
						 $query_ins ="insert into ri_company(cname,cmpname,cmpemail,submitemail,mailtype,othertype,web_passwd,mobile,telephone,address,country_code,about_company,product,company_url,status,created_date) values('".trim($_POST['cname'])."','".trim($_POST['cmpname'])."','".trim($_POST['cmpemail'])."','".trim($_POST['submitemail'])."','".trim($_POST['mailtype'])."','".trim($_POST['othertype'])."','".trim($_POST['web_passwd'])."','".trim($_POST['mobile'])."','".trim($_POST['telephone'])."','".trim($_POST['address'])."','".trim($_POST['country_code'])."','".trim($_POST['about_company'])."','".trim($_POST['product'])."','".trim($_POST['cmpurl'])."','1','".$created_date."')";
						mysqli_query($conn,$query_ins);	
						$insert_id = mysqli_insert_id();
					}
					catch(Exception $e)
					{
						$error =  $e->getMessage();
						
					}
					
					if(!empty($insert_id))
					{		
						if($_POST['cmpwebsite']!="")			
						{
							$query1 ="insert into ri_company_website(cmpid,website,status,created_date)
							values('".trim($insert_id)."','".trim($_POST['cmpwebsite'])."','1','".$created_date."')";
						
							mysqli_query($conn,$query1);
						}
		
					}
				
				}
			}
			
			header('location:'.BASE_URL.'dashboard.php?action=company_list');	
			//exit;
		}
		
		// for website insert // 
		
		if(($_POST['submit']=="AdminSubmit") && ($_POST['submittype']=='website'))
		{
			if(!empty($_POST['webid']))
			{
				
			}
			else
			{
				
				if (!preg_match("/\b[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$_POST['website'])) {
					
					  $websiteErr = "Invalid URL"; 
					}
					
				
				$query2 ="insert into ri_company_website(cmpid,website,status,created_date)
				values('".trim($_POST['cmpid'])."','".trim($_POST['website'])."','1','".$created_date."')";
				
				mysqli_query($conn,$query2);	
				
				header('location:'.BASE_URL.'dashboard.php?action=company_website_list');	
				exit;
			}
		}
		
		// user submit //
		if($_POST['submit']=="UserSubmit")
		{
			$webid	=	substr(decode($_POST['webid']),2);
			
			if(!empty($_POST['username']))
			{
				$username = trim($_POST['username']);
				$passwd = trim($_POST['passwd']);
			}
			else
			{
				$username = '';
				$passwd = '';
			}
			//exit;
			try
			{
				if($_POST['urlstatus']==1)
				{
					// when  website is completed //
				
					$update1 = "update "._PREFIX."company_website set assigned='3', surlstatus='1',stage='3', SURL='".$_POST['surl']."',comments='".trim($_POST['comments'])."', username='".$username."',passwd='".$passwd."',complete_date='".$created_date."' where id='".$webid."'";
					mysqli_query($conn, $update1);
				}
				else
				{
					// when  website's SURL  is blank //
					
					$update2 = "update "._PREFIX."company_website set assigned='2', surlstatus='0',stage='2',comments='".trim($_POST['comments'])."', username='".$username."',passwd='".$passwd."',modified_date='".$created_date."' where id='".$webid."'";
					mysqli_query($conn, $update2);	
						
				}
			}
			catch(Exception $e)
			{
				echo $error =  $e->getMessage();
				
			}
            // added on 01062020 //
			if(isset($_POST['removeweb']) && $_POST['removeweb']==1)
			{
				
				 $update3 = "update "._PREFIX."company_website set status=2 where webid='".$_POST['websiteid']."' and assigned=0";
				mysqli_query($conn, $update3);

				
				 $update4 = "update "._PREFIX."website set status=2 where id='".$_POST['websiteid']."'";
				mysqli_query($conn, $update4);
			}
			
			if($_SESSION['role']=="dataentry")
			{
				echo"assign==";//exit;
				
				$AssignNew = AssignWebsite($conn,$_SESSION['user_id']);
			}
			
			if($_REQUEST['Type']=="assign")
			{
				$urltype = "assign_list";
			}
			else if($_REQUEST['Type']=="blksurl")
			{
				$urltype = "blank_surl_list";	
			}
			else
			{
				$urltype = "";	
			}
			
			//header('location:'.BASE_URL.'dashboard.php?action=company_'.$urltype);	
			exit;
			
		}	// end website update //
		else if($_POST['submit']=="CmpWebSubmit")
		{
			//echo"<pre>post==";print_r($_POST);
			
			if($_POST['operation']=='completedWebsite')
			{
				$webid	=	substr(decode($_POST['webid']),2);
				
				if($_POST['urlstatus']==1)
				{
					$update = "update "._PREFIX."company_website set surlstatus='".$_POST['urlstatus']."',SURL='".$_POST['surl']."',comments='".trim($_POST['comments'])."', surl_update_date='".$created_date."' where id='".$webid."'";
				}
				else
				{
					$update = "update "._PREFIX."company_website set assigned='2',stage='2',surlstatus='".$_POST['urlstatus']."',SURL='".$_POST['surl']."',comments='".trim($_POST['comments'])."', surl_update_date='".$created_date."' where id='".$webid."'";	
				}			
				 
				mysqli_query($conn, $update);	
			}
			
			
			header('location:'.BASE_URL.'dashboard.php?action=completed_website');	
			exit;
		}
		// for website insert // 
		
		else if(($_POST['operation']=="addcrmmap") && ($_POST['submittype']=='crmcompany'))
		{
			//echo"<pre>post";print_r($_POST);//exit;
			
			$isseo = (!empty($_POST['isseo'])?$_POST['isseo']:0);
			$seouid = (!empty($_POST['isseo'])?$_POST['hidseouser']:0);
			
			$query2 ="insert into ri_company_crm(cmpid,crmuid,isseo,seouid,contact,mobile,email,status,created_date)
			values('".trim($_POST['hidcmpid'])."','".trim($_POST['hidcrmuser'])."','".$isseo."','".trim($seouid)."','".trim($_POST['contact'])."','".trim($_POST['mobile'])."','".trim($_POST['email'])."','1','".$created_date."')";
			
			mysqli_query($conn,$query2);	
			
			header('location:'.BASE_URL.'dashboard.php?action=company_crm');	
			exit;
			
		}
		else if(($_POST['operation']=="editcrmmap") && ($_POST['submittype']=='crmcompany'))
		{
			//echo"<pre>post";print_r($_POST);
			
			$ID	=	substr(decode($_REQUEST['cmpcrmid']),2);	
			
			$isseo = (!empty($_POST['isseo'])?$_POST['isseo']:0);
			
			$seouid = (!empty($_POST['isseo'])?$_POST['hidseouser']:0);
			
			 $company = "update "._PREFIX."company_crm set cmpid='".trim($_POST['hidcmpid'])."',crmuid='".trim($_POST['hidcrmuser'])."',isseo='".$isseo."',seouid='".trim($seouid)."',contact='".trim($_POST['contact'])."',mobile='".trim($_POST['mobile'])."',email='".trim($_POST['email'])."',update_date='".$created_date."' where id='".$ID."'";
			mysqli_query($conn, $company);	
			
			
			header('location:'.BASE_URL.'dashboard.php?action=company_crm');	
			exit;
			
		}
		else if($_REQUEST['mode']=='deletecompcrm')
		{	
			
			$ID	=	substr(decode($_REQUEST['d']),2);	
			 $update = "update ri_company_crm set status='2' where id='".$ID."'";
			mysqli_query($conn, $update);
			
			header('location:'.BASE_URL.'dashboard.php?action=company_crm');		
		}
		
		//echo"submi";exit;
		
	}
	

	
function AssignWebsite($conn,$userid)
{
	
	$cmpArr = array();
	
	$fromdate = date('Y-m')."-01";
	$todate = date('Y-m')."-31";
	
	
	//exit;
					
	// check if already assigned //
	  $sql = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and assigned =1 and userid='".$userid."'"; // ORDER BY RAND() limit 0,1
					
	$result = mysqli_query($conn,$sql);
	$web_rows = mysqli_num_rows($result);

	// if not assigned//
	if(empty($web_rows) || ($web_rows==0))
	{
	    $cmpRows = mysqli_fetch_array($result);
	    
	   
	    	
		// first check each company for single submission RAND()//
		
	
		
		$Cmpsql = "SELECT c.cmpid,c.cmpname, 
		(SELECT count(id) FROM `ri_company_website` cw WHERE status=1 and c.cmpid=cw.cmpid and 
		date_format(assign_date,'%Y-%m-%d')= '".date('Y-m-d')."' ) as totcnt
		FROM `ri_company` c WHERE c.status=1 order by RAND() limit 1";
		$cmpRes = mysqli_query($conn,$Cmpsql);
		$cmpNumRow = mysqli_num_rows($cmpRes);
		$_SESSION['assign']  = 0;
		
		$cmpData = mysqli_fetch_array($cmpRes);	
		
		// check total count 75 for whole month submission and update table//
		
		//echo"cmp==".$cmpData['cmpid'];
			
		$chkCompnyTot = ChkCompanyTotCount($conn,$cmpData['cmpid']); 
		
		//$cmpData['cmpid']  =3;
		//exit;
		
		if($chkCompnyTot <=75)
		{
			// chk everyday sbmission count//
			//if($cmpNumRow > 0)	
			
			if($cmpData['totcnt']!= 0)	
			{
				
				// check total submission in a month ==75 //
				
				$uc_sql = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and stage=0 and assigned=0 and userid='0' and cmpid='".$cmpData['cmpid']."' limit 1 ";
				$uc_result= mysqli_query($conn,$uc_sql);
				$uc_rows = mysqli_num_rows($uc_result);
			//exit;
				if(!empty($uc_rows))
				{
					
					
					$cmpRows 	= mysqli_fetch_array($uc_result);
					
					$newwebid	= $cmpRows["id"];				
					
					$UpdSql = "UPDATE "._PREFIX."company_website set assigned =1,stage=1,userid='".$userid."',assign_date='".date('Y-m-d H:i:s')."' where id ='".$newwebid."' and cmpid ='".$cmpData['cmpid']."'";
					mysqli_query($conn,$UpdSql);
					
					
					// update company for next day submit flag as 1 //
					$UpdCmpSql = "UPDATE "._PREFIX."company set submit_flag =1,submit_date='".date('Y-m-d')."' where cmpid ='".$cmpData['cmpid']."'";
					mysqli_query($conn,$UpdCmpSql);
			
					$assign = "Yes";
				}
				else
				{
					
				    $impdata =  getCmpNotAssign($conn,$fromdate,$todate);
					
					$impdata = (!empty($impdata)?$impdata:$cmpData['cmpid']);
				    
					$sql1 = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and assigned =0 and userid=0 and cmpid<>".$cmpData['cmpid']." and cmpid NOT IN(".$impdata.") ORDER BY RAND() limit 0,1";	
					$result1= mysqli_query($conn,$sql1);
					$web_rows1 = mysqli_num_rows($result1);
					$cmpRows1=mysqli_fetch_array($result1);
					
					$webid1	= $cmpRows1["id"];		
					$cmpid1	= $cmpRows1["cmpid"];
				
				
					if($web_rows1 > 0)		
					{
						
						// Assign user to  company website//			
						
						$update = "update "._PREFIX."company_website set userid='".$userid."',assigned=1,stage=1,assign_date='".date('Y-m-d H:i:s')."' where cmpid='".$cmpid1."' and id='".$webid1."'";
						$sender = mysqli_query($conn, $update);	
						
							// update company for next day submit flag as 1 //
					    $UpdCmpSql = "UPDATE "._PREFIX."company set submit_flag =1,submit_date='".date('Y-m-d')."' where cmpid ='".$cmpid1."'";
					    mysqli_query($conn,$UpdCmpSql);
					
						$assign = "Yes";	// assign
					}
					else
					{
						
						$assign = "Not";
					}
				}

			}
			else
			{
				
				// if submission count zero for eachday//
				$_SESSION['assign'] = 1;

				$sql1 = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and assigned =0 and userid=0 and cmpid='".$cmpData['cmpid']."' ORDER BY RAND() limit 0,1";	
				$result1= mysqli_query($conn,$sql1);
				$web_rows1 = mysqli_num_rows($result1);
				$cmpRows1=mysqli_fetch_array($result1);
				
				$webid1	= $cmpRows1["id"];		
				$cmpid1	= $cmpData['cmpid'];
			//exit;
			
				if($web_rows1 > 0)		
				{
					echo $mdd = "888888";
					// Assign user to  company website//			
					
					$update = "update "._PREFIX."company_website set userid='".$userid."',assigned=1,stage=1,assign_date='".date('Y-m-d H:i:s')."' where cmpid='".$cmpid1."' and id='".$webid1."'";
					$sender = mysqli_query($conn, $update);	
					
					// update company for next day submit flag as 1 //
				    $UpdCmpSql = "UPDATE "._PREFIX."company set submit_flag =1,submit_date='".date('Y-m-d')."' where cmpid ='".$cmpid1."'";
				    mysqli_query($conn,$UpdCmpSql);
					    
					$assign = "Yes";	// assign
				}
				else
				{
					echo $mdd= "55555";
					
				    $impdata =  getCmpNotAssign($conn,$fromdate,$todate);
					$impdata = (!empty($impdata)?$impdata:$cmpData['cmpid']);
				    
					$sql1 = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and assigned =0 and userid=0 and cmpid<>".$cmpid1." and cmpid NOT IN(".$impdata.") ORDER BY RAND() limit 0,1";	
					$result1= mysqli_query($conn,$sql1);
					$web_rows1 = mysqli_num_rows($result1);
					$cmpRows1=mysqli_fetch_array($result1);
					
					$webid1	= $cmpRows1["id"];		
					$cmpid1	= $cmpRows1["cmpid"];
				
				
					if($web_rows1 > 0)		
					{
						// Assign user to  company website//			
						
						$update = "update "._PREFIX."company_website set userid='".$userid."',assigned=1,stage=1,assign_date='".date('Y-m-d H:i:s')."' where cmpid='".$cmpid1."' and id='".$webid1."'";
						$sender = mysqli_query($conn, $update);	
						$assign = "Yes";	// assign
					}
				}
			}
		}
		else{
			
			
			// find total count of company submission//
				$arrcmp = array();
			/*
				
                $Cmpsql2 = "select distinct(c.cmpid) cmpid,
                (SELECT count(cw.id) cmpcount FROM `ri_company_website` cw
                WHERE cw.status=1 and c.cmpid=cw.cmpid
                and date_format(cw.assign_date,'%Y-%m-%d') BETWEEN '".$fromdate."' and '".$todate."') totcnt
                from ri_company_website c where c.status=1 and c.assigned<>0 order by cmpid asc";
                
				$cmpRes2 = mysqli_query($conn,$Cmpsql2);
				$cmpNumRow = mysqli_num_rows($cmpRes2);
				if($cmpNumRow > 0)
				{
					while($cmpRowsd=mysqli_fetch_array($cmpRes2))
					{
					    if($cmpRowsd['totcnt'] > 75)
					    {
						    $arrcmp[] = $cmpRowsd['cmpid'];
					    }
					}
				}
					$impdata = implode(",",$arrcmp);*/
			
			// if company submissin greater then 75 in a month//
			
			
		
			
			$impdata =  getCmpNotAssign($conn,$fromdate,$todate);
			$impdata = (!empty($impdata)?$impdata:$cmpData['cmpid']);
			
			$uc_sql = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and stage=0 and assigned=0 and userid='0' and cmpid NOT IN(".$impdata." ) ORDER BY RAND() limit 1";
			$uc_result= mysqli_query($conn,$uc_sql);
			$uc_rows = mysqli_num_rows($uc_result);
			
			$cmpRows 	= mysqli_fetch_array($uc_result);
					
			$newwebid	= $cmpRows["id"];				
			$newcmpid	= $cmpRows["cmpid"];				
			
			$UpdSql = "UPDATE "._PREFIX."company_website set assigned =1,stage=1,userid='".$userid."',assign_date='".date('Y-m-d H:i:s')."' where id ='".$newwebid."' and cmpid ='".$newcmpid."'";
			mysqli_query($conn,$UpdSql);
			
				// update company for next day submit flag as 1 //
		    $UpdCmpSql = "UPDATE "._PREFIX."company set submit_flag =1,submit_date='".date('Y-m-d')."' where cmpid ='".$newcmpid."'";
		    mysqli_query($conn,$UpdCmpSql);
		    
			$assign = "Yes";	// assign
		}
		
	}	
	else
	{
		$assign = "Not";	// assign
	}
	
	//echo"mdd==".$mdd;
	//exit;
	return $assign;
}

function getCmpNotAssign($conn,$fromdate,$todate)
{
    $impdata  = 0;
   echo"<li>GET==". $Cmpsql2 = "select distinct(c.cmpid) cmpid,
    (SELECT count(cw.id) cmpcount FROM `ri_company_website` cw
    WHERE cw.status=1 and c.cmpid=cw.cmpid
    and date_format(cw.assign_date,'%Y-%m-%d') BETWEEN '".$fromdate."' and '".$todate."') totcnt
    from ri_company_website c where c.status=1 and c.assigned<>0 order by cmpid asc";
    
	$cmpRes2 = mysqli_query($conn,$Cmpsql2);
	$cmpNumRow = mysqli_num_rows($cmpRes2);
	if($cmpNumRow > 0)
	{
		while($cmpRowsd=mysqli_fetch_array($cmpRes2))
		{
		    if($cmpRowsd['totcnt'] > 75)
		    {
			    $arrcmp[] = $cmpRowsd['cmpid'];
		    }
		}
	}

// if company submissin greater then 75 in a month//


    return $impdata = implode(",",$arrcmp);
}

/*
function AssignWebsite($conn,$userid)
{
	
	$cmpArr = array();
	
	
	//exit;
					
	// check if already assigned //
	  $sql = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and assigned =1 and userid='".$userid."'"; // ORDER BY RAND() limit 0,1
					
	$result = mysqli_query($conn,$sql);
	$web_rows = mysqli_num_rows($result);

	// if not assigned//
	if(empty($web_rows) || ($web_rows==0))
	{
	    $cmpRows = mysqli_fetch_array($result);
	    
	    $fromdate = date('Y-m')."-01";
	    $todate = date('Y-m')."-31";
	    	
		// first check each company for single submission RAND()//
		$Cmpsql = "SELECT cmpid from "._PREFIX."company where status = 1 and submit_flag=0 and submit_date='".date('Y-m-d')."'  ORDER BY  RAND() limit 0,1";
		$cmpRes = mysqli_query($conn,$Cmpsql);
		$cmpNumRow = mysqli_num_rows($cmpRes);
		$_SESSION['assign']  = 0;
		
		$cmpData = mysqli_fetch_array($cmpRes);	
		
		// check total count 75 for whole month submission and update table//
			
		$chkCompnyTot = ChkCompanyTotCount($conn,$cmpData['cmpid']); 
		
		//exit;
		if($chkCompnyTot <=75)
		{

			if($cmpNumRow > 0)	
			{
				
				
				// check total submission in a month ==75 //
				
				$uc_sql = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and stage=0 and assigned=0 and userid='0' and cmpid='".$cmpData['cmpid']."' ";
				$uc_result= mysqli_query($conn,$uc_sql);
				$uc_rows = mysqli_num_rows($uc_result);
			
				if(!empty($uc_rows))
				{
					$cmpRows 	= mysqli_fetch_array($uc_result);
					
					$newwebid	= $cmpRows["id"];				
					
					$UpdSql = "UPDATE "._PREFIX."company_website set assigned =1,stage=1,userid='".$userid."',assign_date='".date('Y-m-d H:i:s')."' where id ='".$newwebid."' and cmpid ='".$cmpData['cmpid']."'";
					mysqli_query($conn,$UpdSql);
					
					
					// update company for next day submit flag as 1 //
					$UpdCmpSql = "UPDATE "._PREFIX."company set submit_flag =1,submit_date='".date('Y-m-d')."' where cmpid ='".$cmpData['cmpid']."'";
					mysqli_query($conn,$UpdCmpSql);
			
					$assign = "Yes";
				}
				else
				{
					$sql1 = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and assigned =0 and userid=0 ORDER BY RAND() limit 0,1";	
					$result1= mysqli_query($conn,$sql1);
					$web_rows1 = mysqli_num_rows($result1);
					$cmpRows1=mysqli_fetch_array($result1);
					
					$webid1	= $cmpRows1["id"];		
					$cmpid1	= $cmpRows1["cmpid"];
				
				
					if($web_rows1 > 0)		
					{
						// Assign user to  company website//			
						
						$update = "update "._PREFIX."company_website set userid='".$userid."',assigned=1,stage=1,assign_date='".date('Y-m-d H:i:s')."' where cmpid='".$cmpid1."' and id='".$webid1."'";
						$sender = mysqli_query($conn, $update);	
						$assign = "Yes";	// assign
					}
					else
					{
						$assign = "Not";
					}
				}

			}
			else
			{
			
				
				$_SESSION['assign'] = 1;

				$sql1 = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and assigned =0 and userid=0 ORDER BY RAND() limit 0,1";	
				$result1= mysqli_query($conn,$sql1);
				$web_rows1 = mysqli_num_rows($result1);
				$cmpRows1=mysqli_fetch_array($result1);
				
				$webid1	= $cmpRows1["id"];		
				$cmpid1	= $cmpRows1["cmpid"];
			
			
				if($web_rows1 > 0)		
				{
					// Assign user to  company website//			
					
					$update = "update "._PREFIX."company_website set userid='".$userid."',assigned=1,stage=1,assign_date='".date('Y-m-d H:i:s')."' where cmpid='".$cmpid1."' and id='".$webid1."'";
					$sender = mysqli_query($conn, $update);	
					$assign = "Yes";	// assign
				}
				else
				{
					$assign = "Not";
				}
			}
		}
		else{
			// find total count of company submission//
				$arrcmp = array();
				$fromdate = date('Y-m')."-01";
				$todate = date('Y-m')."-31";
				
				//$Cmpsql2 = "SELECT cmpid from "._PREFIX."company where status = 1 and totcnt>=75";
				
                $Cmpsql2 = "select distinct(c.cmpid) cmpid,
                (SELECT count(cw.id) cmpcount FROM `ri_company_website` cw
                WHERE cw.status=1 and c.cmpid=cw.cmpid
                and date_format(cw.assign_date,'%Y-%m-%d') BETWEEN '".$fromdate."' and '".$todate."') totcnt
                from ri_company_website c where c.status=1 and c.assigned<>0 order by cmpid asc";
                
				$cmpRes2 = mysqli_query($conn,$Cmpsql2);
				$cmpNumRow = mysqli_num_rows($cmpRes2);
				if($cmpNumRow > 0)
				{
					while($cmpRowsd=mysqli_fetch_array($cmpRes2))
					{
					    if($cmpRowsd['totcnt'] > 75)
					    {
						    $arrcmp[] = $cmpRowsd['cmpid'];
					    }
					}
				}
			
			// if company submissin greater then 75 in a month//
			
			
			$impdata = implode(",",$arrcmp);
			
			$uc_sql = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and stage=0 and assigned=0 and userid='0' and cmpid NOT IN(".$impdata." ) ORDER BY RAND() limit 1";
			$uc_result= mysqli_query($conn,$uc_sql);
			$uc_rows = mysqli_num_rows($uc_result);
			
			$cmpRows 	= mysqli_fetch_array($uc_result);
					
			$newwebid	= $cmpRows["id"];				
			$newcmpid	= $cmpRows["cmpid"];				
			
			$UpdSql = "UPDATE "._PREFIX."company_website set assigned =1,stage=1,userid='".$userid."',assign_date='".date('Y-m-d H:i:s')."' where id ='".$newwebid."' and cmpid ='".$newcmpid."'";
			mysqli_query($conn,$UpdSql);
			$assign = "Yes";	// assign
		}
		
	}	
	else
	{
		$assign = "Not";	// assign
	}
	
	//exit;
	return $assign;
}
*/

function ChkCompanyTotCount($conn,$cmpid)
{
	$fromdate = date('Y-m')."-01";
	$todate = date('Y-m')."-31";
	
	$sql1="SELECT count(id) cmpcount FROM `ri_company_website` 
	WHERE status=1 AND cmpid='".$cmpid."'
	and date_format(assign_date,'%Y-%m-%d') between '".$fromdate."' and '".$todate."'";

	$result1= mysqli_query($conn,$sql1);
	$web_rows1 = mysqli_num_rows($result1);
	$cmpRows1=mysqli_fetch_array($result1);
	
	if($web_rows1 >= 75)
	{
		$nextmnth = date('Y-m-d',strtotime('first day of +1 month'));
	
		echo"upd11==".$UpdCmpCnt = "UPDATE "._PREFIX."company set totcnt =0,submit_date='".$nextmnth."',submit_flag=0 where status=1 and cmpid='".$cmpid."'";
		mysqli_query($conn,$UpdCmpCnt);
	}
	else{
		echo"upd22==".$UpdCmpCnt = "UPDATE "._PREFIX."company set totcnt =".$cmpRows1['cmpcount']." where status=1 and cmpid='".$cmpid."'";
		mysqli_query($conn,$UpdCmpCnt);
	}
	
	
	
	return $cmpRows1['cmpcount'];
	
}

function chkSunday($date)
{
	//$date = '2011-01-01';
	$timestamp = strtotime($date);
	$weekday= date("l", $timestamp );
	$normalized_weekday = strtolower($weekday);
	//echo"wweek==". $normalized_weekday ;
	if (($normalized_weekday == "sunday")) {
		return "true";
	} else {
		return "false";
	}
}









	
	function AssignNewCompany($conn)
	{
		 $sql = "SELECT id from "._PREFIX."company_website where status = 1 and assigned =1 and userid='".$_SESSION['user_id']."' ORDER BY RAND() limit 0,1";
		
		$cresult = mysqli_query($conn,$sql);
		$chkRows = mysqli_num_rows($cresult);
		
			
		// check already assigned or not //
		
	
		if(empty($chkRows))
		{
			 $uc_sql = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and stage=0 and assigned=0 and userid='0' ORDER BY RAND()";
			$uc_result= mysqli_query($conn,$uc_sql);
			$uc_rows = mysqli_num_rows($uc_result);
			if(!empty($uc_rows))
			{
				$cmpRows = mysqli_fetch_array($uc_result);	
				//shuffle($cmpRows);
				$newwebid	= $cmpRows["id"];
				$newcmpid	= $cmpRows["cmpid"];
				
				$UpdSql = "UPDATE "._PREFIX."company_website set assigned =1,stage=1,userid='".$_SESSION['user_id']."',assign_date='".date('Y-m-d H:i:s')."' where id ='".$newwebid."' and cmpid ='".$newcmpid."'";
				mysqli_query($conn,$UpdSql);
		
				$assign = "Yes";
			}
			else
			{
				$assign = "Not";	
			}
			
		}
		else
		{
			
			$assign = "Not";
		}
		//echo $assign;
		return $assign;
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