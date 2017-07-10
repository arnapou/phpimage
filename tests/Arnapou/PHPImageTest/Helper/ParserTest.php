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

use Arnapou\PHPImage\Exception\InvalidPositionException;
use Arnapou\PHPImage\Exception\InvalidRelativeValueException;
use Arnapou\PHPImageTest\TestCase;

/**
 * @coversDefaultClass Arnapou\PHPImage\Helper\Parser
 */
class ParserTest extends TestCase
{
    /**
     * @covers ::parseColor
     */
    public function testParseValidColor()
    {
        // hex syntax
        $this->assertEquals(
            [43, 136, 160, 127],
            $this->parser()->parseColor('2b88a0')
        );
        $this->assertEquals(
            [43, 136, 160, 127],
            $this->parser()->parseColor('#2b88a0')
        );
        $this->assertEquals(
            [43, 136, 160, 13],
            $this->parser()->parseColor('2b88a0 10%')
        );

        // rgb syntax
        $this->assertEquals(
            [43, 136, 160, 127],
            $this->parser()->parseColor('43,136,160')
        );
        $this->assertEquals(
            [43, 136, 160, 127],
            $this->parser()->parseColor('43 136 160')
        );
        $this->assertEquals(
            [43, 136, 160, 40],
            $this->parser()->parseColor('43 136 160 40')
        );
        $this->assertEquals(
            [43, 136, 160, 51],
            $this->parser()->parseColor('43 136 160 40%')
        );

        // array syntax
        $this->assertEquals(
            [43, 136, 160, 51],
            $this->parser()->parseColor([43, 136, 160, '40%'])
        );

        // named color syntax
        $this->assertEquals(
            [222, 184, 135, 51],
            $this->parser()->parseColor('BurlyWood 40%')
        );
    }

    /**
     * @covers ::parseRelativeValue
     */
    public function testParseInvalidRelativeValue()
    {
        $this->expectException(InvalidRelativeValueException::class);
        $this->parser()->parseRelativeValue('stupid_input');
    }

    /**
     * @covers ::parseRelativeValue
     */
    public function testParseValidRelativeValue()
    {
        // numeric
        $this->assertEquals(
            [43.56, false],
            $this->parser()->parseRelativeValue('43.56')
        );
        // percent
        $this->assertEquals(
            [43.56, true],
            $this->parser()->parseRelativeValue('43.56%')
        );
        // px
        $this->assertEquals(
            [234, false],
            $this->parser()->parseRelativeValue('234px')
        );
        // top bottom left right center
        $this->assertEquals(
            [0, true],
            $this->parser()->parseRelativeValue('top')
        );
        $this->assertEquals(
            [0, true],
            $this->parser()->parseRelativeValue('left')
        );
        $this->assertEquals(
            [100, true],
            $this->parser()->parseRelativeValue('right')
        );
        $this->assertEquals(
            [100, true],
            $this->parser()->parseRelativeValue('bottom')
        );
        $this->assertEquals(
            [50, true],
            $this->parser()->parseRelativeValue('center')
        );
    }

    /**
     * @covers ::parsePosition
     */
    public function testParseInvalidPosition()
    {
        $this->expectException(InvalidPositionException::class);
        $this->parser()->parsePosition('left right');
    }

    /**
     * @covers ::parsePosition
     */
    public function testParseValidPosition()
    {
        // percent
        $this->assertEquals(
            [24, 67],
            $this->parser()->parsePosition('24% 67%')
        );
        // correct top bottom left right center
        $this->assertEquals(
            [0, 0],
            $this->parser()->parsePosition('left top')
        );
        $this->assertEquals(
            [100, 100],
            $this->parser()->parsePosition('right bottom')
        );
        $this->assertEquals(
            [50, 0],
            $this->parser()->parsePosition('center top')
        );
        // inverted (x<->y) top bottom left right center
        $this->assertEquals(
            [0, 100],
            $this->parser()->parsePosition('bottom left')
        );
        $this->assertEquals(
            [100, 0],
            $this->parser()->parsePosition('top right')
        );
        // array
        $this->assertEquals(
            [0, 100],
            $this->parser()->parsePosition(['bottom', 'left'])
        );
    }

}