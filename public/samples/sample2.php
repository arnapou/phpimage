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

class MyClass extends PHPImage
{
    public function makeframe($text, $forecolor, $bgcolor, $corner, $fontsize)
    {
        list($w, $h) = $this->gettextbox($text, $fontsize);
        if ($w > $h) {
            $margin = round(0.30*$h);
        } else {
            $margin = round(0.30*$w);
        }
        $w += 2*$margin;
        $h += 2*$margin;
        $this->create($w, $h);
        $this->drawrectanglewh(0, 0, $w, $h, $forecolor, 1, 'solid', 'all('.$corner.', '.$margin.')');
        $this->fill($w/2, $h/2, $bgcolor);
        $this->writetext('50%', '50%', $text, $fontsize, 0, $forecolor, 'center', 'center');
    }
}

$image = new MyClass();
$image->fontfile = '../methods/verdana.ttf';
$image->makeframe("The really true\nsolution is : 42 ...", 'gold', 'black', 'curve', 14);
$image->format = 'png';
$image->display();
