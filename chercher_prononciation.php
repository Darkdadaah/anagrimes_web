<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

<head>
	<title>Prononciation</title>
	<meta http-equiv="Content-Type" content="text/HTML; charset=utf-8" />
	<link rel="stylesheet" type="text/css" media="all" href="style/default.css" />
	<link rel="stylesheet" type="text/css" media="print" href="style/print.css" />
	<link rel="shortcut icon" href="favicon.png" />
</head>

<body>
	<div class="page">
	<h1>Recherche avancée de prononciation</h1>
	<?php require ("part_avertissement.php"); ?>
	<?php require ("part_retour.php"); ?>
	<?php require ("lib_formulaire.php"); ?>
	
	<form class="formulaire" name="form_rime" action="<?=$_SERVER['SCRIPT_NAME']?>#liste" method="get">
	<fieldset>
		<legend>Options de recherche</legend>
		<table>
		<tr><td style="width: 50%;"><fieldset>
			<legend>Dans la prononciation</legend>
			<p class="NB">Note&nbsp;: vous pouvez écrire la prononciation de votre mot en <a href="//fr.wiktionary.org/wiki/Annexe:Prononciation">API ou X-SAMPA</a>. Si vous ne la connaissez pas, <? echo "<a href=\"prononciation_de.php\">" ?>cherchez-la d'abord sur la page dédiée</a>.</p>
			<p>
				<?php
					echo '<label for="rime">Rime&nbsp;:&nbsp;<input type="text" name="rime" id="rime" value="' ;
					if (isset($_GET['rime'])) { echo $_GET['rime'] ; }
					echo '" />' ;
				?></label><input type="submit" value="Lancer la recherche" /><input type="button" onclick="form.rime.value=''" value="Effacer" />
			</p>
			<? langues(isset($_GET['langue']) ? $_GET['langue'] : ''); ?>
			<? types(isset($_GET['type']) ? $_GET['type'] : ''); ?>
			<ul>
			<li><? flexions(true); ?></li>
			<li><? locutions(true); ?></li>
			<li><? gentiles(true); ?></li>
			<li><? print_checkbox('no_correction', 'oui', 'Ne pas convertir en API', false) ?></li>
			
			</ul>
		</fieldset></td>
		
		<td style="width: 50%;">
		<? require("part_clavier_prononciation.php"); ?>
		</td>
		</tr>
		<tr>
		<td style="width: 50%;"><? listes(); ?></td>
		<td style="width: 50%;"><? place('rime'); ?></td>
		</tr>
		</table>
		
		<input type="submit" value="Lancer la recherche" />
	</fieldset>
	</form>
	<script>var focusHere = document.getElementById('rime') ; focusHere = focusHere.focus();</script>
