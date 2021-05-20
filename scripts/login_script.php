<?php

ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);


include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';
include_once '../google/gpConfig.php';


date_default_timezone_set('Asia/Calcutta');


$table = _PREFIX."users_log";
$success=0;

$arrMobile = "9967355303"; //MOBILE_NUMBER;
	
	$arrMobile = explode(',',$arrMobile);
	//echo"<pre>";print_r($arrMobile);
	foreach($arrMobile as $mobile)
	{
		$mobile='91'.$mobile; //Concatenate mobile number and country code.
	}
		
 // google chk //

 

if(isset($_GET['code'])){
	
	$gClient->authenticate($_GET['code']);
	
	$_SESSION['token'] = $gClient->getAccessToken();
	
}

if (isset($_SESSION['token'])) {
	$gClient->setAccessToken($_SESSION['token']);
}
// get google token  for access//

if ($gClient->getAccessToken()) {
	//Get user profile data from google
	$gpUserProfile = $google_oauthV2->userinfo->get();
	
	//echo"<pre>user==";print_r($gpUserProfile);
	
	$output = '';
	
	$email = explode('@',$gpUserProfile['email']);	
	
	//Initialize User class
	
	if($email[1]!='rathinfotech.com')
	{
		$domainChk = 0;	
	}
	else
	{
		$domainChk = 1;
	}
	
	$ipAddress = getIPaddress();		// get user ip address GIEGMPL//
	$ipAddress =  (!empty($ipAddress)?$ipAddress:'120.138.1.37');
	
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$agent_details = IPDetails($ipAddress);
	
	$ISPurl = ISPDetails($ipAddress);
	
	$currentTime = strtotime(date('Y-m-d H:i A'));					
	$inTime = strtotime(date('Y-m-d 09:30').' AM');	
	$outTime = strtotime(date('Y-m-d 06:30').' PM');

	//echo"<pte>ip==";print_r($ISPurl);
	
	$ISP = (!empty($ISPurl['isp'])?$ISPurl['isp']:"") ;
	
	// get user id // 

	$userSql = "SELECT id,role,role_active,empcode from "._PREFIX."users where status = 1 and email = '".trim($gpUserProfile['email'])."'";
	$uresult = mysqli_query($conn,$userSql);
	$userNum = mysqli_num_rows($uresult);
	$UserRow = mysqli_fetch_array($uresult);
	//echo"uid==".$UserRow['id'];
	
	
	
	if($userNum > 0 )
	{
		if($domainChk==1)
		{		
			
			try
			{
				// insert log details //
				 $query ="insert into ".$table."(userid,oauth_uid,first_name,last_name,email,locale,picture,useragent,ipaddress,agent_details,isp,status,login_date)
					values('".$UserRow['id']."','".$gpUserProfile['id']."','".$gpUserProfile['given_name']."','".$gpUserProfile['family_name']."','".$gpUserProfile['email']."','".$gpUserProfile['locale']."','".$gpUserProfile['picture']."','".$useragent."','".$ipAddress."','".$agent_details."','".$ISP."','".$domainChk."','".date("Y-m-d H:i:s")."')";
				
				mysqli_query($conn,$query);					
				$usid = mysqli_insert_id($conn);	// userlog id//
				
				session_start();
				$_SESSION['username'] 	= $gpUserProfile['given_name'];
				$_SESSION['name'] 		= $gpUserProfile['given_name'];
				$_SESSION['email'] 		= $gpUserProfile['email'];
				$_SESSION['user_id'] 	= $UserRow['id'];	//user id ;
				$_SESSION['empcode'] 	= $UserRow['empcode'];	//user id ;
				$_SESSION['role'] 		= $UserRow['role'];	//user role ;
				$_SESSION['roleActive'] = $UserRow['role_active'];	//dataentry role should active ;
				
				$_SESSION['logid'] 		= $usid;	//user log id ;
				$_SESSION['ipaddress'] 	= $ipAddress;	//ipaddress ;
			}
			catch (Exception $ex) 
			{
				$error = $ex->getMessage();
				
				$query_log ="insert into ri_error_log(userid,oauth_uid,name,email,remote_addr,types,pages,message,log_date,flag)
					values('".$UserRow['id']."','".$gpUserProfile['id']."','".$gpUserProfile['name']."','".$gpUserProfile['email']."','".$ipAddress."','Login','Login','".$error."','".date("Y-m-d H:i:s")."',0)";

				 mysqli_query($conn,$query_log);
			}
			
			// if login with admin//
			if($UserRow['role']=='admin')	
			{
				
				?>
				<script> 			
					opener.location.href ='<?=BASE_URL?>dashboard.php?action=';
					close();		
				</script>
				
				<?php
			}
						
				// for dataentry user login //
			
			else if((!empty($UserRow['role']) &&($UserRow['role']!="admin") ))
			{

				if($ipAddress=="103.146.229.121")		
				
				{
					
					if(($currentTime < $inTime) && ($currentTime > $outTime)) // chk intime and outtime //
					{				
								
					//	$SendSms = SmsOTP($gpUserProfile['email'],$ipAddress);	// send sms for login when intime > 9:30 and outtime >0630 //

						$errMsg = $gpUserProfile['email'].' - OTP for '.$inTime.$outTime;
						$query_log ="insert into ri_error_log(userid,name,email,remote_addr,types,pages,message,log_date,flag)
						values('".$UserRow['id']."','".$gpUserProfile['name']."','".$gpUserProfile['email']."','".$ipaddress."','Login User','Login User','".$errMsg."','".date("Y-m-d H:i:s")."',0)";					
						mysqli_query($conn,$query_log);	
						
						if(($UserRow['role']=='dataentry') && ($UserRow['role_active']==1)){				
							$assign = AssignWebsite($conn,$UserRow['id']); // assign website //
						}
						
					?>
					 <script> 			
						//opener.location.href ='<?=BASE_URL?>verifyotp.php?action=verifyotp';
						    	opener.location.href ='<?=BASE_URL?>dashboard.php?action=';
						close();		
					</script>
							
					<?php
					
					}	
					else
					{
						//$ispStatus = 'GAutam Import Export and General Merchant Pvt Ltd';	
						if(($UserRow['role']=='dataentry') && ($UserRow['role_active']==1)){				
							$assign = AssignWebsite($conn,$UserRow['id']); // assign website //
						}
					
					?>
					 <script> 			
						opener.location.href ='<?=BASE_URL?>dashboard.php?action=';
						close();		
					</script>
					<?php
					}
				}			
				else
				{

					$ispStatus = 'Other';
					$cookieId = md5('rathinfo#'.$UserRow['id']);
					$cookieStatus = SetCookies($conn,$cookieId,$UserRow['id']);		// chk cookies set or not//	
					
					if(($UserRow['role']=='dataentry') && ($UserRow['role_active']==1)){				
							$assign = AssignWebsite($conn,$UserRow['id']); // assign website //
							$message = "Task assigned.Login with other ipaddress";
							$pages = "Assign list";
							
					}
					else{
						$message = "Login with other ipaddress";
						$pages = $UserRow['role']." Dashboard";
					}
					
					$query_log ="insert into ri_error_log(userid,oauth_uid,name,email,remote_addr,types,pages,message,log_date,flag)
					values('".$UserRow['id']."','".$gpUserProfile['id']."','".$gpUserProfile['name']."','".$gpUserProfile['email']."','".$ipAddress."','".$pages."','".$message."','".$chkCookie."','".date("Y-m-d H:i:s")."',0)";					
					mysqli_query($conn,$query_log);	

					if($cookieStatus=="yes")
					{
					?>
					 <script>							
                        opener.location.href ='<?=BASE_URL?>dashboard.php?action=';
                        close();		
                    </script>
                    <?php
					}
					else
					{
						//$SendSms = SmsOTP($gpUserProfile['email'],$ipAddress);	// send sms to user//
					?>
						<script> 			
							// opener.location.href ='<?=BASE_URL?>verifyotp.php?action=verifyotp';	
							 	opener.location.href ='<?=BASE_URL?>dashboard.php?action=';
							close();		
						</script>
                    <?php
					}
				
				}						
					
			}	// end dataentry //
			else
			{					
				?>
				 <script> 			
					opener.location.href ='<?=BASE_URL?>dashboard.php?action=';
					close();		
				</script>
				<?php	
			}			
		
		
		}	// end status //
		
		else		// error for login with another IP //
		{
		    $userId = (isset($UserRow['id'])?$UserRow['id']:0);
			$errMsg = $gpUserProfile['email'].' Use Another Email Id. IP address: '.$ipAddress;
		$query_log ="insert into ri_error_log(userid,oauth_uid,name,email,remote_addr,types,pages,message,log_date,flag)
					values('".$userId."','".$gpUserProfile['id']."','".$gpUserProfile['name']."','".$gpUserProfile['email']."','".$ipAddress."','Login','Login','".$errMsg."','".date("Y-m-d H:i:s")."',0)";

			 mysqli_query($conn,$query_log);
		
			?>
			<script>
				opener.location.href ="<?=BASE_URL?>login.php?errmsg=yes";
				close();     
			</script>
			
		   <?php
			
		}
		
	}
	else
	{
	     $userId = (isset($UserRow['id'])?$UserRow['id']:0);
		$errMsg = $gpUserProfile['email'].' User Not Registered!IP address: '.$ipAddress;
	 $query_log ="insert into ri_error_log(userid,oauth_uid,name,email,remote_addr,types,pages,message,log_date,flag)
					values('".$userId."','".$gpUserProfile['id']."','".$gpUserProfile['name']."','".$gpUserProfile['email']."','".$ipAddress."','Login','Login','".$errMsg."','".date("Y-m-d H:i:s")."',0)";
					
			 mysqli_query($conn,$query_log);
			 
			
		?>
			<script>
				opener.location.href ="<?=BASE_URL?>login.php?errmsg=yes";
				close();     
			</script>
			
		  <?php	
	}
	
 
} 

