<?php

/*
 * This file is part of the Arnapou PHPImage package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include __DIR__ . '/../src/autoload.php';

spl_autoload_register(function ($class) {
    if (0 === strpos($class, 'Arnapou\PHPImageTest')) {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        if (is_file($filename)) {
            include $filename;
        }
    }
});