<?php
	require('lib_database.php') ;
	dbconnect() ;
	
	$rime = mysql_real_escape_string(isset($_GET['rime']) ? $_GET['rime'] : NULL) ;
	$rime_position = mysql_real_escape_string(isset($_GET['place']) ? $_GET['place'] : NULL) ;
	$liste = mysql_real_escape_string(isset($_GET['liste']) ? $_GET['liste'] : NULL) ;
	$no_correction = mysql_real_escape_string(isset($_GET['no_correction']) ? $_GET['no_correction'] : false) ;
	$type = mysql_real_escape_string(isset($_GET['type']) ? $_GET['type'] : NULL) ;
        $gent = mysql_real_escape_string(isset($_GET['gent']) ? $_GET['gent'] : NULL) ;
	$langue = mysql_real_escape_string(isset($_GET['langue']) ? $_GET['langue'] : NULL) ;
	$depuis = mysql_real_escape_string(isset($_GET['depuis']) ? $_GET['depuis'] : NULL) ;
	
	# Limit?
	$max_by_page = 100 ;
	if (!$depuis or $depuis < 0) {
		$depuis = 0 ;
		$next = $depuis + $max_by_page ;
	}
	
	$limit = "$depuis, $max_by_page" ;
	
	if (!is_null($rime)) {
		$rime0 = $rime ;
		echo "\t\t<h2 id=\"liste\"> Résultats</h2>\n" ;
		echo "<!---- $rime ---->\n" ;
		
		# Conversion SAMPA - API
		if (!$no_correction) {
			# Souplesse
			$rime = eregi_replace('A~', 'ɑ̃', $rime) ;
			$rime = eregi_replace('O~', 'ɔ̃', $rime) ;
			$rime = eregi_replace('E~', 'ɛ̃', $rime) ;
			$rime = eregi_replace('9~', 'œ̃', $rime) ;
			$rime = eregi_replace('1', 'œ̃', $rime) ;
			echo "<!---- $rime ---->\n" ;
			# X-SAMPA
			$rime = ereg_replace('A', 'ɑ', $rime) ;
			$rime = ereg_replace('O', 'ɔ', $rime) ;
			$rime = ereg_replace('E', 'ɛ', $rime) ;
			$rime = ereg_replace('9', 'œ', $rime) ;
			$rime = ereg_replace('2', 'ø', $rime) ;
			$rime = ereg_replace('@', 'ə', $rime) ;
			$rime = ereg_replace('R', 'ʁ', $rime) ;
			$rime = ereg_replace('S', 'ʃ', $rime) ;
			$rime = ereg_replace('J', 'ŋ', $rime) ;
			$rime = ereg_replace('g', 'ɡ', $rime) ;
			$rime = ereg_replace('N', 'ɲ', $rime) ;
			$rime = ereg_replace('Z', 'ʒ', $rime) ;
			$rime = ereg_replace('H', 'ɥ', $rime) ;
			echo "<!---- $rime ---->\n" ;
			# Ponctuation
			$rime = ereg_replace('/', '', $rime) ;
			$rime = ereg_replace('\[\?\]', 'ʔ', $rime) ;
			$rime = ereg_replace('~', '̃', $rime) ;
			echo "<!---- $rime ---->\n" ;
			
			# Français -> API
			$rime = eregi_replace('chr', 'kʁ', $rime) ;
			$rime = eregi_replace('ch', 'ʃ', $rime) ;
			echo "<!---- $rime ---->\n" ;
			
			$rime = eregi_replace('ʁ', 'r', $rime) ;
			$rime = eregi_replace('é', 'e', $rime) ;
			$rime = eregi_replace('è', 'ɛ', $rime) ;
			$rime = ereg_replace('ï', 'i', $rime) ;
			$rime = eregi_replace('ô', 'o', $rime) ;
			$rime = eregi_replace('\.', '', $rime) ;
			echo "<!---- $rime ---->\n" ;
			# Maj -> min
			$rime = utf8_strtolower($rime) ;
			echo "<!---- $rime ---->\n" ;
		}
		
		$cond = " articles.titre IS NOT NULL";
		$order = 'mots.titre' ;
		switch($rime_position) {
			case 'debut':
				$cond_rime = "mots.pron_simple LIKE '$rime%'" ;
				$order = 'mots.titre, mots.type, mots.num' ;
				break;
			case 'fin':
				$reverse = utf8_strrev($rime) ;
				$cond_rime = "mots.r_pron_simple LIKE '$reverse%'" ;
				$order = 'mots.r_pron_simple, mots.titre, mots.type, mots.num' ;
				break;
			case 'exact':
				$cond_rime = "mots.pron_simple = '$rime'" ;
				$order = 'mots.titre, mots.type, mots.num' ;
				break;
// 			case 'partout':
// 				$cond_rime = "mots.pron_simple LIKE '%$rime%'" ;
// 				break;
// 			case 'milieu':
// 				$cond_rime = "mots.pron_simple LIKE '%$rime%' AND NOT mots.pron_simple LIKE '%$rime' AND NOT mots.r_pron_simple LIKE REVERSE('$rime%')" ;
// 				break;
			default:
				break;
		}
		
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
		
		if ($cond and $cond_rime) {
			# Compte
			$requete_compte = "SELECT count(*) FROM articles LEFT JOIN mots ON articles.titre=mots.titre WHERE $cond AND $cond_rime" ;
			echo "<!-- Requète compte : $requete_compte -->\n" ;
			$resultat_compte = mysql_query($requete_compte) or die("Query failed");
			$compte = mysql_fetch_array($resultat_compte) ;
			$num = $compte[0] ;
			
			if ($num == 0) {
				$terme = 'terme trouvés' ;
				if ($rime_position=='fin') { $terme = 'rime trouvées' ; }
				echo "<p>Pas de $terme pour /$rime/" ;
				if ($type != '') { echo " de type $type" ; }
				if ($langue != '') { echo " en $langues[$langue]" ; } else { echo " en toutes langues" ; }
				if (!$_GET['flex']) { echo ", flexions incluses" ; } else { echo ", hors flexions" ; }
				if (!$_GET['loc']) { echo ", locutions incluses" ; } else { echo ", hors locutions" ; }
				echo ".</p>\n" ;
				
				# Suggérer le mot s'il existe
				$mot_ascii = $rime0 ;
				$requete_mot = "SELECT * FROM articles LEFT JOIN mots ON articles.titre=mots.titre WHERE $cond AND articles.titre='$mot_ascii' AND pron!=''" ;
				echo "<!-- Requète mot : $requete_mot -->\n" ;
				$resultat_mot = mysql_query($requete_mot) or die("Query failed");
				$mot = mysql_fetch_array($resultat_mot) ;
				if ($mot[0]) {
					echo "<p>Vous pouvez essayer en utilisant la prononciation phonétique suivante du mot <a href=\"//fr.wiktionary.org/wiki/" .$rime0. "\">" .$rime0. "</a> en " .$langues[$mot['langue']]. " : " ;
                                        echo "<a href=\"#\" onclick=\"reinit_rime(); tapron('" . $mot['pron'] . "')\">/" . $mot['pron'] . "/</a>.</p>\n" ;
                                }
				
				# Suggestions
				$suggestions = '' ;
				if (preg_match("/bb|cc|dd|ee|ff|gg|hh|ii|jj|kk|ll|mm|nn|pp|rr|ss|tt/", $rime)) {
					$suggestions .= "<li>Éviter les dédoublement de lettres</li>\n" ;
				}
				if (preg_match("/[^cps]h/", $rime)) {
					$suggestions .= "<li>Enlevez le h (muet en français)</li>\n" ;
				}
				if (preg_match("/eʁ$/", $rime)) {
					$suggestions .= "<li>Remplacer le -er par -e (le r de -er ne se prononce généralement pas en français)</li>\n" ;
				}
				if (preg_match("/e$/", $rime)) {
					$suggestions .= "<li>Enlever le -e à la fin s'il doit être muet (le e se prononce « é » en API)</li>\n" ;
				}
				if (preg_match("/[std]$/", $rime)) {
					$suggestions .= "<li>Enlever le -s, le -t, le -d à la fin s'il doit être muet</li>\n" ;
				}
				if (preg_match("/ɡu/", $rime)) {
					$suggestions .= "<li>Remplacer le gu par g (/gu/ se lit « gou » en API)</li>\n" ;
				}
				if (preg_match("/ou/", $rime)) {
					$suggestions .= "<li>Remplacer le ou par u (/ou/ se lit « ô-ou » en API)</li>\n" ;
				}
				if (preg_match("/eu/", $rime)) {
					$suggestions .= "<li>Remplacer le eu par ø (fermé, comme 2) ou œ comme (ouvert, comme 9)</li>\n" ;
				}
				if (preg_match("/on/", $rime)) {
					$suggestions .= "<li>Remplacer le on par ɔ̃ (O~) (/on/ se lit « ô-n » en API)</li>\n" ;
				}
				if (preg_match("/oi/", $rime)) {
					$suggestions .= "<li>Remplacer le oi par wa (/oi/ se lit « ô-i » en API)</li>\n" ;
				}
				if (preg_match("/o/", $rime) and !preg_match("/ɔ/")) {
					$suggestions .= "<li>Remplacer le o fermé par un ɔ (O) ouvert (comme dans <i>bol</i>)</li>\n" ;
				}
				if (preg_match("/(ille?|[eaɛ]il$)/i", $rime)) {
					$suggestions .= "<li>Remplacer le « ill » par j ou ij (prononciation API de ill, y, etc.)</li>\n" ;
				}
				if (preg_match("/[iy][eaɛouy]/", $rime)) {
					$suggestions .= "<li>Remplacer le i ou le y par un /j/ devant une voyelle</li>\n" ;
				}
				if (preg_match("/in([^eɛaiouy]|$)/", $rime)) {
					$suggestions .= "<li>Remplacez « in » par /ɛ̃/ (s'il ne se prononce par « i-n »)</li>\n" ;
				}
				if (preg_match("/en([^eɛaiouy]|$)/", $rime)) {
					$suggestions .= "<li>Remplacez « en » par /ɑ̃/ (comme an) ou /ɛ̃/ (comme un)</li>\n" ;
				}
				if (preg_match("/e/", $rime) and !preg_match("/ɛ/")) {
					$suggestions .= "<li>Remplacer le e par un ɛ (E) ouvert (comme dans <i>fer</i>)</li>\n" ;
				}
				
				if ($suggestions != '') {
					$suggestions .= "<li>Pensez à raccourcir le mot recherché si vous voulez des rimes.</li>\n" ;
				}
				
				if ($suggestions != '') {
					$suggestions = "<p>Suggestions&nbsp;:</p><ul>\n".$suggestions."</ul>\n" ;
					print $suggestions ;
				}
				
			} else {
				# Requète
				$requete = "SELECT articles.titre, mots.pron, mots.flex, mots.loc, mots.num $select_type $select_langue FROM articles LEFT JOIN mots ON articles.titre=mots.titre WHERE $cond AND $cond_rime ORDER BY $order LIMIT $limit" ;
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
				
				if ($rime_position=='fin' and $langue=='fr') {
					$nom_langue = $langue ;
					if ($langues[$langue]) {
						$nom_langue = $langues[$langue] ;
					}
					echo "<p>$num rimes trouvées pour <a href=\"//fr.wiktionary.org/w/index.php?title=Annexe:Rimes_en_".$nom_langue."_en_/$rime/&action=edit\">/$rime/</a>" ;
				} else {
					echo "<p>$num termes trouvés pour /$rime/" ;
				}
				if ($type != '') { echo " de type $type" ; }
				if ($langue != '') { echo " en $langues[$langue]" ; } else { echo " en toutes langues" ; }
				echo "&nbsp;:</p>\n" ;
				
				$titre_wiki = '' ;
				$option = array("titre" => $titre_wiki, 'langue' => $langue, 'type' => $type);
				
				echo $navigation_string ;
				affiche_liste($liste, $resultat, $option) ;
				echo $navigation_string ;

                                # Suggérer le mot s'il existe
                                $mot_ascii = $rime0 ;
				$requete_mot = "SELECT * FROM articles LEFT JOIN mots ON articles.titre=mots.titre WHERE $cond AND articles.titre='$mot_ascii' AND pron!=''" 
;
                                echo "<!-- Requète mot : $requete_mot -->\n" ;
                                $resultat_mot = mysql_query($requete_mot) or die("Query failed");
                                $mot = mysql_fetch_array($resultat_mot) ;
                                if ($mot[0]) {
                                        echo "<p>Vous pouvez essayer en utilisant la prononciation phonétique suivante du mot <a href=\"//fr.wiktionary.org/wiki/" .$rime0. "\">" .$rime0. "</a> en " .$langues[$mot['langue']]. " : " ;
                                        echo "<a href=\"#\" onclick=\"reinit_rime(); tapron('" . $mot['pron'] . "')\">/" . $mot['pron'] . "/</a>.</p>\n" ;
                                }

			}
			
			#############
			# LOG
			$message = "terme='$rime'\tposition='$rime_position'\tlangue='$langue'\ttype='$type'\tnum='$num'" ;
			log_action('rimes', $message, $requete) ;
			#############
			
			mysql_close();
		}
	}
	if ($depuis == 0) { unset($_GET['depuis']) ; }
	if ($langue == '') { unset($_GET['langue']) ; }
	if ($type == '') { unset($_GET['type']) ; }
	if ($rime == '') { unset($_GET['rime']) ; }
	if ($gent == '') { unset($_GET['gent']) ; }
?>
	<?php require ("part_piedpage.php"); ?>
	</div>
	<?php require ("part_entete.php"); ?>
</body>
</html>

