<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$flowers = new PHPImage('tulipes.jpg');
$image = new PHPImage(120, 180);
// if you use a hight transparency and on not too big images
// don't forget to set realcopy to true to have a correct transparency
$image->realcopy = true;
$image->setbackgroundimage($flowers, '70%');
$flowers->destroy();
$image->format = 'png';
$image->display();
?>