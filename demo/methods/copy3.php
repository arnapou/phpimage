<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$tux = new PHPImage('tux.gif');
$image = new PHPImage('php.gif');
// transparency: 50%
$image->copy($tux, 50, 20, 0, 0, 0, 0, '50%');
$image->format = 'png';
$image->display();
?>