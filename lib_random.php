<?php

$max = array();

# Retrieve an id for a random word
function get_random_id($langue) {
	global $max;

	# Français par défaut
	if (!$langue) {
		$langue = 'fr';
	}
	
	if (!array_key_exists($langue, $max)) {
		$query = "SELECT lg_num_min FROM langs WHERE lg_lang='$langue'";
		$result = mysql_query($query);
		$max[$langue] = mysql_result($result, 0);
	}
	
	return rand(1, $max[$langue]);
}

# Returns a random word (mot), url-friendly (raw) and anchor (ancre)
function get_random_word($lang) {
	# Defined language?
	if ($lang) {
		$langue = mysql_real_escape_string($lang);
	# Not defined: default to fr
	} else {
		$langue = 'fr';
	}
	
	# Get a random id
	$rand = get_random_id($langue);
	
	# Get the random word
	$requete = "SELECT a_title FROM entries WHERE l_lang='$langue' AND l_rand=$rand";
	$result = mysql_query($requete);
	$mot = mysql_result($result, 0);
	
	#############
	# LOG
	log_action('mot_au_hasard', "$langue\t$mot", $requete);
	#############
	
	$m['mot'] = $mot;
	$m['raw'] = rawurlencode($mot);
	$m['ancre'] = ($m['raw'] and $langue != 'fr') ? '#'.$langue : '';
	
	return $m;
}

?>
