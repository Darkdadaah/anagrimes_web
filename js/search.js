// Anagrams
var api = 'api.php';
var wikturl = '//fr.wiktionary.org/wiki/';
$throbber = $("<img src='style/Loading.gif' id='throbber'>");

var fnames = {
	'title' : 'Mot',
       	'pron' : 'Prononciation',
       	'type' : 'Type de mot',
       	'genre' : 'Genre',
       	'lang' : 'Langue',
};

var searchs = {
	'search' : 'Graphie',
	'anagram' : 'Anagrammes',
};

var langs = {
// FRANÇAIS
	'fr' : 'français',
	'*' : '*',
	'fro' : 'ancien français',
// ALPHABET LATIN
	'af' : 'afrikaans',
	'en' : 'anglais',
	'de' : 'allemand',
	'br' : 'breton',
	'bg' : 'bulgare',	// ALPHABET CYRILLIQUE
	'ca' : 'catalan',
	'zh' : 'chinois',		// AUTRES SYSTÈMES D'ÉCRITURE
	'co' : 'corse',
	'da' : 'danois',
	'es' : 'espagnol',
	'fi' : 'finnois',
	'fy' : 'frison',
	'el' : 'grec',		// ALPHABET GREC
	'grc' : 'grec ancien',	// ALPHABET GREC
	'hbo' : 'hébreu ancien',	// AUTRES SYSTÈMES D'ÉCRITURE
	'hu' : 'hongrois',
	'id' : 'indonésien',
	'is' : 'islandais',
	'it' : 'italien',
	'ja' : 'japonais',		// AUTRES SYSTÈMES D'ÉCRITURE
	'es' : 'espagnol',
	'ko' : 'coréen',		// AUTRES SYSTÈMES D'ÉCRITURE
	'ko-Hani' : 'coréen (hanja)',	// AUTRES SYSTÈMES D'ÉCRITURE
	'la' : 'latin',
	'ln' : 'lingala',
	'mn' : 'mongol',	// ALPHABET CYRILLIQUE
	'nl' : 'néerlandais',
	'no' : 'norvégien',
	'oc' : 'occitan',
	'pap' : 'papiamento',
	'fa' : 'persan',
	'pl' : 'polonais',
	'pt' : 'portugais',
	'ro' : 'roumain',
	'ru' : 'russe',	// ALPHABET CYRILLIQUE
	'sl' : 'slovène',
	'sv' : 'suédois',
	'tl' : 'tagalog',
	'cs' : 'tchèque',
	'tr' : 'turc',
	'vi' : 'vietnamien',
}

var types = {
	'*' : '*',
	'nom' : 'nom commun',
	'nom-pr' : 'nom propre',
	'nom-fam' : 'nom de famille',
	'verb' : 'verbe',
	'adv' : 'adverbe',
	'adj' : 'adjectif',
	'adj-num' : 'adjectif numéral',
	'adv' : 'adverbe',
	'conj' : 'conjonction',
	'interj' : 'interjection',
	'prép' : 'préposition',
	'prov' : 'proverbe',
	'prenom' : 'prénom',
	'verb' : 'verbe',
	'onoma' : 'onomatopée',
	'part' : 'particule',
};

var genres = {
	'*' : '*',
	'm' : 'masculin',
	'f' : 'féminin',
	'mf' : 'masculin et féminin',
	'mf?' : 'masculin et féminin, hésitant',
	'n' : 'neutre',
	'c' : 'commun',
};

function print_form() {
	// Langs select
	selector(langs, 'lang');
	selector(types, 'type');
	selector(searchs, 'search_type');
	selector(genres, 'genre');
}

function selector(list, id) {
	var sel = $("#" + id);
	for (var k in list) {
		var opt = $("<option>");
		opt.val(k);
		opt.html(list[k]);
		sel.append(opt);
	}
}

function anagrams() {
	var pars = get_form_pars();
	var url = api + '?' + jQuery.param(pars);
	console.log(url);
	console.log(pars);
	var xhr = $.getJSON(url, function(data) {
		search_ended();
		if (data.status == 'success') {
			console.log("Success");
			print_table(data.list);
			console.log(data);
		} else {
			console.log("Error...");
			print_error(data);
		}
	}).fail(function(e) {
		search_ended();
		console.log("Failed");
	});
	search_started(xhr);
}

