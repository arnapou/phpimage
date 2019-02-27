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

use Arnapou\PHPImage\Component\Color;
use Arnapou\PHPImage\Component\Point;
use Arnapou\PHPImage\Component\Position;
use Arnapou\PHPImage\Component\Size;
use Arnapou\PHPImage\Exception\InvalidAlphaException;
use Arnapou\PHPImage\Exception\InvalidFloatException;
use Arnapou\PHPImage\Exception\InvalidImageResourceException;
use Arnapou\PHPImage\Exception\InvalidIntegerException;
use Arnapou\PHPImage\Exception\OutOfBoundsException;
use Arnapou\PHPImage\Image;

class TypeChecker
{
    /**
     * @param mixed    $value
     * @param int      $x
     * @param int      $y
     * @param int|null $maxx
     * @param int|null $maxy
     * @param bool     $strict
     * @return Point
     */
    public function checkPoint($value, & $x, &$y, $maxx = null, $maxy = null, $strict = true)
    {
        $point = new Point($value);
        $x = $point->getX($maxx);
        $y = $point->getY($maxy);
        if ($strict) {
            $this->checkInteger($x, 0, $maxx);
            $this->checkInteger($y, 0, $maxy);
        }
        return $point;
    }

    /**
     * @param mixed    $value
     * @param int      $w
     * @param int      $h
     * @param int|null $maxw
     * @param int|null $maxh
     * @param bool     $strict
     * @return Size
     */
    public function checkSize($value, & $w, &$h, $maxw = null, $maxh = null, $strict = true)
    {
        $point = new Size($value);
        $w = $point->getW($maxw);
        $h = $point->getH($maxh);
        if ($strict) {
            $this->checkInteger($w, 0, $maxw);
            $this->checkInteger($h, 0, $maxh);
        }
        return $point;
    }

    /**
     * @param mixed    $value
     * @param int      $x
     * @param int      $y
     * @param int|null $maxx
     * @param int|null $maxy
     * @param bool     $strict
     * @return Position
     */
    public function checkPosition($value, & $x, &$y, $maxx = null, $maxy = null, $strict = true)
    {
        $position = new Position($value);
        $posx = $position->getX($maxx);
        $posy = $position->getY($maxy);
        if ($strict) {
            $this->checkInteger($posx, 0, $maxx);
            $this->checkInteger($posy, 0, $maxy);
        }
        $x -= $posx;
        $y -= $posy;
        return $position;
    }

    /**
     * @param string $value
     * @return resource
     * @throws InvalidImageResourceException
     */
    public function checkResource($value)
    {
        if ($value instanceof Image) {
            return $value->getResource();
        } elseif (\is_resource($value) && \get_resource_type($value) === 'gd') {
            return $value;
        }
        throw new InvalidImageResourceException();
    }

    /**
     * @param string $value
     * @throws InvalidAlphaException
     */
    public function checkAlpha(&$value)
    {
        if ($value === null) {
            $value = Color::MAX_ALPHA;
        } elseif (is_numeric($value)) {
            $value = \intval($value);
            $this->checkInteger($value, 0, Color::MAX_ALPHA);
        } elseif (preg_match('/^\s*([0-9]+(\.[0-9]+)?)%\s*$/si', (string)$value, $m)) {
            $value = round(\floatval($m[1]) * Color::MAX_ALPHA / 100);
            $this->checkInteger($value, 0, Color::MAX_ALPHA);
        } else {
            throw new InvalidAlphaException('value should be a correct numeric value');
        }
    }

    /**
     * @param string $value
     */
    public function checkColorInteger(&$value)
    {
        if ($value === null) {
            $value = 0;
        }
        $this->checkInteger($value, 0, Color::MAX_RGB);
    }

    /**
     * @param string $value
     * @param int    $min
     * @param int    $max
     * @throws InvalidIntegerException
     * @throws OutOfBoundsException
     */
    public function checkInteger(&$value, $min = null, $max = null)
    {
        $value = trim("$value");
        if (is_numeric($value)) {
            $value = \intval($value);
            if ($min !== null && $value < $min) {
                throw new OutOfBoundsException("value should be >= $min");
            }
            if ($max !== null && $value > $max) {
                throw new OutOfBoundsException("value should be <= $max");
            }
        } else {
            throw new InvalidIntegerException('value should be a correct numeric value');
        }
    }

    /**
     * @param string $value
     * @param null   $min
     * @param null   $max
     * @throws InvalidFloatException
     * @throws OutOfBoundsException
     */
    public function checkFloat(&$value, $min = null, $max = null)
    {
        $value = trim("$value");
        if (is_numeric($value)) {
            $value = \floatval($value);
            if ($min !== null && $value < $min) {
                throw new OutOfBoundsException("value should be >= $min");
            }
            if ($max !== null && $value > $max) {
                throw new OutOfBoundsException("value should be <= $max");
            }
        } else {
            throw new InvalidFloatException('value should be a correct numeric value');
        }
    }
}