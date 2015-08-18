var api = '//tools.wmflabs.org/anagrimes/api.php';
var devapi = '//tools.wmflabs.org/anagrimes/dev/api.php';
var wikturl = '//fr.wiktionary.org/wiki/';
$throbber = $("<img src='//upload.wikimedia.org/wikipedia/commons/d/d2/Spinning_wheel_throbber.gif' id='throbber'>");

var fnames = {
	'title' : 'Mot',
       	'pron' : 'Prononciation',
       	'type' : 'Type de mot',
       	'genre' : 'Genre',
       	'lang' : 'Langue',
};

var searchs = {
	'search' : 'Graphie',
	'pron' : 'Prononciation',
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
};

var types = {
	'*' : '*',
	'nom' : 'nom commun',
	'nom-pr' : 'nom propre',
	'nom-fam' : 'nom de famille',
	'verb' : 'verbe',
	'adv' : 'adverbe',
	'adj' : 'adjectif',
	'adj-num' : 'adjectif numéral',
	'conj' : 'conjonction',
	'interj' : 'interjection',
	'prép' : 'préposition',
	'prov' : 'proverbe',
	'prenom' : 'prénom',
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

var rows = {
	'string' : {
		'description': 'Recherche',
		'type': 'text',
	},
	'search_type' : {
		'description': 'Méthode',
		'list': searchs
	},
	'lang' : {
		'description': 'Langue',
		'list': langs
	},
	'advanced' : {
		'description': 'Options...',
		'type': 'advanced_button',
	},
	'type' : {
		'description': 'Type',
		'list': types,
		'advanced': true,
	},
	'genre' : {
		'description': 'Genre',
		'list': genres,
		'advanced': true,
	},
	'flex' : {
		'description': 'Inclure flexions',
		'advanced': true,
	},
	'loc' : {
		'description': 'Inclure locutions',
		'advanced': true,
	},
	'nom-pr' : {
		'description': 'Inclure noms propres',
		'advanced': true,
	},
	'gent' : {
		'description': 'Inclure gentilés',
		'advanced': true,
	},
	'noflat' : {
		'description': 'Accents/majuscules',
		'advanced': true,
	},
	'dev' : {
		'description': 'Version dev',
		'advanced': true,
	},
};

function print_form() {
	// Create the form
	var form = $("<form action='' id='anag_form' />");
	var tab = $("<div class='table' />");
	form.append(tab);
	
	for (var name in rows) {
		tab.append( add_row(name, rows[name]) );
	}
	var submit = $("<input type='submit' value='Chercher' id='search_button' />");
	form.append(submit)
		.append( $("<div id='error'>") );
	
	$("#anag_search")
		.append(form)
		.append( $("<div id='results'>") );
	$("#results")
		.append( $("<div id='results_num'>") )
		.append( $("<div id='list'>") );
}

function add_row(name, pars) {
	var description = pars.description;
	
	var row = $("<div class='row' />");
	var title;
	var input_cell;
	
	// Create advanced menu button
	if (pars.type == 'advanced_button') {
		var goback = $('<span class="arrow">←</span>');
		var gotow = $('<span class="arrow">→</span>');
		goback.hide();
		row.attr('id', 'advanced_button');
		title = $("<div class='cell' />")
			.append( description + "&nbsp;" );
		input_cell = $("<div class='cell' />")
			.append( gotow )
			.append( goback );
		row.on("click", function() {
			$(".advanced").toggle();
			goback.toggle();
			gotow.toggle();
		});
	// Create inputs
	} else {
		title = $("<div class='cell' />")
			.append( description + '&nbsp;:' );
		var input;
		
		// Text inputs
		if (pars.type && pars.type == 'text') {
			input = $("<input type='text' name='" + name + "' id='" + name + "' />");
		}
		// List selection inputs
		else if (pars.list) {
			input = $("<select name='" + name + "' id='" + name + "'></select>");
			selector(input, pars.list);
		// Checkbox inputs (default if no type)
		} else {
			input = $("<input type='checkbox' name='" + name + "' id='" + name + "' />");
		}
		input_cell = $("<div class='cell' />")
			.append( input );
	}
	// Advanced options are hidden by default
	if (pars.advanced) {
		row.addClass('advanced');
		row.hide();
	}
	row.append(title).append(input_cell);
	return row;
}

function selector(sel, list) {
	for (var k in list) {
		var opt = $("<option>");
		opt.val(k);
		opt.html(list[k]);
		sel.append(opt);
	}
}

function anagrimes() {
	var pars = get_form_pars();
	var url = pars.dev ? devapi : api;
	console.log(url + '?' + $.param(pars));
	console.log(pars);
	if (!pars.string || pars.string === '') {
		print_error({'status' : "Empty search"});
	} else {
		var xhr = $.getJSON(url, pars, function(data) {
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
}

function search_started(xhr) {
	$("#results_num").empty();
	$("#list").empty();
	$("#error").empty();
	$("#search_button")
		.attr("value", "Interrompre")
		.after($throbber);
	$("#anag_form").off().on("submit", function(e) {
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
	$("#anag_form").off().on("submit", function(e) {
		$("#anag_form input").blur();
		e.preventDefault();
		anagrimes();
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
	if (num === 0) {
		$("#results_num").html("Pas de résultat");
		return;
	}
	$res = num == 1 ? " résultat" : " résultats";
	$("#results_num").html(num + $res);
	
	list = prepare_list(list);
	var fields = define_fields();
	
	var tab = $("#list");
	if (tab.length === 0) {
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
			l.pron = "\\" + l.pront.join("\\ <small>ou</small> \\") + "\\";
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
		'nom-pr' : $("#nom-pr").is(':checked'),
		'noflat' : $("#noflat").is(':checked'),
		'dev' : $("#dev").is(':checked'),
	};
	fpars = remove_all(fpars);
	return fpars;
}

function remove_all(pars) {
	for (var k in pars) {
		if (pars[k] == '*') {
			delete pars[k];
		}
	}
	return pars;
}

$(function() {
	if ( $("#anag_search") ) {
		print_form();
		$("#anag_form").on("submit", function(e) {
			$("#anag_form input").blur();
			e.preventDefault();
			anagrimes();
		});
	}
});

