<en>
	This destroys the drawing space and free memory.<br />
	Always remember to destroy images when you won't need them : it is good for the server.
</en>
<fr>
	Cela détruit l'espace de dessin et libère la mémoire.<br />
	Pensez toujours à détruire vos images quand vous en avez plus besoin : cela est profitable au serveur.
</fr>
<pre>$image = new PHPImage(200, 100);
// draw something 
// ...
// and then
$image->destroy();</pre>
</en>
