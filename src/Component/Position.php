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

use Arnapou\PHPImage\Exception\InvalidPositionException;
use Arnapou\PHPImage\Helper\HelperTrait;

class Position
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
     * Position constructor.
     * @param mixed $position
     */
    public function __construct($position = null)
    {
        $this->x = new RelativeValue('0%'); // in order to force percent
        $this->y = new RelativeValue('0%'); // in order to force percent
        if ($position) {
            $this->setPosition($position);
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
     * @throws InvalidPositionException
     */
    public function setX($x)
    {
        $this->x->set($x);
        if (!$this->x->isPercent()) {
            throw new InvalidPositionException();
        }
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
     * @throws InvalidPositionException
     */
    public function setY($y)
    {
        $this->y->set($y);
        if (!$this->y->isPercent()) {
            throw new InvalidPositionException();
        }
    }

    /**
     * @param string $position
     * @throws InvalidPositionException
     */
    public function setPosition($position)
    {
        if ($position !== null) {
            if ($position instanceof Position) {
                $this->setPosition((string)$position);
            } else {
                if (\is_array($position)) {
                    if (\count($position) != 2) {
                        throw new InvalidPositionException();
                    }
                    $position = implode(' ', $position);
                }
                $position = $this->parser()->parsePosition((string)$position);

                $this->setX($position[0] . '%');
                $this->setY($position[1] . '%');
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
