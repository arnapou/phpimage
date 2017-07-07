<?php

/*
 * This file is part of the Arnapou PHPImage package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PHPImageTest\Helper;

use Arnapou\PHPImage\Exception\InvalidArgumentException;
use Arnapou\PHPImage\Helper\HelperTrait;
use Arnapou\PHPImageTest\TestCase;

/**
 * @coversDefaultClass Arnapou\PHPImage\Helper\GD
 */
class GDTest extends TestCase
{
    use HelperTrait;

    /**
     * @covers ::createImage
     */
    public function testCreateImageInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->gd()->createImage(10, 10, 'invalid_color');
    }

    /**
     * @covers ::createImage
     * @covers ::colorAllocate
     */
    public function testCreateImageValid()
    {
        $tests = [
            'createImage1' => function () {
                return $this->gd()->createImage(10, 10);
            },
            'createImage2' => function () {
                return $this->gd()->createImage(10, 10, [255, 0, 0]);
            },
            'createImage3' => function () {
                return $this->gd()->createImage(10, 10, [255, 0, 0, 60]);
            },
        ];

        foreach ($tests as $name => $factory) {
            $image = $factory();
            $this->assertImageIdentical($image, $name);
        }
    }

    /**
     * @covers ::setAlpha
     */
    public function testSetAlpha()
    {
        $tests = [
            'setAlpha1' => function () {
                $image = $this->gd()->createImage(10, 10, [255, 0, 0]);
                $this->gd()->setAlpha($image, 60);
                return $image;
            },
            'setAlpha2' => function () {
                $image = $this->gd()->createImage(10, 10, [255, 0, 0, 60]);
                $this->gd()->setAlpha($image, 20);
                return $image;
            },
        ];

        foreach ($tests as $name => $factory) {
            $image = $factory();
            $this->assertImageIdentical($image, $name);
        }
    }

    /**
     * @covers ::realCopy
     * @covers ::setPixel
     */
    public function testRealCopy()
    {
        $tests = [
            'realCopy1' => function () {
                $image1 = $this->gd()->createImage(10, 10, [255, 0, 0, 30]);
                $image2 = $this->gd()->createImage(5, 5, [0, 0, 255, 70]);

                \imagealphablending($image1, false);
                \imagealphablending($image2, false);
                $this->gd()->setPixel($image1, 2, 2, [0, 0, 0, 0]);
                $this->gd()->setPixel($image2, 0, 0, [0, 0, 0, 0]);

                $this->gd()->setPixel($image1, 6, 6, [0, 0, 0, 110]);
                $this->gd()->setPixel($image2, 4, 4, [0, 0, 0, 110]);
                \imagealphablending($image1, true);
                \imagealphablending($image2, true);

                $this->gd()->realCopy($image1, $image2, 2, 2, 0, 0, 5, 5);
                \imagedestroy($image2);
                return $image1;
            },
            'realCopy2' => function () {
                $image1 = $this->gd()->createImage(10, 10, [255, 0, 0, 30]);
                $image2 = $this->gd()->createImage(5, 5, [0, 0, 255, 70]);
                $this->gd()->realCopy($image1, $image2, 2, 2, 0, 0, 5, 5, 60);
                \imagedestroy($image2);
                return $image1;
            },
        ];

        foreach ($tests as $name => $factory) {
            $image = $factory();
            $this->assertImageIdentical($image, $name);
        }
    }

}