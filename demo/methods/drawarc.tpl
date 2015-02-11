<fr>
	Dessine une partie de cercle.<br />
	Si la couleur, l'épaisseur ou le style ne sont pas précisés, cela reprendra automatiquement les dernières valeurs utilisées pour dessiner des lignes.
</fr>
<en>
	Draw a circle part.<br />
	If line color, thickness or line style are not set, this will take the last values used.
</en>
<ol>
	<li>
<fr>
	Syntaxe
<pre>drawellipsearc($cx, $cy, $r, $start, $end, 
	$linecolor='', $thickness=0, $linestyle='', $drawborders=false)
 
$cx          : x centre
$cy          : y centre
$r           : rayon
$start       : angle de début
$end         : angle de fin
$drawborders : dessine la ligne et début et de fin
$linecolor   : couleur de la ligne
$thickness   : épaisseur
$linestyle   : style de la ligne (solid, dot, square, dash, bigdash, double, triple)
</pre>
</fr>
<en>
	Syntax
<pre>drawellipsearc($cx, $cy, $r, $start, $end, 
	$linecolor='', $thickness=0, $linestyle='', $drawborders=false)

$cx          : x center
$cy          : y center
$r           : radius
$start       : start angle
$end         : end angle
$drawborders : draw the start and end lines
$linecolor   : line color
$thickness   : thickness
$linestyle   : line style (solid, dot, square, dash, bigdash, double, triple)
</pre></en>
	</li><li>
<fr>Exemple.</fr>
<en>Example.</en>
<img src="_ELEMENT_1.php" class="right" />
{CODE _ELEMENT_1.php}
	</li>
</ol>
