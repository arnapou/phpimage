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
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    public function testCreateFromValidText()
    {
        $this->assertInstanceOf(
            Color::class,
            new Color('red')
        );
    }

    public function testCreateFromValidTextWithAlpha()
    {
        $this->assertInstanceOf(
            Color::class,
            new Color('blue 50%')
        );
    }

    public function testCreateFromValidRGB()
    {
        $this->assertInstanceOf(
            Color::class,
            new Color('120 100 50')
        );
    }

    public function testCreateFromValidRGBA()
    {
        $this->assertInstanceOf(
            Color::class,
            new Color('120 100 50 100')
        );
    }
}