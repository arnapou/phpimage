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
$tux = new PHPImage('tux.gif');
$image = new PHPImage('php.gif');
// transparency: 50%
$image->copy($tux, 50, 20, 0, 0, 0, 0, '50%');
$image->format = 'png';
$image->display();
