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

$image->drawrectanglewh(5, 5, 110, 30, 'red 20%', 4, 'solid', 'all(round, 6)');
$image->drawrectanglewh(17, 17, 86, 30, 'blue 20%', 4, 'dot');
$image->drawrectanglewh(29, 29, 62, 30, 'darkgreen 20%', 4, 'double', 'all(curve, 6)');
$image->drawrectanglewh(41, 41, 38, 30, 'maroon 20%', 4, 'square');
$shape = 'tl(round, 20) tr(empty, 10, 20) bl(curve, 30, 20) br(biseau, 30)';
$image->drawrectanglewh(5, 80, 110, 95, 'blue', 1, 'solid', $shape);
$image->drawrectanglewh(15, 90, 90, 75, 'red', 2, 'dot', $shape);
$image->drawrectanglewh(25, 100, 70, 55, 'maroon', 3, 'double', $shape);
$shape = 'all(curve+round, 20, 10) tr(curve+round, 10, 20) bl(curve+round, 10, 20)';
$image->drawrectanglewh(45, 110, 30, 30, 'black', 1, 'solid', $shape);

$image->format = 'png';
$image->display();
