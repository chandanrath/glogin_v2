<?php
	//phpinfo();
session_start();
if (isset($_GET['debug'])) error_reporting(-1);
else error_reporting(0);

include_once('includes/connect.php');
include_once('includes/define.php');
include_once 'google/gpConfig.php';
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Login Page - Rath Infotech </title>
    <link rel="apple-touch-icon" href="<?=BASE_URL?>app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?=BASE_URL?>images/icons/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>app-assets/vendors/css/vendors.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>app-assets/css/plugins/forms/form-validation.css">
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>app-assets/css/pages/page-auth.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>assets/css/style.css">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="auth-wrapper auth-v2">
                    <div class="auth-inner row m-0">
                        <!-- Brand logo--><a class="brand-logo" href="javascript:void(0);">
                            <svg viewBox="0 0 139 95" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="28">
                               <img src="images/logo.png" style="width:80px;">
                               
                            </svg>
                            <h2 class="brand-text text-primary ml-1">Rath Infotech</h2>
                        </a>
                        <!-- /Brand logo-->
                        <!-- Left Text-->
                        <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                            <div class="w-100 d-lg-flex align-items-center justify-content-center px-5"><img class="img-fluid" src="<?=BASE_URL?>app-assets/images/pages/login-v2.svg" alt="Login V2" /></div>
                        </div>
                        <!-- /Left Text-->
                        <!-- Login-->
                        <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                            <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                                <h2 class="card-title font-weight-bold mb-1">Welcome to Rath</h2>
                                <p class="card-text mb-2">Please sign-in to your account</p>
								
							<?php if(!isset($_GET['otp'])) { $display = "none";?>	
								
								<div id="glogin">
									<?php
									if(isset($_GET['errmsg']))
									{
										if($_GET['errmsg']=='yes')
										{ 
									?>
									<div id="msg" style="display:;" class="login100-form-title p-b-13">
									<span style="font-size:12px; color:#b10707; "> <?php echo WRONG_USER_NAME;?></span> </div>
									<?php
										}
									}else{
										echo '<div id="msg" style="display:none;" class="login100-form-title p-b-23"></div>';
									}
									?>
									
									<?php $authUrl = $gClient->createAuthUrl();?>  
									<span class="login100-form-title p-b-13">
									<a href="javascript:poptastic('<?php echo filter_var($authUrl, FILTER_SANITIZE_URL) ?>');" >
									<img src="<?=BASE_URL?>images/glogin.png" width="230px;" height="50px;"></a></span>
								
									<div class="divider my-2">
										<div class="divider-text"><span>&nbsp;&nbsp; Click here for</span><span id="SwapLogin" style="cursor: pointer;font-weight: 600;color: #7367F0;">&nbsp;Manual login</span></div>
									</div>								
									
								
								</div>
							<?php } else { $display = ""; ?>
								
							<div id="manual" style="display:<?=$display?>;">
							<?php if(isset($_GET['otp']) && ($_GET['otp']=="fail")) { ?>
								<div class="error-alert" style="font-size:12px; color:#b10707; "> Invalid OTP - Please try again.!</div>
							<?php } else if(isset($_GET['otp']) && ($_GET['otp']=="sent")) { ?>
								<div class="error-alert" style="font-size:12px; color:#b10707; ">An OTP has been sent to your Administrator </div>
							<?php } else { ?>	
								<span class="error-alert" style="font-size:12px; color:#b10707; " id="otperror"></span>
							<?php } ?>
                                <form class="auth-login-form mt-2" action="scripts/sendotp.php" name="loginFrm" id="loginFrm" method="POST" onsubmit="return SendOTP();">
								<?php if(isset($_GET['email']) && ($_GET['email']!="")){ $emailid = $_GET['email']; } else { $emailid =""; } ?>
                                    <div class="form-group">
                                        <label class="form-label" for="login-email">Registered EmailID</label>
                                        <input class="form-control" id="loginemail" type="text" name="loginemail" placeholder="abcd@example.com" aria-describedby="login-email" autofocus="" tabindex="1" value="<?=$emailid?>" />
										<span id="loginemail-error" class="error"></span>
                                    </div>
									<?php if(isset($_GET['otp']) && ($_GET['otp']=="sent") || ($_GET['otp']=="fail")) { ?>
									
									
									<div class="form-group">
                                        <label class="form-label" for="login-email">Verify OTP</label>
                                        <input class="form-control" type="text" name="verifyotp" id="verifyotp"  value="" placeholder="Enter OTP" aria-describedby="verify-otp" autofocus="">
										<span id="votp-error" class="error"></span>
                                    </div>
									
									<button type="submit" class="btn btn-primary btn-block" name="verifyBtn" id="verifyBtn" onClick="return ValidateOTP();"/>LOGIN</button>
									<input type="hidden" name="hidtype" id="hidtype" value="verifyOTP">
                                   
									<?php } else { ?>
									
									 <input type="hidden" name="hidtype" id="hidtype" value="sendOTP">
									
									<button type="submit" class="btn btn-primary btn-block" name="submit" id="submit" />Send OTP </button>
									
                                    <!--<button class="btn btn-primary btn-block" id="SendOTP" tabindex="4">Send OTP </button>-->
									<?php } ?>
								
								<div class="divider my-2">
                                    <div class="divider-text"><span>&nbsp;&nbsp; Click here to login via </span><span id="SwapGLogin" style="cursor: pointer;font-weight: 600;color: #7367F0;">&nbsp;Google </span></div>
                                </div>
								
								
							</form>	
							</div>
							<?php } ?>
								
                            </div>
                        </div>
                        <!-- /Login-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->
	 <script src="<?=BASE_URL?>assets/js/jquery-3.4.1.js"></script>
	<script>
    function poptastic(url) {
      var newWindow = window.open(url, 'name', 'height=600,width=450');
      if (window.focus) {
        newWindow.focus();
      }
    }
	
	$("#SwapLogin").click(function(){
	  $("#glogin").attr("style", "display:none;");
	  $("#manual").attr("style", "display:;");
	  window.location.href = 'login.php?otp=yes';
	});
	
	$("#SwapGLogin").click(function(){
	  $("#glogin").attr("style", "display:;");
	  $("#manual").attr("style", "display:none;");
	  
	  window.location.href = 'login.php';
	});
	
	function SendOTP()
	{
		var emailid = $("#loginemail").val();		
		
		if(emailid=="")
		{
			
			$("#loginemail").addClass('error');
			document.getElementById('loginemail-error').innerHTML="This field is required.";	
			document.forms["loginFrm"]["loginemail"].focus();
			return false;
		}
		else{
			$("#loginemail").removeClass('error');
			document.getElementById('loginemail-error').innerHTML="";	
		}
		if(emailid!="")
		{
			var regexp = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if (!regexp.test(emailid)) {
				
				//document.getElementById('otperror').innerHTML="Please enter valid emailid";	
				//document.forms["loginFrm"]["loginemail"].focus();
				
				$("#loginemail").addClass('error');
				document.getElementById('loginemail-error').innerHTML="Enter valid emailid.";	
				document.forms["loginFrm"]["loginemail"].focus();
			
				return false;
			}
		}
	}
	
	function ValidateOTP()
	{
		var verifyotp = $("#verifyotp").val();	
		$(".error-alert").attr("style", "display:none;");
		
		if(verifyotp=="")
		{
			$("#verifyotp").addClass('error');			
			document.getElementById('votp-error').innerHTML="Please Enter Valid OTP"	;
			document.getElementById('verifyotp').focus();
			return false;
		}
		else{
			$("#verifyotp").removeClass('error');
		}
		if(verifyotp.length < 4)
		{
			$("#verifyotp").addClass('error');
			document.getElementById('votp-error').innerHTML="Please Enter Valid OTP"	;
			document.getElementById('verifyotp').focus();
			return false;
		}

	}
	
</script>
    <!-- BEGIN: Vendor JS-->
    <script src="<?=BASE_URL?>app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="<?=BASE_URL?>app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="<?=BASE_URL?>app-assets/js/core/app-menu.js"></script>
    <script src="<?=BASE_URL?>app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <!--<script src="<?=BASE_URL?>app-assets/js/scripts/pages/page-auth-login.js"></script>-->
    <!-- END: Page JS-->

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>
</body>
<!-- END: Body-->

</html>