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
$image = new PHPImage(120, 180);
$blue = 'blue 20%';
$red  = 'red 20%';

$image->drawline(110, 10, 10, 170, 'green 60%', 15, 'double');
$image->drawline(10, 10, 110, 170, 'green 60%', 15, 'dot');

$image->drawline(10, 10, 50, 80, $blue, 1, 'solid');
$image->drawline(20, 10, 60, 80, $blue, 1, 'square');
$image->drawline(30, 10, 70, 80, $blue, 1, 'dot');
$image->drawline(40, 10, 80, 80, $blue, 1, 'dash');
$image->drawline(50, 10, 90, 80, $blue, 1, 'bigdash');

$style = array(  // '' empty string means no color at all
    $blue, $blue, $blue, ''   ,
    $blue, $blue, ''   , ''   ,
    $blue, ''   , ''   , ''   ,
    ''   , ''   , ''   , $red ,
    ''   , ''   , $red , $red ,
    ''   , $red , $red , $red ,
    ''   , ''   , $red , $red ,
    ''   , ''   , ''   , $red ,
    $blue, ''   , ''   , ''   ,
    $blue, $blue, ''   , ''   ,
);
$image->drawline(10, 90, 110, 90, '', 4, $style);

$image->drawline(10, 100, 50, 170, $red, 5, 'solid');
$image->drawline(20, 100, 60, 170, $red, 5, 'square');
$image->drawline(30, 100, 70, 170, $red, 5, 'dot');
$image->drawline(40, 100, 80, 170, $red, 5, 'dash');
$image->drawline(50, 100, 90, 170, $red, 5, 'bigdash');
$image->drawline(60, 100, 100, 170, $red, 5, 'double');
$image->drawline(70, 100, 110, 170, $red, 5, 'triple');
$image->format = 'png';
$image->display();
