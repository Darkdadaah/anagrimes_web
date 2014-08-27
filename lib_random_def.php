<?php

# Returns a random list of defs for a word
function get_random_def($lang, $num) {
	# Defined language?
	$langue = 'fr';
	if ($lang) {
		$langue = mysql_real_escape_string($lang);
	}
	if (!$num) {
		$num = 1;
	}
	
	# Get a random id
	$limit = 30;
	$all_defs = array();
	for ($n = 0; $n <= $num; $n++) {
		$rand = get_random_id($langue);
		
		# Get the random defs
		$requete = "SELECT a_title, l_type, l_is_locution, d_num, d_def, d_defid FROM lexemes INNER JOIN articles ON l_artid=a_artid INNER JOIN defs ON l_lexid=d_lexid WHERE l_lang='$langue' AND l_rand=$rand AND d_def IS NOT NULL GROUP BY d_defid, d_num ORDER BY d_num";
		$result = mysql_query($requete);
		$defs = array();
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$def = array();
			$def['title'] = $row[0];
			$def['type'] = $row[1];
			$def['loc'] = $row[2];
			$def['num'] = $row[3];
			$def['def'] = $row[4];
			$def['defid'] = $row[5];
			if ($row[5] != '') {
				$defs[] = $def;
			} else {
				if ($limit > 0) {
					$n--;
					$limit--;
					continue;
				} else {
					break;
				}
			}
		}
		$all_defs[] = $defs;
	}

	return $all_defs;
}

?>
