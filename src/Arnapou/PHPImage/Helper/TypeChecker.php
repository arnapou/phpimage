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
use Arnapou\PHPImage\Exception\InvalidImageResourceException;
use Arnapou\PHPImage\Exception\OutOfBoundsException;
use Arnapou\PHPImage\Exception\NotIntegerException;
use Arnapou\PHPImage\Image;

class TypeChecker
{

    /**
     * @param string $value
     * @throws InvalidImageResourceException
     * @internal param int $min
     * @internal param int $max
     */
    public function checkResource(& $value)
    {
        if ($value instanceof Image) {
            $value = $value->getResource();
        }
        if (!\is_resource($value)) {
            throw new InvalidImageResourceException();
        }
    }

    /**
     * @param string $value
     */
    public function checkAlpha(&$value)
    {
        if ($value === null) {
            $value = Color::MAX_ALPHA;
        }
        $this->checkInteger($value, 0, Color::MAX_ALPHA);
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
     * @throws NotIntegerException
     * @throws OutOfBoundsException
     */
    public function checkInteger(&$value, $min = null, $max = null)
    {
        $value = trim("$value");
        if (is_numeric($value)) {
            $value = intval($value);
            if ($min !== null && $value < $min) {
                throw new OutOfBoundsException("value should be >= $min");
            }
            if ($max !== null && $value > $max) {
                throw new OutOfBoundsException("value should be <= $max");
            }
        } else {
            throw new NotIntegerException("value should be a correct numeric value");
        }
    }

    /**
     * @param string $value
     * @param null   $min
     * @param null   $max
     * @throws NotFloatException
     * @throws OutOfBoundsException
     */
    public function checkFloat(&$value, $min = null, $max = null)
    {
        $value = trim("$value");
        if (is_numeric($value)) {
            $value = floatval($value);
            if ($min !== null && $value < $min) {
                throw new OutOfBoundsException("value should be >= $min");
            }
            if ($max !== null && $value > $max) {
                throw new OutOfBoundsException("value should be <= $max");
            }
        } else {
            throw new NotFloatException("value should be a correct numeric value");
        }
    }

}