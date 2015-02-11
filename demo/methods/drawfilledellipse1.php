<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage(120, 80);
$image->drawfilledellipse('50%', '50%', 50, 50, 'blue');
$image->drawfilledellipse('17%', '50%', '45%', '110%', 'red 50%');
$image->drawfilledellipse('83%', '50%', '60%', '30%', 'yellow 20%');
$image->format = 'png';
$image->display();
?>