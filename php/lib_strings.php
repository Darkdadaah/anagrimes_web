<?php
/*******************************
** Fonctions pour les chaines
*******************************/

function utf8_strrev($str) {
	preg_match_all('/./us', $str, $ar);
	return join('',array_reverse($ar[0]));
}

function utf8_strtolower($str) {
	return mb_convert_case($str, MB_CASE_LOWER, "UTF-8");
	return $str ;
}

function utf8_length($str) {
	preg_match_all('/./us', $str, $ar);
	return count($ar[0]);
}

function non_diacritique($graphie) {
	$graphie = Normalizer::normalize($graphie, Normalizer::FORM_D);
	$graphie = preg_replace('/\pM|\-/u', "", $graphie);
	$graphie = utf8_strtolower($graphie);
	$graphie = preg_replace("/'|’/u", "", $graphie);
	return $graphie;
}

function non_diacritique_full($graphie) {
	$graphie = Normalizer::normalize($graphie, Normalizer::FORM_D);
	$graphie = preg_replace('/\pM|\pP/u', "", $graphie);
	$graphie = utf8_strtolower($graphie);
	return $graphie;
}

function known($word) {
	return preg_replace('/[\?\*]+/', '', $word);

}
function count_known($word) {
	return strlen(known($word));

}
function clean_string($word) {
	$clean_word = str_replace("'", "’", $word);
	return str_replace('.', '?', $clean_word);
}

function clean_pron($word) {
	$word = clean_string($word);
	$word = str_replace(array(' ', '_', '‿'), '', $word);
	// X-SAMPA -> API (except for r, special)
	$from = array('é', 'è', 'ê', 'ô', 'â', 'g', 'ʁ', 'R', 'Z', 'A', 'O', 'E', '@', 'S', 'H', '~', 'N', 'J', '2', '9', 'T', 'D', 'V', 'I', 'U', '{');
	$to =   array('e', 'ɛ', 'ɛ', 'o', 'ɑ', 'ɡ', 'r', 'r', 'ʒ', 'ɑ', 'ɔ', 'ɛ', 'ə', 'ʃ', 'ɥ', '̃' , 'ŋ', 'ɲ', 'ø', 'œ', 'θ', 'ð', 'ʌ', 'ɪ', 'ʊ', 'æ');	// '
	return str_replace($from, $to, $word);
}
?>
