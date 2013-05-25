<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

<head>
	<title>Anagrammes</title>
	<meta http-equiv="Content-Type" content="text/HTML; charset=utf-8" />
	<link rel="stylesheet" type="text/css" media="all" href="style/default.css" />
	<link rel="stylesheet" type="text/css" media="print" href="style/print.css" />
	<link rel="shortcut icon" href="favicon.png" />
</head>

<body>
	<div class="page">
	<h1>Recherche d'anagrammes</h1>
	<?php require ("part_avertissement.php"); ?>
	<?php require ("part_retour.php"); ?>
	<?php require ("lib_formulaire.php"); ?>
	
	<form class="formulaire" id="formulaire" action="<?=$_SERVER['SCRIPT_NAME']?>#liste" method="get">
	<fieldset>
		<legend>Options de recherche</legend>
		<fieldset>
			<p>Note&nbsp;: évitez les caractères trop exotiques...</p>
			<p>
				<label for="mot">Mot à chercher&nbsp;:&nbsp;<input type="text" name="mot" id="mot"  value="<?
				if (isset($_GET['mot'])) { echo $_GET['mot']; } ?>" /></label><input type="submit" value="Lancer la recherche" /><input type="button" onclick="form.mot.value=''" value="Effacer" />
			</p>
			<? langues(isset($_GET['langue']) ? $_GET['langue'] : ''); ?>
			<? types(isset($_GET['type']) ? $_GET['type'] : ''); ?>
			<ul>
			<li><? flexions(true); ?></li>
			<li><? locutions(false); ?></li>
                        <li><? gentiles(true); ?></li>
                        <li><? nom_propre(true); ?></li>
			</ul>
		</fieldset>
		<? listes(isset($_GET['liste']) ? $_GET['liste'] : ''); ?>
		<input type="submit" value="lancer la recherche" />
	</fieldset>
	</form>
	<script>var focusHere = document.getElementById('mot') ; focusHere = focusHere.focus();</script>
<?php
	require('lib_database.php');

	dbconnect() ;
	
	$mot = mysql_real_escape_string(isset($_GET['mot']) ? $_GET['mot'] : '') ;
	$liste = mysql_real_escape_string(isset($_GET['liste']) ? $_GET['liste'] : '') ;
	$langue = mysql_real_escape_string(isset($_GET['langue']) ? $_GET['langue'] : '') ;
	$type = mysql_real_escape_string(isset($_GET['type']) ? $_GET['type'] : '') ;
	
	$limit = 200 ;
	
	if ($mot) {
		if (strlen(utf8_decode($mot)) < 3) {
			echo "<p>Merci de taper plus de caractères pour cette recherche (il faut au moins 3 lettres pour faire un anagramme).</p>\n" ;
		} else {
	
			echo "\t\t<h2 id=\"liste\"> Résultats</h2>\n" ;
	
			$anag = non_diacritique($mot) ;
			
			$lettres = preg_split('//', $anag, -1, PREG_SPLIT_NO_EMPTY);
			sort($lettres) ;
			$anag = join(' ', $lettres) ;
			$anag = ereg_replace(' ', '', $anag) ;
			
			$cond = "articles.anagramme_id='$anag'" ;
			$cond_langue = '';
			$cond_type = '';
			$select_langue = '';
			$select_type = '';
			
			if ($langue) {
				$cond_langue = "mots.langue='$langue'" ;
			} else {
				$select_langue = ", mots.langue" ;
			}
			
			if ($type) {
				switch($type) {
					case 'nom-tous':
						$cond_type = "(mots.type='nom' OR mots.type='nom-pr')" ;
						$select_type = ", mots.type" ;
						break ;
					default:
						$cond_type = "mots.type='$type'" ;
						break ;
				}
			} else {
				$select_type = ", mots.type" ;
			}
			
			if ($cond_langue) { $cond .= " AND $cond_langue" ; }
			if ($cond_type) { $cond .= " AND $cond_type" ; }

                        if (!$_GET['flex'] or !($_GET['flex']=='oui')) {
                                $cond .= " AND NOT flex" ;
                        }
                        if (!$_GET['loc'] or !($_GET['loc']=='oui')) {
                                $cond .= " AND NOT loc" ;
                        }
                        if (!$_GET['gent'] or !($_GET['gent']=='oui')) {
                                $cond .= " AND NOT gent" ;
                        }
                        if (!$_GET['nom_propre'] or !($_GET['nom_propre']=='oui')) {
                                $cond .= " AND NOT mots.type='nom-pr'" ;
			}
			
			$cond .= " AND NOT articles.titre='$mot'" ;
		
			if ($cond) {
				# Compte
				$requete_compte = "SELECT count(*) FROM articles LEFT JOIN mots ON articles.titre=mots.titre WHERE $cond" ;
				echo "<!-- Requète : $requete_compte -->\n" ;
				$resultat_compte = mysql_query($requete_compte) or die("Query failed");
				$compte = mysql_fetch_array($resultat_compte) ;
				$num = $compte[0] ;
				$requete = '';
				
				if ($num==0 or ($num == 1 and $datatemp->titre==$mot) ) {
						echo "<p>Pas d'anagrammes trouvées pour <a href=\"//fr.wiktionary.org/wiki/$mot\">$mot</a> (autre que lui même).</p>" ;
				} else {
					# Requète
					$requete = "SELECT articles.titre, mots.pron, mots.flex, mots.loc, mots.num $select_type $select_langue FROM articles LEFT JOIN mots ON articles.titre=mots.titre WHERE $cond ORDER BY articles.titre LIMIT $limit" ;
					echo "<!-- Requète : $requete -->\n" ;
				
					$resultat = mysql_query($requete) or die("Query failed");
				
					$en_langue = '' ;
					if ($langue) {
						$en_langue = " en $langues[$langue]" ;
					} else {
						$en_langue = " en toutes langues" ;
					}
					
					if ($num > $limit) {
						echo "<p>Plus de $limit résultats : seuls les $limit premiers résultats sont affichés</p>" ;
					}
					if ($num==1) {
						echo "<p>1 anagramme trouvée pour <a href=\"//fr.wiktionary.org/wiki/$mot\">$mot</a>$en_langue&nbsp;:</p>\n" ;
					} else {
						echo "<p>$num anagrammes trouvées pour <a href=\"//fr.wiktionary.org/wiki/$mot\">$mot</a>$en_langue&nbsp;:</p>\n" ;
					}
					$titre_wiki = '{{-anagr-}}' ;
					$option = array("titre" => $titre_wiki, 'langue' => $langue, 'type' => $type);
				
					echo $navigation_string ;
					affiche_liste($liste, $resultat, $option) ;
					echo $navigation_string ;
					
					echo "<p id='alphagramme'>Alphagramme&nbsp;: $anag</p>" ;
				}
			
				#############
				# LOG
				$message = "'$mot'\tlangue='$langue'\tnum='$num'" ;
				log_action('anagrammes', $message, $requete) ;
				#############
				mysql_close() ;
			}
		}
	}
?>
	<?php require ("part_piedpage.php"); ?>
	</div>
	<?php require ("part_entete.php"); ?>
</body>

</html>

