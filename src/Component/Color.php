<?php

/*
 * This file is part of the Arnapou PHPImage package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PHPImage\Component;

use Arnapou\PHPImage\Helper\GD;
use Arnapou\PHPImage\Helper\HelperTrait;

class Color
{
    use HelperTrait;
    const MAX_ALPHA = GD::MAX_ALPHA;
    const MAX_RGB = GD::MAX_RGB;
    /**
     * @var array
     */
    protected $RGBA = [0, 0, 0, 0];

    /**
     * Color constructor.
     * @param null $color
     */
    public function __construct($color = null)
    {
        if ($color) {
            $this->setColor($color);
        }
    }

    /**
     * modify the current color object and desaturate RGB (keeping alpha)
     */
    public function desaturate()
    {
        $avg = floor(($this->RGBA[0] + $this->RGBA[1] + $this->RGBA[2]) / 3);
        $this->setColor([$avg, $avg, $avg]);
    }

    /**
     * invert the current color object
     * @param bool $keepAlpha
     */
    public function invert($keepAlpha = true)
    {
        $R = self::MAX_RGB - $this->RGBA[0];
        $G = self::MAX_RGB - $this->RGBA[1];
        $B = self::MAX_RGB - $this->RGBA[2];
        $A = $keepAlpha ? $this->RGBA[3] : self::MAX_ALPHA - $this->RGBA[3];
        $this->setColor([$R, $G, $B, $A]);
    }

    /**
     * @param int $value
     */
    public function setRed($value)
    {
        $this->type()->checkColorInteger($value);
        $this->RGBA[0] = $value;
    }

    /**
     * @param int $value
     */
    public function setGreen($value)
    {
        $this->type()->checkColorInteger($value);
        $this->RGBA[1] = $value;
    }

    /**
     * @param int $value
     */
    public function setBlue($value)
    {
        $this->type()->checkColorInteger($value);
        $this->RGBA[2] = $value;
    }

    /**
     * @param int $value
     */
    public function setAlpha($value)
    {
        $this->type()->checkAlpha($value);
        $this->RGBA[3] = $value;
    }

    /**
     * @return int
     */
    public function getRed()
    {
        return $this->RGBA[0];
    }

    /**
     * @return int
     */
    public function getGreen()
    {
        return $this->RGBA[1];
    }

    /**
     * @return int
     */
    public function getBlue()
    {
        return $this->RGBA[2];
    }

    /**
     * @return int Opacity : 0 is transparent
     */
    public function getAlpha()
    {
        return $this->RGBA[3];
    }

    /**
     * @return int Transparency : 0 is opaque
     */
    public function getTransparency()
    {
        return self::MAX_ALPHA - $this->RGBA[3];
    }

    /**
     * @return bool
     */
    public function isTransparent()
    {
        return $this->RGBA[3] == 0;
    }

    /**
     * @return bool
     */
    public function isOpaque()
    {
        return $this->RGBA[3] == self::MAX_ALPHA;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        if ($color instanceof Color) {
            $this->RGBA = $color->toArray();
        } elseif (\is_array($color)) {
            $i = 0;
            foreach ($color as $val) {
                $this->RGBA[$i] = $val;
                if (++$i > 3) {
                    break;
                }
            }
        } else {
            $this->RGBA = $this->parser()->parseColor((string)$color);
        }
        $this->type()->checkColorInteger($this->RGBA[0]);
        $this->type()->checkColorInteger($this->RGBA[1]);
        $this->type()->checkColorInteger($this->RGBA[2]);
        $this->type()->checkAlpha($this->RGBA[3]);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->RGBA;
    }

    /**
     * @return string
     */
    public function toHex()
    {
        return sprintf('#%02x%02x%02x', $this->RGBA[0], $this->RGBA[1], $this->RGBA[2]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(' ', $this->RGBA);
    }
}
