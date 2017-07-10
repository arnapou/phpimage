<?php

/*
 * This file is part of the Arnapou PHPImage package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PHPImageTest\Component;

use Arnapou\PHPImage\Component\Point;
use Arnapou\PHPImage\Exception\InvalidPointException;
use Arnapou\PHPImageTest\TestCase;

/**
 * @coversDefaultClass Arnapou\PHPImage\Component\Point
 */
class PointTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::setPoint
     */
    public function testConstructor()
    {
        $point1 = new Point('30 40');
        $this->assertInstanceOf(Point::class, $point1);

        $point2 = new Point($point1);
        $this->assertInstanceOf(Point::class, $point2);
    }

    /**
     * @covers ::__construct
     * @covers ::setPoint
     */
    public function testConstructorInvalid()
    {
        $this->expectException(InvalidPointException::class);

        $point = new Point([1, 2, 3]);
    }

    /**
     * @covers ::getX
     * @covers ::setX
     * @covers ::isPercentX
     * @covers ::getY
     * @covers ::setY
     * @covers ::isPercentY
     */
    public function testGetterSetter()
    {
        $point = new Point();
        $point->setX('30%');
        $point->setY('40');

        $this->assertEquals(30, $point->getX());
        $this->assertEquals(true, $point->isPercentX());
        $this->assertEquals(40, $point->getY());
        $this->assertEquals(false, $point->isPercentY());
    }

    /**
     * @covers ::toArray
     * @covers ::__toString
     */
    public function testFormatters()
    {
        $point = new Point('30% 40');

        $this->assertEquals([30, 40], $point->toArray());
        $this->assertEquals('30% 40', $point->__toString());
    }

}