<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$php = new PHPImage('php.gif');
$image = new PHPImage();
$image->bgcolor = 'yellow 80%';
$image->create($php->width, $php->height);
$image->copy($php, 0,0, 0,0, 0,0, '70%');
$php->destroy();
$image->format = 'png';
$image->display();
?>