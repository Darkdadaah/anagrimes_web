<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

<head>
	<title>Aide</title>
	<meta http-equiv="Content-Type" content="text/HTML; charset=utf-8" />
	<link rel="stylesheet" type="text/css" media="screen" href="style/default.css" />
	<link rel="stylesheet" type="text/css" media="print" href="style/print.css" />
</head>

<body>
	<div class="page">
		<h1>Aide</h1>
		<?php require ("part_retour.php"); ?>
		<h2>Joker</h2>
		<p>La recherche des graphies peut être effectuée en utilisant deux sortes de jokers : * (astérisque) et ? (point d'interrogation).</p>
		
		<h3>Astérisque</h3>
		<p>L'astérisque représente un ensemble de lettres indéfinies. Quelques exemples d'utilisation&nbsp;:</p>
		<ul>
			<li><a href="chercher_graphie.php?graphie=clair*&langue=fr">clair*</a> <small>(tous les mots commençant par « clair »)</small></li>
			<li><a href="chercher_graphie.php?graphie=*clair&langue=fr">*clair</a> <small>(tous les mots finissant par « clair »)</small></li>
			<li><a href="chercher_graphie.php?graphie=clair*ance&langue=fr">clair*ance</a> <small>(tous les mots commençant par « clair » et finissant par « ance »)</small></li>
			<li><a href="chercher_graphie.php?graphie=*clair*&langue=fr">*clair*</a> <small>(tous les mots contenant « clair ») ATTENTION : cette recherche est plus longue que les autres (pas d'optimisation pour les recherches en plein mot).</small></li>
		</ul>
		<p>Il s'utilise ainsi&nbsp;:</p>
		<ul>
			<li>Elle représente de 0 à plusieurs lettres&nbsp;;</li>
		</ul>
		
		<h3>Point d'interrogation</h3>
		<p>Le point d'interrogation représente une lettre indéfinie. Quelques exemples d'utilisation&nbsp;:</p>
		<ul>
			<li><a href="chercher_graphie.php?graphie=clar??&langue=fr">clar??</a> <small>(tous les mots de 5 lettres commençant par « clar »)</small></li>
			<li><a href="chercher_graphie.php?graphie=?clair&langue=fr">?clair</a> <small>(tous les mots de 5 lettres finissant par « clair »)</small></li>
			<li><a href="chercher_graphie.php?graphie=clair???ance&langue=fr">clair???ance</a> <small>(tous les mots de 12 lettres commençant par « clair » et finissant par « ance »)</small></li>
		</ul>
		<p>Il s'utilise ainsi&nbsp;:</p>
		<ul>
			<li>Chaque point représente une lettre seule&nbsp;;</li>
			<li>Plusieurs points peuvent être utilisés à condition qu'ils se jouxtent&nbsp;;</li>
			<li>Il ne peut s'utiliser en combinaison avec d'autres jokers.</li>
		</ul>
		
		<?php require ("part_retour.php"); ?>
	</div>
	<?php require ("part_entete.php"); ?>
</body>

</html>
