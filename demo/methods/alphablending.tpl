<fr>
	L'alpha blending est une option permettant la fusion automatique des couleurs et transparence de pixel lors du dessin.<br />
	S'il est d�sactiv�, le dessin se comporte de mani�re tr�s simple en �crasant les couleurs existantes (cela est n�cessaire lors de certaines op�rations de traitement sur les images ou on veut mettre une couleur avec transparence sp�cifique � un endroit sans fusion avec la couleur d�j� pr�sente).<br />
	Cette option est activ�e par d�faut.
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
