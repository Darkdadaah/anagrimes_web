<?php
function start_db() {
	$mydbpars = parse_ini_file("/data/project/anagrimes/anagrimes.cnf");
	$dbname = $mydbpars['dbname'];
	$db = openToolDB($dbname);
	mysqli_set_charset($db, 'utf8');
	return $db;
}

function get_string_pars($db) {
	$pars = array();

	$text = array("string", "lang", "type", "genre");
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

function new_request($db, $pars) {
	$request = array(
		'conditions' => array(),
		'values' => array(),
		'types' => ''
	);
	
	# Language? Default: all
	if ($pars['lang']) {
		array_push($request['conditions'], "l_lang=?");
		array_push($request['values'], mysqli_real_escape_string($db, $pars['lang']));
		$request['types'] .= "s";
	}
	# Type
	if ($pars['type']) {
		array_push($request['conditions'], "l_type=?");
		array_push($request['values'], mysqli_real_escape_string($db, $pars['type']));
		$request['types'] .= "s";
	}
	# Genre
	if ($pars['genre']) {
		array_push($request['conditions'], "l_genre=?");
		array_push($request['values'], mysqli_real_escape_string($db, $pars['genre']));
		$request['types'] .= "s";
	}
	# Flexion
	if (isset($pars['flex'])) {
		if ($pars['flex'] == true) {
			array_push($request['conditions'], "l_is_flexion=TRUE");
		} else {
			array_push($request['conditions'], "l_is_flexion=FALSE");
		}
	}
	# Locution
	if (isset($pars['loc'])) {
		if ($pars['loc'] == true) {
			array_push($request['conditions'], "l_is_locution=TRUE");
		} else {
			array_push($request['conditions'], "l_is_locution=FALSE");
		}
	}
	# Gentilé
	if (isset($pars['gent'])) {
		if ($pars['gent'] == true) {
			array_push($request['conditions'], "l_is_gentile=TRUE");
		} else {
			array_push($request['conditions'], "l_is_gentile=FALSE");
		}
	}
	# Nom propre
	if (isset($pars['nom-pr'])) {
		if ($pars['nom-pr'] == true) {
			array_push($request['conditions'], "(l_type='nom-pr' OR l_type='prenom' OR l_type='nom-fam')");
		} else {
			array_push($request['conditions'], "(NOT l_type='nom-pr' AND NOT l_type='prenom' AND NOT l_type='nom-fam')");
		}
	}
	return $request;	
}

function get_entries($db, $request) {
	# Those are the only fields that we need
	$fields = array("a_title", "l_genre", "l_is_flexion", "l_is_gentile", "l_is_locution", "l_lang", "l_num", "l_sigle", "l_type", "l_lexid", "p_num", "p_pron");
	$fields_txt = join(", ", $fields);
	$query = "SELECT $fields_txt FROM entries";
	if (count($request['conditions']) > 0) {
		$query = $query . " WHERE " . join(" AND ", $request['conditions']);
	}
	$query .= " ORDER BY a_title_flat, a_title, l_lang, l_type, l_num";
	$list = array();
	
	if ($st = $db->prepare($query)) {
		# This part is messy because get_results() is not available and
		# both bind_param and bind_result are a pain to use since they
		# require a list of refs, so we can't just give them arrays
		
		# We need to bind the values for the placeholder
		# For that, bind_params only accepts refs, so we convert the list
		$val_params[] = & $request['types'];
		for($i = 0; $i < count($request['values']); $i++) {
		  $val_params[] = & $request['values'][$i];
		}
		# We also need to bind the values returned, also as refs
		# We bind the data in $row
		$row = array();
		for($i = 0; $i < count($fields); $i++) {
		  $fields_params[] = & $row[$fields[$i]];
		}
		
		# We bind each value to a placeholder
		call_user_func_array(array($st, 'bind_param'), $val_params);
		if ($st->execute()) {
			# We bind each result field in an array
			call_user_func_array(array($st, "bind_result"), $fields_params);
			# Fetch all rows
			while( $res = $st->fetch() ) {
				$line = array();
				# Copy the data from the refs
				foreach ($row as $k => $v) {
					$line[$k] = $v;
				}
				$list[] = $line;
			}
			
			# Additionnal step: the "entries" table returns duplicated rows when
			# several pronunciations are found.
			$list = fuse_prons($list);
		}
	}
	return $list;
}

function fuse_prons($list) {
	#return $list;
	# The words are supposed to be in order
	$list2 = array();
	for ($i = 0; $i < count($list); $i++) {
		$lexid = $list[$i]['l_lexid'];
		if (array_key_exists($lexid, $list2)) {
			$list2[ $lexid ]['pront'][] = $list[$i]['p_pron'];
		} else {
			$list2[ $lexid ] = $list[$i];
			$list2[ $lexid ]['pront'] = array($list[$i]['p_pron']);
		}
	}
	$final_list = array();
	foreach ($list2 as $lex) {
		$final_list[] = $lex;
	}
	return $final_list;
}
?>

