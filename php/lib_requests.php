<?php
function get_entries($db, $conditions, $values, $types) {
	# Those are the only fields that we need
	$fields = array("a_title", "l_genre", "l_is_flexion", "l_is_gentile", "l_is_locution", "l_lang", "l_num", "l_sigle", "l_type", "p_num", "p_pron");
	$fields_txt = join(", ", $fields);
	$query = "SELECT $fields_txt FROM entries";
	if (count($conditions) > 0) {
		$query = $query . " WHERE " . join(" AND ", $conditions);
	}
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
	return $list;
}
?>

