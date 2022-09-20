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
$image->drawrectangle(5, 5, 115, 175, 'black', 3, 'solid');
$image->drawrectangle(12, 12, 108, 168, 'blue', 3, 'dot');
$image->drawrectangle(19, 19, 101, 161, 'red', 3, 'square');
$image->drawrectangle(26, 26, 93, 153, 'darkgreen', 3, 'dash');
$image->drawrectangle(33, 33, 87, 147, 'orange', 3, 'bigdash');
$image->drawrectangle(40, 40, 80, 140, 'blue', 3, 'double');
$image->drawrectangle(50, 50, 70, 130, 'red', 5, 'triple');
$image->format = 'png';
$image->display();
