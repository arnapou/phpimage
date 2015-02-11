<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage(120, 120);
// font size has no effect when no TTF font is set (here 12)
$image->writetext(5, 5, "hello World !", 12, 0, 'black', '', '', '', 1);
$image->writetext(5, 26, "hello World !", 12, 0, 'red', '', '', '', 2);
$image->writetext(5, 49, "hello World !", 12, 0, 'blue', '', '', '', 3);
$image->writetext(5, 72, "hello World !", 12, 0, 'darkgreen', '', '', '', 4);
$image->writetext(5, 100, "hello World !", 12, 0, 'maroon', '', '', '', 5);
$image->format = 'png';
$image->display();
?>