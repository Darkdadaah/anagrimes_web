<?php
require_once( 'lib_chaines.php' );
require_once( 'lib_requests.php' );

$max = array();

function count_known($word) {
	return strlen(known($word));
}
function known($word) {
	return preg_replace('/[\?\*]+/', '', $word);
}
function clean_string($word) {
	return str_replace('.', '?', $word);
}
function decide_search($pars, $nchars, $nkchars, $request) {
	$str = $pars['string'];
	$title = 'a_title_flat';
	$flat = true;
	if (array_key_exists('noflat', $pars) and $pars['noflat'] == true) {
		$title = 'a_title';
		$flat = false;
	}
	$catch = array();
	$request['word'] .= $str;
	# Exact same length? Exact search
	if ($nchars == $nkchars) {
		$q = $flat ? non_diacritique($str) : $str;
		array_push($request['conditions'], "$title=?");
		array_push($request['values'], $q);
		$request['types'] .= "s";
	} else {
		if (preg_match("/\?/", $str)) {
			array_push($request['conditions'], "length(a_title_flat)=$nchars");
		}
		$search_ok = false;
		# Include one incomplete part at the end?
		if (preg_match("/^([^*\?]+)[*\?]+/", $str, $catch)) {
			$q = $flat ? non_diacritique($catch[1]) : $catch[1];
			$q .= "%";
			array_push($request['conditions'], "$title LIKE ?");
			array_push($request['values'], $q);
			$request['types'] .= 's';
			$search_ok = true;
		}
		# Include one incomplete part at the start?
		if (preg_match("/[*\?]+([^*\?]+)+$/", $str, $catch)) {
			$q = $flat ? non_diacritique(utf8_strrev($catch[1])) : utf8_strrev($catch[1]);
			$q .= "%";
			array_push($request['conditions'], $title . "_r LIKE ?");
			array_push($request['values'], $q);
			$request['types'] .= 's';
			$search_ok = true;
		}
		# Any letter between stars/interrogation mark?
		if (preg_match("/[*\?][^*\?]+[*\?]/", $str)) {
			$search_ok = false;
			$q = $flat ? non_diacritique($str) : $str;
			$q = str_replace('*', '.+', $q);
			$q = str_replace('?', '.', $q);
			$q = "^$q$";
			array_push($request['conditions'], $title . " REGEXP ?");
			array_push($request['values'], $q);
			$request['types'] .= 's';
			$search_ok = true;
		} else {
			if (!$search_ok) {
				return array();

			}
		}
		if (!$search_ok) {
			return array();
		}
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
		$pars['string'] = clean_string($pars['string']);
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
			return array('status' => "2_chars_needed ($flat, ".$pars['string'] . ', '. known($pars['string']).", $char_count, $known_char_count)");
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
	$request = get_entries($db, $request, $pars);
	if (array_key_exists('list', $request)) {
		$output = $request;
		$output['status'] = 'success';
		return $output;
	} else {
		return array("status" => "error_no_list");
	}
}

function get_graphies() {
	$db = start_db();
	return get_graphies_list($db);
}
?>

