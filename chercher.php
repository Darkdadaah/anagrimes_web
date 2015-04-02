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
	<div class="cell"><label for="string">Recherche&nbsp;:</label></div>
	<div class="cell"><input type="text" name="string" id="string" value="" /></div>
</div>
<div class="row">
	<div class="cell">Méthode&nbsp;:</div>
	<div class="celle"><select name="search_type" id="search_type"></select></div>
</div>
<div class="row">
	<div class="cell">Langue&nbsp;:</div>
	<div class="cell"><select name="lang" id="lang"></select></div>
</div>
<div class="row">
	<div class="cell">Type de mot&nbsp;:</div>
	<div class="cell"><select name="type" id="type"></select></div>
</div>
<div class="row">
	<div class="cell">Genre&nbsp;:</div>
	<div class="cell"><select name="genre" id="genre"></select></div>
</div>
<div class="row">
	<div class="cell"><label for="flex">Inclure flexions&nbsp;:</label></div>
	<div class="cell"><input type="checkbox" name="flex" id="flex" /></div>
</div>
<div class="row">
	<div class="cell"><label for="loc">Inclure locutions&nbsp;:</label></div>
	<div class="cell"><input type="checkbox" name="loc" id="loc" /></div>
</div>
<div class="row">
	<div class="cell"><label for="gent">Inclure gentilés&nbsp;:</label></div>
	<div class="cell"><input type="checkbox" name="gent" id="gent" /></div>
</div>
</div>
<br /><input type="submit" value="Chercher" id="search_button" />
</p>
</form>
<div id="error"></div>
<div id="results_num"></div>
<div id="results"></div>
</body>
</html>

