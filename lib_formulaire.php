<?php
	//ini_set('log_errors',1);
	//ini_set('error_log','/home/darkdadaah/logs/php.txt');
	require('lib_listes.php') ;
	require('lib_chaines.php') ;
	
	function langues() {
		global $langues ;
		print '<select id="langue" name="langue">'."\n" ;
		print "\t<option value=\"\"" ;
		if (isset($_GET['langue']) and !($_GET['langue'])) { print " selected=\"selected\""; }
		print ">Toutes langues</option>\n" ;
		
		$langues_names = array_keys($langues) ;
		while ($title = array_shift($langues_names)) {
			$label = ucfirst($langues[$title]) ;
			print "\t<option value=\"$title\"" ;
			if (isset($_GET['langue']) and $_GET['langue'] == $title or ($title == 'fr' and !isset($_GET['langue']))) { print " selected=\"selected\""; }
			print ">$label</option>\n" ;
		}
		print '</select>'."\n" ;
	}
	
	function types() {
		global $types ;
		print '<select id="type" name="type">'."\n" ;
		print "\t<option value=\"\"" ;
		if (!isset($_GET['type'])) { print " selected=\"selected\""; }
		print ">Tous types</option>\n" ;
		
		$type_names = array_keys($types) ;
		while ($title = array_shift($type_names)) {
			$label = ucfirst($types[$title]) ;
			print "\t<option value=\"$title\"" ;
			if (isset($_GET['type']) and $_GET['type'] == $title) { print " selected=\"selected\""; }
			print ">$label</option>\n" ;
		}
		print '</select>'."\n" ;
	}
	
	function flexions($check) {
		print_checkbox('flex', 'oui', 'Inclure les flexions <small>(conjugaisons, accords, déclinaisons)</small>', $check) ;
	}
	
	function locutions($check) {
		print_checkbox('loc', 'oui', 'Inclure les locutions <small>(expressions à plusieurs mots)</small>', $check) ;
	}
	
	function gentiles($check) {
		print_checkbox('gent', 'oui', 'Inclure les gentilés <small>(noms et adjectifs d\'habitants)</small>', $check) ;
	}
	
	function nom_propre($check) {
		print_checkbox('nom_propre', 'oui', 'Inclure les noms propres', $check) ;
	}
	
	function place($rime) {
		$fin = 'À la fin' ;
		if ($rime) { $fin .= ' (rime)'; }
		
		$list = array(
			array('debut', 'Au début'),
			array('fin', $fin),
			array('milieu', 'En milieu de mot'),
			array('exact', 'Exact'),
		) ;
		$current = isset($_GET['place']) ? $_GET['place'] : '' ;
		$default = 'fin' ;
		print_radio('Place recherchée', 'place', $list, $current, $default) ;
	}
	function listes() {
		$list = array(
			array('wiki', 'Liste wiki'),
			array('table', 'Tableau détaillé'),
			array('simple', 'Liste simple'),
		) ;
		$current = isset($_GET['liste']) ? $_GET['liste'] : '' ;
		$default = 'table' ;
		print_radio('Type de liste', 'liste', $list, $current, $default) ;
	}
	
	function print_checkbox($name, $value, $text, $def_check) {
		$checked=false ;
		if (sizeof($_GET)==0) {
			$checked=$def_check ;
		} else {
			if ($_GET[$name]) {
				$checked = true ;
			}
		}
		print "<input type=\"checkbox\" value=\"$value\" name=\"$name\" id=\"$name"."_box\"" ;
		if ($checked) { print  ' checked="checked"' ; }
		print "/>&nbsp;<label for=\"$name"."_box\">&nbsp;$text&nbsp;?</label>" ;
	}
	
	function print_radio($title, $name, $list, $current, $default) {
		print '<fieldset>' ;
		print "<legend>$title&nbsp;</legend>\n" ;
		print "\t<ul>\n" ;
		while ($title = array_shift($list)) {
			$value = $title[0] ;
			$text = $title[1] ;
			print "\t\t<li><input type=\"radio\" value=\"$value\" name=\"$name\" id=\"$value".'_radio"' ;
			if ($current==$value or (!$current and $default==$value)) { print  ' checked="checked"' ; }
			print " /><label for=\"$value"."_radio\">&nbsp;$text</label></li>\n" ;
		}
		print "\t</ul>\n" ;
		print "</fieldset>\n" ;
	}
	
	function affiche_liste($liste, $resultat, $option) {
		switch($liste) {
			case 'simple' :
				affiche_simple($resultat) ;
				break ;
			case 'wiki' :
				affiche_wiki($resultat) ;
				break ;
			case 'table' :
				affiche_table($resultat, $option) ;
				break ;
			default :
				affiche_table($resultat, $option) ;
				break ;
		}
	}
	
	function affiche_simple($resultat) {
		print "<ul>\n" ;
		while ($ligne = mysql_fetch_array($resultat)) {
			$titre = $ligne['titre'] ;
			$ancre = '' ;
			if ($option['langue'] or $ligne['langue']) {
				$lang_name = $ligne['langue'] ? $ligne['langue'] : $option['langue'] ;
				if ($option['type'] or $ligne['type']) {
					$type_name = $ligne['type'] ? $ligne['type'] : $option['type'] ;
					if ($ligne['loc']) { $type_name = 'loc-' . $type_name ; }
					if ($ligne['flex']) { $type_name = 'flex-' . $type_name ; }
					if ($ligne['num'] > 1) {
						$ancre = '#' . $lang_name . '-' . $type_name . '-' . $ligne['num'] ;
					} else {
						$ancre = '#' . $lang_name . '-' . $type_name ;
					}
				} else {
						$ancre = '#' . $lang_name ;
				}
			}
			print "\t<li><a href=\"//fr.wiktionary.org/wiki/$titre$ancre\">$titre</a></li>\n" ;
		}
		print "</ul>\n" ;
	}
	
	function affiche_wiki($resultat) {
		print "<ul>\n" ;
		while ($ligne = mysql_fetch_array($resultat)) {
			$titre = $ligne['titre'] ;
			print "* [[$titre]]<br />\n" ;
		}
		print "</ul>\n" ;
	}
	
	function affiche_table($resultat, $option) {
		global $langues, $types ;
		print "<table class=\"ortho-list\">\n" ;
			print "<tr>\n" ;
			print "\t<th>Titre</th>\n" ;
			if ($option['transcrit']) { print "\t<th>Transcription</th>\n" ; }
                        if (!$option['no_pron']) { print "\t<th>Prononciation</th>\n" ; }
			if (!$option['langue']) { print "\t<th>Langue</th>\n" ; }
			if (!$option['type'] and !$option['no_type']) { print "\t<th>Type</th>\n" ; }
			print "</tr>\n" ;
		
		while ($ligne = mysql_fetch_array($resultat)) {
			$titre = $ligne['titre'] ;
			$pron = $ligne['pron'] ;
			$ancre = '' ;
			if ($option['langue'] or $ligne['langue']) {
				$lang_name = $ligne['langue'] ? $ligne['langue'] : $option['langue'] ;
				if ($option['type'] or $ligne['type'] and !$option['no_type']) {
					$type_name = $ligne['type'] ? $ligne['type'] : $option['type'] ;
					if ($ligne['loc']) { $type_name = 'loc-' . $type_name ; }
					if ($ligne['flex']) { $type_name = 'flex-' . $type_name ; }
					if ($ligne['num'] > 1) {
						$ancre = '#' . $lang_name . '-' . $type_name . '-' . $ligne['num'] ;
					} else {
						$ancre = '#' . $lang_name . '-' . $type_name ;
					}
				} else {
						$ancre = '#' . $lang_name ;
				}
			}
			
			print "\t<tr>" ;
			print "<td><a href=\"//fr.wiktionary.org/wiki/$titre$ancre\">$titre</a></td>" ;
			
                        if ($option['transcrit']) {
                                $transcript_label = $ligne['transcrit'] ;
                                print "\t<td class=\"transcrit\">$transcript_label</td>\n" ;
                        }
			
			if (!$option['no_pron']) {
				if ($pron) {
// 					if ($option['pron_lien']) {
						print "<td><a href=\"chercher_prononciation.php?rime=$pron&langue=".$option['langue']."&place=exact#liste\">/<span class=\".API\">$pron</span>/</a></td>\n" ;
// 					} else {
// 						print "<td>/<span class=\".API\">$pron</span>/</a></td>\n" ;
// 					}
				} else {
					print "<td><a href=\"//fr.wiktionary.org/wiki/$titre$ancre\"><abbr title=\"Pas de prononciation disponible. Cliquez pour accéder à l'article.\">//</abbr></a></td>\n" ;
				}
			}
			if (!$option['langue']) {
				if ($langues[$ligne['langue']]) {
					$lang_label = ucfirst($langues[$ligne['langue']]) ;
					$langue_url = '//fr.wiktionary.org/wiki/Catégorie:'.$langues[$ligne['langue']] ;
					$lang_label = "<a href=\"$langue_url\">$lang_label</a>" ;
				} else {
					$lang_label = $ligne['langue'] ;
				}
				print "\t<td>$lang_label</td>\n" ;
			}
			if (!$option['type'] and !$option['no_type']) {
				$type_label = $types[$ligne['type']] ? ucfirst($types[$ligne['type']]) : $ligne['type'] ;
				if ($ligne['num'] > 1) { $type_label = $type_label . ' ' . $ligne['num'] ; }
				print "\t<td>$type_label</td>\n" ;
			}
			print "</tr>\n" ;
		}
		print "</table>\n" ;
	}
	
	function joker($mot, $titre, $r_titre) {
		$num_quest = substr_count($mot, '?') ;
// 		
// 		# Questions?
		if ($num_quest == 1) {
			$mot = str_replace('?', '*', $mot) ;
		} else if ($num_quest > 1) {
			# Tous au même endroit ?
			if (preg_match('/\?[^\?]\?/', $mot)) {
				echo "<p>Merci de n'utiliser qu'un seul joker (*).</p>\n" ;
				return $cond ;
			} else {
				$mot = preg_replace('/\?+/', '*', $mot) ;
			}
		}
		$num_aster = substr_count($mot, '*') ;
// 		
		$cond = '' ;
		
		if ($num_aster == 0) {
			$cond = " $titre='$mot'" ;
			return $cond ;
		}
		else if ($num_aster == 1) {
			# Joker seul? Renvoyer l'astérique...
			if ($mot == '*') {
				echo "<p>Tous les articles du Wiktionnaire sont disponibles à l'adresse <a href=\"//fr.wiktionary.org\">fr.wiktionary.org</a>. (à moins que vous ne cherchiez l'article sur <a href=\"//fr.wiktionary.org/wiki/*\">*</a>&nbsp;?)</p>\n" ;
				return $cond ;
			}
			# Joker à la fin = début de mot
			else if (preg_match('/\*$/', $mot)) {
				$mot2 = str_replace('*', '', $mot) ;
				$cond = " $titre LIKE '$mot2%'" ;
			}
// 			# Joker au début = fin de mot
			else if (preg_match('/^\*/', $mot)) {
				$mot2 = str_replace('*', '', $mot) ;
				$reverse = utf8_strrev($mot2) ;
				$cond = " $r_titre LIKE '$reverse%'" ;
				
			}
// 			# Joker au milieu
			else if (preg_match('/^.+\*.+$/', $mot)) {
				$parties = preg_split('/\*/', $mot) ;
				$debut = $parties[0] ;
				$fin = utf8_strrev($parties[1]) ;
				$cond = " $titre LIKE '$debut%' AND $r_titre LIKE '$fin%'" ;
			}
			else {
				echo "<p>Utilisation du joker non prise en compte (*).</p>\n" ;
				return $cond ;
			}
			
			if ($num_quest > 0) {
				$length = utf8_length($mot) + $num_quest - 1 ;
				if (!preg_match('/_plat$/', $titre)) {
					$titre =  $titre.'_plat' ;
				}
				$cond .= " AND length($titre)=$length" ;
			}
			
			return $cond ;
		}
		else {
			echo "<p>Merci de n'utiliser qu'un seul joker (*).</p>\n" ;
			return $cond ;
		}
	}
?>
