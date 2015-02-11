<fr>
	Dessine une image.
</fr>
<en>
	Draw an image.
</en>
<ol>
	<li>
<fr>
	Syntaxe
<pre>drawimage(& $src, $dstx, $dsty, $srcx=0, $srcy=0, $srcw=0, $srch=0, 
          $alpha=-1, $dstpos='', $srcpos='', $shapestyle='', 
		  $watermark=false, $creux=true)

$src        : image source
$dstx       : destination - valeur X
$dsty       : destination - valeur Y
$srcx       : source - valeur X
$srcy       : source - valeur Y
$srcw       : largeur source
$srch       : hauteur source
$alpha      : transparence à appliquer
$dstpos     : position destination (voir la méthode copy pour des exemples)
$srcpos     : position source (voir la méthode copy pour des exemples)
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
$watermark  : applique un effet watermark à l'image
$creux      : l'effet watermark en creux ou relief
</pre>
</fr>
<en>
	Syntax
<pre>drawimage(& $src, $dstx, $dsty, $srcx=0, $srcy=0, $srcw=0, $srch=0, 
          $alpha=-1, $dstpos='', $srcpos='', $shapestyle='', 
          $watermark=false, $creux=true)

$src        : source image
$dstx       : destination - X value
$dsty       : destination - Y value
$srcx       : source - X value
$srcy       : source - Y value
$srcw       : source width
$srch       : source height
$alpha      : transparency to apply
$dstpos     : destination position (look at copy method for examples)
$srcpos     : source position (look at copy method for examples)
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
$watermark  : whether to apply watermark effect on the image
$creux      : whether to invert watermark effect or not
</pre></en>
	</li><li>
<fr>Exemple.</fr>
<en>Example.</en>
<img src="_ELEMENT_1.php" class="right" />
{CODE _ELEMENT_1.php}
	</li>
</ol>
