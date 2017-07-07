<?php

/*
 * This file is part of the Arnapou PHPImage package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PHPImageTest;

class TestCase extends \PHPUnit\Framework\TestCase
{

    public function assertImageIdentical($image, $name)
    {
        $filename = __DIR__ . '/../../images/tested/' . $name . '.png';
        if (!\is_file($filename)) {
            \imagepng($image, $filename);
            return true;
        }
        $image2 = \imagecreatefrompng($filename);

        $w1 = \imagesx($image);
        $h1 = \imagesy($image);
        $w2 = \imagesx($image2);
        $h2 = \imagesy($image2);
        if ($w1 !== $w2 || $h1 !== $h2) {
            return false;
        }
        for ($y = 0; $y < $h1; $y++) {
            for ($x = 0; $x < $w1; $x++) {

                $val1 = \imagecolorat($image, $x, $y);
                $R1 = ($val1 >> 16) & 0xFF;
                $G1 = ($val1 >> 8) & 0xFF;
                $B1 = $val1 & 0xFF;
                $A1 = $val1 >> 24;

                $val2 = \imagecolorat($image2, $x, $y);
                $R2 = ($val2 >> 16) & 0xFF;
                $G2 = ($val2 >> 8) & 0xFF;
                $B2 = $val2 & 0xFF;
                $A2 = $val2 >> 24;

                if ($R1 !== $R2 || $G1 !== $G2 || $B1 !== $B2 || $A1 !== $A2) {
                    \imagedestroy($image);
                    \imagedestroy($image2);
                    $this->assertThat(IsImageIdentical::DIFFERENT, new IsImageIdentical(), $name);
                    return false;
                }

            }
        }
        \imagedestroy($image);
        \imagedestroy($image2);
        $this->assertThat(IsImageIdentical::IDENTICAL, new IsImageIdentical(), $name);
    }

}