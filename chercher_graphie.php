<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

<head>
	<title>Graphies</title>
	<meta http-equiv="Content-Type" content="text/HTML; charset=utf-8" />
	<link rel="stylesheet" type="text/css" media="all" href="style/default.css" />
	<link rel="stylesheet" type="text/css" media="print" href="style/print.css" />
	<link rel="shortcut icon" href="favicon.png" />
</head>

<body>
	<div class="page">
	<h1>Recherche avancée de graphies</h1>
	<?php require ("part_avertissement.php"); ?>
	<?php require ("part_retour.php"); ?>
	<?php require ("lib_formulaire.php"); ?>
	
	<form class="formulaire" action="<?=$_SERVER['SCRIPT_NAME']?>#liste" method="get">
	<fieldset>
		<legend>Options de recherche</legend>
		<fieldset>
			<legend>Rechercher une partie de mot</legend>
			<p>
				<?php
				echo "<label for=\"graphie\">Graphie&nbsp;:&nbsp;<input type=\"text\" name=\"graphie\" id=\"graphie\" value=\"" ;
				if (isset($_GET['graphie'])) { echo $_GET['graphie'] ; }
				echo "\" />" ;
				?></label><input type="submit" value="Lancer la recherche" /><input type="button" onclick="form.graphie.value=''" value="Effacer" /><span><small>Vous pouvez <a href="aide.php">utiliser un joker</a> pour affiner la recherche.</small></span>
			</p>
			<? langues($_GET['langue']); ?>
			<? types($_GET['type']); ?>
			<ul>
			<li><? flexions(false); ?></li>
			<li><? locutions(false); ?></li>
			<li><? gentiles(false); ?></li>
			<li><? nom_propre(false); ?></li>
			<li><input type="checkbox" value="OK" name="no_diac" id="diacbox" <?php if (isset($_GET['no_diac'])) { echo ' checked="checked"' ; } ?> /><label for="diacbox">&nbsp;Ne pas prendre en compte les diacritiques (accents) et les majuscules&nbsp;?</label></li>
			<li><input type="checkbox" value="OK" name="transcrit" id="transcriptbox" <?php if (isset($_GET['transcrit'])) { echo ' checked="checked"' ; } ?> /><label for="transcritbox">&nbsp;Rechercher par transcriptions/translittérations (approximatives)</label></li>
			</ul>
		</fieldset>
		<? listes($_GET['liste']); ?>
	
	<input type="submit" value="Lancer la recherche" />
	</fieldset>
	</form>
	<script>var focusHere = document.getElementById('graphie') ; focusHere = focusHere.focus();</script>
