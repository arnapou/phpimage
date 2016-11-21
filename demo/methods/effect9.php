<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
// syntax : $image->effect('contrast', [value]);
// value default = 10
$image->effect('contrast', 50);
$image->format = 'png';
$image->display();
?>