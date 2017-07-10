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

use Arnapou\PHPImage\Exception\InvalidPointException;
use Arnapou\PHPImage\Helper\HelperTrait;

class Point
{
    use HelperTrait;
    /**
     * @var RelativeValue
     */
    protected $x;
    /**
     * @var RelativeValue
     */
    protected $y;

    /**
     * Point constructor.
     * @param mixed $point
     */
    public function __construct($point = null)
    {
        $this->x = new RelativeValue();
        $this->y = new RelativeValue();
        if ($point) {
            $this->setPoint($point);
        }
    }

    /**
     * @param int $max
     * @return int
     */
    public function getX($max = null)
    {
        return $this->x->get($max);
    }

    /**
     * @param int $x
     */
    public function setX($x)
    {
        $this->x->set($x);
    }

    /**
     * @param int $max
     * @return int
     */
    public function getY($max = null)
    {
        return $this->y->get($max);
    }

    /**
     * @param int $y
     */
    public function setY($y)
    {
        $this->y->set($y);
    }

    /**
     * @return bool
     */
    public function isPercentX()
    {
        return $this->x->isPercent();
    }

    /**
     * @return bool
     */
    public function isPercentY()
    {
        return $this->y->isPercent();
    }

    /**
     * @param string $point
     * @throws InvalidPointException
     */
    public function setPoint($point)
    {
        if ($point !== null) {
            if ($point instanceof Point) {
                $this->setPoint((string)$point);
            } else {
                if (!is_array($point)) {
                    $point = \str_replace([',', ';'], ' ', $point);
                    $point = \preg_replace('!\s+!s', ' ', trim($point)); // sanitize spaces
                    $point = explode(' ', $point);
                }
                if (count($point) != 2 || !isset($point[0], $point[1])) {
                    throw new InvalidPointException();
                }
                $this->setX($point[0]);
                $this->setY($point[1]);
            }
        }
    }

    /**
     * @param null $maxX
     * @param null $maxY
     * @return array
     */
    public function toArray($maxX = null, $maxY = null)
    {
        return [$this->getX($maxX), $this->getY($maxY)];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return ($this->x) . ' ' . ($this->y);
    }
}