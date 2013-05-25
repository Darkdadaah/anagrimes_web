<script language=JavaScript>
en_API = true ;
function tapron(API, SAMPA) {
	if (en_API) {
		document.forms["form_rime"].rime.value += API ;
	} else {
		document.forms["form_rime"].rime.value += SAMPA ;
	}
}

function reinit_rime() {
	document.forms["form_rime"].rime.value = '' ;
}

function retour_arriere_rime() {
	rime = document.forms["form_rime"].rime.value ;
	document.forms["form_rime"].rime.value = rime.slice(0, rime.length-1) ;
}

function entrer_API() {
	en_API = true ;
}

function entrer_SAMPA() {
	en_API = false ;
}

</script>

<table style="margin-left:auto; margin-right: auto">
<tr>
<td>
<table style="margin-left:auto; margin-right: auto">
<tr>
<td><input type="button" value="Tout effacer" name="effacer" onclick="reinit_rime()" class="touche" /></td>
<td><input type="button" value="Effacer dernière lettre" name="effacer_lettre" onclick="retour_arriere_rime()" class="touche" /></td>
<td><input type="button" value="Écrire en API" name="script_API" onclick="entrer_API()" class="touche" /></td>
<td><input type="button" value="Écrire en SAMPA" name="script_SAMPA" onclick="entrer_SAMPA()" class="touche" /></td>
<tr/>
</table>
</td>
</tr>

<tr><th>Voyelles</th></tr>
<tr><td>

<table style="margin-left:auto; margin-right: auto">
<tr>
<td><input type="button" value="a" name="a" onclick="tapron('a', 'a')" class="touche" /></td>
<td><input type="button" value="â" name="A" onclick="tapron('ɑ', 'A')" class="touche" /></td>
<td><input type="button" value="i" name="i" onclick="tapron('i', 'i')" class="touche" /></td>
<td><input type="button" value="ô" name="o" onclick="tapron('o', 'o')" class="touche" /></td>
<td><input type="button" value="o ouvert" name="O" onclick="tapron('ɔ', 'O')" class="touche" /></td>
<td><input type="button" value="u" name="y" onclick="tapron('y', 'y')" class="touche" /></td>
<td><input type="button" value="y, ill" name="j" onclick="tapron('j', 'j')" class="touche" /></td>
<td><input type="button" value="ou" name="u" onclick="tapron('u', 'u')" class="touche" /></td>
<td><input type="button" value="oi" name="wa" onclick="tapron('wa', 'wa')" class="touche" /></td>
</tr>
</table>

<table style="margin-left:auto; margin-right: auto">
<tr>
<td><input type="button" value="é" name="e" onclick="tapron('e', 'e')" class="touche" /></td>
<td><input type="button" value="è" name="E" onclick="tapron('ɛ', 'E')" class="touche" /></td>
<td><input type="button" value="eu (deux)" name="2" onclick="tapron('ø', '2')" class="touche" /></td>
<td><input type="button" value="eu (neuf)" name="9" onclick="tapron('œ', '9')" class="touche" /></td>
<td><input type="button" value="e (le, demi)" name="@" onclick="tapron('ə', '@')" class="touche" /></td>
</tr>
</table>

<table style="margin-left:auto; margin-right: auto">
<tr>
<td><input type="button" value="an" name="u" onclick="tapron('ɑ̃', 'A~')" class="touche" /></td>
<td><input type="button" value="in" name="E~" onclick="tapron('ɛ̃', 'E~')" class="touche" /></td>
<td><input type="button" value="un" name="9~" onclick="tapron('œ̃', '9~')" class="touche" /></td>
<td><input type="button" value="on" name="O~" onclick="tapron('ɔ̃', 'O~')" class="touche" /></td>
</tr>
</table>
</td></tr>

<tr><th>Consonnes</th></tr>
<tr><td>

<table style="margin-left:auto; margin-right: auto">
<tr>
<td><input type="button" value="b" name="b" onclick="tapron('b', 'b')" class="touche" /></td>
<td><input type="button" value="d" name="d" onclick="tapron('d', 'd')" class="touche" /></td>
<td><input type="button" value="f" name="f" onclick="tapron('f', 'f')" class="touche" /></td>
<td><input type="button" value="l" name="l" onclick="tapron('l', 'l')" class="touche" /></td>
<td><input type="button" value="m" name="m" onclick="tapron('m', 'm')" class="touche" /></td>
<td><input type="button" value="n" name="n" onclick="tapron('n', 'n')" class="touche" /></td>
<td><input type="button" value="p" name="p" onclick="tapron('p', 'p')" class="touche" /></td>
<td><input type="button" value="r" name="R" onclick="tapron('ʁ', 'R')" class="touche" /></td>
<td><input type="button" value="s" name="s" onclick="tapron('s', 's')" class="touche" /></td>
<td><input type="button" value="t" name="t" onclick="tapron('t', 't')" class="touche" /></td>
<td><input type="button" value="v" name="v" onclick="tapron('v', 'v')" class="touche" /></td>
<td><input type="button" value="z" name="z" onclick="tapron('z', 'z')" class="touche" /></td>
</tr>
</table>

<table style="margin-left:auto; margin-right: auto">
<tr>
<td><input type="button" value="k (quel, cœur)" name="k" onclick="tapron('k', 'k')" class="touche" /></td>
<td><input type="button" value="ch" name="S" onclick="tapron('ʃ', 'S')" class="touche" /></td>
<td><input type="button" value="gu" name="g" onclick="tapron('ɡ', 'g')" class="touche" /></td>
<td><input type="button" value="ge" name="Z" onclick="tapron('ʒ', 'Z')" class="touche" /></td>
<td><input type="button" value="x (sexe)" name="ks" onclick="tapron('ks', 'ks')" class="touche" /></td>
<td><input type="button" value="x (exauce)" name="gz" onclick="tapron('ɡz', 'gz')" class="touche" /></td>
</tr>
</table>

<table style="margin-left:auto; margin-right: auto">
<tr>
<td><input type="button" value="ui (huit)" name="H" onclick="tapron('ɥ', 'H')" class="touche" /></td>
<td><input type="button" value="gn (agneau)" name="J" onclick="tapron('ɲ', 'J')" class="touche" /></td>
<td><input type="button" value="ng (parking)" name="N" onclick="tapron('ŋ', 'N')" class="touche" /></td>
</tr>
</table>

</td></tr>
</table>
