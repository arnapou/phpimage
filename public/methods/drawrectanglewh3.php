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
$image->drawrectanglewh(5, 5, 30, 30, 'black', 1, 'solid', 'all(none, 11)');
$image->drawrectanglewh(45, 5, 30, 30, 'blue', 1, 'solid', 'all(empty, 11)');
$image->drawrectanglewh(85, 5, 30, 30, 'red', 1, 'solid', 'all(biseau, 11)');

$image->drawrectanglewh(5, 45, 30, 30, 'black', 1, 'solid', 'all(biseau2, 11)');
$image->drawrectanglewh(45, 45, 30, 30, 'blue', 1, 'solid', 'all(biseau3, 11)');
$image->drawrectanglewh(85, 45, 30, 30, 'red', 1, 'solid', 'all(biseau4, 11)');

$image->drawrectanglewh(5, 85, 30, 30, 'black', 1, 'solid', 'all(round, 11)');
$image->drawrectanglewh(45, 85, 30, 30, 'blue', 1, 'solid', 'all(round2, 11)');
$image->drawrectanglewh(85, 85, 30, 30, 'red', 1, 'solid', 'all(curve, 11)');

$image->drawrectanglewh(5, 125, 30, 30, 'black', 1, 'solid', 'all(curve2, 11)');
$image->drawrectanglewh(45, 125, 30, 30, 'blue', 1, 'solid', 'all(curve3, 11)');
$image->drawrectanglewh(85, 125, 30, 30, 'red', 1, 'solid', 'all(curve4, 11)');

$image->drawrectanglewh(5, 165, 30, 30, 'black', 1, 'solid', 'all(curve5, 11)');
$image->drawrectanglewh(45, 165, 30, 30, 'blue', 1, 'solid', 'all(curve6, 11)');
$image->drawrectanglewh(85, 165, 30, 30, 'red', 1, 'solid', 'all(trait, 11)');

$image->drawrectanglewh(5, 205, 30, 30, 'black', 1, 'solid', 'all(trait2, 11)');
$image->drawrectanglewh(45, 205, 30, 30, 'blue', 1, 'solid', 'all(trait3, 11)');
$image->drawrectanglewh(85, 205, 30, 30, 'red', 1, 'solid', 'all(round+curve, 11)');

$image->drawrectanglewh(5, 245, 30, 30, 'black', 1, 'solid', 'all(curve4+curve5, 11)');
$image->drawrectanglewh(45, 245, 30, 30, 'blue', 1, 'solid', 'all(biseau+empty, 11)');
$image->drawrectanglewh(85, 245, 30, 30, 'red', 1, 'solid', 'all(trait2+trait, 11)');
$image->format = 'png';
$image->display();
