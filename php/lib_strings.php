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
	return $graphie;
}

function non_diacritique_full($graphie) {
	$graphie = Normalizer::normalize($graphie, Normalizer::FORM_D);
	$graphie = preg_replace('/\pM|\pP/u', "", $graphie);
	return $graphie;
}
?>
