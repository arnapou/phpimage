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

class Color
{
    use TypeCheckerTrait;
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
     * @param int $value
     */
    public function setRed($value)
    {
        $this->checkColorInteger($value);
        $this->RGBA[0] = $value;
    }

    /**
     * @param int $value
     */
    public function setGreen($value)
    {
        $this->checkColorInteger($value);
        $this->RGBA[1] = $value;
    }

    /**
     * @param int $value
     */
    public function setBlue($value)
    {
        $this->checkColorInteger($value);
        $this->RGBA[2] = $value;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->checkColor($color);
        $this->RGBA = $color;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->RGBA;
    }
}