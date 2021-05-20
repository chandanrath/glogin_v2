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

if($operation=='Addgrades')
{	

	//echo"<pre>";print_r($_POST);//exit;

	$gradescode = strtolower(str_replace(" ","-",$_POST['grades']));


	 $query ="insert into ri_grades(product_id,material_id,gradesname,gradescode,status,active)
	values('".$_POST['product']."','".$_POST['material']."','".$_POST['grades']."','".$gradescode."','1',1)";
	
	mysqli_query($conn,$query);			 
	$insert_id = mysqli_insert_id();
	
	header('location:'.BASE_URL.'dashboard.php?action=grades');	
	
}

else if($operation=='Editgrades')
{	
	//echo"<pre>";print_r($_POST); exit;

	$gradescode = strtolower(str_replace(" ","-",$_POST['grades']));
	
	$fp = (!empty($_POST['appndfp'])?"&fp=".$_POST['appndfp']:"");
	$fm = (!empty($_POST['appndfm'])?"&fm=".$_POST['appndfm']:"");
	$fg = (!empty($_POST['appndfg'])?"&fg=".$_POST['appndfg']:"");
	$pg = (!empty($_POST['appndpage'])?"&page=".$_POST['appndpage']:"");
	
	$gradesid	=	substr(decode($_POST['gradesid']),2);	
	
	 $update = "update ri_grades set gradesname='".$_POST['grades']."',gradescode='".$gradescode."' where id='".$gradesid."'";
	mysqli_query($conn, $update);	
	
	header('location:'.BASE_URL.'dashboard.php?action=grades'.$fp.$fm.$fg.$pg);	
	//exit;
}

