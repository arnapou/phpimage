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
$image = new PHPImage();
$image->fontfile = '../methods/verdana.ttf';
$text      = "The really true\nsolution is : 42 ...";
$forecolor = 'red';
$bgcolor   = 'lightblue';
$corner    = 'round';
$fontsize  = 14;
list($w, $h) = $image->gettextbox($text, $fontsize);
if ($w > $h) {
    $margin = round(0.30*$h);
} else {
    $margin = round(0.30*$w);
}
$w += 2*$margin;
$h += 2*$margin;
$image->create($w, $h);
$image->drawrectanglewh(0, 0, $w, $h, $forecolor, 1, 'solid', 'all('.$corner.', '.$margin.')');
$image->fill($w/2, $h/2, $bgcolor);
$image->writetext('50%', '50%', $text, $fontsize, 0, $forecolor, 'center', 'center');
$image->format = 'png';
$image->display();
