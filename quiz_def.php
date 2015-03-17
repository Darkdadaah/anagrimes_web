<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title>Quiz Anagrimes</title>
	<meta http-equiv="Content-Type" content="text/HTML; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" media="screen" href="style/quiz.css" />
	<link rel="shortcut icon" href="favicon.png" />
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/quiz.js"></script>
</head>
<body>
	<div id="boite">
		<div id="message" style="display: none"></div>
		<div id="waiting" style="display: none">Chargement de quelques définitions...</div>
		<p id="description" style="display: none;"><span id="type"></span> en <span id="langue"></span>&nbsp;:</p>
		<ol id="defs"></ol>
		<input type="text" id="answer" placeholder="Tapez votre réponse ici..." style="display:none" />
		<div id="answerbox" style="display: none"></div>
		<div id="solution" style="display: none"></div>
		<div id="buttons" style="display: none">
			<div class="bouton" id="checkbutton">Vérifier ma réponse !</div><br>
			<div class="bouton" id="showbutton">Donner sa langue au chat</div>
			<div class="bouton" id="updatebutton">Question suivante...</div>
		</div>
		<div id="qresults" style="display: none">
			<p>Résultats&nbsp;:</p>
			<ul>
				<li>Bonnes réponses&nbsp;: <span id="count_ok">0</span></li>
				<li>Mauvaises réponses&nbsp;: <span id="count_bad">0</span></li>
				<li>Pas de réponses&nbsp;: <span id="count_skip">0</span></li>
			</ul>
		</div>
	</div>
</body>
</html
>
