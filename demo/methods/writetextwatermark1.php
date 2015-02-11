<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$flowers = new PHPImage('tulipes.jpg');
$image = new PHPImage(120, 120);
$image->fontfile = __DIR__.'/verdana.ttf';
$image->setbackgroundimage($flowers);
$image->writetextwatermark('0%', '100%', "arnapou.net", 10, 0, false, '', 'bottom left');
$image->writetextwatermark('100%', '100%', "arnapou.net", 10, 90, false, '', 'bottom left');
$image->writetextwatermark('100%', '0%', "arnapou.net", 10, 180, false, '', 'bottom left');
$image->writetextwatermark('0%', '0%', "arnapou.net", 10, -90, false, '', 'bottom left');
$image->writetextwatermark('50%', '50%', "arnapou.net", 10, 0, true, 'center', 'center');
$image->format = 'png';
$image->display();
?>