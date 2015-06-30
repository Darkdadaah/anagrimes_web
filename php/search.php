<?php
require_once( 'lib_strings.php' );
require_once( 'lib_requests.php' );

# Returns a list of graphies found with the string
function get_list($db) {
	$pars = get_string_pars($db);
	$words = array();
	if (!isset($pars['string']) || $pars['string'] == '') {
		return array('status' => 'empty_request');
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
		# Length 1 to 3: at least 1 char
		# Length 3+: at least 2 chars
		if ($char_count == 0) {
			return array('status' => 'no_char');
		} elseif ($known_char_count == 0) {
			return array('status' => "char_needed");
		} elseif ($char_count > 3 and $known_char_count <= 1) {
			return array('status' => "2_chars_needed");
		} elseif ($char_count <= 3 && $known_char_count <= 1 && preg_match("/\*/", $flat)) {
			return array('status' => "3_chars_no_joker");
		} else {
			# Ok! search
			$request = decide_search('a_title', $pars, $char_count, $known_char_count, $request);
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
	return get_list($db);
}
?>

