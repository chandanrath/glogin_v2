<?php
header('Content-Type: text/html; charset=utf-8');
ob_start(); 
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);

include_once '../includes/connect.php';
include_once '../includes/define.php';
include_once '../includes/functions.php';


date_default_timezone_set('Asia/Calcutta'); 


unset($_SESSION['error']);

$date			= date('Y-m-d H:i:s');
$operation		= $_POST['operation'];

$created_date	= date('Y-m-d H:i:s');

//echo"<pre>";print_r($_POST);//exit;

if($operation=='add_chemical')
{	

	$chmname = $_POST['chmname'];


	 $query ="insert into ri_chemical(chmname,status,type)
	values('".$chmname."','1','chem')";
	
	mysqli_query($conn,$query);			 
	$insert_id = mysqli_insert_id();
	
	header('location:'.BASE_URL.'dashboard.php?action=chemical_list');	
	
}
else if($_REQUEST['mode']=='delete_chemical')
{	
	
	$ID	=	substr(decode($_REQUEST['d']),2);	
	 $update = "update ri_chemical set status='2' where id='".$ID."'";
	mysqli_query($conn, $update);
	
	header('location:'.BASE_URL.'dashboard.php?action=chemical_list');		
}
else if($operation=='edit_chemical')
{	
	$chmname = $_POST['chmname'];
	$chmid	=	substr(decode($_POST['chmid']),2);	
	 $update = "update ri_chemical set chmname='".$chmname."' where id='".$chmid."'";
	mysqli_query($conn, $update);	
	
	header('location:'.BASE_URL.'dashboard.php?action=chemical_list');	
	//exit;
}

else if($operation=='add_mechanical')
{	

	$mechname = $_POST['mechname'];

	$query ="insert into ri_mechanical(mechname,status) values('".$mechname."','1')";

	mysqli_query($conn,$query);			 
	$insert_id = mysqli_insert_id();

	header('location:'.BASE_URL.'dashboard.php?action=mechanical_list');	
	
}
else if($_REQUEST['mode']=='delete_mechanical')
{	
	
	$ID	=	substr(decode($_REQUEST['d']),2);	
	 $update = "update ri_mechanical set status='2' where id='".$ID."'";
	mysqli_query($conn, $update);
	
	header('location:'.BASE_URL.'dashboard.php?action=mechanical_list');		
}
else if($operation=='edit_mechanical')
{	
	$mechname = $_POST['mechname'];
	$chmid	=	substr(decode($_POST['chmid']),2);	
	 $update = "update ri_mechanical set mechname='".$mechname."' where id='".$chmid."'";
	mysqli_query($conn, $update);	
	
	header('location:'.BASE_URL.'dashboard.php?action=mechanical_list');	
	//exit;
}
else if($operation=='AddchemicalComp')
{	
	//echo"<pre>";print_r($_POST);//exit;
	
	$chmArray = array();
	
	$product 	= $_POST['product'];
	$material 	= $_POST['material'];
	$grades 	= $_POST['grades'];
	$astm 		= $_POST['astm'];
	$subproduct = $_POST['subproduct'];
	
	if(isset($_POST['chmname']))
	{
		foreach($_POST['chmname'] as $chmname)
		{
			$chmname = str_replace(" ","_",$chmname);
			
			$chmArray[$chmname] = $_POST[$chmname.'name'];
		}
	}
	
	//echo"<pre>chmArray=";print_r($chmArray);
	
	$jsonChemComp = json_encode($chmArray, JSON_UNESCAPED_UNICODE);
	
	 $query ="insert into ri_chemical_comp(product,subproduct,material,grades,astm,comp_text,status,type) values('".$product."','".$subproduct."','".$material."','".$grades."','".$astm."','".$jsonChemComp."','1','chem')";
	
	
		mysqli_query($conn,$query);			 
		$insert_id = mysqli_insert_id();
	
	
	header('location:'.BASE_URL.'dashboard.php?action=chemical_composition');	
	//exit;
}
else if($operation=='EditchemicalComp')
{	
	//echo"<pre>";print_r($_POST);//exit;
	
	$chmArray = array();
	
	$chmid	=	substr(decode($_POST['chmcompid']),2);	
	
	$product 	= $_POST['product'];
	$material 	= $_POST['material'];
	$grades 	= $_POST['grades'];
	$astm 		= $_POST['astm'];
	$subproduct = $_POST['subproduct'];
	
	$fp = (!empty($_POST['appndfp'])?"&fp=".$_POST['appndfp']:"");
	$fm = (!empty($_POST['appndfm'])?"&fm=".$_POST['appndfm']:"");
	$fg = (!empty($_POST['appndfg'])?"&fg=".$_POST['appndfg']:"");
	
	//echo 'location:'.BASE_URL.'dashboard.php?action=chemical_composition'.$fp.$fm.$fg;
	
	if(isset($_POST['chmname']))
	{
		foreach($_POST['chmname'] as $chmname)
		{
			$chmname = str_replace(" ","_",$chmname);
			
			$chmArray[$chmname] = $_POST[$chmname.'name'];
		}
	}
	
	//echo"<pre>chmArray=";print_r($chmArray);
	
	$jsonChemComp = json_encode($chmArray, JSON_UNESCAPED_UNICODE);
	//exit;
	
	 $update = "update ri_chemical_comp set product='".$product."',subproduct='".$subproduct."',material='".$material."',grades='".$grades."',astm='".$astm."',comp_text='".$jsonChemComp."' where id='".$chmid."' and type='chem'";
	
	mysqli_query($conn,$update);			 
		
	
	header('location:'.BASE_URL.'dashboard.php?action=chemical_composition'.$fp.$fm.$fg);	
	//exit;
}
else if($_REQUEST['mode']=='deletechemical_comp')
{	
	
	$ID	=	substr(decode($_REQUEST['d']),2);	
	 $update = "update ri_chemical_comp set status='2' where id='".$ID."'";
	mysqli_query($conn, $update);
	
	header('location:'.BASE_URL.'dashboard.php?action=chemical_composition');		
}

