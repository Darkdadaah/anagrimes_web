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
	$all_defs = array();
	for ($n = 0; $n <= $num; $n++) {
		$rand = get_random_id($langue);
		
		# Get the random defs
		$requete = "SELECT a_title, l_type, l_is_locution, l_num, d_def, l_lexid FROM articles LEFT JOIN lexemes ON a_artid=l_artid LEFT JOIN prons ON l_lexid=p_lexid LEFT JOIN defs ON l_lexid=d_lexid WHERE l_lang='$langue' AND l_rand=$rand ORDER BY d_num";
		$result = mysql_query($requete);
		$defs = array();
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$def = array();
			$def['title'] = $row[0];
			$def['type'] = $row[1];
			$def['loc'] = $row[2];
			$def['num'] = $row[3];
			$def['def'] = $row[4];
			$defs[] = $def;
		}
		$all_defs[] = $defs;
	}

	return $all_defs;
}

?>
