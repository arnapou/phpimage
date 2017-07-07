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

use Arnapou\PHPImage\Exception\InvalidAlphaException;
use Arnapou\PHPImage\Exception\InvalidFloatException;
use Arnapou\PHPImage\Exception\InvalidImageResourceException;
use Arnapou\PHPImage\Exception\InvalidIntegerException;
use Arnapou\PHPImage\Exception\InvalidPointException;
use Arnapou\PHPImage\Exception\InvalidSizeException;
use Arnapou\PHPImage\Exception\OutOfBoundsException;
use Arnapou\PHPImage\Helper\HelperTrait;
use Arnapou\PHPImage\Image;
use Arnapou\PHPImageTest\TestCase;

/**
 * @coversDefaultClass Arnapou\PHPImage\Helper\TypeChecker
 */
class TypeCheckerTest extends TestCase
{
    use HelperTrait;

    /**
     * @covers ::checkSize
     */
    public function testSizeInvalid()
    {
        $this->expectException(InvalidSizeException::class);
        $this->type()->checkSize('40 50 30', $w, $h);
    }

    /**
     * @covers ::checkSize
     */
    public function testSizeOutOfBounds()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->type()->checkSize('100 200', $w, $h, null, 199);
    }

    /**
     * @covers ::checkSize
     */
    public function testSizeValid()
    {
        // simple standard size
        $this->type()->checkSize('  100  200  ', $w, $h);
        $this->assertEquals(100, $w);
        $this->assertEquals(200, $h);

        // percent size
        $this->type()->checkSize('  80%  60%  ', $w, $h);
        $this->assertEquals(80, $w);
        $this->assertEquals(60, $h);

        // percent size with max size
        $this->type()->checkSize('  80%  60%  ', $w, $h, 150, 200);
        $this->assertEquals(120, $w);
        $this->assertEquals(120, $h);

        // percent mixed with integer and no strict check of bounds
        $this->type()->checkSize('  150%  100  ', $w, $h, 150, 200, false);
        $this->assertEquals(225, $w);
        $this->assertEquals(100, $h);
    }

    /**
     * @covers ::checkPoint
     */
    public function testPointInvalid()
    {
        $this->expectException(InvalidPointException::class);
        $this->type()->checkPoint('40 50 30', $w, $h);
    }

    /**
     * @covers ::checkPoint
     */
    public function testPointOutOfBound()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->type()->checkPoint('100 200', $w, $h, null, 199);
    }

    /**
     * @covers ::checkPoint
     */
    public function testPointValid()
    {
        // simple standard size
        $this->type()->checkPoint('  100  200  ', $w, $h);
        $this->assertEquals(100, $w);
        $this->assertEquals(200, $h);

        // percent size
        $this->type()->checkPoint('  80%  60%  ', $w, $h);
        $this->assertEquals(80, $w);
        $this->assertEquals(60, $h);

        // named point position
        $this->type()->checkPoint('  center  bottom  ', $w, $h, 150, 200);
        $this->assertEquals(75, $w);
        $this->assertEquals(200, $h);

        // percent size with max size
        $this->type()->checkPoint('  80%  60%  ', $w, $h, 150, 200);
        $this->assertEquals(120, $w);
        $this->assertEquals(120, $h);

        // percent mixed with integer and no strict check of bounds
        $this->type()->checkPoint('  150%  100  ', $w, $h, 150, 200, false);
        $this->assertEquals(225, $w);
        $this->assertEquals(100, $h);
    }

    /**
     * @covers ::checkResource
     */
    public function testResourceInvalid()
    {
        $this->expectException(InvalidImageResourceException::class);
        $resource = 'not_a_resource';
        $this->type()->checkResource($resource);
    }

    /**
     * @covers ::checkResource
     */
    public function testResourceValid()
    {
        $image = new Image(50, 50);
        $resource = $this->type()->checkResource($image);
        $this->assertEquals(true, "gd" === \get_resource_type($resource));

        $image = \imagecreatetruecolor(10, 10);
        $resource = $this->type()->checkResource($image);
        $this->assertEquals(true, "gd" === \get_resource_type($resource));
    }

    /**
     * @covers ::checkAlpha
     */
    public function testAlphaInvalid()
    {
        $this->expectException(InvalidAlphaException::class);
        $value = 'not_alpha';
        $this->type()->checkAlpha($value);
    }

    /**
     * @covers ::checkAlpha
     */
    public function testAlphaValid()
    {
        $values = [
            [null, 127],
            [120, 120],
            ['49%', 62],
        ];
        foreach ($values as $value) {
            $this->type()->checkAlpha($value[0]);
            $this->assertEquals($value[1], $value[0]);
        }
    }

    /**
     * @covers ::checkColorInteger
     */
    public function testIntegerValidColor()
    {
        $values = [
            [null, 0],
            [120, 120],
        ];
        foreach ($values as $value) {
            $this->type()->checkColorInteger($value[0]);
            $this->assertEquals($value[1], $value[0]);
        }
    }

    /**
     * @covers ::checkInteger
     */
    public function testIntegerNegative()
    {
        $this->expectException(OutOfBoundsException::class);
        $value = -2;
        $this->type()->checkInteger($value, 0);
    }

    /**
     * @covers ::checkInteger
     */
    public function testIntegerTooBig()
    {
        $this->expectException(OutOfBoundsException::class);
        $value = 300;
        $this->type()->checkInteger($value, null, 255);
    }

    /**
     * @covers ::checkInteger
     */
    public function testIntegerInvalid()
    {
        $this->expectException(InvalidIntegerException::class);
        $value = 'not_an_integer';
        $this->type()->checkInteger($value);
    }

    /**
     * @covers ::checkInteger
     */
    public function testIntegerValid()
    {
        $values = [
            ['20', 20],
        ];
        foreach ($values as $value) {
            $this->type()->checkInteger($value[0]);
            $this->assertEquals($value[1], $value[0]);
        }
    }

    /**
     * @covers ::checkFloat
     */
    public function testFloatNegative()
    {
        $this->expectException(OutOfBoundsException::class);
        $value = -2;
        $this->type()->checkFloat($value, 0);
    }

    /**
     * @covers ::checkFloat
     */
    public function testFloatTooBig()
    {
        $this->expectException(OutOfBoundsException::class);
        $value = 300;
        $this->type()->checkFloat($value, null, 255);
    }

    /**
     * @covers ::checkFloat
     */
    public function testFloatInvalid()
    {
        $this->expectException(InvalidFloatException::class);
        $value = 'not_an_integer';
        $this->type()->checkFloat($value);
    }

    /**
     * @covers ::checkFloat
     */
    public function testFloatValid()
    {
        $values = [
            ['20', 20],
        ];
        foreach ($values as $value) {
            $this->type()->checkFloat($value[0]);
            $this->assertEquals($value[1], $value[0]);
        }
    }
}
