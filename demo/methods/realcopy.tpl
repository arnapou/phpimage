<fr>
	C'est une fonction de copie qui fusionne corectement les valeurs alpha des couleurs.<br />
	En effet, avec des transparences �lev�es, la simple copie ne rend pas les couleurs vraies.<br />
	Attention, cette m�thode est consommatrice de CPU, donc �viter les grosse images, ou alors g�rez un cache.<br/>
	Dans tous les cas, si vous n'usez pas de transparence, n'utilisez pas la copie r�elle !<br />
	Utilisez la propri�t� <a href="?page=properties#realcopy">realcopy</a> pour que les copies (copy, copyresize...) utilisent cette m�thode.
</fr>
<en>
	This is copy method which merge correctly the alpha value of colors.<br />
	With high transparencies, simple GD copy doesn't show the right color.<br />
	Be carefull, this method load your CPU.<br />
	In all case, if you don't use transparency, don't use real copy !<br />
	Use <a href="?page=properties#realcopy">realcopy</a> property to make copies (copy, copyresize...) use this method.
</en>
<ol>
	<li>
<fr>
	Syntaxe
<pre>realcopy(& $src, $dstx=0, $dsty=0, $srcx=0, $srcy=0, 
     $srcw=0, $srch=0, $alpha=-1, $dstpos='', $srcpos='')

$src    : image source
$dstx   : destination - valeur X
$dsty   : destination - valeur Y
$srcx   : source - valeur X
$srcy   : source - valeur Y
$srcw   : largeur source
$srch   : hauteur source
$alpha  : transparence � appliquer
$dstpos : position destination
$srcpos : position source
</pre>
</fr>
<en>
	Syntax
<pre>realcopy(& $src, $dstx=0, $dsty=0, $srcx=0, $srcy=0, 
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
<fr>Copie r�elle avec un fond jaune transparent.</fr>
<en>Real copy with a transparent yellow background</en>
<img src="_ELEMENT_1.php" class="right" />
{CODE _ELEMENT_1.php}
	</li><li>
<fr>
	Copie GD avec un fond jaune transparent.<br/>
	Observez la diff�rence... les couleurs ne sont pas correctement fusionn�es !
</fr>
<en>
	GD copy with a transparent yellow background<br/>
	Look at the difference... colors are not correctly merged !
</en>
<img src="_ELEMENT_2.php" class="right" />
{CODE _ELEMENT_2.php}
	</li>
</ol>
