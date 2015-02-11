<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage(120, 180);
$image->fontfile = __DIR__.'/verdana.ttf';
$image->drawfilledcircle(10, 10, 3, 'blue 50%');
$image->drawfilledcircle(10, 65, 3, 'blue 50%');
$image->drawfilledcircle(10, 120, 3, 'blue 50%');
$image->writetext(10, 10, "hello\nWorld !", 12, 0, 'black', 'left'); // left
$image->writetext(10, 65, "hello\nWorld !", 12, 0, 'black', 'center'); // center
$image->writetext(10, 120, "hello\nWorld !", 12, 0, 'black', 'right'); // right
$image->format = 'png';
$image->display();
?>