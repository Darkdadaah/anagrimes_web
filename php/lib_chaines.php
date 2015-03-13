<?php
/*******************************
** Fonctions pour les chaines
*******************************/

function utf8_strrev($str) {
	preg_match_all('/./us', $str, $ar);
	return join('',array_reverse($ar[0]));
}

function utf8_strtolower($str) {
	//preg_match_all('/./us', $str, $ar);
	//return join('',$ar[0]);
	return mb_convert_case($str, MB_CASE_LOWER, "UTF-8");
	return $str ;
}

function utf8_length($str) {
	preg_match_all('/./us', $str, $ar);
	return count($ar[0]);
}

function non_diacritique($graphie) {
	# À améliorer
	$graphie = str_replace('é', 'e', $graphie) ;
	$graphie = str_replace('è', 'e', $graphie) ;
	$graphie = str_replace('É', 'e', $graphie) ;
	$graphie = str_replace('È', 'e', $graphie) ;
	$graphie = str_replace('ê', 'e', $graphie) ;
	$graphie = str_replace('Ê', 'e', $graphie) ;
	$graphie = str_replace('à', 'a', $graphie) ;
	$graphie = str_replace('À', 'a', $graphie) ;
	$graphie = str_replace('ç', 'c', $graphie) ;
	$graphie = str_replace('Ç', 'c', $graphie) ;
	$graphie = str_replace('ù', 'u', $graphie) ;
	$graphie = str_replace('Ù', 'u', $graphie) ;
	$graphie = str_replace('ï', 'i', $graphie) ;
	$graphie = str_replace('Ï', 'i', $graphie) ;
	$graphie = str_replace('î', 'i', $graphie) ;
	$graphie = str_replace('Î', 'i', $graphie) ;
	$graphie = str_replace('-', '', $graphie) ;
	$graphie = str_replace('.', '', $graphie) ;
	$graphie = str_replace(' ', '', $graphie) ;
	
 	#$graphie = normalizeUtf8String($graphie) ;
	
	$graphie = strtolower($graphie) ;
	
	return $graphie ;
}

function normalizeUtf8String($s)
{
	set_include_path( dirname('.:$HOME/.pear/pear/lib') . PATH_SEPARATOR . get_include_path() );

    // Normalizer-class missing!
    if (! class_exists("Normalizer", $autoload = false)) {
        echo ("Maintenance") ;
        return $original_string;
    }
   
    // maps German (umlauts) and other European characters onto two characters before just removing diacritics
    $s    = preg_replace( '@\x{00c4}@u'    , "AE",    $s );    // umlaut Ä => AE
    $s    = preg_replace( '@\x{00d6}@u'    , "OE",    $s );    // umlaut Ö => OE
    $s    = preg_replace( '@\x{00dc}@u'    , "UE",    $s );    // umlaut Ü => UE
    $s    = preg_replace( '@\x{00e4}@u'    , "ae",    $s );    // umlaut ä => ae
    $s    = preg_replace( '@\x{00f6}@u'    , "oe",    $s );    // umlaut ö => oe
    $s    = preg_replace( '@\x{00fc}@u'    , "ue",    $s );    // umlaut ü => ue
    $s    = preg_replace( '@\x{00f1}@u'    , "ny",    $s );    // ñ => ny
    $s    = preg_replace( '@\x{00ff}@u'    , "yu",    $s );    // ÿ => yu
   
   
    // maps special characters (characters with diacritics) on their base-character followed by the diacritical mark
        // exmaple:  Ú => U´,  á => a`
    $s    = Normalizer::normalize( $s, Normalizer::FORM_D );
   
    $s    = preg_replace( '@\pM@u'        , "",    $s );    // removes diacritics
   
   
    $s    = preg_replace( '@\x{00df}@u'    , "ss",    $s );    // maps German ß onto ss
    $s    = preg_replace( '@\x{00c6}@u'    , "AE",    $s );    // Æ => AE
    $s    = preg_replace( '@\x{00e6}@u'    , "ae",    $s );    // æ => ae
    $s    = preg_replace( '@\x{0132}@u'    , "IJ",    $s );    // ? => IJ
    $s    = preg_replace( '@\x{0133}@u'    , "ij",    $s );    // ? => ij
    $s    = preg_replace( '@\x{0152}@u'    , "OE",    $s );    // Œ => OE
    $s    = preg_replace( '@\x{0153}@u'    , "oe",    $s );    // œ => oe
   
    $s    = preg_replace( '@\x{00d0}@u'    , "D",    $s );    // Ð => D
    $s    = preg_replace( '@\x{0110}@u'    , "D",    $s );    // Ð => D
    $s    = preg_replace( '@\x{00f0}@u'    , "d",    $s );    // ð => d
    $s    = preg_replace( '@\x{0111}@u'    , "d",    $s );    // d => d
    $s    = preg_replace( '@\x{0126}@u'    , "H",    $s );    // H => H
    $s    = preg_replace( '@\x{0127}@u'    , "h",    $s );    // h => h
    $s    = preg_replace( '@\x{0131}@u'    , "i",    $s );    // i => i
    $s    = preg_replace( '@\x{0138}@u'    , "k",    $s );    // ? => k
    $s    = preg_replace( '@\x{013f}@u'    , "L",    $s );    // ? => L
    $s    = preg_replace( '@\x{0141}@u'    , "L",    $s );    // L => L
    $s    = preg_replace( '@\x{0140}@u'    , "l",    $s );    // ? => l
    $s    = preg_replace( '@\x{0142}@u'    , "l",    $s );    // l => l
    $s    = preg_replace( '@\x{014a}@u'    , "N",    $s );    // ? => N
    $s    = preg_replace( '@\x{0149}@u'    , "n",    $s );    // ? => n
    $s    = preg_replace( '@\x{014b}@u'    , "n",    $s );    // ? => n
    $s    = preg_replace( '@\x{00d8}@u'    , "O",    $s );    // Ø => O
    $s    = preg_replace( '@\x{00f8}@u'    , "o",    $s );    // ø => o
    $s    = preg_replace( '@\x{017f}@u'    , "s",    $s );    // ? => s
    $s    = preg_replace( '@\x{00de}@u'    , "T",    $s );    // Þ => T
    $s    = preg_replace( '@\x{0166}@u'    , "T",    $s );    // T => T
    $s    = preg_replace( '@\x{00fe}@u'    , "t",    $s );    // þ => t
    $s    = preg_replace( '@\x{0167}@u'    , "t",    $s );    // t => t
   
    // remove all non-ASCii characters
    $s    = preg_replace( '@[^\0-\x80]@u'    , "",    $s );
   
    // possible errors in UTF8-regular-expressions
    if (empty($s))
        return $original_string;
    else
        return $s;
}

?>
