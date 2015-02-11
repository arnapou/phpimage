<?php
require(__DIR__.'/../../src/class.PHPImage.php');
$image = new PHPImage(120, 360);
$blue = 'blue 30%';
$red  = 'red 30%';

$image->drawellipsearc('50%', '50%', '90%', '90%', 40, 220, 'green 60%', 15, 'double', false);
$image->drawellipsearc('50%', '50%', '50%', '50%', 40, 220, 'green 60%', 15, 'dot', false);

$image->drawellipsearc('25%', '15%', 40, 50, 100, 380, $blue, 1, 'solid', false);
$image->drawellipsearc('50%', '15%', 40, 50, 100, 380, $blue, 1, 'square', false);
$image->drawellipsearc('75%', '15%', 40, 50, 100, 380, $blue, 1, 'dot', false);
$image->drawellipsearc('33%', '35%', 40, 50, 100, 380, $blue, 1, 'dash', false);
$image->drawellipsearc('67%', '35%', 40, 50, 100, 380, $blue, 1, 'bigdash', false);

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
$image->drawellipsearc('50%', '50%', '90%', 20, -45, 225, '', 1, $style);

$image->drawellipsearc('10%', '65%', 40, 50, -100, 100, $red, 5, 'solid', true);
$image->drawellipsearc('33%', '65%', 40, 50, -100, 100, $red, 5, 'square', true);
$image->drawellipsearc('57%', '65%', 40, 50, -100, 100, $red, 5, 'dot', true);
$image->drawellipsearc('80%', '65%', 40, 50, -100, 100, $red, 5, 'dash', true);
$image->drawellipsearc('25%', '85%', 40, 50, -100, 100, $red, 5, 'bigdash', true);
$image->drawellipsearc('50%', '85%', 40, 50, -100, 100, $red, 5, 'double', true);
$image->drawellipsearc('75%', '85%', 40, 50, -100, 100, $red, 5, 'triple', true);
$image->format = 'png';
$image->display();
?>