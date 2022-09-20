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
$image = new PHPImage('php.gif');
$image->fill(27, 23, 'red 50%');
$image->fill(60, 23, 'green 50%');
$image->fill(82, 23, 'blue 50%');
$image->format = 'png';
$image->display();
