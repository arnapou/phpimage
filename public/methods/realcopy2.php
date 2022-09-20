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
$php = new PHPImage('php.gif');
$image = new PHPImage();
$image->bgcolor = 'yellow 80%';
$image->create($php->width, $php->height);
$image->copy($php, 0, 0, 0, 0, 0, 0, '70%');
$php->destroy();
$image->format = 'png';
$image->display();
