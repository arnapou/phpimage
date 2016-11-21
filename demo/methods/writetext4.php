<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage(120, 190);
$image->fontfile = __DIR__.'/verdana.ttf';
$image->drawfilledcircle(10, 10, 3, 'blue 50%');
$image->drawfilledcircle(10, 45, 3, 'blue 50%');
$image->drawfilledcircle(10, 80, 3, 'blue 50%');
$image->drawfilledcircle(10, 115, 3, 'blue 50%');
$image->drawfilledcircle(10, 150, 3, 'blue 50%');
$text = "hello\nWorld !";
$image->writetext(10, 10, $text, 12, 0, 'black', 'underline'); // simple
$image->writetext(10, 45, $text, 12, 0, 'black', 'underline(red, 20%)'); // red 50%
$image->writetext(10, 80, $text, 12, 0, 'black', 'underline(3px)'); // thickness
$image->writetext(10, 115, $text, 12, 0, 'black', 'underline(blue, 3px, dot)'); // dot line
$image->writetext(10, 150, $text, 12, 0, 'black', 'underline(darkgreen, 3px, double)'); // double line
$image->format = 'png';
$image->display();
?>