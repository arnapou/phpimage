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
Vous pouvez créer un objet PHPImage par trois méthodes :
<ol>
	<li>
		Cette méthode est la plus simple et crée l'objet mais ne crée pas l'espace de dessin.<br />
		C'est utile si vous voulez changer par exemple la couleur de fond par défaut avant de créer l'espace de dessin.
		<pre>$image = new PHPImage();</pre>
	</li><li>
		Cette méthode crée l'objet à partir d'une image existante.<br />
		Le format de l'image est le même que celui du fichier.
		<pre>$image = new PHPImage($mon_fichier_image_existant);</pre>
	</li><li>
		Enfin la dernière méthode permet de créer l'objet et son espace de dessin en une seule ligne.
		<pre>$image = new PHPImage($largeur, $hauteur);</pre>
	</li>
</ol>
</fr>