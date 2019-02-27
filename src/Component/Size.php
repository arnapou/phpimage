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

use Arnapou\PHPImage\Exception\InvalidSizeException;
use Arnapou\PHPImage\Helper\HelperTrait;

class Size
{
    use HelperTrait;
    /**
     * @var RelativeValue
     */
    protected $w;
    /**
     * @var RelativeValue
     */
    protected $h;

    /**
     * Point constructor.
     * @param mixed $size
     */
    public function __construct($size = null)
    {
        $this->w = new RelativeValue();
        $this->h = new RelativeValue();
        if ($size) {
            $this->setSize($size);
        }
    }

    /**
     * @param int $max
     * @return int
     */
    public function getW($max = null)
    {
        return $this->w->get($max);
    }

    /**
     * @param int $w
     */
    public function setW($w)
    {
        $this->w->set($w);
    }

    /**
     * @param int $max
     * @return int
     */
    public function getH($max = null)
    {
        return $this->h->get($max);
    }

    /**
     * @param int $h
     */
    public function setH($h)
    {
        $this->h->set($h);
    }

    /**
     * @return bool
     */
    public function isPercentW()
    {
        return $this->w->isPercent();
    }

    /**
     * @return bool
     */
    public function isPercentH()
    {
        return $this->h->isPercent();
    }

    /**
     * @param string $size
     * @throws InvalidSizeException
     */
    public function setSize($size)
    {
        if ($size !== null) {
            if ($size instanceof Size) {
                $this->setSize((string)$size);
            } else {
                if (!\is_array($size)) {
                    $size = \str_replace([',', ';'], ' ', $size);
                    $size = \preg_replace('!\s+!s', ' ', trim($size)); // sanitize spaces
                    $size = explode(' ', $size);
                }
                if (\count($size) != 2 || !isset($size[0], $size[1])) {
                    throw new InvalidSizeException();
                }
                $this->setW($size[0]);
                $this->setH($size[1]);
            }
        }
    }

    /**
     * @param null $maxW
     * @param null $maxH
     * @return array
     */
    public function toArray($maxW = null, $maxH = null)
    {
        return [$this->getW($maxW), $this->getH($maxH)];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return ($this->w) . ' ' . ($this->h);
    }
}
