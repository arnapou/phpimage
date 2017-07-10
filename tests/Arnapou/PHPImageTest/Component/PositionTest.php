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

use Arnapou\PHPImage\Component\Position;
use Arnapou\PHPImage\Exception\InvalidPositionException;
use Arnapou\PHPImageTest\TestCase;

/**
 * @coversDefaultClass Arnapou\PHPImage\Component\Position
 */
class PositionTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::setPosition
     */
    public function testConstructor()
    {
        $position1 = new Position('30% 40%');
        $this->assertInstanceOf(Position::class, $position1);

        $position2 = new Position($position1);
        $this->assertInstanceOf(Position::class, $position2);

        $position3 = new Position(['top', 'left']);
        $this->assertInstanceOf(Position::class, $position3);
    }

    /**
     * @covers ::__construct
     * @covers ::setPosition
     */
    public function testConstructorInvalid()
    {
        $this->expectException(InvalidPositionException::class);

        $position = new Position([1, 2, 3]);
    }

    /**
     * @covers ::setX
     */
    public function testSetXInvalid()
    {
        $this->expectException(InvalidPositionException::class);

        $position = new Position();
        $position->setX(30);
    }

    /**
     * @covers ::setY
     */
    public function testSetYInvalid()
    {
        $this->expectException(InvalidPositionException::class);

        $position = new Position();
        $position->setY(30);
    }

    /**
     * @covers ::getX
     * @covers ::setX
     * @covers ::getY
     * @covers ::setY
     */
    public function testGetterSetter()
    {
        $position = new Position();
        $position->setX('30%');
        $position->setY('40%');

        $this->assertEquals(30, $position->getX());
        $this->assertEquals(60, $position->getX(200));
        $this->assertEquals(40, $position->getY());
    }

    /**
     * @covers ::toArray
     * @covers ::__toString
     */
    public function testFormatters()
    {
        $position = new Position('30% 40%');

        $this->assertEquals([30, 40], $position->toArray());
        $this->assertEquals('30% 40%', $position->__toString());
    }

}