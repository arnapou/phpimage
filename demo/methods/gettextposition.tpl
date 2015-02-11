<fr>
	Récupère la position haut gauche (x, y) où dessiner le texte.<br />
	Cette méthode est surtout utile si vous utilisez un angle différent de 0 et avec des positions différentes de 'top left'.
</fr>
<en>
	Get the top left position (x, y) where to draw the text.<br />
	This method is usefull if you use an angle different from 0 and with positions different from 'top left'.
</en>
<ol>
	<li>
<fr>
	Syntaxe
	<pre>gettextposition($cx, $cy, $text, $fontsize=12, $angle=0, 
        $position='top left', $linespacing='', $font='')

$cx          : position X
$cy          : position Y
$text        : texte (retours à la ligne autorisés)
$fontsize    : taille du texte
$angle       : angle du texte
$position    : position des coordonnées d'ancrage du texte
$linespacing : interligne
$font        : police de caractère
</pre>
</fr>
<en>
	Syntax
	<pre>gettextposition($cx, $cy, $text, $fontsize=12, $angle=0, 
        $position='top left', $linespacing='', $font='')

$cx          : X position
$cy          : Y position
$text        : text (multiline allowed)
$fontsize    : font size
$angle       : text angle
$position    : where is the text anchor
$linespacing : line spacing
$font        : font file name
</pre></en>
	</li><li>
<fr>Exemple.</fr>
<en>Example.</en>
<pre>$image = new PHPImage(200, 100);

list($x, $y) = $image->gettextposition(100, 50, "Hello\nWorld", 12, 60, 'bottom center', '', 'arial.ttf');

// ... or ...

$dims = $image->gettextposition(100, 50, "Hello\nWorld", 12, 60, 'bottom center', '', 'arial.ttf');
$x = $dims[0];
$y = $dims[1];
</pre>
	</li>
</ol>