ob_flush(); 


function SetCookies($conn,$cookieId,$userid)
{
	$chkCookie = "SELECT is_delete,login_date from "._PREFIX."setuser_cookie where status = 1 and cookieid = '".trim($cookieId)."' and userid='".$userid."' ";
	$cookieRes = mysqli_query($conn,$chkCookie);
	$cookieNum = mysqli_num_rows($cookieRes);

	if($cookieNum > 0)
	{
		$cookieRow = mysqli_fetch_array($cookieRes);
		
		$loginDate = $cookieRow['login_date'];
		$currDate = date('Y-m-d H:i:s');
		
		$diffDate = (strtotime($currDate) - strtotime($loginDate));	// find no of days of cookies 
		
		$diffDate = abs(round($diffDate / 86400));	// 7days
	
		if($cookieRow['is_delete']==0)
		{
			if($diffDate < 7)
			{
				$mssg = "yes";
			
			}
			else
			{
				
				 $updateCookis = "update "._PREFIX."setuser_cookie set status=2,is_delete=1,modified_date='".date('Y-m-d H:i:s')."' where cookieid = '".trim($cookieId)."' and userid='".$userid."'";
	$sender = mysqli_query($conn, $updateCookis);
	
				$mssg = "no";
				
			}
		}
		else
		{
			$mssg = "no";	
				
		}
	}
	else
	{
		$mssg = "no";	
	}
		return $mssg;
}


