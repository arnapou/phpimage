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
$image->drawfilledellipsearc('50%', '50%', 50, 50, -45, 180, 'blue');
$image->drawfilledellipsearc('17%', '50%', '45%', '110%', -60, 90, 'red 50%');
$image->drawfilledellipsearc('83%', '50%', '60%', '30%', 80, 10, 'yellow 20%');
$image->format = 'png';
$image->display();
