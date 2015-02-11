<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage(120, 360);
$blue = 'blue 30%';
$red  = 'red 30%';

$image->drawellipse('50%', '50%', '90%', '90%', 'green 60%', 15, 'double');
$image->drawellipse('50%', '50%', '50%', '50%', 'green 60%', 15, 'dot');

$image->drawellipse('25%', '15%', 40, 50, $blue, 1, 'solid');
$image->drawellipse('50%', '15%', 40, 50, $blue, 1, 'square');
$image->drawellipse('75%', '15%', 40, 50, $blue, 1, 'dot');
$image->drawellipse('33%', '35%', 40, 50, $blue, 1, 'dash');
$image->drawellipse('67%', '35%', 40, 50, $blue, 1, 'bigdash');

$style = array(  // '' empty string means no color at all
	$blue, $blue, $blue, $blue, $blue, $blue, $blue, $blue,
	$blue, $blue, $blue, $blue, $blue, $blue, $blue, $blue,
	''   , ''   , ''   , ''   , ''   , ''   , ''   , ''   ,
	''   , ''   , ''   , ''   , ''   , ''   , ''   , ''   ,
	$red , $red , $red , $red , $red , $red , $red , $red ,
	$red , $red , $red , $red , $red , $red , $red , $red ,
	''   , ''   , ''   , ''   , ''   , ''   , ''   , ''   ,
	''   , ''   , ''   , ''   , ''   , ''   , ''   , ''   ,
);
$image->drawellipse('50%', '50%', '90%', 20, '', 1, $style);

$image->drawellipse('20%', '65%', 40, 50, $red, 5, 'solid');
$image->drawellipse('40%', '65%', 40, 50, $red, 5, 'square');
$image->drawellipse('60%', '65%', 40, 50, $red, 5, 'dot');
$image->drawellipse('80%', '65%', 40, 50, $red, 5, 'dash');
$image->drawellipse('25%', '85%', 40, 50, $red, 5, 'bigdash');
$image->drawellipse('50%', '85%', 40, 50, $red, 5, 'double');
$image->drawellipse('75%', '85%', 40, 50, $red, 5, 'triple');
$image->format = 'png';
$image->display();
?>