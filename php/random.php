<?php
include_once( 'shared/common.php' );

$max = array();

# Retrieve an id for a random word
function get_random_id($db, $langue) {
	global $max;

	# Français par défaut
	if (!$langue) {
		$langue = 'fr';
	}
	
	if (!array_key_exists($langue, $max)) {
		$query = "SELECT lg_num_min FROM langs WHERE lg_lang='$langue'";
		
		if (!$result = $db->query($query)) {
			return 'There was an error running the query: ' . $query;
		}
		$res = 0;
		while ($row = $result->fetch_assoc()){
			$res = $row['lg_num_min'];
		}
		$max[$langue] = $res;
	}
	
	return rand(1, $max[$langue]);
}

# Returns a random word (mot), url-friendly (raw) and anchor (ancre)
function get_random_word($db, $pars) {
	$langue = $pars["lang"];
	$num = $pars["num"];
	
	$words = array();
	for ($i = 0; $i < $num; $i++) {
		# Get a random id
		$rand = get_random_id($db, $langue);
		
		# Get the random word
		$query = "SELECT a_title FROM entries WHERE l_lang='$langue' AND l_rand=$rand";
		if (!$result = $db->query($query)) {
			return 'There was an error running the query: ' . $query;
		}
		$mot = '';
		while ($row = $result->fetch_assoc()){
			$mot = $row['a_title'];
		}
		$m['mot'] = $mot;
		$m['raw'] = rawurlencode($mot);
		$m['ancre'] = ($m['raw'] and $langue != 'fr') ? '#'.$langue : '';
		$words[] = $m;
	}
	
	return $words;
}

function get_rand_pars($db) {
	$pars = array();
	# Language
	if (isset($_GET["lang"])) {
		$pars["lang"] = mysqli_real_escape_string($db, $_GET["lang"]);
	} else {
		$pars["lang"] = "fr";
	}
	# Number of articles
	if (isset($_GET["num"])) {
		$num = $_GET["num"];
		if (!isset($num) || !is_numeric($num) || $num < 0) {
			$num = 1;
		}
		if ($num > 10) {
			$num = 10;
		}
		$pars["num"] = $num;
	} else {
		$pars["num"] = 1;
	}
	return $pars;
}

function get_random() {
	$mydbpars = parse_ini_file("/data/project/anagrimes/anagrimes.cnf");
	$dbname = $mydbpars['dbname'];
	$db = openToolDB($dbname);
	mysqli_set_charset($db, 'utf8');
	$pars = get_rand_pars($db);
	return get_random_word($db, $pars);
	return "word";
}
?>
