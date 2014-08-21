<?php
	require('lib_database.php');
	dbconnect();
	require('lib_random.php');
	require('lib_random_def.php');
	
	# Redirect to a random word url
	$def = get_random_def(isset($_GET['langue']) ? $_GET['langue'] : NULL);
	mysql_close();
	echo json_encode($def);
?>
