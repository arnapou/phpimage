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
// syntax : $image->effect('mosaic', [size_x], [size_y]);
// size_x default = 2
// size_y default = 2
$image->effect('mosaic', 4);
$image->format = 'png';
$image->display();
