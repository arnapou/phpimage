<fr>
	Applique une matrice de convolution sur l'image.<br />
	La matrice peut être 3x3, 5x5 ou 7x7.<br />
	Attention, cela peut être très consommateur de CPU.
</fr>
<en>
	Apply a convolution matrix on the image.<br />
	The matrix can be 3x3, 5x5 or 7x7.<br />
	Be carefull, this can use a lot of CPU.
</en>
<ol>
	<li>
<fr>
	Syntaxe
	<pre>$image->convolution($matrix, $offset=0, $usealpha=false);

$matrix   : tableau de valeurs ou bien valeurs séparées par des espaces
$offset   : offset pour toutes les couleurs
$usealpha : mettre à true pour utiliser la matrice sur la transparence
</pre>
</fr>
<en>
	Syntax
	<pre>$image->convolution($matrix, $offset=0, $usealpha=false);

$matrix   : values array or string of space separated values 
$offset   : offset for all colors
$usealpha : set to true to use the matrix on transparency
</pre></en>
	</li><li>
<fr>Simple matrice de flou gaussien.</fr>
<en>Simple gaussian blur matrix.</en>
<img src="_ELEMENT_1.php" class="right" />
{CODE _ELEMENT_1.php}
	</li><li>
<fr>Pareil que précédemment mais avec flou sur la transparence.</fr>
<en>Same as previous but with blur on transparency.</en>
<img src="_ELEMENT_2.php" class="right" />
{CODE _ELEMENT_2.php}
	</li><li>
<fr>Pareil que précédemment mais avec offset décalé et matrice passée sous forme de tableau.</fr>
<en>Same as previous but with offset set.</en>
<img src="_ELEMENT_3.php" class="right" />
{CODE _ELEMENT_3.php}
	</li>
</ol>
