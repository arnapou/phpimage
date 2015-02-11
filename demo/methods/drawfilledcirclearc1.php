<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage(120, 80);
$image->drawfilledcirclearc('50%', '50%', '25%', -45, 180, 'blue');
$image->drawfilledcirclearc('17%', '50%', '25%', -60, 90, 'red 50%');
$image->drawfilledcirclearc('83%', '50%', '25%', 80, 10, 'yellow 20%');
$image->format = 'png';
$image->display();
?>