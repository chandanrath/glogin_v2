<?php

//phpinfo();

define("PROJECT_TITLE", "Google Login" );
define('HTTP_SERVER', 'http://'.$_SERVER['HTTP_HOST'].'/dashboard.php');
date_default_timezone_set('Asia/Calcutta');
/** MySQL hostname */
define("DB_HOST","localhost");	
/** MySQL database username */

define("DB_USER","fkpcmgrymq");	
/** MySQL database password */
define("DB_PASSWORD","SE79j69Pgq");		
/** The name of the database */
define("DB_NAME","fkpcmgrymq");
/** MySQL hostname port, leave it empty, if you don't know it. */
define('DB_HOST_PORT', '21');

define("WRONG_USER_NAME","Invalid Login Credentials.");

define('_PREFIX', 'ri_');

define('ADMIN_PATH', 'http://'.$_SERVER['HTTP_HOST'].'/dashboard.php?action=');

define('IMG_PATH', 'http://'.$_SERVER['HTTP_HOST'].'/');
define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].'/');

define('RECORDS_PER_PAGE', '50');
define('REFERENCE_PERIOD_DAYS', 10);
define('STATUS_DRAFT', 0);
define('STATUS_ACTIVE', 1);
define('STATUS_DEACTIVE', 3);
define('STATUS_EXPIRED', 2);
define('STATUS_PENDING', 7);
define('SOURCE1_DEFAULT', 0);
define('SOURCE1_CONTENT', 1);
define('SOURCE2_IMAGE', 0);
define('SOURCE2_CONTENT', 1);
define('ABSPATH', dirname(dirname(__FILE__)));



?>