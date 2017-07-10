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

use Arnapou\PHPImage\Component\Color;
use Arnapou\PHPImageTest\TestCase;

/**
 * @coversDefaultClass Arnapou\PHPImage\Component\Color
 */
class ColorTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::setColor
     */
    public function testConstructor()
    {
        $color1 = new Color('10 20 30 40');
        $this->assertInstanceOf(Color::class, $color1);

        $color2 = new Color($color1);
        $this->assertInstanceOf(Color::class, $color2);

        $color3 = new Color([10, 20, 30, 40]);
        $this->assertInstanceOf(Color::class, $color3);
    }

    /**
     * @covers ::getRed
     * @covers ::setRed
     * @covers ::getGreen
     * @covers ::setGreen
     * @covers ::getBlue
     * @covers ::setBlue
     * @covers ::getAlpha
     * @covers ::SetAlpha
     * @covers ::getTransparency
     */
    public function testGetterSetterRGBAT()
    {
        $color = new Color();
        $color->setRed(10);
        $color->setGreen(20);
        $color->setBlue(30);
        $color->setAlpha(40);
        $this->assertEquals(10, $color->getRed());
        $this->assertEquals(20, $color->getGreen());
        $this->assertEquals(30, $color->getBlue());
        $this->assertEquals(40, $color->getAlpha());
        $this->assertEquals(87, $color->getTransparency());
    }

    /**
     * @covers ::isTransparent
     * @covers ::isOpaque
     */
    public function testTransparentOpaque()
    {
        $color = new Color([10, 20, 30, 0]);
        $this->assertEquals(true, $color->isTransparent(), "1/ isTransparent ? $color");
        $this->assertEquals(false, $color->isOpaque(), "1/ isOpaque ? $color");

        $color = new Color([10, 20, 30, Color::MAX_ALPHA]);
        $this->assertEquals(false, $color->isTransparent(), "2/ isTransparent ? $color");
        $this->assertEquals(true, $color->isOpaque(), "2/ sOpaque ? $color");
    }

    /**
     * @covers ::desaturate
     * @covers ::invert
     */
    public function testFilters()
    {
        $color = new Color([10, 20, 30, 40]);
        $color->desaturate();
        $this->assertEquals([20, 20, 20, 40], $color->toArray());

        $color = new Color([10, 20, 30, 40]);
        $color->invert();
        $this->assertEquals([245, 235, 225, 40], $color->toArray());
    }

    /**
     * @covers ::toArray
     * @covers ::toHex
     * @covers ::__toString
     */
    public function testFormatters()
    {
        $color = new Color([10, 20, 30, 40]);

        $this->assertEquals([10, 20, 30, 40], $color->toArray());
        $this->assertEquals('#0a141e', $color->toHex());
        $this->assertEquals('10 20 30 40', $color->__toString());
    }

}