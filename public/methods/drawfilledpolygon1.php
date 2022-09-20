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
$image = new PHPImage(120, 120);
$form1 = array(
    array(10, 10), array(100, 10), array(50, 50), array(100, 100),
    array(30, 80), array(20, 50), array(40, 30),
);
$form2 = array(
    array(20, 100), array(95, 40), array(15, 30), array(100, 80)
);
$form3 = array(
    array(20, 105), array(30, 100), array(40, 110), array(50, 100),
    array(60, 115), array(70, 105), array(80, 120), array(90, 110),
    array(110, 110), array(110, 60), array(100, 70), array(95, 70), array(85, 60),
    array(40, 95), array(90, 105), array(40, 60), array(10, 80),
);
$image->drawfilledpolygon($form1, 'blue 30%');
$image->drawfilledpolygon($form2, 'red 30%');
$image->drawfilledpolygon($form3, 'green 30%');
$image->format = 'png';
$image->display();
