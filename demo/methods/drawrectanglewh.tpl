<fr>
	Dessine un rectangle.<br />
	Si la couleur, l'épaisseur ou le style ne sont pas précisés, cela reprendra automatiquement les dernières valeurs utilisées pour dessiner des lignes.
</fr>
<en>
	Draw a rectangle.<br />
	If line color, thickness or line style are not set, this will take the last values used.
</en>
<ol>
	<li>
<fr>
	Syntaxe
<pre>drawrectanglewh($x1, $y1, $w, $h, $linecolor='', $thickness=0, $linestyle='', $shapestyle='')
 
$x1         : x point 1
$y1         : y point 1
$w          : largeur
$h          : hauteur
$linecolor  : couleur de la ligne
$thickness  : épaisseur
$linestyle  : style de la ligne
$shapestyle : style du rectangle (plusieurs valeurs séparées par des espaces)
              syntaxe des valeurs: '<corner>(<shape>, width[, height])'
              corner : 'all',
		               'tl' (top left),
			    	   'tr' (top right),
					   'bl' (bottom left),
					   'br' (bottom right)
			  shape  : 'biseau', 'biseau2', 'biseau3', 'biseau4',
			           'round', 'round2',
			           'curve', 'curve2', 'curve3', 'curve4', 'curve5', 'curve6',
					   'trait', 'trait2', 'trait3',
					   'empty', 'none'
</pre>
</fr>
<en>
	Syntax
<pre>drawrectanglewh($x1, $y1, $w, $h, $linecolor='', $thickness=0, $linestyle='', $shapestyle='')

$x1         : x point 1
$y1         : y point 1
$w          : width
$h          : height
$linecolor  : line color
$thickness  : thickness
$linestyle  : line style
$shapestyle : shape style (several values separated by spaces)
              values syntax: '<corner>(<shape>, width[, height])'
              corner : 'all',
		               'tl' (top left),
			    	   'tr' (top right),
					   'bl' (bottom left),
					   'br' (bottom right)
			  shape  : 'biseau', 'biseau2', 'biseau3', 'biseau4',
			           'round', 'round2',
			           'curve', 'curve2', 'curve3', 'curve4', 'curve5', 'curve6',
					   'trait', 'trait2', 'trait3',
					   'empty', 'none'
</pre></en>
	</li><li>
<fr>Exemple.</fr>
<en>Example.</en>
<img src="_ELEMENT_1.php" class="right" />
{CODE _ELEMENT_1.php}
	</li><li>
<fr>Styles de ligne.</fr>
<en>Line styles.</en>
<img src="_ELEMENT_2.php" class="right" />
{CODE _ELEMENT_2.php}
	</li><li>
<fr>Styles de forme.</fr>
<en>Shape styles.</en>
<img src="_ELEMENT_3.php" class="right" />
{CODE _ELEMENT_3.php}
	</li>
</ol>
