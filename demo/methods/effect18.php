<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
// syntax : $image->effect('threshold', [threshold]);
// threshold default = 127
$image->effect('threshold');
$image->format = 'png';
$image->display();
?>