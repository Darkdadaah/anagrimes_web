<?php
require_once( 'lib_chaines.php' );
require_once( 'lib_requests.php' );

$max = array();

function alphagram($word) {
	$simple = non_diacritique($word);
	$lettres = preg_split('//', $simple, -1, PREG_SPLIT_NO_EMPTY);
	sort($lettres);
	$alphag = join(' ', $lettres);
	$alphag = ereg_replace(' ', '', $alphag);
	return $alphag;
}

# Returns a random word (mot), url-friendly (raw) and anchor (ancre)
function get_anagrams_list($db, $pars) {
	# Prepare request
	$conditions = array();
	$values = array();
	$types = "";
	# Word?
	if ($pars['string']) {
		array_push($conditions, "a_alphagram=?");
		array_push($values, alphagram($pars['string']));
		$types .= "s";
		#array_push($conditions, "a_alphagram=\"aeimr\"");
	} else {
		# no word: no anagrams
		return array();
	}
	# Language? Default: all
	if ($pars['lang']) {
		array_push($conditions, "l_lang=?");
		array_push($values, mysqli_real_escape_string($db, $pars['lang']));
		$types .= "s";
	}
	# Type
	if ($pars['type']) {
		array_push($conditions, "l_type=?");
		array_push($values, mysqli_real_escape_string($db, $pars['type']));
		$types .= "s";
	}
	# Flexion
	if (isset($pars['flex'])) {
		if ($pars['flex'] == true) {
			array_push($conditions, "l_is_flexion=TRUE");
		} else {
			array_push($conditions, "l_is_flexion=FALSE");
		}
	}
	# Locution
	if (isset($pars['loc'])) {
		if ($pars['loc'] == true) {
			array_push($conditions, "l_is_locution=TRUE");
		} else {
			array_push($conditions, "l_is_locution=FALSE");
		}
	}
	# Gentilé
	if (isset($pars['gent'])) {
		if ($pars['gent'] == true) {
			array_push($conditions, "l_is_gentile=TRUE");
		} else {
			array_push($conditions, "l_is_gentile=FALSE");
		}
	}
	# Nom propre
	if (isset($pars['nom-pr'])) {
		if ($pars['nom-pr'] == true) {
			array_push($conditions, "(l_type='nom-pr' OR l_type='prenom' OR l_type='nom-fam')");
		} else {
			array_push($conditions, "(NOT l_type='nom-pr' AND NOT l_type='prenom' AND NOT l_type='nom-fam')");
		}
	}
	
	$anagrams = get_entries($db, $conditions, $values, $types);
	return $anagrams;
}

function get_string_pars($db) {
	$pars = array();

	$text = array("string", "lang", "type");
	$bool = array("flex", "loc", "gent", "nom-pr");

	for ($i = 0; $i < count($text); $i++) {
		if (isset( $_GET[ $text[$i] ] )) {
			$pars[ $text[$i] ] = mysqli_real_escape_string($db, $_GET[ $text[$i] ]);
		}
	}
	for ($i = 0; $i < count($bool); $i++) {
		if (isset( $_GET[ $bool[$i] ] )) {
			if ( $_GET[ $bool[$i] ] == '1') {
				$pars[ $bool[$i] ] = true;
			} elseif ( $_GET[ $bool[$i] ] == '0') {
				$pars[ $bool[$i] ] = false;
			}
		}
	}
	return $pars;
}

function get_anagrams() {
	$mydbpars = parse_ini_file("/data/project/anagrimes/anagrimes.cnf");
	$dbname = $mydbpars['dbname'];
	$db = openToolDB($dbname);
	mysqli_set_charset($db, 'utf8');
	$pars = get_string_pars($db);
	return get_anagrams_list($db, $pars);
}
?>
