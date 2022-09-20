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
// syntax : $image->effect('selective_blur', [number]);
// number = 1 (default), 2 or 3
$image->effect('selective_blur');
$image->format = 'png';
$image->display();