else if($operation=='AddmechanicalComp')
{	
	//echo"<pre>";print_r($_POST);//exit;
	
	$mechvalue = array();
	
	$product = $_POST['product'];
	$material = $_POST['material'];
	$grades = $_POST['grades'];
	$subproduct = $_POST['subproduct'];
	
	$jsonMechComp 	= json_encode($_POST['mechname'], JSON_UNESCAPED_UNICODE);
	$astm 			= (!empty($_POST['astm'])?$_POST['astm']:"");
	$asme 			= (!empty($_POST['asme'])?$_POST['asme']:"");
	
	$keycount 		= count(array_filter($_POST['mechname']));
	
	//echo"<pre>ssss==";print_r($jsonastmArr);
	
	  $query ="insert into ri_mechanical_comp(product,subproduct,material,grades,astm,asme,comp_text,status,keycount) values('".$product."','".$subproduct."','".$material."','".$grades."','".$astm."','".$asme."','".$jsonMechComp."','1','".$keycount."')";

		mysqli_query($conn,$query);			 
		$insert_id = mysqli_insert_id();
	
	
	header('location:'.BASE_URL.'dashboard.php?action=mechanical_composition');	
	//exit;
}
else if($operation=='EditmechanicalComp')
{	
	//echo"<pre>";print_r($_POST);//exit;
	
	$mechvalue = array();
	
	$mechid	=	substr(decode($_POST['mechcompid']),2);	
	
	$subproduct 	= $_POST['subproduct'];
	$grades 		= $_POST['grades'];
	$jsonMechComp 	= json_encode($_POST['mechname'], JSON_UNESCAPED_UNICODE);
	$astm 			= (!empty($_POST['astm'])?$_POST['astm']:"");
	$asme 			= (!empty($_POST['asme'])?$_POST['asme']:"");
	
	$keycount = count(array_filter($_POST['mechname']));
	//echo"<pre>chmArray=";print_r($chmArray);
	
	
	$fp = (!empty($_POST['appndfp'])?"&fp=".$_POST['appndfp']:"");
	$fm = (!empty($_POST['appndfm'])?"&fm=".$_POST['appndfm']:"");
	$fg = (!empty($_POST['appndfg'])?"&fg=".$_POST['appndfg']:"");
	
	
	 $update = "update ri_mechanical_comp set grades='".$grades."',subproduct='".$subproduct."', comp_text='".$jsonMechComp."',astm='".$astm."',asme='".$asme."',keycount='".$keycount."' where id='".$mechid."' ";
	
		mysqli_query($conn,$update);			 
		
	
	header('location:'.BASE_URL.'dashboard.php?action=mechanical_composition'.$fp.$fm.$fg);	
	//exit;
}
else if($_REQUEST['mode']=='deletemechanical_comp')
{	
	
	$ID	=	substr(decode($_REQUEST['d']),2);	
	 $update = "update ri_mechanical_comp set status='2' where id='".$ID."'";
	mysqli_query($conn, $update);
	
	header('location:'.BASE_URL.'dashboard.php?action=mechanical_composition');		
}
else if($operation=='addequivalent')
{	

	$chmname = $_POST['chmname'];


	 $query ="insert into ri_chemical(chmname,status,type)
	values('".$chmname."','1','equiv')";
	
	mysqli_query($conn,$query);			 
	$insert_id = mysqli_insert_id();
	
	header('location:'.BASE_URL.'dashboard.php?action=equivalent');	
	
}
else if($_REQUEST['mode']=='delete_equivalent')
{	
	
	$ID	=	substr(decode($_REQUEST['d']),2);	
	 $update = "update ri_chemical set status='2' where id='".$ID."'";
	mysqli_query($conn, $update);
	
	header('location:'.BASE_URL.'dashboard.php?action=equivalent');		
}
else if($operation=='editequivalent')
{	
	$chmname = $_POST['chmname'];
	$chmid	=	substr(decode($_POST['chmid']),2);	
	 $update = "update ri_chemical set chmname='".$chmname."' where id='".$chmid."' and type='equiv'";
	mysqli_query($conn, $update);	
	
	header('location:'.BASE_URL.'dashboard.php?action=equivalent');	
	//exit;
}
else if($operation=='Addequivalent_grades')
{	
	//echo"<pre>";print_r($_POST); //exit;
	
	
	$chmArray = array();
	
	$product = $_POST['product'];
	$material = $_POST['material'];
	$grades = $_POST['grades'];
	$subproduct = $_POST['subproduct'];
	$astm 			= (!empty($_POST['astm'])?$_POST['astm']:"");
	
	if(isset($_POST['chmname']))
	{
		foreach($_POST['chmname'] as $chmname)
		{
			//echo"<li>name==".$chmname.'=='.$_POST[$chmname.'name'];
			
			$chmArray[$chmname] = $_POST[$chmname.'name'];
		}
	}
	
	
	$jsonChemComp = json_encode($chmArray , JSON_UNESCAPED_UNICODE);
	
	 $query ="insert into ri_chemical_comp(product,subproduct,material,grades,astm,comp_text,status,type) values('".$product."','".$subproduct."','".$material."','".$grades."','".$astm."','".$jsonChemComp."','1','equiv')";

		mysqli_query($conn,$query);			 
		$insert_id = mysqli_insert_id();
	
	
	
	header('location:'.BASE_URL.'dashboard.php?action=equivalent_grades');	
	//exit;
}
else if($operation=='Editequivalent_grades')
{	
	//echo"<pre>";print_r($_POST); //exit;
	
	$chmArray = array();
	
	$product 		= $_POST['product'];
	$material 		= $_POST['material'];
	$grades 		= $_POST['grades'];
	$subproduct 	= $_POST['subproduct'];
	$astm 			= (!empty($_POST['astm'])?$_POST['astm']:"");
	
	$fp = (!empty($_POST['appndfp'])?"&fp=".$_POST['appndfp']:"");
	$fm = (!empty($_POST['appndfm'])?"&fm=".$_POST['appndfm']:"");
	$fg = (!empty($_POST['appndfg'])?"&fg=".$_POST['appndfg']:"");
	
	$chmid	=	substr(decode($_POST['chmcompid']),2);	
	
	
	
	if(isset($_POST['chmname']))
	{
		foreach($_POST['chmname'] as $chmname)
		{
			//echo"<li>name==".$chmname = $_POST[$chmname.'name'];
			
			$chmArray[$chmname] = $_POST[$chmname.'name'];
		}
	}
	
	//echo"<pre>chmArray=";print_r($chmArray);
	
	 $jsonChemComp = json_encode($chmArray , JSON_UNESCAPED_UNICODE);
	
	
	  $update = "update ri_chemical_comp set product='".$product."',subproduct='".$subproduct."',astm='".$astm."',comp_text='".$jsonChemComp."' where id='".$chmid."' and type='equiv'";
	
	mysqli_query($conn,$update);			 
		
	
	header('location:'.BASE_URL.'dashboard.php?action=equivalent_grades'.$fp.$fm.$fg);	
	//exit;
}
else if($_REQUEST['mode']=='deleteequivalent_grades')
{	
	
	$ID	=	substr(decode($_REQUEST['d']),2);	
	 $update = "update ri_chemical_comp set status='2' where id='".$ID."' and type='equiv'";
	mysqli_query($conn, $update);
	
	header('location:'.BASE_URL.'dashboard.php?action=equivalent_grades');		
}


?>
