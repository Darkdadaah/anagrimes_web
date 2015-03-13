<?php
	require('lib_database.php');
	dbconnect();
	require('lib_random.php');
	require('lib_random_def.php');
	
	# Redirect to a random word url
	$num = 5;
	$defs = get_random_def(isset($_GET['lang']) ? $_GET['lang'] : NULL, $num);
	mysql_close();
	$callback = isset($_GET['callback']) ? $_GET['callback'] : "";
	if ($callback != "") {
		echo $callback . "(" . json_encode($defs) . ")";
	} else {
		echo json_encode($defs);
	}
?>
