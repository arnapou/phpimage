<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$tux = new PHPImage('tux.gif');
$image = new PHPImage('php.gif');
$image->copy($tux, '50%', '100%', 0, 0, 0, 0, 0, 'center bottom');
$image->format = 'png';
$image->display();
?>