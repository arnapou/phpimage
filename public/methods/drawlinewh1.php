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

$image->drawlinewh(110, 10, -100, 160, 'green 60%', 15, 'double');
$image->drawlinewh(10, 10, 100, 160, 'green 60%', 15, 'dot');

$image->drawlinewh(10, 10, 40, 70, $blue, 1, 'solid');
$image->drawlinewh(20, 10, 40, 70, $blue, 1, 'square');
$image->drawlinewh(30, 10, 40, 70, $blue, 1, 'dot');
$image->drawlinewh(40, 10, 40, 70, $blue, 1, 'dash');
$image->drawlinewh(50, 10, 40, 70, $blue, 1, 'bigdash');

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
$image->drawlinewh(10, 90, 100, 0, '', 4, $style);

$image->drawlinewh(10, 100, 40, 70, $red, 5, 'solid');
$image->drawlinewh(20, 100, 40, 70, $red, 5, 'square');
$image->drawlinewh(30, 100, 40, 70, $red, 5, 'dot');
$image->drawlinewh(40, 100, 40, 70, $red, 5, 'dash');
$image->drawlinewh(50, 100, 40, 70, $red, 5, 'bigdash');
$image->drawlinewh(60, 100, 40, 70, $red, 5, 'double');
$image->drawlinewh(70, 100, 40, 70, $red, 5, 'triple');
$image->format = 'png';
$image->display();
