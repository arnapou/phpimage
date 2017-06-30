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

use Arnapou\PHPImage\Exception\OutOfBoundsException;
use Arnapou\PHPImage\Exception\NotIntegerException;

trait TypeCheckerTrait
{
    /**
     * @param string $value
     * @param int    $maxSize
     * @throws NotSizeException
     */
    protected function checkSize(&$value, $maxSize)
    {
        $value = trim("$value");
        if (is_numeric($value)) {
            $value = round(floatval($value));
        } elseif (preg_match('/^\s*([0-9]+)\s*(px)?\s*$/si', $value, $m)) {
            // pixel integer pattern
            $value = $m[1];
        } elseif (preg_match('/^\s*([0-9]+(\.[0-9]+)?)\s*%\s*$/si', $value, $m)) {
            // percent pattern
            $value = round(floatval($m[1]) * $maxSize / 100);
        } else {
            throw new NotSizeException("value should be a correct size value");
        }
    }

    /**
     * @param string $value
     * @param int    $min
     * @param int    $max
     * @throws NotIntegerException
     * @throws OutOfBoundsException
     */
    protected function checkInteger(&$value, $min = null, $max = null)
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
    protected function checkFloat(&$value, $min = null, $max = null)
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

    /**
     * @param string $value
     */
    protected function checkColorInteger(&$value)
    {
        $this->checkInteger($value, 0, 255);
    }

    /**
     * @param string $value
     */
    protected function checkColor(&$value)
    {
        $RGBA = [0, 0, 0, 0];

        if ($value instanceof Color) {
            $RGBA = $value->toArray();
        } elseif (is_array($value)) {
            $i = 0;
            foreach ($value as $val) {
                $RGBA[$i] = $val;
                $i++;
                if ($i > 3) {
                    break;
                }
            }
        } else {
            $color = strtolower(trim($value));

            if (preg_match('/\#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i', $color, $m)) {
                // hex pattern (ie 'fa2bd2' or '#fa2bd2')
                $RGBA[0] = hexdec($m[1]);
                $RGBA[1] = hexdec($m[2]);
                $RGBA[2] = hexdec($m[3]);
                $color = str_replace($m[0], '', $color);
            } elseif (preg_match('/^[^0-9]*([0-9]{1,3})[^0-9]+([0-9]{1,3})[^0-9]+([0-9]{1,3})[^0-9]*/', $color, $m)) {
                // R G B pattern (ie '120,45,200' or '120 45 200')
                $RGBA[0] = $m[1];
                $RGBA[1] = $m[2];
                $RGBA[2] = $m[3];
                $color = str_replace($m[0], '', $color);
            }

            $color = " $color ";
            if (preg_match('/\s*([0-9]+(\.[0-9]+)?)\s*%\s*/si', $color, $m)) {
                // floating alpha
                $RGBA[3] = round(floatval($m[1]) * 127 / 100);
                $color = str_replace($m[0], '', $color);
            } elseif (preg_match('/\s+([0-9]+)\s+/si', $color, $m)) {
                // integer alpha
                $RGBA[3] = intval($m[1]);
                $color = str_replace($m[0], '', $color);
            }

            $color = trim($color);
            if ($color !== '') {
                // check named color if input is not empty (it should be empty if the input is a valid color)
                $RGB = NamedColors::get($color);
                $RGBA[0] = $RGB[0];
                $RGBA[1] = $RGB[1];
                $RGBA[2] = $RGB[2];
            }
        }

        $this->checkColorInteger($RGBA[0]);
        $this->checkColorInteger($RGBA[1]);
        $this->checkColorInteger($RGBA[2]);
        $this->checkColorInteger($RGBA[3]);

        $value = $RGBA;
    }
}