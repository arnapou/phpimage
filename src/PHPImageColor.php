<?php

/*
 * This file is part of the PHPImage - PHP Drawing package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * PHPImageColor class
 */
class PHPImageColor
{
    /**
     * RED
     * @var int
     */
    public $R = 0;
    /**
     * GREEN
     * @var int
     */
    public $G = 0;
    /**
     * BLUE
     * @var int
     */
    public $B = 0;
    /**
     * ALPHA / TRANSPARENCY
     * @var int
     */
    public $A = 0;
    /**
     *
     */
    public $value = '';

    /**
     * @param string $color
     */
    public function __construct($color = 'black')
    {
        $this->setcolor($color);
    }

    /**
     * @return string
     */
    public function getHTML()
    {
        return sprintf('#%02x%02x%02x', $this->R, $this->G, $this->B);
    }

    /**
     * @param bool $invertalpha
     */
    public function invert($invertalpha = false)
    {
        $R = 255 - $this->R;
        $G = 255 - $this->G;
        $B = 255 - $this->B;
        $A = $invertalpha === true ? 127 - $this->A : $this->A;
        $this->setcolor("$R,$G,$B,$A");
    }

    /**
     * @return string
     */
    public function getvalues()
    {
        return $this->R . ',' . $this->G . ',' . $this->B . ',' . $this->A;
    }

    /**
     *
     */
    public function togray()
    {
        $avg = floor(($this->R + $this->G + $this->B) / 3);
        $this->setcolor("$avg,$avg,$avg," . $this->A);
    }

    /**
     * @param string $color
     */
    public function setcolor($color)
    {
        $this->value = $color;
        PHPImageTools::checkcolor($color);
        $this->R = $color[0];
        $this->G = $color[1];
        $this->B = $color[2];
        $this->A = $color[3];
    }
}
