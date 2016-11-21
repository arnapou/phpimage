<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
$image->resample('50%');
$image->rotate(30, false);
$image->format = 'png';
$image->display();
?>