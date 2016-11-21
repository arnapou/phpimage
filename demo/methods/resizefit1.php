<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
$image->resizefit(75, 75);
$image->format = 'png';
$image->display();
?>