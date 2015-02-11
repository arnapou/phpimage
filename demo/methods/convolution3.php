<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$matrix = array(array(1, 2, 1), array(2, 4, 2), array(1, 2, 1));
$image = new PHPImage('php.gif');
$image->convolution($matrix, 80, true);
$image->format = 'png';
$image->display();
?>