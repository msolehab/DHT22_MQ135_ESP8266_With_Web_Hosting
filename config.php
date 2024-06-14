<?php
define('DB_HOST', '*****'); //enter host name
define('DB_USERNAME', '*****'); //enter username
define('DB_PASSWORD', '*****'); //enter password
define('DB_NAME', '*****'); //enter name

define('POST_DATA_URL', 'your_web_url');

//PROJECT_API_KEY is the exact duplicate of, PROJECT_API_KEY in NodeMCU sketch file
//Both values must be same
define('PROJECT_API_KEY', 'tempQuality');


//set time zone for your country
date_default_timezone_set("Asia/Kuala_Lumpur");

// Connect with the database 
$db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME); 
 
// Display error if failed to connect 
if ($db->connect_errno) { 
    echo "Connection to database is failed: ".$db->connect_error;
    exit();
}
