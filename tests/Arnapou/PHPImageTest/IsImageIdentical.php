<?php
/*
 * This file is part of the Arnapou PHPImage package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PHPImageTest;

use PHPUnit\Framework\Constraint\Constraint;

class IsImageIdentical extends Constraint
{

    const IDENTICAL = 'identical images';
    const DIFFERENT = 'different images';

    /**
     * @param mixed $other
     * @return bool
     */
    protected function matches($other)
    {
        return $other === self::IDENTICAL;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'is identical';
    }
}
