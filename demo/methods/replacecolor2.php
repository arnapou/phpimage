<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
$image->replacecolor('white' , 'red', false);
$image->format = 'png';
$image->display();
?>