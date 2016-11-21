<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
$image->fill(27, 23, 'red 50%');
$image->fill(60, 23, 'green 50%');
$image->fill(82, 23, 'blue 50%');
$image->format = 'png';
$image->display();
?>