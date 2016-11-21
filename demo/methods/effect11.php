<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
// syntax : $image->effect('colorize', [Red], [Green], [Blue]);
// RGB default = 0
$image->effect('colorize', 180, 180, 0); // yellow
$image->format = 'png';
$image->display();
?>