function AssignWebsite($conn,$userid)
{
	
	$cmpArr = array();
	$fromdate = date('Y-m')."-01";
	$todate = date('Y-m')."-31";
					
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
		
		// check total count for whole month submission and update table //
			
		$chkCompnyTot = ChkCompanyTotCount($conn,$cmpData['cmpid']); 
		
		//exit;
		if($chkCompnyTot <=75)
		{

			if($cmpData['totcnt']!= 0)	
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
				    $impdata =  getCmpNotAssign($conn,$fromdate,$todate);
				    
					$sql1 = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and assigned =0 and userid=0 and cmpid<>".$cmpData['cmpid']." and cmpid NOT IN(".$impdata.")  ORDER BY RAND() limit 0,1";	
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
			
				$_SESSION['assign'] = 1;

			
				$sql1 = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and assigned =0 and userid=0 and cmpid='".$cmpData['cmpid']."' ORDER BY RAND() limit 0,1";	
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
		else{
			// find total count of company submission//
				$arrcmp = array();
				
				
			$impdata =  getCmpNotAssign($conn,$fromdate,$todate);
			
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
		    
			
			$assign = "Yes";
		}
		
	}	
	else
	{
		$assign = "Not";	// assign
	}
	
	//exit;
	return $assign;
}


function getCmpNotAssign($conn,$fromdate,$todate)
{
    
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


    return $impdata = implode(",",$arrcmp);
}

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
	
		$UpdCmpCnt = "UPDATE "._PREFIX."company set totcnt =0,submit_date='".$nextmnth."',submit_flag=0 where status=1 and cmpid='".$cmpid."'";
		mysqli_query($conn,$UpdCmpCnt);
	}
	else{
		$UpdCmpCnt = "UPDATE "._PREFIX."company set totcnt =".$cmpRows1['cmpcount']." where status=1 and cmpid='".$cmpid."'";
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

/*
function AssignWebsite($conn,$userid)
{
	$cmpArr = array();
					
	 $sql = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and assigned =1 and userid='".$userid."'"; // ORDER BY RAND() limit 0,1
					
	$result = mysqli_query($conn,$sql);
	$web_rows = mysqli_num_rows($result);
	$cmpRows = mysqli_fetch_array($result);
	
	if(empty($web_rows) || ($web_rows==0))
	{
		// first check each company for single submission //
		$Cmpsql = "SELECT cmpid from "._PREFIX."company where status = 1 and submit_flag=0 and submit_date='".date('Y-m-d')."' ORDER BY RAND() limit 0,1";
		$cmpRes = mysqli_query($conn,$Cmpsql);
		$cmpNumRow = mysqli_num_rows($cmpRes);
		$_SESSION['assign']  = 0;

		if($cmpNumRow > 0)	
		{
			$cmpData = mysqli_fetch_array($cmpRes);	
			
			$uc_sql = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and stage=0 and assigned=0 and userid='0' and cmpid='".$cmpData['cmpid']."' ";
			$uc_result= mysqli_query($conn,$uc_sql);
			$uc_rows = mysqli_num_rows($uc_result);

			if(!empty($uc_rows))
			{
				$cmpRows 	= mysqli_fetch_array($uc_result);	
				
				$newwebid	= $cmpRows["id"];				
				
				$UpdSql = "UPDATE "._PREFIX."company_website set assigned =1,stage=1,userid='".$userid."',assign_date='".date('Y-m-d H:i:s')."' where id ='".$newwebid."' and cmpid ='".$cmpData['cmpid']."'";
				mysqli_query($conn,$UpdSql);

				$UpdCmpSql = "UPDATE "._PREFIX."company set submit_flag =1,submit_date='".date('Y-m-d')."' where cmpid ='".$cmpData['cmpid']."'";
				mysqli_query($conn,$UpdCmpSql);
		
				$msg = 1;
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
					$msg = 1;	// assign
				}
				else
				{
					$msg = 0;
				}
			}

		}
		else
		{
			if($_SESSION['assign']==0){
				$UpdCmpSql = "UPDATE "._PREFIX."company set submit_flag =0,submit_date='".date('Y-m-d', strtotime(' +1 day'))."' where status=1";
				mysqli_query($conn,$UpdCmpSql);
			}
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
				$msg = 1;	// assign
			}
			else
			{
				$msg = 0;
			}
		}
		
	}	
	else
	{
		$msg = 0;	// assign
	}
	return $msg;
}
*/



/*
function AssignWebsite($conn,$userid)
{
	$cmpArr = array();
					
	 $sql = "SELECT id,cmpid from "._PREFIX."company_website where status = 1 and assigned =1 and userid='".$userid."'"; // ORDER BY RAND() limit 0,1
					
	$result = mysqli_query($conn,$sql);
	$web_rows = mysqli_num_rows($result);
	$cmpRows = mysqli_fetch_array($result);
	
	if(empty($web_rows) || ($web_rows==0))
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
			$msg = 1;	// assign
		}
		else
		{
			$msg = 0;
		}
		
	}	
	else
	{
		$msg = 0;	// assign
	}
	return $msg;
}*/

function SmsOTP($email,$ipadd)
{	
		
	$mobileAuth = MOBILE_AUTH;
	
	$arrMobile1 = array(9967355303,9867159161,9999097408);
	$OTPmsg = "";
	
	
	if(!empty($arrMobile1))
	{   
	    $otp= rand(1000,9999);  //Generate random number as OTP
			
		//$OTPmsg="Your OTP is ".$otp." to Login Verification for '".$email."'.";   //Your Custom Message
		$OTPmsg = $otp." is OTP for login to RATH app for ".$email;
		$OTPmsg .=" IP: http://ip-api.com/line/".$ipadd;
		
		foreach($arrMobile1 as $smobile)
		{		
		
			$mobile='91'.$smobile; //Concatenate mobile number and country code.
			
				
			//Hit the API
			  $postUrl = htmlspecialchars_decode("https://control.msg91.com/api/sendotp.php?authkey=".$mobileAuth."&mobile=".$mobile."&message=".$OTPmsg."&sender=RATHINFO&otp=".$otp,ENT_NOQUOTES);
			
			
		
			$contents = file_get_contents($postUrl);    
		   
			$result=json_decode($contents,true);		  
			//echo"type==".$result['type'];exit;
		}
		
		if($result['type']=="success")
		{
		  return "send";
		
		}
		else
		{
		  return "notsend";
		
		}
			
						 
		 	//Get response in $result variable typically you will recieve the message  ID as part od succesfully send message.
		
	  
	}
	
}
	
	

?>