<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
$image->effect('edgedetect');
$image->format = 'png';
$image->display();
?>