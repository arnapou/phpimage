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
$image = new PHPImage(120, 180);
// if you use a hight transparency and on not too big images
// don't forget to set realcopy to true to have a correct transparency
$image->realcopy = true;
$image->setbackgroundimage($flowers, '70%');
$flowers->destroy();
$image->format = 'png';
$image->display();