function search_started(xhr) {
	$("#results_num").empty();
	$("#results").empty();
	$("#error").empty();
	$("#search_button")
		.attr("value", "Interrompre")
		.after($throbber);
	$("#search").off().on("submit", function(e) {
		e.preventDefault();
		console.log("ABORT");
		xhr.abort();
		console.log("ABORTED");
		search_ended();
	});
}

function search_ended() {
	$throbber.detach();
	$("#search_button").attr( "value", "Chercher");
	$("#search").off().on("submit", function(e) {
		e.preventDefault();
		anagrams();
	});
}

function define_fields() {
	var fpars = get_form_pars();
	var flist = ['title', 'pron', 'type', 'genre', 'lang'];
	var fields = [];
	for (var i = 0; i < flist.length; i++) {
		if (!fpars[ flist[i] ]) {
			fields.push(flist[i]);
		}
	}
	return fields;
}

function print_error(data) {
	var stat = 'Unknown error';
	if (data.status) {
		stat = data.status;
	}
	$('#error').html(stat);
	console.log(data);
}

function print_table(list) {
	var num = list.length;
	if (num == 0) {
		$("#results_num").html("Pas de résultat");
		return;
	}
	$res = num == 1 ? " résultat" : " résultats";
	$("#results_num").html(num + $res);
	
	list = prepare_list(list);
	var fields = define_fields();
	
	var tab = $("#list");
	if (tab.length == 0) {
		tab = $("<table id='list'>");
		$("#results").append(tab);
	} else {
		tab.empty();
	}
	
	// Header
	var header = $("<tr>");
	for (var i = 0; i < fields.length; i++) {
		var fname = fnames[ fields[i] ];
		var head = $("<th>").html(fname);
		header.append(head);
	}
	tab.append(header);
	// Content
	for (var i=0; i < list.length; i++) {
		var row = $("<tr>");
		for (var j=0; j < fields.length; j++) {
			var f = list[i][ fields[j] ];
			var cell = $("<td>");
			cell.html(f);
			row.append(cell);
		}
		tab.append(row);
	}
}

function prepare_list(list) {
	for (var i = 0; i < list.length; i++) {
		var l = list[i];
		l.title = wikilink(l);
		if (l.p_pron) {
			l.pron = "/" + l.pront.join("/ <small>ou</small> /") + "/";
		} else {
			l.pron = "";
		}
		l.lang = langs[ l.l_lang ];
		if (!l.lang) {
			l.lang = "<span class='ulang'>" + l.l_lang + "</span>";
		}
		l.type = types[ l.l_type ];
		if (!l.type) {
			l.type = "<span class='utype'>" + l.l_type + "</span>";
		}
		l.genre = genres[ l.l_genre ];
		if (!l.genre) {
			l.genre = "<span class='ugenre'>" + l.l_genre + "</span>";
		}
		if (l.l_num > 0) {
			l.type += " " + l.l_num;
		}
		if (l.l_is_flexion) {
			l.type += " (flexion)";
		}
		list[i] = l;
	}
	return list;
}

function wikilink(w) {
	var link = $("<a />");
	link.text(w.a_title);
	var anchor_elts = [w.l_lang, w.l_type];
	if (w.l_num > 0) {
		anchor_elts.push(w.l_num);
	}
	var anchor = "#" + anchor_elts.join('-');
	link.attr("href", wikturl + w.a_title + anchor);
	return link;
}

function get_form_pars() {
	var fpars = {
		'string' : $("input#string").val(),
		'action' : $("#search_type").val(),
		'lang' : $("#lang").val(),
		'type' : $("#type").val(),
		'genre' : $("#genre").val(),
		'flex' : $("#flex").is(':checked'),
		'loc' : $("#loc").is(':checked'),
		'gent' : $("#gent").is(':checked'),
		'noflat' : $("#noflat").is(':checked'),
	};
	fpars = remove_all(fpars);
	return fpars;
}

function remove_all(pars) {
	for (k in pars) {
		if (pars[k] == '*') {
			delete pars[k];
		}
	}
	return pars;
}

$(function() {
	print_form();
	$("#search").on("submit", function(e) {
		e.preventDefault();
		anagrams();
	});
});

