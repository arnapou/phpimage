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
$image->drawfilledcircle('50%', '50%', 30, 'blue');
$image->drawfilledcircle('17%', '50%', '40%', 'red 50%');
$image->drawfilledcircle('83%', '50%', '20%', 'yellow 20%');
$image->format = 'png';
$image->display();
