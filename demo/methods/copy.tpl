<fr>
	Copie une autre image sur l'image courante.<br />
	Chaque paramètre peut recevoir un entier ou un pourcentage.
</fr>
<en>
	Copy an other image on the current image.<br />
	Each parameter can receive an integer or a percentage.
</en>
<ol>
	<li>
<fr>
	Syntaxe
<pre>copy(& $src, $dstx=0, $dsty=0, $srcx=0, $srcy=0, 
     $srcw=0, $srch=0, $alpha=-1, $dstpos='', $srcpos='')

$src    : image source
$dstx   : destination - valeur X
$dsty   : destination - valeur Y
$srcx   : source - valeur X
$srcy   : source - valeur Y
$srcw   : largeur source
$srch   : hauteur source
$alpha  : transparence à appliquer
$dstpos : position destination
$srcpos : position source
</pre>
</fr>
<en>
	Syntax
<pre>copy(& $src, $dstx=0, $dsty=0, $srcx=0, $srcy=0, 
     $srcw=0, $srch=0, $alpha=-1, $dstpos='', $srcpos='')

$src    : source image
$dstx   : destination - X value
$dsty   : destination - Y value
$srcx   : source - X value
$srcy   : source - Y value
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
<fr>Copie de l'image entière vers une position donnée.</fr>
<en>Copy of the entire image to a precise position.</en>
<img src="_ELEMENT_2.php" class="right" />
{CODE _ELEMENT_2.php}
	</li><li>
<fr>Application d'une transparence.</fr>
<en>Transparency application.</en>
<img src="_ELEMENT_3.php" class="right" />
{CODE _ELEMENT_3.php}
	</li><li>
<fr>Copie de la tête de Tux.</fr>
<en>Copy of Tux's head.</en>
<img src="_ELEMENT_4.php" class="right" />
{CODE _ELEMENT_4.php}
	</li><li>
<fr>Copie au centre en bas.</fr>
<en>Copy to center bottom.</en>
<img src="_ELEMENT_5.php" class="right" />
{CODE _ELEMENT_5.php}
	</li>
</ol>
