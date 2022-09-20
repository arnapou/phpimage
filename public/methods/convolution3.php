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
$matrix = array(array(1, 2, 1), array(2, 4, 2), array(1, 2, 1));
$image = new PHPImage('php.gif');
$image->convolution($matrix, 80, true);
$image->format = 'png';
$image->display();
