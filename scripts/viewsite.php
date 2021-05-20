
 <?php 

 ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);

include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

	
	$url = $_POST['url'];
	$baseurl = basedomain($url);
	$ipaddress = $_POST['ipadd'];
	$pa = $_POST['pa'];
	$da = $_POST['da'];
	$comment = $_POST['comment'];
	
	

	
	//exit;
	if(isset($_POST['type']) && $_POST['type']='delete')
	{
		
		foreach($_POST['site'] as $value)
		{			
			
			  $update = "update "._PREFIX."website set status='2' where id='".$value."'";
			mysqli_query($conn, $update);	
			
			  $update1 = "update "._PREFIX."company_website set status='2' where webid='".$value."'";
			mysqli_query($conn, $update1);	
		}
		
		header('location:'.BASE_URL.'dashboard.php?action=removesite');	
		//exit;
	}
	else
	{
	
		if(!empty($url))
		{
			$html = '';	
			$SQL="SELECT id,url,ipaddress,pa,da,comment,status FROM ri_website WHERE  url like '%".$baseurl."%'";	
			$result= mysqli_query($conn,$SQL);
			$count = mysqli_num_rows($result);
			
			if($count > 0)
			{			
				$html .='<form name="chksiteFrm" id="chksiteFrm" action="scripts/viewsite.php" method="post"><div class="widget-content"><table class="table table-bordered "><thead><tr><th>URL</th><th>IP Address</th><th>PA</th><th>DA</th><th>Status</th><th>Action </th></tr></thead><tbody>';
				$i=1;
				while($fetch_data=mysqli_fetch_array($result))
				{
					$status = ($fetch_data["status"]==1?"Active":"Deactive");
				$html .='<tr><td>'.$fetch_data["url"].'</td><td>'.$fetch_data["ipaddress"].'</td><td>'.$fetch_data["pa"].'</td><td>'.$fetch_data["da"].'</td><td>'.$status.'</td><td><input type="checkbox" name="site[]" id="site" class="site" value="'.$fetch_data["id"].'"></td>';
					$i++;
				}
				$html .='<tr><td colspan="5" style="text-align:center"><input type="submit" class="btn btn-success" name="submit" id="remove"  value="Remove"/></td></tr><input type="hidden" name="type" value="remove"></form></tbody></table></div>';	
				
			}
		}
			
	}
			
			
			echo  $html;
		
	
	
	

//scripts/insert_site.php

?>
