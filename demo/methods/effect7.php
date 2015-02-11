<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
// syntax : $image->effect('sharpen', [number]);
// number = 1 (default), 2 or 3
$image->effect('sharpen');
$image->format = 'png';
$image->display();
?>