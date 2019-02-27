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
use Arnapou\PHPImage\Exception\InvalidPositionException;
use Arnapou\PHPImage\Exception\InvalidRelativeValueException;

class Parser
{
    /**
     * @param string $value
     * @return array
     */
    public function parseColor($value)
    {
        if (\is_array($value)) {
            $value = implode(' ', $value);
        }
        $RGBA = [0, 0, 0, Color::MAX_ALPHA];
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
            $RGBA[3] = round(\floatval($m[1]) * Color::MAX_ALPHA / 100);
            $color = str_replace($m[0], '', $color);
        } elseif (preg_match('/\s+([0-9]+)\s+/si', $color, $m)) {
            // integer alpha
            $RGBA[3] = \intval($m[1]);
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

        return $RGBA;
    }

    /**
     * @param string $value
     * @return array [ value, isPercent ]
     * @throws InvalidRelativeValueException
     */
    public function parseRelativeValue($value)
    {
        $value = trim((string)$value);
        $isPercent = false;
        if (is_numeric($value)) {
            $value = \floatval($value);
        } elseif (preg_match('/^\s*([0-9]+(\.[0-9]+)?)%\s*$/si', $value, $m)) {
            // percent pattern
            $value = \floatval($m[1]);
            $isPercent = true;
        } elseif (preg_match('/^\s*([0-9]+)\s*(px)?\s*$/si', $value, $m)) {
            // pixel integer pattern
            $value = \floatval($m[1]);
        } elseif (stripos($value, 'center') !== false) {
            $value = 50;
            $isPercent = true;
        } elseif (stripos($value, 'top') !== false || stripos($value, 'left') !== false) {
            $value = 0;
            $isPercent = true;
        } elseif (stripos($value, 'bottom') !== false || stripos($value, 'right') !== false) {
            $value = 100;
            $isPercent = true;
        } else {
            throw new InvalidRelativeValueException();
        }
        return [$value, $isPercent];
    }

    /**
     * @param string $value
     * @return array [ x, y ]
     * @throws InvalidPositionException
     */
    public function parsePosition($value)
    {
        if (\is_array($value)) {
            $value = implode(' ', $value);
        }
        $value = str_replace([',', ';'], ' ', trim((string)$value));
        // sanitize
        if (stripos($value, 'center') !== false) {
            $value = str_ireplace('center', '50%', $value);
        }
        if (stripos($value, 'top') !== false) {
            $value = str_ireplace('top', '', $value) . ' 0%';
        } elseif (stripos($value, 'bottom') !== false) {
            $value = str_ireplace('bottom', '', $value) . ' 100%';
        }
        if (stripos($value, 'left') !== false) {
            $value = '0% ' . str_ireplace('left', '', $value);
        } elseif (stripos($value, 'right') !== false) {
            $value = '100% ' . str_ireplace('right', '', $value);
        }
        // final check
        if (preg_match('!([0-9]+(?:\.[0-9]+)?)%\s+([0-9]+(?:\.[0-9]+)?)%!', $value, $m)) {
            return [\floatval($m[1]), \floatval($m[2])];
        }
        throw new InvalidPositionException($value);
    }
}
