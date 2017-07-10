<?php
/*
 * This file is part of the Arnapou PHPImage package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PHPImage\Helper;

use Arnapou\PHPImage\Exception\InvalidArgumentException;

class GD
{
    const MAX_ALPHA = 127;
    const MAX_RGB = 255;

    /**
     * @param $image
     * @param $color              RGB or RGBA array of int
     *                            alpha is 0 for transparent and MAX_ALPHA (127) for opaque
     * @return int
     * @throws InvalidArgumentException
     */
    public function colorAllocate($image, $color)
    {
        if (\is_array($color) && count($color) == 3) {
            return \imagecolorallocatealpha($image, $color[0], $color[1], $color[2], 0);
        } elseif (\is_array($color) && count($color) == 4) {
            return \imagecolorallocatealpha($image, $color[0], $color[1], $color[2], self::MAX_ALPHA - $color[3]);
        } else {
            throw new InvalidArgumentException('bgColor should be a RGB or RGBA array of int');
        }
    }

    /**
     * @param $image
     * @param $x
     * @param $y
     * @param $color              RGB or RGBA array of int
     *                            alpha is 0 for transparent and MAX_ALPHA (127) for opaque
     */
    public function setPixel($image, $x, $y, $color)
    {
        \imagesetpixel($image, $x, $y, $this->colorAllocate($image, $color));
    }

    /**
     * @param            $w
     * @param            $h
     * @param array|null $bgColor RGB or RGBA array of int
     *                            alpha is 0 for transparent and MAX_ALPHA (127) for opaque
     * @return resource
     * @throws InvalidArgumentException
     */
    public function createImage($w, $h, $bgColor = null)
    {
        $image = \imagecreatetruecolor($w, $h);
        \imagesavealpha($image, true);
        \imagealphablending($image, true);

        if ($bgColor === null) {
            $fillColor = \imagecolorallocatealpha($image, 255, 255, 255, self::MAX_ALPHA);
        } else {
            $fillColor = $this->colorAllocate($image, $bgColor);
        }

        \imagefill($image, 0, 0, $fillColor);
        $alpha = $fillColor >> 24;
        if ($alpha == self::MAX_ALPHA) {
            \imagecolortransparent($image, $fillColor);
        }

        return $image;
    }

    /**
     * @param resource $image
     * @param int      $x
     * @param int      $y
     * @return array
     */
    public function getPixel($image, $x, $y)
    {
        $val = \imagecolorat($image, $x, $y);
        $R = ($val >> 16) & 0xFF;
        $G = ($val >> 8) & 0xFF;
        $B = $val & 0xFF;
        $A = $val >> 24;
        return [$R, $G, $B, self::MAX_ALPHA - $A];
    }

    /**
     * @param resource $image
     * @param int      $alpha 0 for transparent and MAX_ALPHA (127) for opaque
     * @param bool     $overwriteTransparentPixels
     */
    public function setAlpha($image, $alpha, $overwriteTransparentPixels = false)
    {
        $w = \imagesx($image);
        $h = \imagesy($image);
        \imagealphablending($image, false);
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $val = \imagecolorat($image, $x, $y);
                $A = $val >> 24;
                if ($overwriteTransparentPixels || $A != self::MAX_ALPHA) {
                    $R = ($val >> 16) & 0xFF;
                    $G = ($val >> 8) & 0xFF;
                    $B = $val & 0xFF;
                    $color = \imagecolorallocatealpha($image, $R, $G, $B, self::MAX_ALPHA - $alpha);
                    \imagesetpixel($image, $x, $y, $color);
                }
            }
        }
        \imagealphablending($image, true);
    }

    /**
     * @param      $dst
     * @param      $src
     * @param      $dstx
     * @param      $dsty
     * @param      $srcx
     * @param      $srcy
     * @param      $srcw
     * @param      $srch
     * @param null $forcedTransparency 0 for transparent and MAX_ALPHA (127) for opaque
     */
    public function realCopy($dst, $src, $dstx, $dsty, $srcx, $srcy, $srcw, $srch, $forcedTransparency = null)
    {
        $dstw = \imagesx($dst);
        $dsth = \imagesy($dst);

        $tmp = $this->createImage($srcw, $srch);
        \imagealphablending($tmp, false);
        \imagecopy($tmp, $dst, 0, 0, $dstx, $dsty, $srcw, $srch);
        \imagealphablending($tmp, true);

        \imagecopy($tmp, $src, 0, 0, $srcx, $srcy, $srcw, $srch);

        \imagealphablending($dst, false);
        for ($x = 0; $x < $srcw; $x++) {
            for ($y = 0; $y < $srch; $y++) {
                $val = \imagecolorat($tmp, $x, $y);
                $R = ($val >> 16) & 0xFF;
                $G = ($val >> 8) & 0xFF;
                $B = $val & 0xFF;
                $Asrc = \imagecolorat($src, $x + $srcx, $y + $srcy) >> 24;
                if ($Asrc != self::MAX_ALPHA) {
                    if ($x + $dstx < $dsth && $x + $dstx >= 0 && $y + $dsty < $dstw && $y + $dsty >= 0) {
                        if ($forcedTransparency !== null) {
                            $A = self::MAX_ALPHA - $forcedTransparency;
                        } else {
                            $Adst = \imagecolorat($dst, $x + $dstx, $y + $dsty) >> 24;
                            $A = $Adst + $Asrc - self::MAX_ALPHA;
                            if ($A < 0) {
                                $A = 0;
                            }
                        }
                        $val = \imagecolorallocatealpha($dst, $R, $G, $B, $A);
                        \imagesetpixel($dst, $x + $dstx, $y + $dsty, $val);
                    }
                }
            }
        }
        \imagedestroy($tmp);
        \imagealphablending($dst, true);
    }

    /**
     * @param resource $image1
     * @param resource $image2
     * @return bool
     */
    public function areImagesIdentical($image1, $image2)
    {
        $w1 = \imagesx($image1);
        $h1 = \imagesy($image1);
        $w2 = \imagesx($image2);
        $h2 = \imagesy($image2);
        if ($w1 !== $w2 || $h1 !== $h2) {
            return false;
        }
        for ($y = 0; $y < $h1; $y++) {
            for ($x = 0; $x < $w1; $x++) {

                $val1 = \imagecolorat($image1, $x, $y);
                $R1 = ($val1 >> 16) & 0xFF;
                $G1 = ($val1 >> 8) & 0xFF;
                $B1 = $val1 & 0xFF;
                $A1 = $val1 >> 24;

                $val2 = \imagecolorat($image2, $x, $y);
                $R2 = ($val2 >> 16) & 0xFF;
                $G2 = ($val2 >> 8) & 0xFF;
                $B2 = $val2 & 0xFF;
                $A2 = $val2 >> 24;

                if ($R1 !== $R2 || $G1 !== $G2 || $B1 !== $B2 || $A1 !== $A2) {
                    return false;
                }
            }
        }
        return true;
    }

}