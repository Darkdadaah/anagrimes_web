def_url = 'hasard_def.php';
witk_url = '//fr.wiktionary.org/wiki/';
answered = false;
solution = '';
count_ok = 0;
count_skip = 0;
count_bad = 0;
var cached_defs = [];
var retrieving = false;
var waiting = true;
var langues = {
// FRANÇAIS
	'fr' : 'français',
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


function get_url_pars(sParam) {
	var page_url = window.location.search.substring(1);
	var url_pars = page_url.split('&');
	var pars = [];
	for (var i = 0; i < url_pars.length; i++) {
		var par = url_pars[i].split('=');
		pars[ par[0] ] = par[1];
	}
	return pars;
}

function wikt_link(mot, lang) {
	return '<a href="' + witk_url + encodeURIComponent(mot) + '#' + lang + '">' + mot + '</a>';
}

function update_counter(ans) {
	if (answered) {
		return;
	}
	// Good answer?
	if (ans == 'good') {
		count_ok = count_ok + 1;
		$('#count_ok').text(count_ok);
	}
	else if (ans == 'skip') {
		count_skip = count_skip + 1;
		$('#count_skip').text(count_skip);
	}
	else if (ans == 'bad') {
		count_bad = count_bad + 1;
		$('#count_bad').text(count_bad);
	}
}

function nom_lang(l) {
	return langues[l] ? langues[l] : l;
}
function nom_type(t) {
	return types[t] ? types[t] : t;
}

function update_def(lang) {
	$('#defs').empty().hide();
	$('#answer').hide().val('');
	$('#answerbox').hide().empty();
	$('#solution').hide().text('');
	$('#description').hide();
	answered = false;
	solution = '';
	display(lang);
}

function display(lang) {
	$('#langue').text(nom_lang(lang));
	var def_url_pars = def_url + '?lang=' + lang;
	var ndefs = cached_defs.length;
	console.log('Number of defs: ' + ndefs);
	console.log(cached_defs);
	// No definition? Init and display the first
	if (ndefs == 0) {
		$('#waiting').show();
		waiting = true;
		console.log('Init defs');
		def_url_pars = def_url_pars + '&num=2';
		if (!retrieving) {
			retrieving = true;
			$.getJSON(def_url_pars, function(data) {
				get_and_display(data, lang);
			});
		} else {
			console.log('No download: already under way');
		}
	}
	// Retrieve some in the background until we have more than 10
	else if (ndefs < 10) {
		display_defs(cached_defs.shift(), lang);
		if (!retrieving) {
			console.log('Retrieve new defs in the background from ' + def_url_pars);
			retrieving = true;
			$.getJSON(def_url_pars, function(data) {
				get_cached(data, lang);
			});
		} else {
			console.log('No download: already under way');
		}
	}
	// Otherwise: just display the next def
	else {
		console.log('Display next def');
		display_defs(cached_defs.shift(), lang);
	}
}

function clean_cache() {
	var new_cached = [];
	var nc = 0;
	for (var i=0; i < cached_defs.length; i++) {
		if (cached_defs[i].length > 0) {
			new_cached.push(cached_defs[i]);
		} else {
			nc++;
		}
	}
	if (nc > 0) {
		console.log('Cleaned defs: ' + nc);
		cached_defs = new_cached;
	}
}
function get_and_display(data, lang) {
	retrieving = false;
	// Error?
	if (data.length == 0) {
		$('#message').html('Erreur de récupération des définitions. Désolé :(').show();
		return;
	}
	$.merge(cached_defs, data);
	clean_cache();
	var next = cached_defs.shift();
	display_defs(next, lang);
	
	// Get cached in advance!
	var def_url_pars = def_url + '?lang=' + lang + '&num=5';
	$.getJSON(def_url_pars, get_cached);
}
function get_cached(data, lang) {
	retrieving = false;
	if (data.length == 0) {
		console.log('Background cache failed!');
		return;
	}
	$.merge(cached_defs, data);
	clean_cache();
	if (waiting) display(lang);
}

function display_defs(data, lang) {
	if (!data) {
		$('#message').html('Erreur de récupération des définitions. Désolé :(').show();
		console.log('No def ??');
		return;
	}
	if (!data[0]) {
		//$('#message').html('Erreur de récupération des définitions. Désolé :(').show();
		console.log('No def 0 ??');
		console.log(data);
		data[0] = {};
	}
	$('#waiting').hide();
	waiting = false;
	type = data[0]['loc'] == 1 ? nom_type(data[0]['type']) + ' (locution)' : nom_type(data[0]['type']);
	$('#type').text(type);
	solution = data[0]['title'];
	link = wikt_link(solution, lang);
	$text = $('<span />').html('Solution : ').append(link);
	$('#solution').append($text);
	for (i in data) {
		def = data[i].def;
		if (!def) continue;
		def.replace('&lt;', '<');
		def.replace('&gt;', '>');
		/*
		if (def) {
			var mot = new RegExp("\\b" + solution + "\\b", 'gi');
			if (lang == 'fr') {
				def = def.replace(mot, '<abbr title="Mot à deviner utilisé dans la définition.">*****</abbr>');
			} else {
				def = def.replace(mot, '<abbr title="Mot à deviner, le même qu\'en français.">*****</abbr>');
			}
		}
		*/
		$line = $('<li />').html(def);
		$('#defs').append($line);
	}
	$('#description').show();
	$('#defs').show();
	$('#answer').show();
	$('#buttons').show();
	$('#qresults').show();
	$('#checkbutton').removeClass('clicked_button');
	$('#showbutton').removeClass('clicked_button');
}

// Insensitivity to case, apostrophe and space/hyphen
function format_word(word0) {
	var word = word0.replace("’", "'");
	word = word.replace("-", " ");
	word = word.replace(" ", "");
	word = word.toLowerCase();
	return word;
}

function show_answer(data) {
	$('#checkbutton').addClass('clicked_button');
	$('#showbutton').addClass('clicked_button');
	$('#answer').hide();
	$('#solution').show();
	answer = $('#answer').val();
	if (answer == '') {
		update_counter('skip');
	}
	
	else if (format_word(solution) == format_word(answer)) {
		update_counter('good');
	}
	else {
		update_counter('bad');
	}
	answered = true;
}

function check_answer(lang) {
	if (answered) {
		return;
	}
	answer = $('#answer').val();
	console.log("Answer : '" + answer + "'; Solution : '" + solution + "'");
	if (answer.length === 0) {
		$('#answerbox').html('(Pas répondu)');
		$('#answerbox').show();
	}
	else if (format_word(solution) == format_word(answer)) {
		$('#answerbox').html('Bonne réponse&nbsp;!');
		$('#answerbox').show();
		show_answer();
	} else {
		$('#answerbox').html('Mauvaise réponse&nbsp;:&nbsp;').append($(wikt_link(answer, lang)));
		$('#answerbox').show();
	}
}

$(function() {
	var pars = get_url_pars();
	var lang = pars['lang'] ? pars['lang'] : 'fr';
	update_def(lang);
	
	$('#showbutton').click(function(event) {
		show_answer();
		event.preventDefault();
	});
	$('#updatebutton').click(function(event) {
		update_def(lang);
		event.preventDefault();
	});
	$('#checkbutton').click(function(event) {
		check_answer(lang);
		event.preventDefault();
	});
	$('#answer').keypress(function (e) {
		if (e.which == 13) {
			check_answer(lang);
			e.preventDefault();
		}
	});
});

