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
$image = new PHPImage(120, 120);
$image->fontfile = __DIR__.'/verdana.ttf';
$image->drawfilledcircle('50%', '50%', 3, 'blue 50%');
$image->writetext('50%', '50%', "hello\nWorld !");
$image->format = 'png';
$image->display();
