<?php

# Returns a random list of defs for a word
function get_random_def($lang) {
	# Defined language?
	if ($lang) {
		$langue = mysql_real_escape_string($lang);
	# Not defined: default to fr
	} else {
		$langue = 'fr';
	}
	
	# Get a random id
	$rand = get_random_id($langue);
	
	# Get the random defs
	$requete = "SELECT a_title, l_type, l_is_locution, l_num, d_def FROM articles LEFT JOIN lexemes ON a_artid=l_artid LEFT JOIN prons ON l_lexid=p_lexid LEFT JOIN defs ON l_lexid=d_lexid WHERE l_lang='$langue' AND l_rand=$rand ORDER BY d_num";
	$result = mysql_query($requete);
	$defs;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$def;
		$def['title'] = $row[0];
		$def['type'] = $row[1];
		$def['loc'] = $row[2];
		$def['num'] = $row[3];
		$def['def'] = $row[4];
		$defs[] = $def;
	}

	return $defs;
}

?>
