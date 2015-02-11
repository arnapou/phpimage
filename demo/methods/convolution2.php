<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$matrix = '1 2 1  2 4 2  1 2 1';
$image = new PHPImage('php.gif');
$image->convolution($matrix, 0, true);
$image->format = 'png';
$image->display();
?>