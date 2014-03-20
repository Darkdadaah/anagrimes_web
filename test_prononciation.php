<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

<head>
	<title>Rimes</title>
	<meta http-equiv="Content-Type" content="text/HTML; charset=utf-8" />
	<link rel="stylesheet" type="text/css" media="all" href="style/default.css" />
	<link rel="stylesheet" type="text/css" media="print" href="style/print.css" />
	<link rel="shortcut icon" href="favicon.png" />
</head>

<body>
	<div class="page">
	<h1>Recherche de prononciation</h1>
	<?php require ("avertissement.php"); ?>
	<?php require ("fonctions_formulaire.php"); ?>
	<?php require ("retour.php"); ?>
	
	<form class="formulaire" name="form_rime" action="<?=$_SERVER['SCRIPT_NAME']?>#liste" method="get">
	<fieldset>
		<legend>Options de recherche</legend>
		<fieldset>
				<?php
					echo '<label for="mot">Mot&nbsp;:&nbsp;<input type="text" name="mot" id="mot" value="' ;
					if ($_GET['mot']) { echo $_GET['mot'] ; }
					echo '" />' ;
				?></label><input type="submit" value="Lancer la recherche" /><input type="button" onclick="form.mot.value=''" value="Effacer" />
			</p>
			<? langues($_GET['langue']); ?>
			<? types($_GET['type']); ?>
			<li><input type="checkbox" value="OK" name="no_diac" id="diacbox" <?php if ($_GET['no_diac']) { echo ' checked="checked"' ; } ?> /><label for="diacbox">&nbsp;Ne pas prendre en compte les diacritiques (accents) et les majuscules&nbsp;?</label></li>
		</fieldset>
		
		<input type="submit" value="Lancer la recherche" />
	</fieldset>
	</form>
	<script>var focusHere = document.getElementById('mot') ; focusHere = focusHere.focus();</script>
<?php
	require('database.php') ;
	dbconnect() ;
	
	$mot = mysql_real_escape_string($_GET['mot']) ;
	$liste = mysql_real_escape_string($_GET['liste']) ;
	$no_diac = mysql_real_escape_string($_GET['no_diac']) ;
	$type = mysql_real_escape_string($_GET['type']) ;
	$langue = mysql_real_escape_string($_GET['langue']) ;
	$depuis = mysql_real_escape_string($_GET['depuis']) ;
	
	# Limit?
	$max_by_page = 100 ;
	if (!$depuis or $depuis < 0) {
		$depuis = 0 ;
		$next = $depuis + $max_by_page ;
	}
	
	$limit = "$depuis, $max_by_page" ;
	
	if ($mot) {
		$mot0 = $mot ;
		echo "\t\t<h2 id=\"liste\"> Résultats</h2>\n" ;
		
		$cond = " a_title IS NOT NULL";
		$order = 'a_title_flat, a_title' ;
		if ($no_diac) {
			$mot_nu = non_diacritique($mot) ;
			$cond_mot = "a_title_flat = '$mot_nu'" ;
		} else {
			$cond_mot = "a_title = '$mot'" ;
		}
		$order = 'a_title_flat, a_title, l_lang, l_type, l_num, p_num' ;
		
		if ($langue) {
			$cond_langue = "l_lang='$langue'" ;
		} else {
			$select_langue = ", l_lang" ;
		}
		
		if ($type) {
			switch($type) {
				case 'nom-tous':
					$cond_type = "(l_type='nom' OR l_type='nom-pr' OR l_type='loc-nom' OR l_type='loc-nom-pr')" ;
					$select_type = ", l_type" ;
					break ;
				default:
					$cond_type = "(l_type='$type' OR l_type='loc-$type')" ;
					break ;
			}
		} else {
			$select_type = ", l_type" ;
		}
		if ($cond_langue) { $cond .= " AND $cond_langue" ; }
		if ($cond_type) { $cond .= " AND $cond_type" ; }
		
		if ($cond and $cond_mot) {
			# Compte
			$requete_compte = "SELECT count(*) FROM entries WHERE $cond AND $cond_mot" ;
			echo "<!-- Requète compte : $requete_compte -->\n" ;
			$resultat_compte = mysql_query($requete_compte) or die("Query failed");
			$compte = mysql_fetch_array($resultat_compte) ;
			$num = $compte[0] ;
			
			if ($num == 0) {
				$terme = 'terme trouvés' ;
				echo "<p>Pas de $terme pour « $mot »" ;
				if ($type != '') { echo " de type $type" ; }
				if ($langue != '') { echo " en $langues[$langue]" ; } else { echo " en toutes langues" ; }
				if ($no_diac) { echo " (diacritiques ignorés)" ; }
				echo ".</p>\n" ;
				
				# Suggérer d'enlever les diacritiques
				if (!$no_diac) {
					$re = "prononciation_de.php?mot=$mot" ;
					if ($type) { $re .= "&type=$type" ; }
					if ($langue) { $re .= "&langue=$langue" ; }
					$re .= "&no_diac=oui" ;
					echo "<p><a href=\"$re\">Rechercher à nouveau en ignorant les diacritiques (accents) et majuscules&nbsp;?</a></p>" ;
				} else {
					echo "<p>Ce mot ne semble pas exister dans le Wiktionnaire. <a href=\"//fr.wiktionary.org/w/index.php?title=$mot\">Peut-être voudrez-vous l'ajouter</a>&nbsp;?</p>" ;
				}
				
			} else {
				# Requète
				$requete = "SELECT a_title, p_pron, l_is_flexion, l_is_locution, l_num, p_num $select_type $select_langue FROM entries WHERE $cond AND $cond_mot ORDER BY $order LIMIT $limit" ;
				echo "<!-- Requète : $requete -->\n" ;
				
				$resultat = mysql_query($requete) or die("Query failed");
				
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
				
				if ($num == 1) {
					echo "<p>$num prononciation trouvée pour <a href=\"//fr.wiktionary.org/w/index.php?title=$mot#$langue&action=edit\">$mot</a>" ;
				} else {
					echo "<p>$num prononciation trouvées pour <a href=\"//fr.wiktionary.org/w/index.php?title=$mot#$langue&action=edit\">$mot</a>" ;
				}
				if ($type != '') { echo " de type $type" ; }
				if ($langue != '') { echo " en $langues[$langue]" ; } else { echo " en toutes langues" ; }
				echo "&nbsp;:</p>\n" ;
				
				$titre_wiki = '' ;
				$option = array("titre" => $titre_wiki, 'langue' => $langue, 'type' => $type);
				
				echo $navigation_string ;
				affiche_liste($liste, $resultat, $option) ;
				echo $navigation_string ;
				echo "<p class=\"NB\">NB&nbsp;: cliquez sur la prononciation pour accéder à la page <a href=\"rimes.php\">de recherche avancée</a> (afin de trouver des rimes pour ce mot par exemple).</p>" ;
			}
			
			#############
			# LOG
			$message = "terme='$mot'\tlangue='$langue'\ttype='$type'\tnum='$num'" ;
			log_action('prononciation', $message, $requete) ;
			#############
			
			mysql_close();
		}
	}
	
	if ($depuis == 0) { unset($_GET['depuis']) ; }
	if ($langue == '') { unset($_GET['langue']) ; }
	if ($type == '') { unset($_GET['type']) ; }
	if ($mot == '') { unset($_GET['rime']) ; }
	if ($gent == '') { unset($_GET['gent']) ; }
?>
	<?php require ("piedpage.php"); ?>
	</div>
	<?php require ("entete.php"); ?>
</body>
</html>
