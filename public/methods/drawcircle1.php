<?php

/*
 * This file is part of the PHPImage - PHP Drawing package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__ . '/../../autoload.php';
$image = new PHPImage(120, 360);
$blue = 'blue 30%';
$red  = 'red 30%';

$image->drawcircle('50%', '50%', '40%', 'green 60%', 15, 'double');
$image->drawcircle('50%', '50%', '20%', 'green 60%', 15, 'dot');

$image->drawcircle('25%', '10%', 20, $blue, 1, 'solid');
$image->drawcircle('50%', '10%', 20, $blue, 1, 'square');
$image->drawcircle('75%', '10%', 20, $blue, 1, 'dot');
$image->drawcircle('33%', '25%', 20, $blue, 1, 'dash');
$image->drawcircle('67%', '25%', 20, $blue, 1, 'bigdash');

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
$image->drawcircle('50%', '50%', '20%', '', 1, $style);

$image->drawcircle('20%', '75%', 20, $red, 5, 'solid');
$image->drawcircle('40%', '75%', 20, $red, 5, 'square');
$image->drawcircle('60%', '75%', 20, $red, 5, 'dot');
$image->drawcircle('80%', '75%', 20, $red, 5, 'dash');
$image->drawcircle('25%', '90%', 20, $red, 5, 'bigdash');
$image->drawcircle('50%', '90%', 20, $red, 5, 'double');
$image->drawcircle('75%', '90%', 20, $red, 5, 'triple');
$image->format = 'png';
$image->display();
