<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$tux = new PHPImage('tux.gif');
$image = new PHPImage('php.gif');
$image->copy($tux);
$image->format = 'png';
$image->display();
?>