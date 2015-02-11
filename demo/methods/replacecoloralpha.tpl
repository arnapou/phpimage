<fr>
	Remplace une couleur de l'image par une autre (tient compte de la transparence).
</fr>
<en>
	Replace a color by another (take care of transparency).
</en>
<ol>
	<li>
<fr>
	Syntaxe
<pre>replacecoloralpha($srccolor, $dstcolor, $keeptransparency=false)

$srccolor : couleur source
$dstcolor : couleur destination
$keeptransparency : si la transparence originale est gardée
</pre>
</fr>
<en>
	Syntax
<pre>replacecoloralpha($srccolor, $dstcolor, $keeptransparency=false)

$srccolor : source color
$dstcolor : destination color
$keeptransparency : if original transparency is kept
</pre></en>
	</li><li>
		<fr>On garde la transparence originale.</fr>
		<en>We kept original transparency.</en>
		<img src="_ELEMENT_1.php" class="right" />
		{CODE _ELEMENT_1.php}
	</li><li>
		<fr>On ne garde pas la transparence originale.</fr>
		<en>We don't kept original transparency.</en>
		<img src="_ELEMENT_2.php" class="right" />
		{CODE _ELEMENT_2.php}
	</li>
</ol>