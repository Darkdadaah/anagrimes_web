<?php
	require('lib_database.php') ;
	dbconnect() ;
	require('lib_random.php') ;
	
	# Redirect to a random word url
	$m = get_random_word(isset($_GET['langue']) ? $_GET['langue'] : NULL) ;
	mysql_close() ;
	
	header('Location: //fr.wiktionary.org/wiki/'.$m['raw'].$m['ancre']);
?>
