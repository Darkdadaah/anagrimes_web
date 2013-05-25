<?php
	//ini_set('log_errors',1);
	//ini_set('error_log','/home/darkdadaah/logs/php.txt');
/***************
** Database connection
***************/
function dbconnect() {
        // fix redundant error-reporting
//        $errorlevel = ini_set('error_reporting','1');
 	
        // connect
        $mycnf = parse_ini_file("/data/project/anagrimes/.my.cnf");
        $username = $mycnf['user'];
        $password = $mycnf['password'];
        unset($mycnf);
	$hostname = 'tools-db';
	$dbname = 'localanagrimes';
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
 
        // restore error-reporting
//        ini_set('error-reporting',$errorlevel);
}

function log_action($action, $message, $requete) {
}
?>
