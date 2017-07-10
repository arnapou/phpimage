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

use Arnapou\PHPImage\Component\RelativeValue;
use Arnapou\PHPImageTest\TestCase;

/**
 * @coversDefaultClass Arnapou\PHPImage\Component\RelativeValue
 */
class RelativeValueTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::set
     */
    public function testConstructor()
    {
        $value = new RelativeValue('60%');
        $this->assertInstanceOf(RelativeValue::class, $value);
    }

    /**
     * @covers ::get
     * @covers ::isPercent
     */
    public function testGetterSetter()
    {
        $value = new RelativeValue('60%');

        $this->assertEquals(60, $value->get());
        $this->assertEquals(120, $value->get(200));
        $this->assertEquals(true, $value->isPercent());
    }

    /**
     * @covers ::__toString
     */
    public function testFormatters()
    {
        $value = new RelativeValue(' 55% ');

        $this->assertEquals('55%', $value->__toString());
    }

}