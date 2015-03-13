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
function get_anagrams_list($db, $word, $lang) {
	# Prepare request
	$conditions = array();
	$values = array();
	$types = "";
	# Word?
	if ($word) {
		array_push($conditions, "a_alphagram=?");
		array_push($values, alphagram($word));
		$types .= "s";
		#array_push($conditions, "a_alphagram=\"aeimr\"");
	} else {
		# no word: no anagrams
		return array();
	}
	# Language? Default: all
	if ($lang) {
		array_push($conditions, "l_lang=?");
		array_push($values, mysqli_real_escape_string($db, $lang));
		$types .= "s";
	}
	
	$anagrams = get_entries($db, $conditions, $values, $types);
	return $anagrams;
}

function get_anagrams($word, $lang) {
	$mydbpars = parse_ini_file("/data/project/anagrimes/anagrimes.cnf");
	$dbname = $mydbpars['dbname'];
	$db = openToolDB($dbname);
	mysqli_set_charset($db, 'utf8');
	return get_anagrams_list($db, $word, $lang);
}
?>
