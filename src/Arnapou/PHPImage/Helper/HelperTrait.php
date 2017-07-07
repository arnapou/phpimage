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

/**
 * @codeCoverageIgnore
 */
trait HelperTrait
{
    /**
     * @var array
     */
    private $helpers = [];

    /**
     * @return Parser
     */
    protected function parser()
    {
        if (!isset($this->helpers[__FUNCTION__])) {
            $this->helpers[__FUNCTION__] = new Parser();
        }
        return $this->helpers[__FUNCTION__];
    }

    /**
     * @return GD
     */
    protected function gd()
    {
        if (!isset($this->helpers[__FUNCTION__])) {
            $this->helpers[__FUNCTION__] = new GD();
        }
        return $this->helpers[__FUNCTION__];
    }

    /**
     * @return TypeChecker
     */
    protected function type()
    {
        if (!isset($this->helpers[__FUNCTION__])) {
            $this->helpers[__FUNCTION__] = new TypeChecker();
        }
        return $this->helpers[__FUNCTION__];
    }
}