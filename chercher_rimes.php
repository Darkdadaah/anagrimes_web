<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

<head>
	<title>Rimes<? if (isset($_GET['mot'])) { echo " de ".addslashes($_GET['mot']) ; } ?></title>
	<meta http-equiv="Content-Type" content="text/HTML; charset=utf-8" />
	<link rel="stylesheet" type="text/css" media="all" href="style/default.css" />
	<link rel="stylesheet" type="text/css" media="print" href="style/print.css" />
	<link rel="shortcut icon" href="favicon.png" />
</head>

<body>
	<div class="page">
	<h1>Recherche de rimes</h1>
	<?php require ("part_avertissement.php"); ?>
	<?php require ("part_retour.php"); ?>
	<?php require ("lib_formulaire.php"); ?>
	
	<form class="formulaire" name="form_rime" action="<?=$_SERVER['SCRIPT_NAME']?>#liste" method="get">
	<fieldset>
		<legend>Options de recherche</legend>
		<fieldset>
				<?php
					echo '<label for="mot">Mot&nbsp;:&nbsp;<input type="text" name="mot" id="mot" value="' ;
					if (isset($_GET['mot'])) { echo $_GET['mot'] ; }
					echo '" />' ;
				?></label><input type="submit" value="Lancer la recherche" /><input type="button" onclick="form.mot.value=''" value="Effacer" />
			</p>
			<? langues(isset($_GET['langue']) ? $_GET['langue'] : ''); ?>
			<? types(isset($_GET['type']) ? $_GET['type'] : ''); ?>
			<ul>
			<li><? flexions(true); ?></li>
			<li><? locutions(true); ?></li>
			<li><? gentiles(true); ?></li>
			</ul>
		</fieldset>
		
		<input type="submit" value="Lancer la recherche" />
	</fieldset>
	</form>
	<script>var focusHere = document.getElementById('mot') ; focusHere = focusHere.focus();</script>
