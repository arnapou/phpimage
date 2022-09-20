<?php

$header = <<<HEADER
This file is part of the PHPImage - PHP Drawing package.

(c) Arnaud Buathier <arnaud@arnapou.net>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

$finder = (new PhpCsFixer\Finder())
    //->notPath('public')
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setRules(
        [
            '@PSR1' => true,
            '@PSR2' => true,
            '@PSR12' => true,
            'phpdoc_indent' => true,
            'phpdoc_order' => true,
            'phpdoc_trim' => true,
            'header_comment' => ['header' => $header],
        ]
    )
    ->setFinder($finder);
