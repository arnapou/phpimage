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
$flowers = new PHPImage('tulipes.jpg');
$flowers->resizefit(100);
$image = new PHPImage(120, 180);
$image->fill(0, 0, 'white');
$image->drawimage($flowers, 10, 10);
$image->drawimage($flowers, 5, 90, '25%', '25%', '50%', '50%', '50%');
$image->drawimage($flowers, 65, 90, '25%', '25%', '50%', '50%', 0, '', '', 'all(biseau, 12)');
$image->drawimage($flowers, 5, 135, '25%', '25%', '50%', '50%', 0, '', '', 'all(round%2, 12)');
$image->drawimage($flowers, 65, 135, '25%', '25%', '50%', '50%', '70%', '', '', 'all(curve, 12)', true);
$flowers->destroy();
$image->format = 'png';
$image->display();
