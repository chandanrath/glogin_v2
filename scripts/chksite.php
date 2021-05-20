
 <?php 

 ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);

include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

	$created_date	= date('Y-m-d H:i:s');
	$url 			= $_POST['url'];
	$baseurl 		= basedomain($url);
	$ipaddress 		= $_POST['ipadd'];
	$pa 			= $_POST['pa'];
	$da 			= $_POST['da'];
	$comment 		= $_POST['comment'];
	$type			 = $_POST['type'];
	
	
	if($_POST['type']=='add_new')
	{
		
		if(!empty($url))
		{
			$html = '';	
			 $SQL="SELECT url,ipaddress,pa,da,comment FROM ri_website WHERE status=1 and  url like '%".$baseurl."%'";	
			$result= mysqli_query($conn,$SQL);
			$count = mysqli_num_rows($result);
			
			
			
				if($count > 0)
				{			
					$html .='<form name="chksiteFrm" id="chksiteFrm" action="scripts/insert_site.php" method="post"><div class="widget-content"><table class="table table-bordered "><thead><tr><th>URL</th><th>IP Address</th><th>PA</th><th>DA</th><th>Status</th></tr></thead><tbody>';
					$i=1;
					while($fetch_data=mysqli_fetch_array($result))
					{
					$html .='<tr><td>'.$fetch_data["url"].'</td><td>'.$fetch_data["ipaddress"].'</td><td>'.$fetch_data["pa"].'</td><td>'.$fetch_data["da"].'</td><td>'.$fetch_data["status"].'</td>';
						$i++;
					}
					$html .='<tr><td colspan="5" style="text-align:center"><input type="submit" class="btn btn-success" name="submit" id="proceed"  value="Proceed" onClick="proceed();"/></td></tr><input type="hidden" name="url" value="'.$url.'"><input type="hidden" name="ipaddress" value="'.$ipaddress.'"><input type="hidden" name="pa" value="'.$pa.'"><input type="hidden" name="da" value="'.$da.'"><input type="hidden" name="comment" value="'.$comment.'"><input type="hidden" name="type" value="'.$type.'"></form></tbody></table></div>';	
					
				}
		
				else if(!empty($ipaddress))
				{
					$html1 = '';	
					 $SQL="SELECT url,ipaddress,pa,da,comment,status FROM ri_website WHERE status=1 and ipaddress like '%".$ipaddress."%'";	
					$result= mysqli_query($conn,$SQL);
					$count = mysqli_num_rows($result);
					
					if($count > 0)
					{			
						$html .='<form name="chksiteFrm" id="chksiteFrm" action="scripts/insert_site.php" method="post"><div class="widget-content"><table class="table table-bordered "><thead><tr><th>URL</th><th>IP Address</th><th>PA</th><th>DA</th><th>Status</th></tr></thead><tbody>';
						$i=1;
						while($fetch_data=mysqli_fetch_array($result))
						{
						$html .='<tr><td>'.$fetch_data["url"].'</td><td>'.$fetch_data["ipaddress"].'</td><td>'.$fetch_data["pa"].'</td><td>'.$fetch_data["da"].'</td><td>'.$fetch_data["status"].'</td>';
							$i++;
						}
						$html .='<tr><td colspan="5" style="text-align:center"><input type="submit" class="btn btn-success" name="submit" id="proceed"  value="Proceed"/></td></tr><input type="hidden" name="url" value="'.$url.'"><input type="hidden" name="ipaddress" value="'.$ipaddress.'"><input type="hidden" name="pa" value="'.$pa.'"><input type="hidden" name="da" value="'.$da.'"><input type="hidden" name="comment" value="'.$comment.'"><input type="hidden" name="type" value="'.$type.'"></form></tbody></table></div>';	
						
					}	
					else
					{
						
						$query = "insert into ri_website(url,ipaddress,pa,da,comment,status,created)
						values('".$url."','".$ipaddress."','".$pa."','".$da."','".$comment."','1','".$created_date."')";
						$sql_qury = mysqli_query($conn,$query);
						$html ="Website Inserted Successfully.";
						
						?><script>window.location.href='dashboard.php?action=website'</script>
                        <?php
					}
				}
				else
				{
						$html ="";
				}
			}
			else
			{
				$html ="Please Enter URL.";
			}
			
			echo  $html;
		}
		else if($_POST['type']=='edit')
		{
			
			$update = "update "._PREFIX."website set url='".$url."',ipaddress='".$ipaddress."',pa='".$pa."',da='".$da."',comment='".$comment."' where id='".substr(decode($_POST['site_id']),2)."'";
			mysqli_query($conn, $update);	
			echo"Website Update Successfullt!";
		
		}
		else
		{
			
		}
	
	
	

//scripts/insert_site.php

?>

<script>
function Proceed()
{
	
	var i=window.confirm("Are You Sure Want To Proceed Data");
	if(i)
	{		
		document.location.href="scripts/insert_site.php&type=add_new";
	}
	
}
</script>