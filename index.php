<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title>Anagrimes - Recherche avancée dans le Wiktionnaire</title>
	<meta http-equiv="Content-Type" content="text/HTML; charset=utf-8" />
	<link rel="stylesheet" type="text/css" media="screen" href="style/default.css" />
	<link rel="stylesheet" type="text/css" media="print" href="style/print.css" />
	<link rel="shortcut icon" href="favicon.png" />
</head>
<body>
	<div class="page">
		<h1>Anagrimes</h1>
		<?php require ("part_avertissement.php"); ?>
		<ul>
			<li><a href="chercher_anagrammes.php">Recherche d'anagrammes</a></li>
			<!--<li><a href="chercher_rimes.php">Recherche de rimes</a></li>-->
			<li><a href="chercher_prononciation.php">Recherche par prononciation</a></li>
			<li><a href="chercher_graphie.php">Recherche par graphies</a></li>
			<li><a href="quiz_def.php">Quiz des définitions du Wiktionnaire</a></li>
			<!--<li><a href="chercher_transcription.php">Recherche par transcription</a></li>-->
		</ul>
		<h1>Liens</h1>
		<ul>
		<li><a href="//fr.wiktionary.org">Aller sur le Wiktionnaire francophone</a></li>
		<li><a href="//fr.wiktionary.org/wiki/Utilisateur:Darkdadaah">Ma page sur le Wiktionnaire</a></li>
		</ul>
	</div>
	<?
	require ("lib_database.php");
        dbconnect();
        log_action('main_page2', '', '');
        mysql_close();
	require ("part_entete.php");
	require ("part_piedpage.php");
	?>
</body>
</html>
