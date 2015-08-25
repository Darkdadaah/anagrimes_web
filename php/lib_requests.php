<?php
require_once( 'lib_strings.php' );

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
	$bool = array("flex", "loc", "gent", "nom-pr", "noflat", "dev");

	for ($i = 0; $i < count($text); $i++) {
		if (isset( $_GET[ $text[$i] ] )) {
			$pars[ $text[$i] ] = mysqli_real_escape_string($db, $_GET[ $text[$i] ]);
		}
	}
	for ($i = 0; $i < count($bool); $i++) {
		if (isset( $_GET[ $bool[$i] ] )) {
			if ( $_GET[ $bool[$i] ] == '1' or $_GET[ $bool[$i] ] == 'true') {
				$pars[ $bool[$i] ] = true;
			} elseif ( $_GET[ $bool[$i] ] == '0' or $_GET[ $bool[$i] ] == 'false') {
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
		'types' => '',
		'pars' => $pars
	);
	
	# Language? Default: all
	if (array_key_exists('lang', $pars)) {
		array_push($request['conditions'], "l_lang=?");
		array_push($request['values'], mysqli_real_escape_string($db, $pars['lang']));
		$request['types'] .= "s";
	}
	# Type
	if (array_key_exists('type', $pars)) {
		array_push($request['conditions'], "l_type=?");
		array_push($request['values'], mysqli_real_escape_string($db, $pars['type']));
		$request['types'] .= "s";
	}
	# Genre
	if (array_key_exists('genre', $pars)) {
		array_push($request['conditions'], "l_genre=?");
		array_push($request['values'], mysqli_real_escape_string($db, $pars['genre']));
		$request['types'] .= "s";
	}
	# Flexion
	if (!array_key_exists('flex', $pars) or $pars['flex'] == false) {
		array_push($request['conditions'], "l_is_flexion=FALSE");
	}
	# Locution
	if (!array_key_exists('loc', $pars) or $pars['loc'] == false) {
		array_push($request['conditions'], "l_is_locution=FALSE");
	}
	# Gentilé
	if (!array_key_exists('gent', $pars) or $pars['gent'] == false) {
		array_push($request['conditions'], "l_is_gentile=FALSE");
	}
	# Nom propre
	if (!array_key_exists('nom-pr', $pars) or $pars['nom-pr'] == false) {
		if ($pars['type'] != 'nom-pr' && $pars['type'] != 'prenom' && $pars['type'] != 'nom-fam') {
			array_push($request['conditions'], "(NOT l_type='nom-pr' AND NOT l_type='prenom' AND NOT l_type='nom-fam')");
		}
	}
	return $request;	
}

function get_entries($db, $request, $pars) {
	# Those are the only fields that we need
	$fields = array("a_title", "l_genre", "l_is_flexion", "l_is_gentile", "l_is_locution", "l_lang", "l_num", "l_sigle", "l_type", "l_lexid", "p_num", "p_pron", "length(a_title_flat)");
	$fields_txt = join(", ", $fields);
	$table = 'entries';
	if (isset($pars) and isset($pars['lang']) and $pars['lang'] == 'fr' and !$pars['flex'] and !$pars['loc'] and !$pars['gent']) {
		$table = 'entries_fr';
	}
	$query = "SELECT $fields_txt FROM $table";
	if (count($request['conditions']) > 0) {
		$query = $query . " WHERE " . join(" AND ", $request['conditions']);
	}
	if ($request['order']) {
		$query .= " ORDER BY " . $request['order'];
	}
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
	$request['list'] = $list;
	$request['query'] = $query;
	return $request;
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

function decide_search($column, $pars, $nchars, $nkchars, $request) {
	$str = $pars['string'];
	$title = $column . '_flat';
	$flat = true;
	$orders = array(
		'pron' => 'p_pron_flat, p_pron, l_lang, a_title, l_type, l_num',
		'pron_r' => 'p_pron_flat_r, p_pron, l_lang, a_title, l_type, l_num',
		'article' => 'a_title_flat, a_title, l_lang, l_type, l_num'
	);
	if (array_key_exists('noflat', $pars) and $pars['noflat'] == true) {
		$title = $column;
		$flat = false;
	}
	// Prons: only use "flat" column, without removing diacritics
	if ($column == 'p_pron') {
		$title = $column . '_flat';
		$flat = false;
	}
	$catch = array();
	$request['word'] = $str;
	$rhyme = false;
	# Exact same length? Exact search
	if ($nchars == $nkchars) {
		$q = $flat ? non_diacritique($str) : $str;
		error_log("$str -> $q");
		array_push($request['conditions'], "$title=?");
		array_push($request['values'], $q);
		$request['types'] .= "s";
	} else {
		if (preg_match("/\?/", $str) && !preg_match("/\*/", $str)) {
			array_push($request['conditions'], "length($title)=$nchars");
		}
		$search_ok = false;
		# Include one incomplete part at the start?
		if (preg_match("/[*\?]+([^*\?]+)+$/", $str, $catch)) {
			$q = $flat ? utf8_strrev(non_diacritique($catch[1])) : utf8_strrev($catch[1]);
			$q .= "%";
			array_push($request['conditions'], $title . "_r LIKE ?");
			array_push($request['values'], $q);
			$request['types'] .= 's';
			$search_ok = true;
			$rhyme = true;
		}
		# Include one incomplete part at the end?
		if (preg_match("/^([^*\?]+)[*\?]+/", $str, $catch)) {
			$q = $flat ? non_diacritique($catch[1]) : $catch[1];
			$q .= "%";
			array_push($request['conditions'], "$title LIKE ?");
			array_push($request['values'], $q);
			$request['types'] .= 's';
			$search_ok = true;
			$rhyme = false;
		}
		# Otherwise: regexp
		if (preg_match("/[*\?]/", $str)) {
			$search_ok = false;
			$q = $flat ? non_diacritique($str) : $str;
			$q = str_replace('*', '.*', $q);
			$q = str_replace('?', '.', $q);
			$q = "^$q$";
			array_push($request['conditions'], $title . " REGEXP ?");
			array_push($request['values'], $q);
			$request['types'] .= 's';
			$search_ok = true;
		}
		if (!$search_ok) {
			return array();
		}
	}
	# Choose order
	if ($column == 'p_pron') {
		$request['rhyme'] = $rhyme;
		if ($rhyme) {
			$request['order'] = $orders['pron_r'];
		} else {
			$request['order'] = $orders['pron'];
		}
	} else {
		$request['order'] = $orders['article'];
	}
	return $request;
}
?>

