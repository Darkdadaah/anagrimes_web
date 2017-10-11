<?php
require_once( 'lib_strings.php' );

function start_db() {
    $ts_pw = posix_getpwuid(posix_getuid());
    $ts_mycnf = parse_ini_file($ts_pw['dir'] . "/replica.my.cnf");
    $an_mycnf = parse_ini_file($ts_pw['dir'] . "/anagrimes.cnf");
	$dbname = $ts_mycnf['user'] . '__' . $an_mycnf['dbname'];
    $db = new PDO("mysql:host=".$an_mycnf['host'].";dbname=".$dbname, $ts_mycnf['user'], $ts_mycnf['password']);
    unset($ts_mycnf, $ts_pw, $an_mycnf);
    return $db;
}

function get_string_pars($db) {
	$pars = array();

	$text = array("string", "lang", "type", "genre");
	$bool = array("flex", "loc", "gent", "nom-pr", "noflat", "without_pron", "dev");

	for ($i = 0; $i < count($text); $i++) {
		if (isset( $_GET[ $text[$i] ] )) {
			$pars[ $text[$i] ] = $_GET[ $text[$i] ];
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

    $cond = array();
    $params = array();
	
	# Language? Default: all
	if (array_key_exists('lang', $pars)) {
		array_push($cond, "l_lang = :lang");
        $params[':lang'] = $pars['lang'];
	}
	# Type
	if (array_key_exists('type', $pars)) {
		array_push($cond, "l_type = :type");
        $params[':type'] = $pars['type'];
	}
	# Genre
	if (array_key_exists('genre', $pars)) {
		array_push($cond, "l_genre = :genre");
        $params[':genre'] = $pars['genre'];
	}
	# Flexion
	if (!array_key_exists('flex', $pars) or $pars['flex'] == false) {
		array_push($cond, "l_is_flexion=FALSE");
	}
	# With or without prononciation
	if (array_key_exists('without_pron', $pars) and $pars['without_pron'] == true) {
		array_push($cond, "p_pron IS NULL");
	}
	# Locution
	if (!array_key_exists('loc', $pars) or $pars['loc'] == false) {
		array_push($cond, "l_is_locution=FALSE");
	}
	# Gentilé
	if (!array_key_exists('gent', $pars) or $pars['gent'] == false) {
		array_push($cond, "l_is_gentile=FALSE");
	}
	# Nom propre
	if (!array_key_exists('nom-pr', $pars) or $pars['nom-pr'] == false) {
		if (!array_key_exists('type', $pars) or ($pars['type'] != 'nom-pr' and $pars['type'] != 'prenom' and $pars['type'] != 'nom-fam')) {
			array_push($cond, "(NOT l_type='nom-pr' AND NOT l_type='prenom' AND NOT l_type='nom-fam')");
        }
    }

    $request["conditions"] = $cond;
    $request["params"] = $params;
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
	if (isset($request['order'])) {
		$query .= " ORDER BY " . $request['order'];
	}
    $list = array();

    $params = array();
	
    error_log($query);
	if ($st = $db->prepare($query)) {
		if ($st->execute($request['params'])) {
			# Fetch all rows
			while( $row = $st->fetch(PDO::FETCH_ASSOC) ) {
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
        } else {
            error_log("Can't execute [".$query."]");
        }
    } else {
        error_log("Can't prepare [".$query."]");
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
		array_push($request['conditions'], "$title = :title");
		$request['params'][':title'] = $q;
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

