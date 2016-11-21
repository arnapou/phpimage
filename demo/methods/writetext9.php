<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage(120, 120);
$image->fontfile = __DIR__.'/verdana.ttf';
$image->writetext('50%', '50%', "center\ntext", 10, 0, 'black', 'center', 'center center');
$image->writetext('1%', '1%', "top\nleft", 10, 0, 'black', 'left', 'top left');
$image->writetext('99%', '1%', "top\nright", 10, 0, 'black', 'right', 'top right');
$image->writetext('99%', '99%', "bottom\nright", 10, 0, 'black', 'right', 'bottom right');
$image->writetext('1%', '99%', "bottom\nleft", 10, 0, 'black', 'left', 'bottom left');
$image->format = 'png';
$image->display();
?>