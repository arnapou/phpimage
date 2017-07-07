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

use Arnapou\PHPImage\Exception\InvalidNamedColorException;
use Arnapou\PHPImage\Helper\NamedColors;
use Arnapou\PHPImageTest\TestCase;

/**
 * @coversDefaultClass Arnapou\PHPImage\Helper\NamedColors
 */
class NamedColorsTest extends TestCase
{
    /**
     * @covers ::get
     */
    public function testGetInvalid()
    {
        $this->expectException(InvalidNamedColorException::class);
        NamedColors::get('invalid_color');
    }

    /**
     * @covers ::get
     */
    public function testGetValid()
    {
        $this->assertEquals(
            [205, 205, 0],
            NamedColors::get('yellow3')
        );
    }

    /**
     * @covers ::exists
     */
    public function testExists()
    {
        $this->assertEquals(
            true,
            NamedColors::exists('yellow3')
        );
    }

    /**
     * @covers ::getAll
     */
    public function testGetAll()
    {
        $this->assertTrue(\is_array(NamedColors::getAll()));
    }

}