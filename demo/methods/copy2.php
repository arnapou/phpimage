<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$tux = new PHPImage('tux.gif');
$image = new PHPImage('php.gif');
$image->copy($tux, 50, 20);
$image->format = 'png';
$image->display();
?>