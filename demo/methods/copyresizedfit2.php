<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$tux = new PHPImage('tux.gif');
$image = new PHPImage('php.gif');
$image->copyresizedfit($tux, 0, 0, 0, 0, 0, '50%', 0, 0);
$image->format = 'png';
$image->display();
?>