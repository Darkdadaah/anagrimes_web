<?php
require_once( 'lib_chaines.php' );
require_once( 'lib_requests.php' );

$max = array();

function count_known($word) {
	$word = preg_replace('/[\?*]/', '', $word);
	return strlen($word);
}

function decide_search($pars, $nchars, $nkchars, $request) {
	$str = $pars['string'];
	$catch = array();
	# Exact same length? Exact search
	if ($nchars == $nkchars) {
		array_push($request['conditions'], "a_title_flat=?");
		array_push($request['values'], non_diacritique($str));
		$request['types'] .= "s";
	# Include one incomplete part at the end?
	} elseif (preg_match("/^([^\*]+)[*\?]+$/", $str, $catch)) {
		$q = non_diacritique($catch[1])."%";
		array_push($request['conditions'], 'a_title_flat LIKE "'.$q.'"');
	# Include one incomplete part at the start?
	} elseif (preg_match("/^[*\?]+([^\*]+)+$/", $str, $catch)) {
		$q = "%".non_diacritique(utf8_strrev($catch[1]));
		array_push($request['conditions'], 'a_title_flat_r LIKE "'.$q.'"');
	} else {
		return array();
	}
	return $request;
}

# Returns a list of graphies found with the string
function get_graphies_list($db) {
	$pars = get_string_pars($db);
	$words = array();
	if (!isset($pars['string']) || $pars['string'] == '') {
		return $words;
	}
	
	# Prepare request from parameters
	$request = new_request($db, $pars);
	
	# Word?
	if ($pars['string']) {
		# Prepare search!
		$flat = non_diacritique($pars['string']);
		$char_count = strlen($flat);
		$known_char_count = count_known($flat);

		# Enough chars to search?
		# Length 1 or 2: at least 1 char
		# Length 3+ : at least 2 chars
		if ($char_count == 0) {
			return array('status' => 'no_char');
		} elseif ($char_count > 2 and $known_char_count <= 1) {
			return array('status' => "2_chars_needed ($flat, $char_count, $known_char_count");
		} else {
			# Ok! search
			$request = decide_search($pars, $char_count, $known_char_count, $request);
			if (count($request) == 0) {
				return array('status' => 'unsupported_search_type');
			}
		}
	} else {
		# no word
		return array('status' => 'no_string');
	}
	$graphies = get_entries($db, $request);
	$output = array(
		'status' => 'success',
		'list' => $graphies,
	);
	return $output;
}

function get_graphies() {
	$db = start_db();
	return get_graphies_list($db);
}
?>

