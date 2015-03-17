<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title>Anagrammes - Anagrimes</title>
	<meta http-equiv="Content-Type" content="text/HTML; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="shortcut icon" href="favicon.png" />
	<link rel="stylesheet" type="text/css" media="screen" href="style/recherche.css" />
	<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="js/anagrammes.js"></script>
</head>
<body>
<form id="search" action="">
<p>
Mot à chercher&nbsp;:&nbsp;<input type="text" name="string" id="string" value="" />
<br />Langue&nbsp;:&nbsp;<select name="lang" id="lang"></select>
<br />Type de mot&nbsp;:&nbsp;<select name="type" id="type"></select>
<br /><input type="submit" value="Chercher des anagrammes" />
</p>
</form>
<div id="results">
</div>
</body>
</html>

