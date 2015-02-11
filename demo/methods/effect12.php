<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
// syntax : $image->effect('watermark', [invert], [blur]);
// invert default = false
// blur default = false
$image->effect('watermark');
$image->format = 'png';
$image->display();
?>