<?php
	require('lib_database.php') ;
	dbconnect() ;
	
	$mot = mysql_real_escape_string(isset($_GET['mot']) ?  : '') ;
	$liste = mysql_real_escape_string(isset($_GET['liste']) ? $_GET['liste'] : '') ;
	$type = mysql_real_escape_string(isset($_GET['type']) ? $_GET['type'] : '') ;
	$langue = mysql_real_escape_string(isset($_GET['langue']) ? $_GET['langue'] : '') ;
	$depuis = mysql_real_escape_string(isset($_GET['depuis']) ? $_GET['depuis'] : '') ;
	$nom_rime = '';
	
	if (!$langue) { $langue = 'fr' ; }
	
	if ($mot) {
		# First get the word if it exists
		$requete_compte = "SELECT count(DISTINCT(rime_riche)) FROM articles LEFT JOIN mots ON articles.titre=mots.titre WHERE articles.titre='$mot' AND langue='$langue' AND rime_pauvre is not null" ;
		echo "<!-- Requète compte : $requete_compte -->\n" ;
		$resultat_compte = mysql_query($requete_compte) or die("Query failed (vérification du mot dans la base)") ;
		$compte = mysql_fetch_array($resultat_compte) ;
		$num = $compte[0] ;
	
		if (!$mot or $num == 0) {
			echo "<p>Il n'y a pas d'article <a href=\"//fr.wiktionary.org/wiki/$mot\">$mot</a> dans le Wiktionnaire (au moment de la dernière mise à jour de ma base). <a href=\"//fr.wiktionary.org/wiki/Wiktionnaire:Proposer_un_mot\">Vous pouvez suggérer ce mot</a> pour enrichir le Wiktionnaire !</p>\n" ;
		} else {
			if ($num > 1) {
				echo"<p>Un article <a href=\"//fr.wiktionary.org/wiki/$mot\">$mot</a> avec plusieurs prononciations existe dans le Wiktionnaire.</p>" ;
			} else if ($num == 1) {
				echo"<p>Un article <a href=\"//fr.wiktionary.org/wiki/$mot\">$mot</a> existe dans le Wiktionnaire.</p>" ;
			}
	
			# Limit?
			$max_by_page = 100 ;
			if (!$depuis or $depuis < 0) {
				$depuis = 0 ;
				$next = $depuis + $max_by_page ;
			}
	
			$cond = '' ;
			
			if ($langue) {
				$cond_langue = "mots.langue='$langue'" ;
			} else {
				$select_langue = ", mots.langue" ;
			}
		
			if ($type) {
				switch($type) {
					case 'nom-tous':
						$cond_type = "(type='nom' OR type='nom-pr' OR type='loc-nom' OR type='loc-nom-pr')" ;
						$select_type = ", mots.type" ;
						break ;
					default:
						$cond_type = "(mots.type='$type' OR mots.type='loc-$type')" ;
						break ;
				}
			} else {
				$select_type = ", mots.type" ;
			}
			if ($cond_langue) { $cond .= " AND $cond_langue" ; }
			if ($cond_type) { $cond .= " AND $cond_type" ; }
			
			if (!$_GET['flex']) {
				$cond .= " AND NOT flex" ;
			}
			if (!$_GET['loc']) {
				$cond .= " AND NOT loc" ;
			}
			if (!$_GET['gent']) {
				$cond .= " AND NOT gent" ;
			}
	
			$limit = "$depuis, $max_by_page" ;
			$order = 'mots.r_pron_simple, mots.titre, mots.type, mots.num' ;
	
			# First retrieve the word data
			$requete_word = "SELECT * FROM articles LEFT JOIN mots ON articles.titre=mots.titre WHERE articles.titre='$mot' AND langue='$langue' AND rime_pauvre is not null" ;
			echo "<!-- Requète : $requete_word -->\n" ;
			$resultat_word = mysql_query($requete_word) or die("Query failed (récupération des données sur le mot)") ;
			$word = mysql_fetch_array($resultat_word) ;
	
			# Then retrieve all the rhymes
			$rimecond = '' ;
			$nom_rime = 'rimes' ;
			if ($word['rime_riche']) {
				$nom_rime = 'rime_riche' ;
			} else if ($word['rime_suffisante']) {
				$nom_rime = 'rime_suffisante' ;
			} else if ($word['rime_pauvre']) {
				$nom_rime = 'rime_pauvre' ;
			}
		}
	}
	
	if ($nom_rime=='') {
		if ($mot and $num>0) {
			echo "<p>Malheureusement il n'a pas de prononciation renseignée dans l'article de ce mot : il est impossible de récupérer les rimes automatiquement.</p>\n" ;
		}
	} else {
		$rime = $word[$nom_rime] ;
		$rimecond = "$nom_rime='$rime'" ;
		echo "<p>Vous pouvez approfondir la recherche des rimes&nbsp;: <a href=\"chercher_prononciation.php?rime=$word[pron_simple]&langue=$langue&place=fin\">basée sur la prononciation complète /$word[pron_simple]/</a> ou sur la <a href=\"chercher_prononciation.php?rime=$rime&langue=$langue&place=fin\">rime affichée /$rime/</a>.</p>\n" ;
	
		# Count
		$requete_rimes_compte = "SELECT count(*) FROM articles LEFT JOIN mots ON articles.titre=mots.titre WHERE $rimecond AND langue='$langue' AND rime_pauvre is not null $cond" ;
		echo "<!-- Requète : $requete_compte -->\n" ;
		$resultat_rimes_compte = mysql_query($requete_rimes_compte) or die("Query failed (décompte des rimes)") ;
		$ligne_compte = mysql_fetch_array($resultat_rimes_compte) ;
		$num = $ligne_compte[0] ;
	
		$nom_langue = $langues[$langue] ;
		echo "<p>$num rimes ($nom_rime en <a href=\"//fr.wiktionary.org/w/index.php?title=Annexe:Rimes_en_".$nom_langue."_en_/$rime/&action=edit\">/$rime/</a>) trouvées pour $mot" ;
		if ($langue != '') { echo " en $langues[$langue]" ; } else { echo " en toutes langues" ; }
		echo "&nbsp;:</p>\n" ;
	
		# Retrieve
		$requete_rimes = "SELECT * FROM articles LEFT JOIN mots ON articles.titre=mots.titre WHERE $rimecond AND langue='$langue' AND rime_pauvre is not null $cond ORDER BY $order LIMIT $limit" ;
		echo "<!-- Requète : $requete_rimes -->\n" ;
		$resultat_rimes = mysql_query($requete_rimes) or die("Query failed (Récupération des rimes)") ;
	
		if ($num >= $max_by_page and $depuis+ $max_by_page < $num) {
			# More pages than thought: navigation through the results
			$target = $_SERVER['SCRIPT_NAME'] ;
		
			$n=0 ;
			$_GET['depuis'] = $depuis + $max_by_page ;
			foreach ($_GET as $key => $value) {
				if ($value) {
					if ($n==0) {
						$target .= '?' . $key . '=' . $value ;
					} else {
						$target .= '&' . $key . '=' . $value ;
					}
				}
				$n++ ;
			}
			$suite = $depuis+1 + $max_by_page ;
			$actuel = "(".($depuis+1)." - $suite) " ;
			$navigation_string = "<p>$actuel <a href=\"$target#liste\">$max_by_page résultats suivants</a></p>" ;
		}
	
		$titre_wiki = '' ;
		$option = array("titre" => $titre_wiki, 'langue' => $langue, 'type' => $type);
	
		echo $navigation_string ;
		affiche_liste($liste, $resultat_rimes, $option) ;
		echo $navigation_string ;
		
		#############
		# LOG
		$message = "terme='$mot'\trime='$rime'\tlangue='$langue'\ttype='$type'\tnum='$num'" ;
		log_action('chercher_rimes', $message, $requete) ;
		#############
	}
	mysql_close();
	
	if ($depuis == 0) { unset($_GET['depuis']) ; }
	if ($langue == '') { unset($_GET['langue']) ; }
	if ($mot == '') { unset($_GET['rime']) ; }
?>
	<?php require ("part_piedpage.php"); ?>
	</div>
	<?php require ("part_entete.php"); ?>
</body>
</html>
