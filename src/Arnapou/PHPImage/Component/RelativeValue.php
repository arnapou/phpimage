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

use Arnapou\PHPImage\Helper\HelperTrait;

class RelativeValue
{
    use HelperTrait;
    /**
     * @var int
     */
    protected $value = 0;
    /**
     * @var bool
     */
    protected $isPercent = false;

    /**
     * Point constructor.
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        if ($value) {
            $this->setValue($value);
        }
    }

    /**
     * @param int $max
     * @return int
     */
    public function get($max = null)
    {
        if ($max && $this->isPercent) {
            return round($max * $this->value / 100);
        }
        return round($this->value);
    }

    /**
     * @param mixed $value
     */
    public function set($value)
    {
        $parsed = $this->parser()->parseRelativeValue($value);
        $this->value = $parsed[0];
        $this->isPercent = $parsed[1];
    }

    /**
     * @return bool
     */
    public function isPercent()
    {
        return $this->isPercent;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $str = $this->value;
        if ($this->isPercent) {
            $str .= '%';
        }
        return (string)$str;
    }
}