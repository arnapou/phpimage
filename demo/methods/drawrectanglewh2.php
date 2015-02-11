<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage(120, 180);
$image->drawrectanglewh(5, 5, 110, 170, 'black', 3, 'solid');
$image->drawrectanglewh(12, 12, 96, 156, 'blue', 3, 'dot');
$image->drawrectanglewh(19, 19, 82, 142, 'red', 3, 'square');
$image->drawrectanglewh(26, 26, 68, 128, 'darkgreen', 3, 'dash');
$image->drawrectanglewh(33, 33, 54, 114, 'orange', 3, 'bigdash');
$image->drawrectanglewh(40, 40, 40, 100, 'blue', 3, 'double');
$image->drawrectanglewh(50, 50, 20, 80, 'red', 5, 'triple');
$image->format = 'png';
$image->display();
?>