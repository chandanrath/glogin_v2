
<?php
include_once '../includes/connect.php';
include_once '../includes/define.php';

//echo"<pre>sess==";print_r($_SESSION);

 $update = "update ri_users_log set logout_date='".date('Y-m-d H:i:s')."' where userid='".$_SESSION['user_id']."'";
	 mysqli_query($conn, $update);

	unset($_SESSION['user_id']);	
	unset($_SESSION['given_name']);
	unset($_SESSION['email']);	
	unset($_SESSION['session_id']);	
	unset($_SESSION['token']);
	
	session_unset();
	
	session_unset($_SESSION['user_id']);
	session_unset($_SESSION['given_name']);
	session_unset($_SESSION['email']);
	session_unset($_SESSION['session_id']);

	
	session_destroy();
	
	
	 
	 
	header("Location:index.php");
?>

<script> 
		
	window.location.href ='<?=BASE_URL?>index.php';
	close();		
</script>