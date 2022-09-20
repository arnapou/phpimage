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
$image = new PHPImage(120, 190);
$image->fontfile = __DIR__.'/verdana.ttf';
$image->fill(0, 0, 'white');
$image->drawfilledcircle(10, 10, 3, 'blue 50%');
$image->drawfilledcircle(10, 45, 3, 'blue 50%');
$image->drawfilledcircle(10, 80, 3, 'blue 50%');
$image->drawfilledcircle(10, 115, 3, 'blue 50%');
$image->drawfilledcircle(10, 150, 3, 'blue 50%');
$text = "hello\nWorld !";
$image->writetext(10, 10, $text, 12, 0, 'black', 'shadow'); // simple
$image->writetext(10, 45, $text, 12, 0, 'black', 'shadow(red)'); // red
$image->writetext(10, 80, $text, 12, 0, 'black', 'shadow(blue, 20%, noblur)'); // blue 20% no blur
$image->writetext(10, 115, $text, 12, 0, 'black', 'shadow(orange, 1x, 1y, noblur)'); // set offset
$image->writetext(10, 150, $text, 12, 0, 'black', 'shadow(orange, 2x, 5y, noblur)'); // set offset
$image->format = 'png';
$image->display();
