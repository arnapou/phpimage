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
 * PHPImageTTFBox class
 */
class PHPImageTTFBox
{
    /**
     * @var int
     */
    public $width = 0;
    /**
     * @var int
     */
    public $height = 0;
    /**
     * Top Left X
     * @var int
     */
    public $tlx = null;
    /**
     * Top Left Y
     * @var int
     */
    public $tly = null;
    /**
     * Top Right X
     * @var int
     */
    public $trx = null;
    /**
     * Top Right Y
     * @var int
     */
    public $try = null;
    /**
     * Bottom Left X
     * @var int
     */
    public $blx = null;
    /**
     * Bottom Left Y
     * @var int
     */
    public $bly = null;
    /**
     * Bottom Right X
     * @var int
     */
    public $brx = null;
    /**
     * Bottom Right Y
     * @var int
     */
    public $bry = null;

    /**
     * @param string $text
     * @param int $size
     * @param string $font
     * @param float $angle
     */
    public function __construct($text, $size, $font, $angle = 0)
    {
        if (is_int($font)) {
            switch ($font) {
                case 1:
                    $this->height = 9;
                    break;
                case 2:
                    $this->height = 12;
                    break;
                case 3:
                    $this->height = 13;
                    break;
                case 4:
                    $this->height = 15;
                    break;
                case 5:
                    $this->height = 14;
                    break;
            }
            $this->width = imagefontwidth($font) * strlen($text);
            $this->blx = 0;
            $this->bly = $this->height;
            $this->brx = $this->width;
            $this->bry = $this->height;
            $this->trx = $this->width;
            $this->try = 0;
            $this->tlx = 0;
            $this->tly = 0;
        } else {
            $b = imagettfbbox($size, $angle, $font, $text);
            $this->width = $b[2] - $b[0];
            $this->height = $b[1] - $b[5];
            $this->blx = $b[0];
            $this->bly = $b[1];
            $this->brx = $b[2];
            $this->bry = $b[3];
            $this->trx = $b[4];
            $this->try = $b[5];
            $this->tlx = $b[6];
            $this->tly = $b[7];
        }
    }
}
