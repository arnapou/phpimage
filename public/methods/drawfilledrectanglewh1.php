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
$image->drawfilledrectanglewh(5, 5, 110, 30, 'red 20%', 'all(round, 10)');
$image->drawfilledrectanglewh(20, 25, 80, 30, 'darkgreen 20%', 'all(curve, 10)');
$image->drawfilledrectanglewh(35, 45, 50, 30, 'blue 20%', 'all(round2, 10)');
$shape = 'tl(round, 20) tr(empty, 10, 20) bl(curve, 30, 20) br(biseau, 30)';
$image->drawfilledrectanglewh(5, 80, 110, 95, 'blue', $shape);
$image->drawfilledrectanglewh(15, 90, 90, 75, 'red', $shape);
$shape = 'tl(round%3, 20) tr(empty, 10, 20) bl(curve%2, 30, 20) br(biseau%4, 30)';
$image->drawfilledrectanglewh(25, 100, 70, 55, 'maroon', $shape);
$shape = 'all(curve, 20, 10) tr(curve, 10, 20) bl(curve, 10, 20)';
$image->drawfilledrectanglewh(45, 110, 30, 30, 'black', $shape);
$image->format = 'png';
$image->display();
