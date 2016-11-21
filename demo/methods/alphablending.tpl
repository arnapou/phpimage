<fr>
	L'alpha blending est une option permettant la fusion automatique des couleurs et transparence de pixel lors du dessin.<br />
	S'il est désactivé, le dessin se comporte de manière très simple en écrasant les couleurs existantes (cela est nécessaire lors de certaines opérations de traitement sur les images ou on veut mettre une couleur avec transparence spécifique à un endroit sans fusion avec la couleur déjà présente).<br />
	Cette option est activée par défaut.
</fr>
<en>
	Aplhablending is an option which allow an automatic merge of colors and transparency during drawing.<br />
	If it is set to false, drawing doesn't take care of previous color during drawing.<br />
	This option this enabled by default.
</en>
<pre>$image = new PHPImage(200, 100);
$image->alphablending(true);
$image->alphablending(false);
</pre>
