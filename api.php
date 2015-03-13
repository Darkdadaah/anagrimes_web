<?php
# This file was shamelessly copied from expose-data/jsonapi.php by Rillke
$tool_user_name = 'anagrimes';
ini_set('log_errors', 1);
ini_set('error_log','/data/project/anagrimes/logs/php.txt');

include_once ( 'shared/common.php' ) ;
#error_reporting( E_ALL & ~E_NOTICE ); # Don't clutter the directory with unhelpful stuff

$prot = getProtocol();
if ( array_key_exists( 'HTTP_ORIGIN', $_SERVER ) ) {
	$origin = $_SERVER['HTTP_ORIGIN'];
}


// Response Headers
header('Content-type: application/json; charset=utf-8');
header('Cache-Control: private, s-maxage=0, max-age=0, must-revalidate');
header('x-content-type-options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-JSONAPI-VERSION: 0.0.0.0');
  
$status = array();
if ( isset( $origin ) ) {
	// Check protocol
	$protOrigin = parse_url( $origin, PHP_URL_SCHEME );
	if ($protOrigin != $prot) {
		header('HTTP/1.0 403 Forbidden');
		if ('https' == $protOrigin) {
			$status["error"] = "Please use this service over https";
		} else {
			$status["error"] = "Please use this service over http";
		}
		echo json_encode($status);
		exit;
	}
	
	// Do we serve content to this origin?
	if ( matchOrigin( $origin ) ) {
		header('Access-Control-Allow-Origin: ' . $origin);
		header('Access-Control-Allow-Methods: GET');
	} else {
		header('HTTP/1.0 403 Forbidden');
		$status["error"] = "Accessing this tool from the origin you are attempting to connect from is not allowed.";
		echo json_encode($status);
		exit;
	}
}

// There are more clever ways to achieve this but for now, it should be sufficient
$action = null;
$res = array();
if ( array_key_exists('action', $_REQUEST) ) {
	$action = $_REQUEST['action'];
}
if (isset($action)) {
	switch ($action) {
		case 'anagram':
			include_once ( 'php/anagrams.php' );
			$res['list'] = get_anagrams( $_REQUEST['word'], $_REQUEST['lang'] );
			break;
		case 'search':
			include_once ( 'php/search.php' );
			$res['list'] = array( $_REQUEST['word'], $_REQUEST['lang'] );
			break;
		case 'random':
			include_once ( 'php/random.php' );
			$res['list'] = get_random( $_REQUEST['lang'], $_REQUEST['num'] );
			break;
		default:
			header('HTTP/1.0 501 Not implemented');
			$res['error'] = 'Unknown action "' . $action . '". Allowed are: random.';
			break;
	}
} else {
	$res['error'] = 'No action provided. Allowed are: random.';
	
}
echo json_encode($res);
?>

