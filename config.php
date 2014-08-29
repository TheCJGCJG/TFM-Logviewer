<?php
define("TFINFO_JSON", 'http://totalfreedom.me/tfinfo/');
define("USERS_JSON", 'http://totalfreedom.me/logviewer/api.php?password=securepassword'); #Use 'users.json' if you want to use a local file and the local update.php
define("HTTPD_URL", 'http://127.0.0.1:28966/logs/'); # To the logs dir (TFM Config for port no)
define("HTTPD_PASSWORD", 'TotallySecurePassword');
define("UPDATE_PASSWORD", "UhHuh"); # For the server to update/delete IP's
define('INDEX_FILENAME', 'index.php'); # The name of index.php
define('getLogs_FILENAME', 'get_log'); # Name of get_log.php
define('MC_AUTH_SCRIPT_LOCATION', 'mcauth/authenticate'); # Location of mcauth/authenticate.php
?>