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

use Arnapou\PHPImage\Component\Size;
use Arnapou\PHPImage\Exception\InvalidSizeException;
use Arnapou\PHPImageTest\TestCase;

/**
 * @coversDefaultClass Arnapou\PHPImage\Component\Size
 */
class SizeTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::setSize
     */
    public function testConstructor()
    {
        $size1 = new Size('30 40');
        $this->assertInstanceOf(Size::class, $size1);

        $size2 = new Size($size1);
        $this->assertInstanceOf(Size::class, $size2);
    }

    /**
     * @covers ::__construct
     * @covers ::setSize
     */
    public function testConstructorInvalid()
    {
        $this->expectException(InvalidSizeException::class);

        $size = new Size([1, 2, 3]);
    }

    /**
     * @covers ::getW
     * @covers ::setW
     * @covers ::isPercentW
     * @covers ::getH
     * @covers ::setH
     * @covers ::isPercentH
     */
    public function testGetterSetter()
    {
        $size = new Size();
        $size->setW('30%');
        $size->setH('40');

        $this->assertEquals(30, $size->getW());
        $this->assertEquals(true, $size->isPercentW());
        $this->assertEquals(40, $size->getH());
        $this->assertEquals(false, $size->isPercentH());
    }

    /**
     * @covers ::toArray
     * @covers ::__toString
     */
    public function testFormatters()
    {
        $size = new Size('30% 40');

        $this->assertEquals([30, 40], $size->toArray());
        $this->assertEquals('30% 40', $size->__toString());
    }

}