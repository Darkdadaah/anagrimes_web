<?php
require_once( 'lib_strings.php' );
require_once( 'lib_requests.php' );

$max = array();

function alphagram($word) {
	# Only a-z letters
	$simple = non_diacritique_full($word);
	# Sort letters in alphabetical order to make an alphagram
	$lettres = preg_split('//', $simple, -1, PREG_SPLIT_NO_EMPTY);
	sort($lettres);
	$alphag = join(' ', $lettres);
	$alphag = str_replace(' ', '', $alphag);
	#error_log("Alphagram of '$word' = '$alphag'");
	return $alphag;
}

# Returns a list of anagrams of the string
function get_anagrams_list($db) {
	$pars = get_string_pars($db);
	$anagrams = array();
	if (!isset($pars['string']) || $pars['string'] == '') {
		return array(
			'status' => 'empty_request',
		);
	}
	error_log("Anagram requested for '" . $pars['string'] . "'");
	
	# Prepare request from parameters
	$request = new_request($db, $pars);
	
	# Word?
	if ($pars['string']) {
		array_push($request['conditions'], "a_alphagram = :alphagram");
		$request['params'][':alphagram'] = alphagram($pars['string']);
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

