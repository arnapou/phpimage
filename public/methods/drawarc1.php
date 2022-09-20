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

$image->drawarc('50%', '50%', 50, 45, 225, 'green 60%', 15, 'double', false);
$image->drawarc('50%', '50%', 30, -135, 45, 'green 60%', 15, 'dot', false);

$image->drawarc('20%', '10%', 20, 100, 380, $blue, 1, 'solid', false);
$image->drawarc('50%', '10%', 20, 100, 380, $blue, 1, 'square', false);
$image->drawarc('80%', '10%', 20, 100, 380, $blue, 1, 'dot', false);
$image->drawarc('25%', '25%', 20, 100, 380, $blue, 1, 'dash', false);
$image->drawarc('75%', '25%', 20, 100, 380, $blue, 1, 'bigdash', false);

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
$image->drawarc('50%', '50%', 20, 45, 225, '', 1, $style);

$image->drawarc('10%', '75%', 20, -100, 100, $red, 5, 'solid', true);
$image->drawarc('33%', '75%', 20, -100, 100, $red, 5, 'square', true);
$image->drawarc('57%', '75%', 20, -100, 100, $red, 5, 'dot', true);
$image->drawarc('80%', '75%', 20, -100, 100, $red, 5, 'dash', true);
$image->drawarc('20%', '90%', 20, -100, 100, $red, 5, 'bigdash', true);
$image->drawarc('50%', '90%', 20, -100, 100, $red, 5, 'double', true);
$image->drawarc('80%', '90%', 20, -100, 100, $red, 5, 'triple', true);
$image->format = 'png';
$image->display();
