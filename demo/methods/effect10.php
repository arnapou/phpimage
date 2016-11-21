<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
// syntax : $image->effect('smooth', [value]);
// value default = 1
$image->effect('smooth');
$image->format = 'png';
$image->display();
?>