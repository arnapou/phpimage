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
$image->drawfilledcircle('50%', '20%', 3, 'blue 50%');
$image->drawfilledcircle('50%', '50%', 3, 'blue 50%');
$image->drawfilledcircle('50%', '80%', 3, 'blue 50%');
$text = "hello\nWorld !";
$image->writetext('50%', '20%', $text, 12, 30, 'black', 'left underline shadow'); // simple
$image->writetext('50%', '50%', $text, 12, 180, 'black', 'right underline(green) shadow(red, -2x, -2y)');
$image->writetext('50%', '80%', $text, 12, 60, 'black', 'underline(dot) shadow(red)', '', 6);
$image->format = 'png';
$image->display();
