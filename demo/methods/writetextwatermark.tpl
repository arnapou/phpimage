<fr>
	Dessine du texte en mode filigrane.
</fr>
<en>
	Write text as waterark.
</en>
<ol>
	<li>
<fr>
	Syntaxe
	<pre>writetextwatermark($cx, $cy, $text, $fontsize=0, $angle=0, 
         $creux=true, $style='left', $position='top left', 
		 $linespacing='', $font='', $alpha=70);

$cx          : position X
$cy          : position Y
$text        : texte (retours à la ligne autorisés)
$fontsize    : taille du texte
$angle       : angle du texte
$creux       : texte en relief ou en creux
$style       : style de texte (ombré, souligné etc ...)
$position    : position des coordonnées d'ancrage du texte
$linespacing : interligne
$font        : police de caractère ou n° de police (1 à 5)
$alpha       : transparence
</pre>
</fr>
<en>
	Syntax
	<pre>writetextwatermark($cx, $cy, $text, $fontsize=0, $angle=0, 
         $creux=true, $style='left', $position='top left', 
		 $linespacing='', $font='', $alpha=70);

$cx          : X position
$cy          : Y position
$text        : text (multiline allowed)
$fontsize    : font size
$angle       : text angle
$creux       : whether to invert watermark or not
$style       : text style (underline, shadow ...)
$position    : where is the text anchor
$linespacing : line spacing
$font        : font file name or font number (1 to 5)
$alpha       : transparency
</pre></en>
	</li><li>
<fr>Exemple.</fr>
<en>Example.</en>
<img src="_ELEMENT_1.php" class="right" />
{CODE _ELEMENT_1.php}
	</li>
</ol>
