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
$tux = new PHPImage('tux.gif');
$image = new PHPImage('php.gif');
// point source : top center ('50%', 0)
// position of point source : top center
// width source : 50% of tux width
// height source : 40% of tux height
$image->copy($tux, 0, 0, '50%', 0, '50%', '40%', 0, '', 'top center');
$image->format = 'png';
$image->display();
