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
$image = new PHPImage(190, 190);
$image->fontfile = __DIR__.'/verdana.ttf';
$image->drawfilledcircle('10%', '20%', 3, 'blue 50%');
$image->drawfilledcircle('50%', '20%', 3, 'blue 50%');
$image->drawfilledcircle('90%', '20%', 3, 'blue 50%');
$image->drawfilledcircle('10%', '50%', 3, 'blue 50%');
$image->drawfilledcircle('50%', '50%', 3, 'blue 50%');
$image->drawfilledcircle('90%', '50%', 3, 'blue 50%');
$image->drawfilledcircle('10%', '80%', 3, 'blue 50%');
$image->drawfilledcircle('50%', '80%', 3, 'blue 50%');
$image->drawfilledcircle('90%', '80%', 3, 'blue 50%');
$text = "hello\nWorld !";
$image->writetext('10%', '20%', $text, 10, 0, 'black', '', 'top left');
$image->writetext('50%', '20%', $text, 10, 0, 'black', '', 'top center');
$image->writetext('90%', '20%', $text, 10, 0, 'black', '', 'top right');
$image->writetext('10%', '50%', $text, 10, 0, 'black', '', 'center left');
$image->writetext('50%', '50%', $text, 10, 0, 'black', '', 'center');
$image->writetext('90%', '50%', $text, 10, 0, 'black', '', 'center right');
$image->writetext('10%', '80%', $text, 10, 0, 'black', '', 'bottom left');
$image->writetext('50%', '80%', $text, 10, 0, 'black', '', 'bottom center');
$image->writetext('90%', '80%', $text, 10, 0, 'black', '', 'bottom right');
$image->format = 'png';
$image->display();
