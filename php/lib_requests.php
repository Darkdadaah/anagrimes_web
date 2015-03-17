<?php
function get_entries($db, $conditions, $values, $types) {
	# Those are the only fields that we need
	$fields = array("a_title", "l_genre", "l_is_flexion", "l_is_gentile", "l_is_locution", "l_lang", "l_num", "l_sigle", "l_type", "l_lexid", "p_num", "p_pron");
	$fields_txt = join(", ", $fields);
	$query = "SELECT $fields_txt FROM entries";
	if (count($conditions) > 0) {
		$query = $query . " WHERE " . join(" AND ", $conditions);
	}
	$query .= " ORDER BY a_title, l_lang, l_type, l_num";
	$list = array();
	
	if ($st = $db->prepare($query)) {
		# This part is messy because get_results() is not available and
		# both bind_param and bind_result are a pain to use since they
		# require a list of refs, so we can't just give them arrays
		
		# We need to bind the values for the placeholder
		# For that, bind_params only accepts refs, so we convert the list
		$val_params[] = & $types;
		for($i = 0; $i < count($values); $i++) {
		  $val_params[] = & $values[$i];
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

