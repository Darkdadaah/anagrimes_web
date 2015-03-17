// Anagrams
$throbber = $("<img src='style/Loading.gif' id='throbber'>");

function anagrams(s) {
	search_started();
	var api = 'api.php';
	var pars = {
		'action': 'anagram',
	};
	pars['string'] = s;
	var url = api + '?' + jQuery.param(pars);
	$.getJSON(url, function(data) {
		search_ended();
		console.log("Done");
		print_table(data.list);
	}).fail(function(e) {
		search_ended();
		console.log("Failed");
	});
}

function search_started() {
	$("#results")
		.empty()
		.after($throbber);
}

function search_ended() {
	$throbber.detach();
}

function print_table(list) {
	var fields = ['a_title', 'l_lang', 'l_type', 'l_num'];
	var tab = $("<table id='list'>");
	for (var i=0; i < list.length; i++) {
		var row = $("<tr>");
		for (var j=0; j < fields.length; j++) {
			var f = list[i][ fields[j] ];
			var cell = $("<td>");
			cell.text(f);
			row.append(cell);
		}
		tab.append(row);
	}
	$("#results").append(tab);
}

$(function() {
	$("#search").on("submit", function(e) {
		e.preventDefault();
		var string = $("input#string").val();
		anagrams(string);
	});
});

