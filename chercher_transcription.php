<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

<head>
	<title>Transcriptions</title>
	<meta http-equiv="Content-Type" content="text/HTML; charset=utf-8" />
	<link rel="stylesheet" type="text/css" media="all" href="style/default.css" />
	<link rel="stylesheet" type="text/css" media="print" href="style/print.css" />
	<link rel="shortcut icon" href="favicon.png" />
</head>

<body>
	<div class="page">
	<h1>Recherche avancée de transcriptions</h1>
	<?php require ("part_avertissement.php"); ?>
	<?php require ("part_retour.php"); ?>
	<?php require ("lib_formulaire.php"); ?>
	
	<form class="formulaire" action="<?=$_SERVER['SCRIPT_NAME']?>#liste" method="get">
	<fieldset>
		<legend>Options de recherche</legend>
		<fieldset>
			<legend>Rechercher une partie de transcription</legend>
			<p>
				<?php
				echo "<label for=\"transc\">Transcrit&nbsp;:&nbsp;<input type=\"text\" name=\"transc\" id=\"transc\" value=\"" ;
				if ($_GET['transc']) { echo $_GET['transc'] ; }
				echo "\" />" ;
				?></label><input type="submit" value="Lancer la recherche" /><input type="button" onclick="form.transc.value=''" value="Effacer" /><span><small>Vous pouvez <a href="aide.php">utiliser un joker</a> pour affiner la recherche.</small></span>
			</p>
			<? langues($_GET['langue']); ?>
			<ul>
			</ul>
		</fieldset>
		<? listes($GET_['liste']); ?>
	
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
	
	$graphie = mysql_real_escape_string($_GET['transc']) ;
	$titre = mysql_real_escape_string($_GET['titre']) ;
	$liste = mysql_real_escape_string($_GET['liste']) ;
	$langue = mysql_real_escape_string($_GET['langue']) ;
	$depuis = mysql_real_escape_string($_GET['depuis']) ;
	
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
		
		echo "\t\t<h2 id=\"liste\">Résultats</h2>\n" ;
		
		$titre = 'transcrits.transcrit_plat' ;
		$r_titre = 'transcrits.r_transcrit_plat' ;
		
		# Joker
		$cond = joker($graphie, $titre, $r_titre) ;
		if ($cond) {
			$cond_graphie = $cond ;
			if ($langue) {
				$cond_langue = "mots.langue='$langue'" ;
			} else {
				$select_langue = ", mots.langue" ;
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

			if ($cond) {
				# Compte
			
				# Table normale
				$requete_compte = "SELECT count(*) FROM transcrits LEFT JOIN mots ON transcrits.titre=mots.titre WHERE $cond" ;
				echo "<!-- Compte transcrits : $requete_compte -->\n" ;
				
				$message = "terme='$graphie'\tlangue='$langue'" ;
				$resultat_compte = mysql_query($requete_compte) or die_graphie($requete_compte, $message) ;
				$compte = mysql_fetch_array($resultat_compte) ;
				$num = $compte[0] ;
				
				# Requète
				$requete = "SELECT transcrits.titre, transcrits.transcrit $select_langue FROM transcrits LEFT JOIN mots ON transcrits.titre=mots.titre WHERE $cond GROUP BY transcrits.titre,transcrits.transcrit ORDER BY mots.titre LIMIT $limit" 
;
				echo "<!-- Requète articles : $requete -->\n" ;
				
				if ($num == 0 ) {
					echo "<p>Pas de mot trouvé pour «&nbsp;$graphie&nbsp;»" ;
					if ($cond_langue != '') { echo " en $langues[$langue]" ; }
					echo ".</p>\n" ;
				} else {
					$message = "terme='$graphie'\tlangue='$langue'\tnum='$num'" ;
					$resultat = mysql_query($requete) or die_graphie($message, $requete) ;
					
					echo "<p>$num graphies trouvées pour « $graphie »" ;
					if ($cond_langue != '') { echo " en $langues[$langue]" ; }
					echo "&nbsp;:</p>\n" ;
				
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
					'titre'		=> $titre_wiki,
					'langue'	=> $langue,
					'no_type'	=> true,
					'no_pron'	=> true,
					'transcrit'	=> true,
					);
					
					echo $navigation_string ;
					affiche_liste($liste, $resultat, $option) ;
					echo $navigation_string ;
				}
				
				#############
				# LOG
				$message = "'$graphie'\t$langue\t$num" ;
				log_action('transcrits', $message, $requete) ;
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
