<fr>
	Dessine un rectangle.<br />
	Si la couleur de remplissage n'est pas précisée pas précisés, cela reprendra automatiquement la dernière valeur utilisée.
</fr>
<en>
	Draw a rectangle.<br />
	If the fill color is not set, it will take the last value used.
</en>
<ol>
	<li>
<fr>
	Syntaxe
<pre>drawfilledrectanglewh($x1, $y1, $w, $h, $fillcolor='', $shapestyle='')
 
$x1         : x point 1
$y1         : y point 1
$w          : hauteur
$h          : largeur
$fillcolor  : couleur de la ligne
$shapestyle : style du rectangle (plusieurs valeurs séparées par des espaces)
              syntaxe des valeurs: '<corner>(<shape>[%int], width[, height])'
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
			  int    : espace de hachurage (valeur entière)
</pre>
</fr>
<en>
	Syntax
<pre>drawfilledrectanglewh($x1, $y1, $w, $h, $fillcolor='', $shapestyle='')

$x1         : x point 1
$y1         : y point 1
$w          : width
$h          : height
$fillcolor  : line color
$shapestyle : shape style (several values separated by spaces)
              values syntax: '<corner>(<shape>[%int], width[, height])'
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
			  int    : hatching space (integer)
</pre></en>
	</li><li>
<fr>Exemple.</fr>
<en>Example.</en>
<img src="_ELEMENT_1.php" class="right" />
{CODE _ELEMENT_1.php}
	</li><li>
<fr>Styles de forme.</fr>
<en>Shape styles.</en>
<img src="_ELEMENT_2.php" class="right" />
{CODE _ELEMENT_2.php}
	</li>
</ol>
