<?php
	ini_set('log_errors',1);
	ini_set('error_log','/data/project/anagrimes/php_errors.txt');
/***************
** Database connection
***************/
function dbconnect() {
        // fix redundant error-reporting
//        $errorlevel = ini_set('error_reporting','1');
 	
        // connect
        $mycnf = parse_ini_file("/data/project/anagrimes/replica.my.cnf");
        $username = $mycnf['user'];
        $password = $mycnf['password'];
        unset($mycnf);
	$hostname = 'tools-db';
	$dbname = $username . '__anagrimes2b';
	//echo "CONNECTION...\n";
        $db['connected'] = mysql_connect($hostname, $username, $password) 
                or print '<p class="fail"><strong>Database server login failed.</strong> '
                         . ' This is probably a temporary problem with the server and will be fixed soon. '
                         . ' The server returned: ' . mysql_error() . '</p>';
        unset($password);
	unset($user);
	//echo "CONNECTED?";

        // select database
        if($db['connected']) {
                mysql_select_db($dbname) 
                        or print '<p class="fail"><strong>Database connection failed: ' 
                                 . mysql_error() . '</strong></p>';
        }
        unset($username);

	// Make all queries in UTF-8
	mb_internal_encoding("UTF-8");
	mysql_query("SET NAMES 'utf8'") or die("Query failed");
 
        // restore error-reporting
//        ini_set('error-reporting',$errorlevel);
}

function log_action($action, $message, $requete) {
}
?>
