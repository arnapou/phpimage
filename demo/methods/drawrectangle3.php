<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage(120, 280);
$image->drawrectangle(5, 5, 35, 35, 'black', 1, 'solid', 'all(none, 11)');
$image->drawrectangle(45, 5, 75, 35, 'blue', 1, 'solid', 'all(empty, 11)');
$image->drawrectangle(85, 5, 115, 35, 'red', 1, 'solid', 'all(biseau, 11)');

$image->drawrectangle(5, 45, 35, 75, 'black', 1, 'solid', 'all(biseau2, 11)');
$image->drawrectangle(45, 45, 75, 75, 'blue', 1, 'solid', 'all(biseau3, 11)');
$image->drawrectangle(85, 45, 115, 75, 'red', 1, 'solid', 'all(biseau4, 11)');

$image->drawrectangle(5, 85, 35, 115, 'black', 1, 'solid', 'all(round, 11)');
$image->drawrectangle(45, 85, 75, 115, 'blue', 1, 'solid', 'all(round2, 11)');
$image->drawrectangle(85, 85, 115, 115, 'red', 1, 'solid', 'all(curve, 11)');

$image->drawrectangle(5, 125, 35, 155, 'black', 1, 'solid', 'all(curve2, 11)');
$image->drawrectangle(45, 125, 75, 155, 'blue', 1, 'solid', 'all(curve3, 11)');
$image->drawrectangle(85, 125, 115, 155, 'red', 1, 'solid', 'all(curve4, 11)');

$image->drawrectangle(5, 165, 35, 195, 'black', 1, 'solid', 'all(curve5, 11)');
$image->drawrectangle(45, 165, 75, 195, 'blue', 1, 'solid', 'all(curve6, 11)');
$image->drawrectangle(85, 165, 115, 195, 'red', 1, 'solid', 'all(trait, 11)');

$image->drawrectangle(5, 205, 35, 235, 'black', 1, 'solid', 'all(trait2, 11)');
$image->drawrectangle(45, 205, 75, 235, 'blue', 1, 'solid', 'all(trait3, 11)');
$image->drawrectangle(85, 205, 115, 235, 'red', 1, 'solid', 'all(round+curve, 11)');

$image->drawrectangle(5, 245, 35, 275, 'black', 1, 'solid', 'all(curve4+curve5, 11)');
$image->drawrectangle(45, 245, 75, 275, 'blue', 1, 'solid', 'all(biseau+empty, 11)');
$image->drawrectangle(85, 245, 115, 275, 'red', 1, 'solid', 'all(trait2+trait, 11)');
$image->format = 'png';
$image->display();
?>