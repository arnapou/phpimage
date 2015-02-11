<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
$image->setalpha('50%');
$image->format = 'png';
$image->display();
?>