<?php
	require('lib_database.php');
	dbconnect();
	require('lib_random.php');
	require('lib_random_def.php');
	
	# Redirect to a random word url
	$num = 5;
	$defs = get_random_def(isset($_GET['langue']) ? $_GET['langue'] : NULL, $num);
	mysql_close();
	echo json_encode($defs);
?>
