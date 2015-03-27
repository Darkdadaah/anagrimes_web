<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title>Search Anagrimes</title>
	<meta http-equiv="Content-Type" content="text/HTML; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="shortcut icon" href="favicon.png" />
	<link rel="stylesheet" type="text/css" media="screen" href="style/recherche.css" />
	<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="js/search.js"></script>
</head>
<body>
<form id="search" action="">
<p>
<div class="table">
<div class="row">
	<div class="cell">Recherche&nbsp;:&nbsp;</div>
	<div class="cell"><input type="text" name="string" id="string" value="" /></div>
</div>
<div class="row">
	<div class="cell">Méthode&nbsp;:&nbsp;</div>
	<div class="celle"><select name="search_type" id="search_type"></select></div>
</div>
<div class="row">
	<div class="cell">Langue&nbsp;:&nbsp;</div>
	<div class="cell"><select name="lang" id="lang"></select></div>
</div>
<div class="row">
	<div class="cell">Type de mot&nbsp;:&nbsp;</div>
	<div class="cell"><select name="type" id="type"></select></div>
</div>
<div class="row">
	<div class="cell">Genre&nbsp;:&nbsp;</div>
	<div class="cell"><select name="genre" id="genre"></select></div>
</div>
</div>
<br /><input type="submit" value="Chercher" />
</p>
</form>
<div id="error"></div>
<div id="results"></div>
</body>
</html>

