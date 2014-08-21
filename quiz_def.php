<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title>Anagrimes - Recherche avancée dans le Wiktionnaire</title>
	<meta http-equiv="Content-Type" content="text/HTML; charset=utf-8" />
	<link rel="stylesheet" type="text/css" media="screen" href="style/default.css" />
	<link rel="stylesheet" type="text/css" media="print" href="style/print.css" />
	<link rel="shortcut icon" href="favicon.png" />
	<script src="jquery-1.11.1.min.js"></script>
	<script>
	def_url = 'hasard_def.php';
	witk_url = '//fr.wiktionary.org/wiki/';
	answered = false;
	solution = '';
	count_ok = 0;
	count_skip = 0;
	count_bad = 0;
	var cached_defs = [];
	
	function wikt_link(mot) {
		return '<a href="' + witk_url + encodeURIComponent(mot) + '">' + mot + '</a>';
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
	
	function update_def() {
		$('#defs').empty();
		$('#answer').hide();
		$('#answer').val('');
		$('#answerbox').hide();
		$('#answerbox').empty();
		$('#solution').hide();
		$('#solution').text('');
		$('#type').text('');
		answered = false;
		solution = '';
		console.log(def_url);
		display();
	}
	
	function display() {
		var ndefs = cached_defs.length;
		console.log(['Number of defs:', ndefs]);
		// No definition? Init and display the first
		if (ndefs == 0) {
			console.log('Init defs');
			$.getJSON(def_url, get_and_display);
		}
		// Retrieve some in the background until we have more than 10
		else if (ndefs < 10) {
			console.log('Retrieve new defs in the background');
			display_defs(cached_defs.shift());
			$.getJSON(def_url, get_cached);
		}
		// Otherwise: just display the next def
		else 
			console.log('Display next def');{
			display_defs(cached_defs.shift());
		}
	}
	
	function get_and_display(data) {
		cached_defs = cached_defs.concat(data);
		console.log(['New defs:', cached_defs.length]);
		var next = cached_defs.shift();
		display_defs(next);
	}
	function get_cached(data) {
		cached_defs = cached_defs.concat(data);
	}
	
	function display_defs(data) {
		if (!data) {
			return;
		}
		type = data[0]['loc'] == 1 ? 'loc-' + data[0]['type'] : data[0]['type'];
		$('#type').text('(' + type + ')');
		solution = data[0]['title'];
		link = wikt_link(solution);
		$text = $('<span />').html('Solution : ').append(link);
		$('#solution').append($text);
		for (i in data) {
			def = data[i];
			$line = $('<li />').html(def['def']);
			$('#defs').append($line);
		}
		$('#answer').show();
		$('#checkbutton').removeClass('clicked_button');
		$('#showbutton').removeClass('clicked_button');
	}
	
	function show_answer(data) {
		$('#checkbutton').addClass('clicked_button');
		$('#showbutton').addClass('clicked_button');
		$('#answer').hide();
		$('#solution').show("fast");
		answer = $('#answer').val();
		if (answer == '') {
			update_counter('skip');
		}
		else if (solution.replace("’", "'") == answer.replace("’", "'")) {
			update_counter('good');
		}
		else {
			update_counter('bad');
		}
		answered = true;
	}
	
	function check_answer(data) {
		if (answered) {
			return;
		}
		answer = $('#answer').val();
		console.log($('#answer').val());
		console.log("Answer : '" + answer + "'");
		console.log("Solution : '" + solution + "'");
		if (answer == '') {
			$('#answerbox').text('(Pas répondu)');
			$('#answerbox').show();
		}
		if (solution.replace("’", "'") == answer.replace("’", "'")) {
			$('#answerbox').text('Bonne réponse !');
			$('#answerbox').show();
			show_answer();
		} else {
			$('#answerbox').text('Mauvaise réponse : ').append($(wikt_link(answer)));
			$('#answerbox').show();
		}
	}
	
	$(function() {
		console.log( "ready!" );
		update_def();
		
		$('#showbutton').click(function(event) {
			show_answer();
			event.preventDefault();
		});
		$('#updatebutton').click(function(event) {
			update_def();
			event.preventDefault();
		});
		$('#checkbutton').click(function(event) {
			check_answer();
			event.preventDefault();
		});
		$('#answer').keypress(function (e) {
			if (e.which == 13) {
				check_answer();
				e.preventDefault();
			}
		});
	});
	</script>
</head>
<body>
	<div class="page">
		<h1>Définition au hasard <span id="type"></span></h1>
		<?php require ("part_avertissement.php"); ?>
		<ul id="defs"></ul>
		<input type="text" id="answer" style="display:none" />
		<div id="answerbox" style="display: none"></div>
		<div id="solution" style="display: none"></div>
		<div id="buttons">
			<div class="bouton" id="checkbutton">Vérifier la réponse...</div>
			<div class="bouton" id="showbutton">Montrer le mot !</div>
			<div class="bouton" id="updatebutton">Autre définition</div>
		</div>
		<div id="qresults">
		<p>Résultats&nbsp;:</p>
		<ul>
			<li>Bonnes réponses&nbsp;: <span id="count_ok">0</span></li>
			<li>Mauvaises réponses&nbsp;: <span id="count_bad">0</span></li>
			<li>Pas de réponses&nbsp;: <span id="count_skip">0</span></li>
		</ul>
		</div>
	</div>
	<?
	require ("part_entete.php");
	require ("part_piedpage.php");
	?>
</body>
</html
>
