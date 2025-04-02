<?php
// Set some parameters
$config = parse_ini_file(__DIR__ . "/../config.ini", true)["database"];
$db_conn = NULL;	// login credentials are used in connectToDB()

// The following 3 lines allow PHP errors to be displayed along with the page
// content. Delete or comment out this block when it's no longer needed.
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$success = true;	// keep track of errors so page redirects only if there are no errors

$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())
?>