
 <?php 

 ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);
ini_set ( 'max_execution_time', 0); 


include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';

	$created_date	= date('Y-m-d H:i:s');
	
	$type			 = $_POST['type'];
	
	//echo"<pre>post";print_r($_POST);exit;
	
	
	if($_POST['type']=='material')
	{
		
		$html = '';	
		$SQL="SELECT m.id,m.materialname,m.materialcode FROM ri_productmaterial pm
		 JOIN ri_material m ON(m.id=pm.material_id) 
		 WHERE pm.status=1 and pm.product_id ='".$_POST['pid']."' GROUP BY m.id
		 ORDER BY m.materialname ASC";	
		$result= mysqli_query($conn,$SQL);
		$count = mysqli_num_rows($result);
	
		if($count > 0)
		{			
			$i=1;
			$html .='<option value="0" >Select Material</option>';
			while($fetch_data=mysqli_fetch_array($result))
			{
				$html .='<option value="'.$fetch_data['id'].'" >'.$fetch_data['materialname'].'</option>';
				$i++;
			}
			echo $html;
			
		}
			
		
		}
		else if($_POST['type']=='subproduct')
		{
			$html = '';	
			$SQL="SELECT id,product_id,opname,opcode FROM ri_subproduct WHERE status=1 and  product_id IN(".$_POST['pid'].") ORDER BY opname ASC";	
			$result= mysqli_query($conn,$SQL);
			$count = mysqli_num_rows($result);
			
			if($count > 0)
			{			
				$json = array();
				$i=1;
				$html .='<option value="0" >Select Subproduct</option>';
				
				while($fetch_data=mysqli_fetch_array($result))
				{
				    //echo"<li>op==".utf8_encode($fetch_data['opname']);
					
					$json[$fetch_data['id']] = ($fetch_data['opname']);
					
					$i++;
				}
				
				echo json_encode($json);
				
		
				
			}
			
			
		}
		else if($_POST['type']=='subproductType')
    	{
    	
    		$html = '';	
			
			$subproduct = explode(",",$_POST['subid']);
			
			foreach($subproduct as $suvVal)
			{
				$SQL="SELECT id,pid,subid,prodtype FROM ri_subproduct_type WHERE status=1 and  subid ='".$suvVal."' ORDER BY prodtype ASC";	
				$result= mysqli_query($conn,$SQL);
				$count = mysqli_num_rows($result);
				
				if($count > 0)
				{
					$html .='<div class="custom-subprodtype'.$suvVal.'" style="background:#a8e3ef7a;font-weight:bold;">'.SubProductValue($conn,$suvVal).'</div>';
					
					while($fetch_data=mysqli_fetch_array($result))
					{						
						$json[$suvVal][] = $fetch_data['prodtype'];
						
						$html .='<div class="custom-select-option-subprodtype" ><input onChange="toggleFillColorSubProdType(this);" class="custom-select-option-checkbox-subprodtype" id="custom-select-option-checkbox-subprodtype" type="checkbox"  name="subproducttype[]" value="'.$fetch_data['id'].'" title="'.utf8_encode($fetch_data['prodtype']).'">'.utf8_encode($fetch_data['prodtype']).'</div>';
						
						$i++;
					}
					
					
				}
			}
			
			echo $html;
			
			
            		/*$SQL="SELECT id,pid,subid,prodtype FROM ri_subproduct_type WHERE status=1 and  subid IN(".$_POST['subid'].") ORDER BY prodtype ASC";	
            		$result= mysqli_query($conn,$SQL);
            		$count = mysqli_num_rows($result);
            		
            		if($count > 0)
            		{			
            			$json = array();
            			$i=1;
            		    
            		    //$json[0] = "Sub Product Type-".$_POST['subid'];
            			
            			while($fetch_data=mysqli_fetch_array($result))
            			{
            				//echo"<li>".$fetch_data['opname'];
            				
            				$json[$fetch_data['id']] = $fetch_data['prodtype'];
            				
            				$i++;
            			}
            			
            		//	echo"<pre>";print_r($json);
            			
            			echo json_encode($json);
            			
            		}
            		*/
    		
    	}
		else if($_POST['type']=='grades')
		{
			$html = '';	
			//echo"<pre>podt==";print_r($_POST);
			$subprod = $_POST['subpid'];
			
			$pid = explode(',',$_POST['pid']);
			
			$product = implode(',',$pid);
			
			$product = "'".implode("'".','."'",$pid)."'";
			
			if(!empty($_POST['subpid']))
			{
				$append = " and subproduct_id='".$_POST['subpid']."'";	
			}
			else
			{
				$append = "";	
			}
			//and  product_id IN(".$product.")
			
			 $SQL="SELECT id,gradesname,gradescode FROM ri_grades WHERE status=1 and product_id =".$product." and material_id ='".$_POST['mid']."' and subproduct_id=0 and equivalent_id=0 GROUP BY gradesname ORDER BY gradesname ASC";	
			
			$result= mysqli_query($conn,$SQL);
			$count = mysqli_num_rows($result);
			
			if($count > 0)
			{			
				
				$i=1;
				$json = array();
				$html .='<option value="0" >Select Grades</option>';
				
				while($fetch_data=mysqli_fetch_array($result))
				{
					
					$json[$i] = $fetch_data['gradesname'];
					
					$i++;
				}
				
				//echo"<pre>json==";print_r($json);
				
				echo json_encode($json);
				
			}
			
		}
		
		else if($_POST['type']=='equivalent')
		{
			$html = '';	
			
			
			
			$grades = explode(',',$_POST['gradVal']);
			
			//echo"<pre>grades==";print_r($_POST);
			
			$gradVal = explode(',',$_POST['gradVal']);
			$impEquVal = "'".implode("'".','."'",$gradVal)."'";
			
			/*
			if(isset($grades))
			{
				foreach($grades as $gdVal)
				{
					$SQLGrade="SELECT id FROM ri_grades WHERE status=1 and  gradesname ='".$gdVal."'";	
					$result= mysqli_query($conn,$SQL);
					$count = mysqli_num_rows($result);	
				}
			}
			
			
			  $SQL="SELECT id,gradesname,gradescode FROM ri_grades WHERE status=1 and  equi_code IN(".$impEquVal.") AND subproduct_id=0 GROUP BY gradesname ORDER BY gradesname ASC";	
			$result= mysqli_query($conn,$SQL);
			$count = mysqli_num_rows($result);
			
			if($count > 0)
			{			
				$json = array();
				$i=1;
				
				
				while($fetch_data=mysqli_fetch_array($result))
				{
					//$html .='<option value="'.$fetch_data['id'].'" >'.$fetch_data['opname'].'</option>';
					//
					
					$json[$i] = $fetch_data['gradesname'];
					
					$i++;
				}
				echo json_encode($json);
				
			}
			*/
			
			  $SQL="SELECT comp_text FROM ri_chemical_comp WHERE status=1 AND TYPE='equiv' and  grades IN(".$impEquVal.")";
			 $result= mysqli_query($conn,$SQL);
			 $count = mysqli_num_rows($result);
			
    		if($count > 0)
    			{
    				while($fetch_data=mysqli_fetch_array($result))
    				{
    				
    					//echo"comp==". $fetch_data['comp_text'];
    				
    					$jsonData[] = json_decode($fetch_data['comp_text'],true);
    					
    					
    					//$jsonData1= array_filter($jsonData);
    					
    					
    				}
    				
    			}
    			
    			//echo"<pre>jsonData@==";print_r($jsonData);
    			
    			foreach($jsonData as $jsData)
    			{
    				//echo"<pre>jsData@==";print_r($jsData);
    				
    				$DataArr[] = $jsData['UNS'];
    				$DataArr[] = $jsData['DIN'];
    				
    			}
			
			echo json_encode($DataArr);
			
		}
		else if($_POST['type']=='getCity')
		{
			$html = '';	
			$SQL="SELECT id,city FROM ri_city WHERE status=1 and  country ='".$_POST['cntid']."' ORDER BY city ASC";	
			$result= mysqli_query($conn,$SQL);
			$count = mysqli_num_rows($result);
			
			if($count > 0)
			{			
				$json = array();
				$i=1;
				$html .='<option value="0" >Select City</option>';
				
				while($fetch_data=mysqli_fetch_array($result))
				{
					
					$json[$fetch_data['city']] = $fetch_data['city'];
					
					$i++;
				}
				echo json_encode($json);
				
			}
			
			
		}
		else if($_POST['type']=='Astm')
		{
			//echo"<pre>post==";print_r($_POST);
			
			//explode($_POST['prodValue']);
			
			$impProd 		= "'".implode("','", explode(",",$_POST['prodValue']))."'";
			$impSubProd 	= "'".implode("','", explode(",",$_POST['SubprodValue']))."'";
			$material 		= $_POST['material'];
			$grades 		= "'".implode("','", explode(",",$_POST['gradVal']))."'";
			
			$html = '';	
			$SQL="SELECT id,astm FROM ri_mechanical_comp WHERE status=1 and  product IN(".$impProd.") and  subproduct IN(".$impSubProd.") and  material ='".$material."' and  grades IN(".$grades.") ORDER BY id ASC";	
			$result= mysqli_query($conn,$SQL);
			$count = mysqli_num_rows($result);
		
			if($count > 0)
			{			
				$json = array();
				$i=1;
				$html .='<option value="0" >Select Astm</option>';
				
				while($fetch_data=mysqli_fetch_array($result))
				{
					
					$json[$fetch_data['astm']] = $fetch_data['astm'];
					
					$i++;
				}
				echo json_encode($json);
				
			}
			
			
		}
		
		else if($_POST['type']=='Asme')
		{
			//echo"<pre>post==";print_r($_POST);
			
			//explode($_POST['prodValue']);
			
			$impProd 		= "'".implode("','", explode(",",$_POST['prodValue']))."'";
			$impSubProd 	= "'".implode("','", explode(",",$_POST['SubprodValue']))."'";
			$material 		= $_POST['material'];
			$grades 		= "'".implode("','", explode(",",$_POST['gradVal']))."'";
			
			$html = '';	
			$SQL="SELECT id,asme FROM ri_mechanical_comp WHERE status=1 and  product IN(".$impProd.") and  subproduct IN(".$impSubProd.") and  material ='".$material."' and  grades IN(".$grades.") ORDER BY id ASC";	
			$result= mysqli_query($conn,$SQL);
			$count = mysqli_num_rows($result);
		
			if($count > 0)
			{			
				$json = array();
				$i=1;
				$html .='<option value="0" >Select Asme</option>';
				
				while($fetch_data=mysqli_fetch_array($result))
				{
					
					$json[$fetch_data['asme']] = $fetch_data['asme'];
					
					$i++;
				}
				echo json_encode($json);
				
			}
			
			
		}
		else if($_POST['type']=='getSubProduct')
		{
			$html = '';	
			 $SQL="SELECT id,product_id,opname,opname FROM ri_subproduct WHERE status=1 and  product_id IN(".$_POST['prodid'].") ORDER BY opname ASC";	
			$result= mysqli_query($conn,$SQL);
			$count = mysqli_num_rows($result);
			
			if($count > 0)
			{			
				$json = array();
				$i=1;
				$html .='<option value="0" >Select Subproduct</option>';
				
				while($fetch_data=mysqli_fetch_array($result))
				{
					//echo"<li>".$fetch_data['opname'];
					
					$json[$fetch_data['id']] = $fetch_data['opname'];
					
					$i++;
				}
				echo json_encode($json);
				
			}
			
			
		}
		else
		{
			
		}
	
	
	exit;

function SubProductValue($conn,$subid)
{
	$SQL="SELECT opname FROM ri_subproduct WHERE status=1 and id ='".$subid."' ";	
	$result= mysqli_query($conn,$SQL);
	$count = mysqli_num_rows($result);
	
	$spdata=mysqli_fetch_array($result);
	
	return $spdata['opname'];
	
}

?>

