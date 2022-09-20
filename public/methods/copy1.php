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
$image->copy($tux);
$image->format = 'png';
$image->display();
