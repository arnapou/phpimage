<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
// syntax : $image->effect('points', [threshold]);
// threshold default = 127
$image->effect('points');
$image->format = 'png';
$image->display();
?>