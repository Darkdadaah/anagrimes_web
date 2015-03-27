<?php
require_once( 'lib_chaines.php' );
require_once( 'lib_requests.php' );

$max = array();

function alphagram($word) {
	# Only a-z letters
	$simple = non_diacritique($word);
	# Sort letters in alphabetical order to make an alphagram
	$lettres = preg_split('//', $simple, -1, PREG_SPLIT_NO_EMPTY);
	sort($lettres);
	$alphag = join(' ', $lettres);
	$alphag = ereg_replace(' ', '', $alphag);
	return $alphag;
}

# Returns a list of anagrams of the string
function get_anagrams_list($db) {
	$pars = get_string_pars($db);
	$anagrams = array();
	if (!isset($pars['string']) || $pars['string'] == '') {
		return array(
			'status' => 'no_parameters',
		);
	}
	
	# Prepare request from parameters
	$request = new_request($db, $pars);
	
	# Word?
	if ($pars['string']) {
		array_push($request['conditions'], "a_alphagram=?");
		array_push($request['values'], alphagram($pars['string']));
		$request['types'] .= "s";
		#array_push($request['conditions'], "a_alphagram=\"aeimr\"");
	} else {
		# no word: no anagrams
		return array(
			'status' => 'no_result',
		);
	}
	$anagrams = get_entries($db, $request);
	$output = array(
		'status' => 'success',
		'list' => $anagrams,
	);
	return $output;
}

function get_anagrams() {
	$db = start_db();
	return get_anagrams_list($db);
}
?>

