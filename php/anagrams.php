<?php
require_once( 'lib_strings.php' );
require_once( 'lib_requests.php' );

$max = array();

function alphagram($word) {
	# Only a-z letters
	error_log("$word = alphagram ?");
	$simple = non_diacritique_full($word);
	error_log("$word -> $simple");
	# Sort letters in alphabetical order to make an alphagram
	$lettres = preg_split('//', $simple, -1, PREG_SPLIT_NO_EMPTY);
	sort($lettres);
	$alphag = join(' ', $lettres);
	$alphag = str_replace(' ', '', $alphag);
	return $alphag;
}

# Returns a list of anagrams of the string
function get_anagrams_list($db) {
	$pars = get_string_pars($db);
	error_log("[ " . $pars['string'] . " ]");
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
	} else {
		# no word: no anagrams
		return array(
			'status' => 'no_result',
		);
	}
	$request = get_entries($db, $request, $pars);
	if (array_key_exists('list', $request)) {
		$output = $request;
		$output['status'] = 'success';
		return $output;
	} else {
		return array("status" => "error_no_list");
	}
	return $output;
}

function get_anagrams() {
	$db = start_db();
	return get_anagrams_list($db);
}
?>

