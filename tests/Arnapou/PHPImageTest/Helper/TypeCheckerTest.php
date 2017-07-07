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

use Arnapou\PHPImage\Exception\InvalidImageResourceException;
use Arnapou\PHPImage\Exception\InvalidPointException;
use Arnapou\PHPImage\Exception\InvalidSizeException;
use Arnapou\PHPImage\Exception\OutOfBoundsException;
use Arnapou\PHPImage\Helper\HelperTrait;
use Arnapou\PHPImage\Image;
use PHPUnit\Framework\TestCase;

class TypeCheckerTest extends TestCase
{
    use HelperTrait;

    public function testCheckInvalidSize()
    {
        $this->expectException(InvalidSizeException::class);
        $this->type()->checkSize('40 50 30', $w, $h);
    }

    public function testCheckOutOfBoundsSize()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->type()->checkSize('100 200', $w, $h, null, 199);
    }

    public function testCheckValidSize()
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

    public function testCheckInvalidPoint()
    {
        $this->expectException(InvalidPointException::class);
        $this->type()->checkPoint('40 50 30', $w, $h);
    }

    public function testCheckOutOfBoundPoint()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->type()->checkPoint('100 200', $w, $h, null, 199);
    }

    public function testCheckValidPoint()
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

    public function testCheckInvalidResource()
    {
        $this->expectException(InvalidImageResourceException::class);
        $resource = 'not_a_resource';
        $this->type()->checkResource($resource);
    }

    public function testCheckValidResource()
    {
        $resource = new Image(50, 50);
        $this->type()->checkResource($resource);
        $this->assertEquals(true, \is_string(\get_resource_type($resource)));
    }
}