<?php
	require('lib_database.php') ;
	dbconnect() ;
	
	
	function die_graphie($requete, $message) {
		log_action('erreur_requete', "$message\t[ $requete ]") ;
		die("La requète a échoué. Ceci est probablement dû à un bug dans le programme. Désolé pour la gêne occasionnée.") ;
	}
	
	$graphie = isset($_GET['graphie']) ? mysql_real_escape_string($_GET['graphie']) : null;
	$titre = isset($_GET['titre']) ? mysql_real_escape_string($_GET['titre']) : null;
	$liste = isset($_GET['liste']) ? mysql_real_escape_string($_GET['liste']) : null;
	$no_diac = isset($_GET['no_diac']) ? mysql_real_escape_string($_GET['no_diac']) : null;
	$transcrit = isset($_GET['transcrit']) ? mysql_real_escape_string($_GET['transcrit']) : null;
	$langue = isset($_GET['langue']) ? mysql_real_escape_string($_GET['langue']) : null;
	$type = isset($_GET['type']) ? mysql_real_escape_string($_GET['type']) : null;
	$depuis = isset($_GET['depuis']) ? mysql_real_escape_string($_GET['depuis']) : null;
	
	# Limit?
	$max_by_page = 100 ;
	if (!$depuis or $depuis < 0) {
		$depuis = 0 ;
		$next = $depuis + $max_by_page ;
	}
	
	$limit = "$depuis, $max_by_page" ;
	
	if ($graphie) {
		# Pas d'espace au début ou à la fin
		$graphie = preg_replace('/^\s+/', '', $graphie) ;
		$graphie = preg_replace('/\s+$/', '', $graphie) ;
		$select_transcrit = '';
		$select_langue = '';
		
		echo "\t\t<h2 id=\"liste\">Résultats</h2>\n" ;
		
		if ($transcrit) {
			$graphie = strtolower($graphie) ;
			$titre = 'articles.transcrit_plat' ;
			$r_titre = 'articles.r_transcrit_plat' ;
			$select_transcrit = ', articles.transcrit_plat AS transcrit' ;
		} else if ($no_diac) {
			$titre = 'articles.titre_plat' ;
			$r_titre = 'articles.r_titre_plat' ;
			
			# Enlève les diacritiques
			$graphie = non_diacritique($graphie) ;
		} else {
			$titre = 'articles.titre' ;
			$r_titre = 'articles.r_titre' ;
		}
		
		# Joker
		$cond = joker($graphie, $titre, $r_titre) ;
		$cond_langue = '';
		$cond_type = '';

		if ($cond) {
			$cond_graphie = $cond ;
			if ($langue) {
				$cond_langue = "mots.langue='$langue'" ;
			} else {
				$select_langue = ", mots.langue" ;
			}
			if ($type) {
				switch($type) {
					case 'nom-tous':
						$cond_type = "(type='nom' OR type='nom-pr')" ;
						$select_type = ", mots.type" ;
						break ;
					default:
						$cond_type = "(mots.type='$type')" ;
						break ;
				}
			} else {
				$select_type = ", mots.type" ;
			}
			$cond = '';
			
			if ($cond_graphie) {
				if ($cond=='') {
					$cond = $cond_graphie ;
				} else {
					$cond .= " AND $cond_graphie" ;
				}
			}
			if ($cond_langue) {
				if ($cond=='') {
					$cond = $cond_langue ;
				} else {
					$cond .= " AND $cond_langue" ;
				}
			}
			if ($cond_type) {
				if ($cond=='') {
					$cond = $cond_type ;
				} else {
					$cond .= " AND $cond_type" ;
				}
			}
		
			if (!isset($_GET['flex']) or !$_GET['flex'] or !($_GET['flex']=='oui')) {
				$cond .= " AND NOT flex" ;
			}
			if (!isset($_GET['loc']) or !$_GET['loc'] or !($_GET['loc']=='oui')) {
				$cond .= " AND NOT loc" ;
			}
			if (!isset($_GET['gent']) or !$_GET['gent'] or !($_GET['gent']=='oui')) {
				$cond .= " AND NOT gent" ;
			}
			if (!isset($_GET['nom_propre']) or !$_GET['nom_propre'] or !($_GET['nom_propre']=='oui')) {
				$cond .= " AND NOT mots.type='nom-pr'" ;
			}
			
			
			if ($cond) {
				# Compte
			
				# Table normale
				$requete_compte = "SELECT count(*) FROM articles LEFT JOIN mots ON articles.titre=mots.titre WHERE $cond" ;
				echo "<!-- Compte articles : $requete_compte -->\n" ;
				
				$message = "terme='$graphie'\tlangue='$langue'\ttype='$type'" ;
				$resultat_compte = mysql_query($requete_compte) or die_graphie($requete_compte, $message) ;
				$compte = mysql_fetch_array($resultat_compte) ;
				$num = $compte[0] ;
				
				# Requète
				$requete = "SELECT articles.titre, mots.pron, mots.flex, mots.loc, mots.num $select_type $select_langue $select_transcrit FROM articles LEFT JOIN mots ON articles.titre=mots.titre WHERE $cond ORDER BY mots.titre LIMIT $limit" ;
				echo "<!-- Requète articles : $requete -->\n" ;
			
				if ($num == 0 ) {
					echo "<p>Pas de graphies trouvées pour «&nbsp;$graphie&nbsp;»" ;
					if ($cond_type != '') { echo " de type $type" ; }
					if ($cond_langue != '') { echo " en $langues[$langue]" ; }
					echo ".</p>\n" ;
				} else {
					# Nettoie
					if ($type=='' or $type=='nom') { $cond_type='' ; }
				
					$message = "terme='$graphie'\tlangue='$langue'\ttype='$type'\tnum='$num'" ;
					$resultat = mysql_query($requete) or die_graphie($message, $requete) ;
					
					echo "<p>$num graphies trouvées pour « $graphie »" ;
					if ($cond_type != '') { echo " de type $type" ; }
					if ($cond_langue != '') { echo " en $langues[$langue]" ; }
					echo "&nbsp;:</p>\n" ;
					
					$navigation_string = '';	
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
									$target .= '&amp;' . $key . '=' . $value ;
								}
							}
							$n++ ;
						}
						$suite = $depuis+1 + $max_by_page ;
						$actuel = "(".($depuis+1)." - $suite) " ;
						$navigation_string = "<p>$actuel <a href=\"$target#liste\">$max_by_page résultats suivants</a></p>" ;
					}
				
					$titre_wiki = '' ;
					$option = array(
					"titre"		=> $titre_wiki,
					'langue'	=> $langue,
					'type'		=> $type,
					'transcrit' => $transcrit
					);
				
					echo $navigation_string ;
					affiche_liste($liste, $resultat, $option) ;
					echo $navigation_string ;
					echo "<p class=\"NB\">NB&nbsp;: cliquez sur la prononciation pour accéder à la page <a href=\"rimes.php\">de recherche avancée</a> (afin de trouver des rimes pour ce mot par exemple).</p>" ;

				}
				
				#############
				# LOG
				$message = "'$graphie'\t$langue\t$num" ;
				log_action('graphies', $message, $requete) ;
				#############
			
				mysql_close();
			}
		}
		if ($depuis == 0) {
			unset($_GET['depuis']) ;
		}
	}
?>
	<?php require ("part_piedpage.php"); ?>
	</div>
	<?php require ("part_entete.php"); ?>
</body>

</html>
