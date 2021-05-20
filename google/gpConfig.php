																																																																							<?php
session_start();

//Include Google client library 
include_once 'src/Google_Client.php';
include_once 'src/contrib/Google_Oauth2Service.php';

/*
 * Configuration and setup Google API
 */
$clientId = '938589445894-7itivrntdg5gqa3ir660b39kb9kum2r3.apps.googleusercontent.com'; //Google client ID
$clientSecret = 'KWE9NugF9I3jQCBpErhqG3-w'; //Google client secret

//$redirectURL = 'http://phpstack-58047-488126.cloudwaysapps.com/demo/index.php'; //Live Callback URL  

 $redirectURL = 'http://localhost/glogin_v2/scripts/login_script.php';  // localhost 
 
 //$redirectURL = 'https://www.rathinfotech.com/portfolio.html'; //Callback URL
 
 //$redirectURL = 'http://'.$_SERVER['HTTP_HOST'].'/scripts/login_script.php';  // live 

//Call Google API
$gClient = new Google_Client();
$gClient->setApplicationName('Login With Google');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectURL);

$google_oauthV2 = new Google_Oauth2Service($gClient);



?>