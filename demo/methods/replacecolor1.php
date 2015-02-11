<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage('php.gif');
$image->replacecolor('white' , 'red');
$image->format = 'png';
$image->display();
?>