<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
// syntax : $image->effect('selective_blur', [number]);
// number = 1 (default), 2 or 3
$image->effect('selective_blur');
$image->format = 'png';
$image->display();
?>