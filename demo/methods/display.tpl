<fr>
	Retourne l'image au navigateur. <br />
	Cette méthode peut aussi enregistrer l'image en même temps qu'elle la renvoie au navigateur.<br />
	Elle sait aussi gérer un cache et renvoie les headers nécessaires.
</fr>
<en>
	Return the image to the browser.<br />
	This method can also save the image in the same time.<br />
	It manages also a cache if needed.
</en>
<ol>
	<li>
<fr>Affichage simple.</fr>
<en>Simple display</en>
<pre>$image = new PHPImage();
$image->create(200, 100);
$image->display();
</pre>
	</li><li>
<fr>Affichage et enregistrement d'une image PNG.</fr>
<en>Display and save the image to PNG format.</en>
<pre>$image = new PHPImage();
$image->create(200, 100);
$image->display('image.png');
</pre>
	</li><li>
<fr>
Affichage et enregistrement d'une image JPG avec qualité précisée.
<pre>$image = new PHPImage();
$image->create(200, 100);
$image->display('image.jpg', 85); // qualité: 0 à 100 (optionel).
</pre>
</fr>
<en>
Display and save the image to JPG format with a precised quality.
<pre>$image = new PHPImage();
$image->create(200, 100);
$image->display('image.jpg', 85); // quality: 0 to 100 (optional).
</pre>
</en>
	</li><li>
<fr>
	Affichage avec cache automatique.<br />
	A noter qu'il ne faut pas créer d'espace de dessin avant d'utiliser la méthode display dans la condition du if.<br />
	En fait si l'image existe déjà et qu'elle est encore valide (moins de 10 minutes), le premier display affiche l'image et retourne true.
</fr>
<en>
	Display with automatic cache.<br />
	Note that the drawing space shouldn't be created before using display method in the if condition.<br />
	If the image already exists and is valid (less than 10 minutes), then it displays the image and return true.
</en>
<pre>$image = new PHPImage();
$image->cachetime = 600; // 10 minutes
if (!$image->display('image.jpg')) {
	$image->create(200, 100);
	$image->display('image.jpg');
}
</pre>
	</li>
</ol>
