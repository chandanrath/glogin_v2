<?php

//phpinfo();

define("PROJECT_TITLE", "Rath INfotech" );
define('HTTP_SERVER', 'http://'.$_SERVER['HTTP_HOST'].'/dashboard.php');
date_default_timezone_set('Asia/Calcutta');
/** MySQL hostname */
//define("DB_HOST","localhost");	
/** MySQL database username */

//define("DB_USER","root");	
/** MySQL database password */
//define("DB_PASSWORD","");		
/** The name of the database */
//define("DB_NAME","googlelogin");
/** MySQL hostname port, leave it empty, if you don't know it. */
define('DB_HOST_PORT', '21');

define("WRONG_USER_NAME","Invalid Login Credentials.");

define('_PREFIX', 'ri_');

define('ADMIN_PATH', 'http://'.$_SERVER['HTTP_HOST'].'/glogin_v2/dashboard.php?action=');

define('IMG_PATH', 'http://'.$_SERVER['HTTP_HOST'].'/glogin_v2/');
define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].'/glogin_v2/');

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
define('MOBILE_AUTH', '235062A3lNWJTtD5b8a7030');


/*********cpanel details***************/
//define('CPANEL_TOKEN', 'V95EQFY7YQWV9C6ZKJFQZ8L1KJV6AFNR');
//define('CPANEL_HOST', 'l4.adnshost.com');
//define('CPANEL_USER', 'rathinf1');

define('CPANEL_TOKEN', '5UL0IJR4SDC4D9DVOVCVAP71U9YJ3HXC');
define('CPANEL_HOST', 'rathinfotech.com:2087');
define('CPANEL_USER', 'rathinfo');

//define('MOBILE_NUMBER', '9967355303,9867159161,9999097408');

define('MOBILE_NUMBER', '9967355303,9867159161,9999097408');





?>