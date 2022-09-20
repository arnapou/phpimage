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
$image = new PHPImage(120, 280);
$image->drawfilledrectanglewh(5, 5, 30, 30, 'black', 'all(none, 11)');
$image->drawfilledrectanglewh(45, 5, 30, 30, 'blue', 'all(empty, 11)');
$image->drawfilledrectanglewh(85, 5, 30, 30, 'red', 'all(biseau, 11)');

$image->drawfilledrectanglewh(5, 45, 30, 30, 'black', 'all(biseau2, 11)');
$image->drawfilledrectanglewh(45, 45, 30, 30, 'blue', 'all(biseau3, 11)');
$image->drawfilledrectanglewh(85, 45, 30, 30, 'red', 'all(biseau4, 11)');

$image->drawfilledrectanglewh(5, 85, 30, 30, 'black', 'all(round, 11)');
$image->drawfilledrectanglewh(45, 85, 30, 30, 'blue', 'all(round2, 11)');
$image->drawfilledrectanglewh(85, 85, 30, 30, 'red', 'all(curve, 11)');

$image->drawfilledrectanglewh(5, 125, 30, 30, 'black', 'all(curve2, 11)');
$image->drawfilledrectanglewh(45, 125, 30, 30, 'blue', 'all(curve3, 11)');
$image->drawfilledrectanglewh(85, 125, 30, 30, 'red', 'all(curve4, 11)');

$image->drawfilledrectanglewh(5, 165, 30, 30, 'black', 'all(curve5, 11)');
$image->drawfilledrectanglewh(45, 165, 30, 30, 'blue', 'all(curve6, 11)');
$image->drawfilledrectanglewh(85, 165, 30, 30, 'red', 'all(trait, 11)');

$image->drawfilledrectanglewh(5, 205, 30, 30, 'black', 'all(trait2, 11)');
$image->drawfilledrectanglewh(45, 205, 30, 30, 'blue', 'all(trait3, 11)');
$image->drawfilledrectanglewh(85, 205, 30, 30, 'red', 'all(round2+curve, 11)');

$image->drawfilledrectanglewh(5, 245, 30, 30, 'black', 'all(curve5+curve6, 11)');
$image->drawfilledrectanglewh(45, 245, 30, 30, 'blue', 'all(round%2+round2, 11)');
$image->drawfilledrectanglewh(85, 245, 30, 30, 'red', 'all(trait2+trait, 11)');
$image->format = 'png';
$image->display();
