<en>
You can create a new PHPImge object by 3 methods :
<ol>
	<li>
		This method is the simpliest but it doesn't create the drawing space.<br/>
		It can be usefull if you want to changes the default background color before creating the drawing space.
		<pre>$image = new PHPImage();</pre>
	</li><li>
		This method create the image from an existing image file.<br />
		The image format is the same as the file.
		<pre>$image = new PHPImage($my_existing_file);</pre>
	</li><li>
		And this last method allow to create the object and its drawing space in one line (avoid calling create method after).
		<pre>$image = new PHPImage($width, $height);</pre>
	</li>
</ol>
</en>


<fr>
Vous pouvez cr�er un objet PHPImage par trois m�thodes :
<ol>
	<li>
		Cette m�thode est la plus simple et cr�e l'objet mais ne cr�e pas l'espace de dessin.<br />
		C'est utile si vous voulez changer par exemple la couleur de fond par d�faut avant de cr�er l'espace de dessin.
		<pre>$image = new PHPImage();</pre>
	</li><li>
		Cette m�thode cr�e l'objet � partir d'une image existante.<br />
		Le format de l'image est le m�me que celui du fichier.
		<pre>$image = new PHPImage($mon_fichier_image_existant);</pre>
	</li><li>
		Enfin la derni�re m�thode permet de cr�er l'objet et son espace de dessin en une seule ligne.
		<pre>$image = new PHPImage($largeur, $hauteur);</pre>
	</li>
</ol>
</fr>