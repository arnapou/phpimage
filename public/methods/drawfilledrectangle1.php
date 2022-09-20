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
$image->drawfilledrectangle(5, 5, 115, 35, 'red 20%', 'all(round, 10)');
$image->drawfilledrectangle(20, 25, 100, 55, 'darkgreen 20%', 'all(curve, 10)');
$image->drawfilledrectangle(35, 45, 85, 75, 'blue 20%', 'all(round2, 10)');
$shape = 'tl(round, 20) tr(empty, 10, 20) bl(curve, 30, 20) br(biseau, 30)';
$image->drawfilledrectangle(5, 80, 115, 175, 'blue', $shape);
$image->drawfilledrectangle(15, 90, 105, 165, 'red', $shape);
$shape = 'tl(round%3, 20) tr(empty, 10, 20) bl(curve%2, 30, 20) br(biseau%4, 30)';
$image->drawfilledrectangle(25, 100, 95, 155, 'maroon', $shape);
$shape = 'all(curve, 20, 10) tr(curve, 10, 20) bl(curve, 10, 20)';
$image->drawfilledrectangle(45, 110, 75, 140, 'black', $shape);
$image->format = 'png';
$image->display();