else if($_REQUEST['mode']=='deletegrades')
{	
	
	$ID	=	substr(decode($_REQUEST['d']),2);	
	$update = "update ri_grades set status='2' where id='".$ID."'";
	mysqli_query($conn, $update);
	
	header('location:'.BASE_URL.'dashboard.php?action=grades');		
}
if($operation=='addproduct')
{	

	//echo"<pre>";print_r($_POST);exit;	
	
	$prodcode = makeurl($_POST['product']);
	
	 $sqlProd="select id from ri_product where status=1 and prodcode='".$prodcode."'";
	$result= mysqli_query($conn,$sqlProd);
	$count = mysqli_num_rows($result);
	
	if($count <=0)
	{
		 $query ="insert into ri_product(prodname,prodcode,othername,prefix,suffix,status)
		values('".$_POST['product']."','".$prodcode."','".$_POST['otname']."','".$_POST['prefix']."','".$_POST['suffix']."','1')";

		mysqli_query($conn,$query);			 
		$insert_id = mysqli_insert_id();
	}
	
	header('location:'.BASE_URL.'dashboard.php?action=product');	
	
}
else if($operation=='editproduct')
{	
	//echo"<pre>";print_r($_POST); //exit;

	$prodcode = makeurl($_POST['product']);
	
	$prodid	=	substr(decode($_POST['prodid']),2);	
	
	$update = "update ri_product set prodname='".$_POST['product']."',prodcode='".$prodcode."',othername='".$_POST['otname']."',prefix='".$_POST['prefix']."',suffix='".$_POST['suffix']."' where id='".$prodid."'";
	mysqli_query($conn, $update);	
	
	header('location:'.BASE_URL.'dashboard.php?action=product');
	//exit;
}
else if($_REQUEST['mode']=='deleteproduct')
{	
	
	$ID	=	substr(decode($_REQUEST['d']),2);	
	$update = "update ri_product set status='2' where id='".$ID."'";
	mysqli_query($conn, $update);
	
	header('location:'.BASE_URL.'dashboard.php?action=product');		
}
if($operation=='addmaterial')
{	

	//echo"<pre>";print_r($_POST);//exit;
	
	

	$materialcode = makeurl($_POST['material']);
	
	 $sqlProd="select id from ri_material where status=1 and materialcode='".$materialcode."'";
	$result= mysqli_query($conn,$sqlProd);
	$count = mysqli_num_rows($result);
	
	if($count <=0)
	{
		 $query ="insert into ri_material(materialname,materialcode,othername,status)
		values('".$_POST['material']."','".$materialcode."','".$_POST['otname']."','1')";

		mysqli_query($conn,$query);			 
		$insert_id = mysqli_insert_id();
	}
	
	header('location:'.BASE_URL.'dashboard.php?action=materials');	
	
}
else if($operation=='editmaterial')
{	
	//echo"<pre>";print_r($_POST); exit;

	$materialcode = makeurl($_POST['material']);
	
	$materialid	=	substr(decode($_POST['materialid']),2);	
	
	 $update = "update ri_material set materialname='".$_POST['material']."',materialcode='".$materialcode."',othername='".$_POST['otname']."' where id='".$materialid."'";
	mysqli_query($conn, $update);	
	
	header('location:'.BASE_URL.'dashboard.php?action=materials');	
	//exit;
}
else if($_REQUEST['mode']=='deletematerial')
{	
	
	$ID	=	substr(decode($_REQUEST['d']),2);	
	$update = "update ri_material set status='2' where id='".$ID."'";
	mysqli_query($conn, $update);
	
	header('location:'.BASE_URL.'dashboard.php?action=materials');		
}
if($operation=='addsubproduct')
{	

	//echo"<pre>";print_r($_POST);//exit;
	$opcode = makeurl($_POST['subproduct']);
	
	 $sqlProd="select id from ri_subproduct where status=1 and opcode='".$opcode."' and product_id='".$_POST['product']."'";
	$result= mysqli_query($conn,$sqlProd);
	$count = mysqli_num_rows($result);
	
	if($count <=0)
	{
		echo $query ="insert into ri_subproduct(product_id,opname,opcode,status)
		values('".$_POST['product']."','".$_POST['subproduct']."','".$opcode."','1')";

		mysqli_query($conn,$query);			 
		$insert_id = mysqli_insert_id();
	}
	
	header('location:'.BASE_URL.'dashboard.php?action=sub_product');	
	
}
else if($operation=='editsubproduct')
{	
	//echo"<pre>";print_r($_POST); exit;

	$opcode = makeurl($_POST['subproduct']);
	
	$subid	=	substr(decode($_POST['subid']),2);	
	
	 $update = "update ri_subproduct set product_id='".$_POST['product']."',opname='".$_POST['subproduct']."',opcode='".$opcode."' where id='".$subid."'";
	mysqli_query($conn, $update);	
	
	header('location:'.BASE_URL.'dashboard.php?action=sub_product');	
	//exit;
}
else if($_REQUEST['mode']=='deletesubproduct')
{	
	
	$ID	=	substr(decode($_REQUEST['d']),2);	
	$update = "update ri_subproduct set status='2' where id='".$ID."'";
	mysqli_query($conn, $update);
	
	header('location:'.BASE_URL.'dashboard.php?action=sub_product');		
}
if($operation=='addsubproducttype')
{	

	//echo"<pre>";print_r($_POST);exit;
	
	//$product_type = makeurl($_POST['product_type']);
	
	if($_POST['Success']=="Submit")
	{
		
		$sqlProd="select id from ri_subproduct_type where status=1 and prodtype='".$_POST['subproduct_type']."' and pid='".$_POST['product']."' and subid='".$_POST['subproduct']."'";
		$result= mysqli_query($conn,$sqlProd);
		$count = mysqli_num_rows($result);

		if($count <=0)
		{
			$typecode = makeurl($_POST['subproduct_type']);
			
		$query ="insert into ri_subproduct_type(pid,subid,prodtype,typecode,status)
		values('".$_POST['product']."','".$_POST['subproduct']."','".$_POST['subproduct_type']."','".$typecode."','1')";

		mysqli_query($conn,$query);			 
		$insert_id = mysqli_insert_id();
		}

		header('location:'.BASE_URL.'dashboard.php?action=subproduct_type');	
	}
	
}
else if($operation=='editsubproducttype')
{	
	//echo"<pre>";print_r($_POST); //exit;
	if($_POST['Success']=="Submit")
	{
		$typecode = makeurl($_POST['subproduct_type']);
		
		$subid	=	substr(decode($_POST['subid']),2);	//exit;
		
		 $update = "update ri_subproduct_type set pid='".$_POST['product']."',subid='".$_POST['subproduct']."',prodtype='".$_POST['subproduct_type']."',typecode='".$typecode."' where id='".$subid."'";
		mysqli_query($conn, $update);	
		
		header('location:'.BASE_URL.'dashboard.php?action=subproduct_type');	
	}
	//exit;
}
else if($_REQUEST['mode']=='deletesubproduct_type')
{	
	
	$ID	=	substr(decode($_REQUEST['d']),2);	
	$update = "update ri_subproduct_type set status='2' where id='".$ID."'";
	mysqli_query($conn, $update);
	
	header('location:'.BASE_URL.'dashboard.php?action=subproduct_type');		
}

?>
