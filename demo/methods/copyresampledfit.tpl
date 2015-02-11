<fr>
	Copie une autre image sur l'image courante en la redimensionnant proprement et en gardant les proportions largeur/hauteur.
</fr>
<en>
	Copy an other image on the current image and resize it correctly keeping the ratio width/height.
</en>
<ol>
	<li>
<fr>
	Syntaxe
<pre>copyresampledfit(& $src, $dstx=0, $dsty=0, $srcx=0, $srcy=0, $srcw=0, 
     $srch=0, $srcw=0, $srch=0, $alpha=-1, $dstpos='', $srcpos='')

$src    : image source
$dstx   : destination - valeur X
$dsty   : destination - valeur Y
$srcx   : source - valeur X
$srcy   : source - valeur Y
$dstw   : largeur destination
$dsth   : hauteur destination
$srcw   : largeur source
$srch   : hauteur source
$alpha  : transparence à appliquer
$dstpos : position destination
$srcpos : position source
</pre>
</fr>
<en>
	Syntax
<pre>copyresampledfit(& $src, $dstx=0, $dsty=0, $srcx=0, $srcy=0, $srcw=0, 
     $srch=0, $srcw=0, $srch=0, $alpha=-1, $dstpos='', $srcpos='')

$src    : source image
$dstx   : destination - X value
$dsty   : destination - Y value
$srcx   : source - X value
$srcy   : source - Y value
$dstw   : destination width
$dsth   : destination height
$srcw   : source width
$srch   : source height
$alpha  : transparency to apply
$dstpos : destination position
$srcpos : source position
</pre></en>
	</li><li>
<fr>Simpe copie sans aucun paramètres.</fr>
<en>Simple copy without any parameters.</en>
<img src="_ELEMENT_1.php" class="right" />
{CODE _ELEMENT_1.php}
	</li><li>
<fr>Hauteur de destination à 50%.</fr>
<en>Destination height at 50%.</en>
<img src="_ELEMENT_2.php" class="right" />
{CODE _ELEMENT_2.php}
	</li>
</ol>
