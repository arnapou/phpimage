<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage(120, 180);
$image->drawrectangle(5, 5, 115, 35, 'red 20%', 4, 'solid', 'all(round, 6)');
$image->drawrectangle(17, 17, 103, 47, 'blue 20%', 4, 'dot');
$image->drawrectangle(29, 29, 91, 59, 'darkgreen 20%', 4, 'double', 'all(curve, 6)');
$image->drawrectangle(41, 41, 79, 71, 'maroon 20%', 4, 'square');
$shape = 'tl(round, 20) tr(empty, 10, 20) bl(curve, 30, 20) br(biseau, 30)';
$image->drawrectangle(5, 80, 115, 175, 'blue', 1, 'solid', $shape);
$image->drawrectangle(15, 90, 105, 165, 'red', 2, 'dot', $shape);
$image->drawrectangle(25, 100, 95, 155, 'maroon', 3, 'double', $shape);
$shape = 'all(curve+round, 20, 10) tr(curve+round, 10, 20) bl(curve+round, 10, 20)';
$image->drawrectangle(45, 110, 75, 140, 'black', 1, 'solid', $shape);
$image->format = 'png';
$image->display();
?>