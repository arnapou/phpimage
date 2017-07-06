<?php

/*
 * This file is part of the Arnapou PHPImage package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function mdToHtml($md)
{
    $html = $md;

    // specific to this project
    $html = preg_replace('!(?:\n|^)[^\n\r]*?https?://phpimage.arnapou.net[^\n\r]*?\n!si', "\n", $html);


    // generic
    $html = preg_replace('!\*\*([^\n\r]+?)\*\*!si', '<strong>$1</strong>', $html);
    $html = preg_replace('!__(.+?)__!si', '<strong>$1</strong>', $html);
    $html = preg_replace('!\*([^\n\r]+?)\*!si', '<em>$1</em>', $html);
    $html = preg_replace('!_(.+?)_!si', '<em>$1</em>', $html);
    $html = preg_replace('!`(.+?)`!si', '<code>$1</code>', $html);
    $html = preg_replace('!`(.+?)`!si', '<code>$1</code>', $html);
    // links
    $html = preg_replace('!\[([^\[]+)\]\(([^\)]+)\)!si', '<a href"$2">$1</a>', $html);
    $html = preg_replace('!([^>])(https?://[^\s]+)!si', '$1<a href="$2">$2</a>', $html);
    // ul
    $html = preg_replace('!\n\*(.*?)\n!si', "\n" . '<ul><li>$1</li></ul>' . "\n", $html);
    $html = preg_replace('!\n\*(.*?)\n!si', "\n" . '<ul><li>$1</li></ul>' . "\n", $html);
    // ol
    $html = preg_replace('!\n[0-9]+(.*?)\n!si', "\n" . '<ol><li>$1</li></ol>' . "\n", $html);
    $html = preg_replace('!\n[0-9]+(.*?)\n!si', "\n" . '<ol><li>$1</li></ol>' . "\n", $html);
    // blockquote
    $html = preg_replace('!\n(&gt;|\>)(.*?)\n!si', "\n" . '<blockquote>$1</blockquote>' . "\n", $html);
    $html = preg_replace('!\n(&gt;|\>)(.*?)\n!si', "\n" . '<blockquote>$1</blockquote>' . "\n", $html);
    // pre
    $html = preg_replace('!\n    (.*?)\n!si', "\n" . '<pre>$1</pre>' . "\n", $html);
    $html = preg_replace('!\n    (.*?)\n!si', "\n" . '<pre>$1</pre>' . "\n", $html);
    // hr
    $html = preg_replace('!\n-{5,}!si', "\n<hr />", $html);
    // h1
    $html = preg_replace('!(?:\n|^)([^<\n\r][^\n\r]*)\n={5,}\n!si', '<h1>$1</h1>', $html);
    // paragraph
    $html = preg_replace('!(?:\n|^)([^<\n\r][^\n\r]*)\n!si', '<p>$1</p>', $html);
    // fixes
    $html = preg_replace('!</ul>\s*<ul>!si', '', $html);
    $html = preg_replace('!</ol>\s*<ol>!si', '', $html);
    $html = preg_replace('!</blockquote>\s*<blockquote>!si', '', $html);
    $html = preg_replace('!</pre>\s*<pre>!si', "\n", $html);

    return $html;
}

/**
 *
 * @param callable $func
 * @return string
 */
function getFunctionSourceCode($func)
{
    $reflection = new ReflectionFunction($func);
    $filename = $reflection->getFileName();
    $start_line = $reflection->getStartLine();
    $end_line = $reflection->getEndLine() - 1;
    $length = $end_line - $start_line;

    $lines = file($filename);
    $lines = array_slice($lines, $start_line, $length);
    $lines = array_map(function ($s) {
        return substr($s, 4);
    }, $lines);
    if (count($lines) && strpos($lines[0], 'return ') === 0) {
        $lines[0] = substr($lines[0], 7);
    }
    return implode("", $lines);
}
