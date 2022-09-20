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
$image = new PHPImage(120, 80);
$image->drawfilledcirclearc('50%', '50%', '25%', -45, 180, 'blue');
$image->drawfilledcirclearc('17%', '50%', '25%', -60, 90, 'red 50%');
$image->drawfilledcirclearc('83%', '50%', '25%', 80, 10, 'yellow 20%');
$image->format = 'png';
$image->display();
