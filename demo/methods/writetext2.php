<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage(120, 120);
$image->fontfile = __DIR__.'/verdana.ttf';
$image->drawfilledcircle('50%', '50%', 3, 'blue 50%');
$image->writetext('50%', '50%', "hello\nWorld !", 12, 30, 'red'); 
$image->format = 'png';
$image->display();
?>