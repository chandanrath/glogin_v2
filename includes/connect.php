<?php

 //localhost
/* 
$servername = "localhost";
$username = "fkpcmgrymq";
$password = "SE79j69Pgq";
$db = "fkpcmgrymq";
 */

$servername = "localhost";
$username = "root";
$password = "";
$db = "glogin";


$conn = mysqli_connect($servername, $username, $password, $db);


/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}else{

  //echo"DB conncet";
}


?>