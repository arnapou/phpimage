<fr>
	Retourne les dimensions du texte � dessiner sous forme de tableau � deux �l�ments.
</fr>
<en>
	Return text dimensions as 2 elements array.
</en>
<ol>
	<li>
<fr>
	Syntaxe
	<pre>gettextbox($text, $fontsize=0, $angle=0, $linespacing='', $font='')

$text        : texte (retours � la ligne autoris�s)
$fontsize    : taille de police
$angle       : angle
$linespacing : interligne
$font        : police de caract�re
</pre>
</fr>
<en>
	Syntax
	<pre>gettextbox($text, $fontsize=0, $angle=0, $linespacing='', $font='')

$text        : text (multiline allowed)
$fontsize    : font size
$angle       : text angle
$linespacing : line spacing
$font        : font file name
</pre></en>
	</li><li>
<fr>Exemple.</fr>
<en>Example.</en>
<pre>$image = new PHPImage(200, 100);

list($width, $height) = $image->gettextbox("Hello\nWorld", 12, 0, '', 'arial.ttf');

// ... or ...

$dims = $image->gettextbox("Hello\nWorld", 12, 0, '', 'arial.ttf');
$width = $dims[0];
$height = $dims[1];
</pre>
	</li>
</ol>
