<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
// syntax : $image->effect('mosaic', [size_x], [size_y]);
// size_x default = 2
// size_y default = 2
$image->effect('mosaic', 4);
$image->format = 'png';
$image->display();
?>