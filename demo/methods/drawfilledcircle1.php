<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage(120, 80);
$image->drawfilledcircle('50%', '50%', 30, 'blue');
$image->drawfilledcircle('17%', '50%', '40%', 'red 50%');
$image->drawfilledcircle('83%', '50%', '20%', 'yellow 20%');
$image->format = 'png';
$image->display();
?>