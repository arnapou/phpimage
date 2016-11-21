<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
// syntax : $image->effect('brightness', [value]);
// value default = 10
$image->effect('brightness', 50);
$image->format = 'png';
$image->display();
?>