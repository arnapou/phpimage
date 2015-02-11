<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$tux = new PHPImage('tux.gif');
$image = new PHPImage('php.gif');
$image->copyresampled($tux);
$image->format = 'png';
$image->display();
?>