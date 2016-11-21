<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
$image->replacecoloralpha('white 100%' , 'red 50%');
$image->format = 'png';
$image->display();
?>