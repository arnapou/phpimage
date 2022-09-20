<?php

/*
 * This file is part of the PHPImage - PHP Drawing package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * PHPImageTools class
 */
class PHPImageTools
{
    /**
     * @return array
     */
    public static function linestylenames()
    {
        return array('solid', 'dot', 'square', 'dash', 'bigdash', 'double', 'triple');
    }

    /**
     * @return array
     */
    public static function shapestylenames()
    {
        return array(
            'biseau',
            'biseau1',
            'biseau2',
            'biseau3',
            'biseau4',
            'round',
            'round1',
            'round2',
            'curve',
            'curve1',
            'curve2',
            'curve3',
            'curve4',
            'curve5',
            'curve6',
            'trait',
            'trait1',
            'trait2',
            'trait3',
            'empty',
            'none'
        );
    }

    /**
     * @param string $color
     */
    public static function checkcolor(&$color)
    {
        global $PHPImageColors;
        $save = $color;
        $color = strtolower(trim($color));
        $RGBA = array(0, 0, 0, 0);
        $patterns = array(
            '/\#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i',
            '/^[^0-9]*([0-9]{1,3})[^0-9]+([0-9]{1,3})[^0-9]+([0-9]{1,3})[^0-9]*/',
            '/\s*([0-9]+(\.[0-9]+)?)\s*%\s*/si',
            '/\s+([0-9]+)\s+/si'
        );
        if (preg_match($patterns[0], $color, $m)) {
            $RGBA = array(hexdec($m[1]), hexdec($m[2]), hexdec($m[3]));
            $color = preg_replace($patterns[0], '', $color);
        } elseif (preg_match($patterns[1], $color, $m)) {
            $RGBA = array($m[1], $m[2], $m[3]);
            PHPImageTools::checkinteger('Red color', $RGBA[0], 0, 255);
            PHPImageTools::checkinteger('Green color', $RGBA[1], 0, 255);
            PHPImageTools::checkinteger('Blue color', $RGBA[2], 0, 255);
            $color = preg_replace($patterns[1], '', $color);
        }
        if (preg_match($patterns[2], $color, $m)) {
            $alpha = round(floatval($m[1]) * 127 / 100);
            $color = preg_replace($patterns[2], '', $color);
        } elseif (preg_match($patterns[3], " $color ", $m)) {
            $alpha = intval($m[1]);
            $color = preg_replace($patterns[3], '', " $color ");
        } else {
            $alpha = 0;
        }
        PHPImageTools::checkinteger('Alpha value', $alpha, 0, 127);
        $color = trim($color);
        if ($color != '') {
            if (array_key_exists($color, $PHPImageColors)) {
                $RGBA = $PHPImageColors[$color];
            } else {
                throw new PHPImageException("The color '$save' is not a valid color !");
            }
        }
        $RGBA[] = $alpha;
        $color = $RGBA;
        return $save;
    }

    /**
     * @param mixed $img
     * @param mixed $angle
     * @param mixed $bgcolor
     * @return mixed
     */
    public static function imagerotation(&$img, $angle, $bgcolor = null)
    {
        $angle = -$angle;
        PHPImageTools::setangle360($angle);
        switch ($angle) {
            case 0:
                $w = imagesx($img);
                $h = imagesy($img);
                $newimg = new PHPImage($w, $h);
                $newimg->alphablending(false);
                for ($y = 0; $y < $h; $y++) {
                    for ($x = 0; $x < $w; $x++) {
                        imagesetpixel($newimg->img, $x, $y, imagecolorat($img, $x, $y));
                    }
                }
                $newimg->alphablending(true);
                return $newimg;
                break;
            case 270:
                $w = imagesx($img);
                $h = imagesy($img);
                $newimg = new PHPImage($h, $w);
                $newimg->alphablending(false);
                for ($y = 0; $y < $h; $y++) {
                    for ($x = 0; $x < $w; $x++) {
                        imagesetpixel($newimg->img, $y, $w - $x - 1, imagecolorat($img, $x, $y));
                    }
                }
                $newimg->alphablending(true);
                return $newimg;
                break;
            case 180:
                $w = imagesx($img);
                $h = imagesy($img);
                $newimg = new PHPImage($w, $h);
                $newimg->alphablending(false);
                for ($y = 0; $y < $h; $y++) {
                    for ($x = 0; $x < $w; $x++) {
                        imagesetpixel($newimg->img, $w - $x - 1, $h - $y - 1, imagecolorat($img, $x, $y));
                    }
                }
                $newimg->alphablending(true);
                return $newimg;
                break;
            case 90:
                $w = imagesx($img);
                $h = imagesy($img);
                $newimg = new PHPImage($h, $w);
                $newimg->alphablending(false);
                for ($y = 0; $y < $h; $y++) {
                    for ($x = 0; $x < $w; $x++) {
                        imagesetpixel($newimg->img, $h - $y - 1, $x, imagecolorat($img, $x, $y));
                    }
                }
                $newimg->alphablending(true);
                return $newimg;
                break;
            default:
                $coss = cos(M_PI * $angle / 180);
                $sins = sin(M_PI * $angle / 180);
                $w = imagesx($img);
                $h = imagesy($img);
                $w2 = $w / 2;
                $h2 = $h / 2;
                $p1x = $w * $coss - $h * $sins;
                $p1y = $w * $sins + $h * $coss;
                $p2x = $w * $coss;
                $p2y = $w * $sins;
                $p3x = -$h * $sins;
                $p3y = $h * $coss;
                $W = round(max($p1x, $p2x, $p3x, 0) - min($p1x, $p2x, $p3x, 0));
                $H = round(max($p1y, $p2y, $p3y, 0) - min($p1y, $p2y, $p3y, 0));
                $W2 = $W / 2;
                $H2 = $H / 2;
                $newimg = new PHPImage($W, $H);
                $newimg->alphablending(false);
                for ($y = 0; $y < $h; $y++) {
                    for ($x = 0; $x < $w; $x++) {
                        $dx = $x - $w2;
                        $dy = $y - $h2;
                        $xx = round($W2 + $dx * $coss - $dy * $sins);
                        $yy = round($H2 + $dx * $sins + $dy * $coss);
                        imagesetpixel($newimg->img, $xx, $yy, imagecolorat($img, $x, $y));
                    }
                }
                $newimg->alphablending(true);
                return $newimg;
                break;
        }
    }

    /**
     * http://www.cs.rit.edu/~icss571/filling/how_to.html
     *
     * @param mixed $img
     * @param mixed $points
     * @param mixed $color
     */
    public static function imagefilledpolygon(&$img, $points, $color)
    {
        $scanline = 99999;
        //foreach($points as $point) { imagesetpixel($img, $point[0], $point[1], $color); }
        // compute edges and find starting scanline
        $all_edges = array();
        $n = count($points);
        for ($i = 0; $i < $n; $i++) {
            $p1 = $points[$i];
            if ($i == $n - 1) {
                $p2 = $points[0];
            } else {
                $p2 = $points[$i + 1];
            }
            $x1 = $p1[0];
            $y1 = $p1[1];
            $x2 = $p2[0];
            $y2 = $p2[1];
            if ($y1 != $y2) {
                $invslope = ($x2 - $x1) / ($y2 - $y1);
                if ($y1 < $y2) {
                    $ymin = $y1;
                    $xval = $x1;
                    $ymax = $y2;
                } else {
                    $ymin = $y2;
                    $xval = $x2;
                    $ymax = $y1;
                }
                $all_edges[] = array($ymin, $ymax, $xval, $invslope);
                if ($ymin < $scanline) {
                    $scanline = $ymin;
                }
            } else {
                if ($y1 < $scanline) {
                    $scanline = $y1;
                }
                if ($y2 < $scanline) {
                    $scanline = $y2;
                }
            }
        }
        $save_edges = $all_edges;
        // draw
        $active = array();
        $pixels = array();
        while (count($all_edges) + count($active) > 0) {
            // add edges to active array
            $tmp = array();
            $n = count($all_edges);
            $added = false;
            for ($i = 0; $i < $n; $i++) {
                if ($all_edges[$i][0] == $scanline) {
                    $active[] = $all_edges[$i];
                    $added = true;
                } else {
                    $tmp[] = $all_edges[$i];
                }
            }
            $all_edges = $tmp;
            // remove previous edges from active array
            $tmp = array();
            $n = count($active);
            for ($i = 0; $i < $n; $i++) {
                if ($active[$i][1] > $scanline) {
                    $tmp[] = $active[$i];
                    // } elseif($active[$i][1] == $scanline && !$added) {
                    // $tmp[] = $active[$i];
                }
            }
            $active = $tmp;
            // sort active array
            $n = count($active);
            for ($i = 0; $i < $n - 1; $i++) {
                $min = $i;
                for ($k = $i + 1; $k < $n; $k++) {
                    if ($active[$k][2] < $active[$min][2]) {
                        $min = $k;
                    }
                }
                if ($i != $min) {
                    $tmp = $active[$i];
                    $active[$i] = $active[$min];
                    $active[$min] = $tmp;
                }
            }
            // get segments
            $pixels[$scanline] = array();
            $n = count($active);
            for ($i = 0; $i < $n; $i += 2) {
                if ($i + 1 < $n) {
                    if ($active[$i][2] == $active[$i + 1][2]) {
                        $x1 = intval(round($active[$i][2]));
                        $pixels[$scanline][] = array($x1, $x1);
                    } else {
                        $x1 = intval(round($active[$i][2]));
                        $x2 = intval(round($active[$i + 1][2]));
                        $pixels[$scanline][] = array($x1, $x2);
                    }
                }
            }
            // manage segments
            $ok = true;
            $tmp = array();
            $n = count($pixels[$scanline]);
            for ($i = 0; $i < $n - 1; $i++) {
                list($x1, $x2) = $pixels[$scanline][$i];
                do {
                    $i++;
                    $ok = false;
                    list($xx1, $xx2) = $pixels[$scanline][$i];
                    if ($x2 >= $xx1) {
                        $x2 = $xx2;
                        $ok = true;
                        if ($i == $n - 1) {
                            $i++;
                        }
                    }
                } while ($ok && $i < $n - 1);
                $i--;
                $tmp[] = array($x1, $x2);
            }
            if ($i == $n - 1) {
                list($x1, $x2) = $pixels[$scanline][$n - 1];
                $tmp[] = array($x1, $x2);
            }
            $pixels[$scanline] = $tmp;
            // draw
            foreach ($pixels[$scanline] as $segment) {
                if ($segment[0] == $segment[1]) {
                    imagesetpixel($img, $segment[0], $scanline, $color);
                } else {
                    imageline($img, $segment[0], $scanline, $segment[1], $scanline, $color);
                }
            }
            // increment x values
            $n = count($active);
            for ($i = 0; $i < $n; $i++) {
                $active[$i][2] += $active[$i][3];
            }
            $scanline++;
        }
        // dessine les pixels de bordure non pris en compte
        $n = count($points);
        for ($i = 0; $i < $n; $i++) {
            $p1 = $points[$i];
            if ($i == $n - 1) {
                $p2 = $points[0];
            } else {
                $p2 = $points[$i + 1];
            }
            $x1 = $p1[0];
            $y1 = $p1[1];
            $x2 = $p2[0];
            $y2 = $p2[1];
            if ($y1 == $y2) {
                if ($x1 > $x2) {
                    PHPImageTools::switchvar($x1, $x2);
                }
                $draw = array();
                foreach ($pixels[$y1] as $segment) {
                    list($xx1, $xx2) = $segment;
                    for ($x = $xx1; $x <= $xx2; $x++) {
                        $draw[$x] = true;
                    }
                }
                $s = null;
                for ($x = intval($x1); $x <= $x2; $x++) {
                    if (array_key_exists($x, $draw)) {
                        if ($s !== null) {
                            $pixels[$y1][] = array($s, $x - 1);
                            $s = null;
                        }
                    } else {
                        if ($s === null) {
                            $s = $x;
                        }
                        imagesetpixel($img, $x, $y1, $color);
                    }
                }
                if ($s !== null) {
                    $pixels[$y1][] = array($s, $x - 1);
                }
            }
        }
        foreach ($save_edges as $edge) {
            list($ymin, $ymax, $xval, $invslope) = $edge;
            for ($y = intval($ymin); $y <= $ymax; $y++) {
                $x = intval(round($xval));
                if (array_key_exists($y, $pixels)) {
                    $draw = array();
                    foreach ($pixels[$y] as $segment) {
                        list($xx1, $xx2) = $segment;
                        for ($k = $xx1; $k <= $xx2; $k++) {
                            $draw[$k] = true;
                        }
                    }
                    if (!array_key_exists($x, $draw)) {
                        imagesetpixel($img, $x, $y, $color);
                        $pixels[$y][] = array($x, $x);
                    }
                } else {
                    imagesetpixel($img, $x, $y, $color);
                    $pixels[$y][] = array($x, $x);
                }
                $xval += $invslope;
            }
        }
    }

    /**
     * @param mixed $w
     * @param mixed $h
     * @param mixed $start
     * @param mixed $end
     * @return mixed
     */
    public static function imageellipsearc_points($w, $h, $start, $end)
    {
        $q1 = $q2 = $q3 = $q4 = array();
        $a = floor($w / 2);
        $b = floor($h / 2);
        if ($w % 2 == 0) {
            $a--;
        }
        if ($h % 2 == 0) {
            $b--;
        }
        $x = 0;
        $y = $b;
        $d1 = $b * $b - $a * $a * $b + $a * $a / 4;
        $q1[] = array($x, $y);
        $q2[] = array(-$x, $y);
        $q3[] = array(-$x, -$y);
        $q4[] = array($x, -$y);
        while ($a * $a * ($y - .5) > $b * $b * ($x + 1)) {
            if ($d1 < 0) {
                $d1 += $b * $b * (2 * $x + 3);
                $x++;
            } else {
                $d1 += $b * $b * (2 * $x + 3) + $a * $a * (-2 * $y + 2);
                $x++;
                $y--;
            }
            $q1[] = array($x, $y);
            $q2[] = array(-$x, $y);
            $q3[] = array(-$x, -$y);
            $q4[] = array($x, -$y);
        }
        $d2 = $b * $b * ($x + .5) * ($x + .5) + $a * $a * ($y - 1) * ($y - 1) - $a * $a * $b * $b;
        while ($y > 0) {
            if ($d2 < 0) {
                $d2 += $b * $b * (2 * $x + 2) + $a * $a * (-2 * $y + 3);
                $y--;
                $x++;
            } else {
                $d2 += $a * $a * (-2 * $y + 3);
                $y--;
            }
            $q1[] = array($x, $y);
            $q2[] = array(-$x, $y);
            $q3[] = array(-$x, -$y);
            $q4[] = array($x, -$y);
        }
        $q = array_merge(array_reverse($q1), $q2, array_reverse($q3), $q4);
        $i = 0;
        $n = count($q);
        $tour = 0;
        $r = array();
        do {
            $a = PHPImageTools::lineangle(0, 0, $q[$i][0], $q[$i][1]);
            if ($a < 0) {
                $a += 360;
            }
            if ($a >= $start) {
                break;
            }
            $i++;
        } while ($i < $n);
        if ($i >= $n) {
            $i = 0;
        }
        $d = 0;
        do {
            $a = PHPImageTools::lineangle(0, 0, $q[$i][0], $q[$i][1]);
            if ($a < 0) {
                $a += 360;
            }
            $a += $d;
            if ($a <= $end) {
                $r[] = $q[$i];
            } else {
                break;
            }
            $i++;
            if ($i >= $n) {
                $i = 0;
                $d += 360;
            }
        } while (true);
        $l = array();
        $n = count($r);
        if ($n > 0) {
            $l[] = $r[0];
            for ($i = 1; $i < $n; $i++) {
                if ($r[$i][0] != $r[$i - 1][0] || $r[$i][1] != $r[$i - 1][1]) {
                    $l[] = $r[$i];
                }
            }
        }
        return $l;
    }

    /**
     * @param mixed $color
     * @param mixed $alpha
     * @param mixed $bgcolor
     * @param mixed $style
     * @param mixed $shadow
     * @param mixed $shadow_x
     * @param mixed $shadow_y
     * @param mixed $shadow_color
     * @param mixed $shadow_bgcolor
     * @param mixed $shadow_alpha
     * @param mixed $shadow_blur
     */
    public static function writetext_style_shadow(
        $color,
        $alpha,
        $bgcolor,
        &$style,
        &$shadow,
        &$shadow_x,
        &$shadow_y,
        &$shadow_color,
        &$shadow_bgcolor,
        &$shadow_alpha,
        &$shadow_blur
    ) {
        $shadow = false;
        if (preg_match('/shadow/i', $style)) {
            $shadow = true;
            $shadow_x = 2;
            $shadow_y = 2;
            $shadow_color = '#303030';
            $shadow_bgcolor = $bgcolor;
            $shadow_alpha = 0;
            $shadow_blur = true;
            if (preg_match('/shadow\((.*?)\)/i', $style, $m)) {
                $items = explode(',', $m[1]);
                foreach ($items as $item) {
                    if (preg_match('/^-?[0-9]+x$/i', $item)) {
                        $shadow_x = $item;
                    } elseif (preg_match('/^-?[0-9]+y$/i', $item)) {
                        $shadow_y = $item;
                    } elseif (preg_match('/^[0-9]+%$/i', $item)) {
                        $item = substr($item, 0, -1);
                        $shadow_alpha = floor(($item > 100 ? 100 : $item) * 127 / 100);
                    } elseif (preg_match('/^bg:.*$/i', $item)) {
                        $shadow_bgcolor = substr($item, 3);
                    } elseif (preg_match('/^noblur$/i', $item)) {
                        $shadow_blur = false;
                    } else {
                        $shadow_color = $item;
                    }
                }
            }
        }
    }

    /**
     * @param mixed $color
     * @param mixed $alpha
     * @param mixed $bgcolor
     * @param mixed $style
     * @param mixed $underline
     * @param mixed $underline_style
     * @param mixed $underline_thickness
     * @param mixed $underline_color
     * @param mixed $underline_alpha
     */
    public static function writetext_style_underline(
        $color,
        $alpha,
        $bgcolor,
        &$style,
        &$underline,
        &$underline_style,
        &$underline_thickness,
        &$underline_color,
        &$underline_alpha
    ) {
        $underline = false;
        if (preg_match('/underline/i', $style)) {
            $underline = true;
            $underline_style = 'solid';
            $underline_thickness = 1;
            $underline_color = $color;
            $underline_alpha = $alpha;
            if (preg_match('/underline\s*\((.*?)\)/i', $style, $m)) {
                $items = explode(',', $m[1]);
                foreach ($items as $item) {
                    if (in_array($item, array('dot', 'square', 'dash', 'bigdash', 'double', 'triple'))) {
                        $underline_style = $item;
                    } elseif (preg_match('/^[0-9]+px$/', $item)) {
                        $underline_thickness = substr($item, 0, -2);
                    } elseif (preg_match('/^[0-9]+%$/i', $item)) {
                        $item = substr($item, 0, -1);
                        $underline_alpha = floor(($item > 100 ? 100 : $item) * 127 / 100);
                    } else {
                        $underline_color = $item;
                    }
                }
            }
        }
    }

    /**
     * @param mixed $color
     * @param mixed $alpha
     * @param mixed $bgcolor
     * @param mixed $style
     * @param mixed $align
     */
    public static function writetext_style($color, $alpha, $bgcolor, &$style, &$align)
    {
        $align = 'left';
        $style = preg_replace('/\s*,\s*/si', ',', $style);
        $style = preg_replace('/\s*\(\s*/si', '(', $style);
        $style = preg_replace('/\s*\)\s*/si', ')', $style);
        $style = preg_replace('/\s*:\s*/si', ':', $style);
        if (preg_match('/right/i', $style)) {
            $align = 'right';
        }
        if (preg_match('/center/i', $style)) {
            $align = 'center';
        }
    }

    /**
     * @param mixed $cx
     * @param mixed $cy
     * @param mixed $text
     * @param mixed $fontsize
     * @param mixed $angle
     * @param mixed $position
     * @param mixed $linespacing
     * @param mixed $font
     * @param mixed $lines
     * @param mixed $sizes
     * @param mixed $W
     * @param mixed $H
     * @param mixed $tlx
     * @param mixed $tly
     * @param mixed $lineH
     */
    public static function writetext_init(
        $cx,
        $cy,
        $text,
        $fontsize,
        &$angle,
        &$position,
        &$linespacing,
        &$font,
        &$lines,
        &$sizes,
        &$W,
        &$H,
        &$tlx,
        &$tly,
        &$lineH
    ) {
        PHPImageTools::setangle360($angle);
        $lines = explode("\n", preg_replace('/\r|\t/si', '', $text));
        $posx = 'top';
        $posy = 'left';
        if (preg_match('/center|middle/i', $position)) {
            $posx = 'center';
            $posy = 'center';
        }
        if (preg_match('/left/i', $position)) {
            $posx = 'left';
        }
        if (preg_match('/right/i', $position)) {
            $posx = 'right';
        }
        if (preg_match('/top/i', $position)) {
            $posy = 'top';
        }
        if (preg_match('/bottom/i', $position)) {
            $posy = 'bottom';
        }
        $position = "$posx $posy";
        $N = count($lines);
        $W = 0;
        $lineH = 0;
        $sizes = array();
        for ($i = 0; $i < $N; $i++) {
            $size = new PHPImageTTFBox($lines[$i], $fontsize, $font, 0);
            if ($size->width > $W) {
                $W = $size->width + 1;
            }
            // if($size->height > $lineH) { $lineH = $size->height	; }
            $sizes[$i] = $size;
        }
        $size = new PHPImageTTFBox('abcdefghijklmnopqrstuvyxyzABCDEFGHIJKLMNOPQRSTUVYXYZ0123456789,;:!&�"\'(-�_��)=?./�%���+�~#{[|`\^@]}', $fontsize, $font, 0);
        $lineH = $size->height;
        if ($linespacing === '') {
            $linespacing = 0; /*floor(0.1*$lineH);*/
        }
        $H = $N * $lineH + ($N - 1) * $linespacing + 1;
        if (is_int($posx)) {
            $cx += $posx;
        }
        if (is_int($posy)) {
            $cy += $posy;
        }
        $tlx = $cx;
        $tly = $cy;
        switch ($position) {
            case 'left top':
                $tlx = $cx;
                $tly = $cy;
                break;
            case 'left center':
                $tlx = $cx;
                $tly = $cy - $H / 2;
                break;
            case 'left bottom':
                $tlx = $cx;
                $tly = $cy - $H;
                break;
            case 'center top':
                $tlx = $cx - $W / 2;
                $tly = $cy;
                break;
            case 'center center':
                $tlx = $cx - $W / 2;
                $tly = $cy - $H / 2;
                break;
            case 'center bottom':
                $tlx = $cx - $W / 2;
                $tly = $cy - $H;
                break;
            case 'right top':
                $tlx = $cx - $W;
                $tly = $cy;
                break;
            case 'right center':
                $tlx = $cx - $W;
                $tly = $cy - $H / 2;
                break;
            case 'right bottom':
                $tlx = $cx - $W;
                $tly = $cy - $H;
                break;
        }
    }

    /**
     * @param mixed $img
     * @param mixed $cx
     * @param mixed $cy
     * @param mixed $w
     * @param mixed $h
     * @param mixed $start
     * @param mixed $end
     * @param mixed $color
     * @param mixed $thickness
     */
    public static function imageellipsearc(&$img, $cx, $cy, $w, $h, $start, $end, $color, $thickness)
    {
        PHPImageTools::setangle360($start, $end);
        $n = floor(abs($end - $start) * PHPImageTools::ellipseperimeter($w, $h) / 360);
        $w = round($w);
        $h = round($h);
        if ($n >= 2) {
            $points = array();
            if ($thickness > 1) {
                PHPImageTools::gete1e2($e1, $e2, $thickness);
                if ($thickness % 2 == 0) {
                    $e1--;
                } else {
                    $e2--;
                }
                $list = array();
                $points = PHPImageTools::imageellipsearc_points($w + 2 * $e1, $h + 2 * $e1, $start, $end);
                $n = count($points);
                for ($i = 0; $i < $n; $i++) {
                    $list[] = array($cx + $points[$i][0], $cy + $points[$i][1]);
                }
                $points = array_reverse(PHPImageTools::imageellipsearc_points($w - 2 * $e2, $h - 2 * $e2, $start, $end));
                $n = count($points);
                for ($i = 0; $i < $n; $i++) {
                    $list[] = array($cx + $points[$i][0], $cy + $points[$i][1]);
                }
                PHPImageTools::imagefilledpolygon($img, $list, $color);
            } else {
                $points = PHPImageTools::imageellipsearc_points($w, $h, $start, $end);
                $n = count($points);
                for ($i = 0; $i < $n; $i++) {
                    imagesetpixel($img, $cx + $points[$i][0], $cy + $points[$i][1], $color);
                }
            }
        } else {
            imageline(
                $img,
                $cx + 0.5 * $w * cos(PHPImageTools::deg2rad($end)),
                $cy + 0.5 * $h * sin(PHPImageTools::deg2rad($end)),
                $cx + 0.5 * $w * cos(PHPImageTools::deg2rad($start)),
                $cy + 0.5 * $h * sin(PHPImageTools::deg2rad($start)),
                $color
            );
        }
    }

    /**
     * @param mixed $img
     * @param mixed $cx
     * @param mixed $cy
     * @param mixed $w
     * @param mixed $h
     * @param mixed $color
     * @param mixed $thickness
     */
    public static function imageellipse(&$img, $cx, $cy, $w, $h, $color, $thickness)
    {
        $n = floor(PHPImageTools::ellipseperimeter($w, $h));
        $w = round($w);
        $h = round($h);
        if ($n >= 2) {
            if ($thickness > 1) {
                $points = array();
                $a = 0;
                $dangle = PHPImageTools::deg2rad(360 / ($n - 1));
                for ($i = 0; $i < $n; $i++) {
                    $points[] = round($cx + 0.5 * ($w + $thickness) * cos($a));
                    $points[] = round($cy + 0.5 * ($h + $thickness) * sin($a));
                    $a += $dangle;
                }
                $a -= $dangle;
                for ($i = 0; $i < $n; $i++) {
                    $points[] = round($cx + 0.5 * ($w - $thickness) * cos($a));
                    $points[] = round($cy + 0.5 * ($h - $thickness) * sin($a));
                    $a -= $dangle;
                }
                imagefilledpolygon($img, $points, 2 * $n, $color);
            } else {
                imageellipse($img, $cx, $cy, $w, $h, $color);
            }
        } else {
            imageellipse($img, $cx, $cy, $w, $h, $color);
        }
    }

    /**
     * @param mixed $img
     * @param mixed $x1
     * @param mixed $y1
     * @param mixed $x2
     * @param mixed $y2
     * @param mixed $color
     * @param mixed $thickness
     * @return mixed
     */
    public static function imageline(&$img, $x1, $y1, $x2, $y2, $color, $thickness)
    {
        $thickness = $thickness < 1 ? 1 : $thickness;
        if ($thickness == 1) {
            imageline($img, $x1, $y1, $x2, $y2, $color);
            return;
        }
        $e = $thickness / 2 - 0.5;
        if ($x1 == $x2) {
            imagefilledrectangle(
                $img,
                round(min($x1, $x2) - $e),
                round(min($y1, $y2)),
                round(max($x1, $x2) + $e),
                round(max($y1, $y2)),
                $color
            );
        } elseif ($y1 == $y2) {
            imagefilledrectangle(
                $img,
                round(min($x1, $x2)),
                round(min($y1, $y2) - $e),
                round(max($x1, $x2)),
                round(max($y1, $y2) + $e),
                $color
            );
        } else {
            $k = ($y2 - $y1) / ($x2 - $x1); // y = kx + q
            $dx = $e / sqrt(1 + $k * $k);
            $dy = $e / sqrt(1 + 1 / ($k * $k));
            $dy *= ($y2 - $y1) / abs($y2 - $y1);
            $points = array(
                round($x1 - $dy),
                round($y1 + $dx),
                round($x1 + $dy),
                round($y1 - $dx),
                round($x2 + $dy),
                round($y2 - $dx),
                round($x2 - $dy),
                round($y2 + $dx),
            );
            if ($points[0] == $points[2] && $points[1] == $points[3] &&
                $points[4] == $points[6] && $points[5] == $points[7]) {
                imageline($img, $x1, $y1, $x2, $y2, $color);
            } else {
                imagefilledpolygon($img, $points, 4, $color);
            }
        }
    }

    /**
     * @param mixed $paramname
     * @param mixed $value
     * @return mixed
     */
    public static function checkshapestyle($paramname, &$value)
    {
        $save = $value;
        PHPImageTools::checkparams($paramname, $value);
        $S = array(
            'tl' => array('forme' => 'none', 'w' => 0, 'h' => 0),
            'tr' => array('forme' => 'none', 'w' => 0, 'h' => 0),
            'bl' => array('forme' => 'none', 'w' => 0, 'h' => 0),
            'br' => array('forme' => 'none', 'w' => 0, 'h' => 0),
        );
        foreach ($value as $param => $items) {
            $tmp = array('forme' => 'none', 'w' => 0, 'h' => 0);
            foreach ($items as $item) {
                if (preg_match('/^([0-9]+)(\s*px)?$/', $item, $m)) {
                    if ($tmp['w'] == 0) {
                        $tmp['h'] = $tmp['w'] = $m[1];
                    } else {
                        $tmp['h'] = $m[1];
                    }
                } else {
                    // if(!in_array($item, PHPImageTools::shapestylenames())) {
                    // throw new PHPImageException("$paramname option '$item' is unknown !");
                    // }
                    $tmp['forme'] = $item;
                }
            }
            switch ($param) {
                case 'all':
                    $S['tl'] = $S['tr'] = $S['bl'] = $S['br'] = $tmp;
                    break;
                case 'top':
                    $S['tl'] = $S['tr'] = $tmp;
                    break;
                case 'bottom':
                    $S['bl'] = $S['br'] = $tmp;
                    break;
                case 'left':
                    $S['bl'] = $S['tl'] = $tmp;
                    break;
                case 'right':
                    $S['br'] = $S['tr'] = $tmp;
                    break;
                case 'tl':
                case 'tr':
                case 'bl':
                case 'br':
                    $S[$param] = $tmp;
                    break;
                default:
                    throw new PHPImageException("$paramname parameter '$param' is unknown !");
            }
        }
        $value = $S;
        return $save;
    }

    /**
     * @param mixed $n1
     * @param mixed $n2
     * @param mixed $nb
     * @param mixed $n
     * @param mixed $stylename
     * @param mixed $thickness
     * @param mixed $length
     * @param mixed $strict
     */
    public static function getn1n2(&$n1, &$n2, &$nb, &$n, $stylename, $thickness, $length, $strict = true)
    {
        switch ($stylename) {
            case 'dash':
                $n1 = 3;
                $n2 = 2;
                break;
            case 'bigdash':
                $n1 = 6;
                $n2 = 2;
                break;
            case 'dot':
            case 'square':
            default:
                $n1 = 1;
                $n2 = 1;
                break;
        }
        $nb = ceil($length / $thickness);
        if ($strict) {
            $n = floor(($nb + $n2) / ($n1 + $n2));
        } else {
            switch ($stylename) {
                case 'dot':
                    $n = floor(($nb + $n2) / ($n1 + $n2));
                    break;
                default:
                    $n = floor(($nb + $n2) / ($n1 + $n2 + 1));
                    break;
            }
        }
        if ($n > 1) {
            $n2 = ($nb - $n * $n1) / ($n - 1);
        }
    }

    /**
     * @param mixed $e1
     * @param mixed $e2
     * @param mixed $thickness
     */
    public static function gete1e2(&$e1, &$e2, $thickness)
    {
        $e1 = floor($thickness / 2);
        $e2 = $thickness - $e1;
    }

    /**
     * @param mixed $paramname
     * @param mixed $value
     * @return mixed
     */
    public static function checklinestyle($paramname, &$value)
    {
        $save = $value;
        if (is_array($value)) {
            $tmp = array();
            foreach ($value as $color) {
                if (is_string($color)) {
                    if ($color == '') {
                        $tmp[] = '';
                    } else {
                        PHPImageTools::checkcolor($color);
                        $tmp[] = $color;
                    }
                } else {
                    throw new PHPImageException("$paramname parameter should be an array of STRINGS !");
                }
            }
            $value = $tmp;
        } else {
            PHPImageTools::checkparams($paramname, $value);
            if (count($value) == 0) {
                $value = 'solid';
            } elseif (count($value) != 1) {
                throw new PHPImageException("$paramname parameter should have only one word !");
            } else {
                foreach ($value as $param => $items) {
                    if (!in_array($param, PHPImageTools::linestylenames())) {
                        throw new PHPImageException("$paramname option '$param' is unknown !");
                    }
                    if ($items != 1 && count($items) != 0) {
                        throw new PHPImageException("$paramname parameter '$param' should not have any property !");
                    }
                    $tmp = $param;
                }
                $value = $tmp;
            }
        }
        return $save;
    }

    /**
     * @param mixed $paramname
     * @param mixed $value
     * @return mixed
     */
    public static function checkparams($paramname, &$value)
    {
        if (is_string($value)) {
            $tags = preg_replace('/\(.*?\)/si', '', $value);
            $tags = preg_split('/[^a-z0-9_-]/i', strtolower($tags), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($tags as $tag) {
                $value = preg_replace('/' . $tag . '[^a-z0-9_-]+\(/si', "$tag(", $value);
                $value = preg_replace('/\)[^a-z0-9_-]+/si', ') ', $value);
                $value = preg_replace('/[^a-z0-9_)-]+' . $tag . '/si', " $tag", $value);
            }
            $value = ' ' . preg_replace('/\s+/si', ' ', trim($value)) . ' ';
            $value = preg_replace('/\s*,\s*/si', ',', $value);
            $value = preg_replace('/\(\s+/si', '(', $value);
            $value = preg_replace('/\s+\)/si', ')', $value);
            $params = array();
            foreach ($tags as $tag) {
                if (preg_match('/ ' . $tag . '\s*\((.*?)\)/si', $value, $m)) {
                    if ($m[1] != '') {
                        $params[$tag] = explode(',', $m[1]);
                    } else {
                        $params[$tag] = true;
                    }
                } elseif (preg_match('/ ' . $tag . ' /si', $value, $m)) {
                    $params[$tag] = true;
                } else {
                    throw new PHPImageException("$paramname syntax error in <u>$value</u> : bad tag <u>$tag</u>");
                }
            }
            ksort($params);
            $value = $params;
        } else {
            throw new PHPImageException("$paramname is not a string");
        }
    }

    /**
     * @param mixed $paramname
     * @param mixed $value
     */
    public static function checkresource($paramname, &$value)
    {
        if (!is_resource($value) && !$value instanceof GdImage) {
            throw new PHPImageException("$paramname is not a resource");
        }
    }

    /**
     * @param mixed $paramname
     * @param mixed $matrix
     * @return mixed
     */
    public static function checkmatrix($paramname, &$matrix)
    {
        if (is_string($matrix)) {
            $matrix = preg_replace('/\s+/si', ' ', trim($matrix));
            $matrix = preg_replace('/[^0-9\. -]/si', '', $matrix);
            $matrix = explode(' ', $matrix);
        } elseif (is_array($matrix)) {
            $tmp = array();
            foreach ($matrix as $row) {
                $tmp = array_merge($tmp, $row);
            }
            $matrix = $tmp;
        } else {
            throw new PHPImageException($paramname . ' $matrix should be a 3x3 array or a 9 values string (separated by spaces)');
        }
        $n = count($matrix);
        $dim = sqrt($n);
        if ($dim * $dim != $n || ($dim != 3 && $dim != 5 && $dim != 7)) {
            throw new PHPImageException($paramname . ' you should have 9, 25 or 49 values for the matrix');
        }
        $tmp = array();
        for ($i = 0; $i < $dim; $i++) {
            $tmp[] = array_slice($matrix, $i * $dim, $dim);
        }
        $matrix = $tmp;
        return $dim;
    }

    /**
     * @param mixed $object
     */
    public static function checkcreated($object)
    {
        if (!is_resource($object->img)) {
            throw new PHPImageException("You should run <b>create</b> or <b>loadfromfile</b> methods before drawing anything");
        }
    }

    /**
     * @param mixed $paramname
     * @param mixed $value
     * @param mixed $shouldexist
     */
    public static function checkfile($paramname, &$value, $shouldexist = true)
    {
        $value = trim("$value");
        if ($shouldexist xor file_exists($value)) {
            if ($shouldexist) {
                throw new PHPImageException("$paramname should exists");
            } else {
                throw new PHPImageException("$paramname should not exists");
            }
        }
    }

    /**
     * @param mixed $font
     */
    public static function checkfont(&$font)
    {
        if (is_string($font)) {
            if (!file_exists($font)) {
                throw new PHPImageException("The font is not defined or doesn't exists ($font)");
            }
        } elseif (is_numeric($font)) {
            $font = intval($font);
            if ($font < 1 || $font > 5) {
                throw new PHPImageException("The font should be an integer between 1 and 5, not '$font'");
            }
        }
    }

    /**
     * @param mixed $folder
     * @param mixed $mode
     * @return mixed
     */
    public static function createfolder($folder, $mode = 0755)
    {
        if (!file_exists($folder)) {
            @mkdir($folder);
            @chmod($folder, $mode);
            if (!file_exists($folder)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed $folder
     * @param mixed $mode
     * @return mixed
     */
    public static function createfolderpath($folder, $mode = 0755)
    {
        $items = explode('/', $folder);
        $path = '';
        foreach ($items as $item) {
            $path .= $item;
            if (!self::createfolder($path)) {
                return false;
            }
            $path .= '/';
        }
        return true;
    }

    /**
     * @param mixed $paramname
     * @param mixed $value
     */
    public static function checkbool($paramname, $value)
    {
        if (!is_bool($value)) {
            throw new PHPImageException("$paramname is not a boolean (true or false)");
        }
    }

    /**
     * @param mixed $paramname
     * @param mixed $value
     * @param mixed $min
     * @param mixed $max
     */
    public static function checkfloat($paramname, &$value, $min = null, $max = null)
    {
        $value = trim("$value");
        if (is_numeric($value)) {
            $value = floatval($value);
            if ($min !== null && $value < $min) {
                throw new PHPImageException("$paramname should be >= $min");
            }
            if ($max !== null && $value > $max) {
                throw new PHPImageException("$paramname should be <= $max");
            }
        } else {
            throw new PHPImageException("$paramname should be a correct numeric value");
        }
    }

    /**
     * @param mixed $paramname
     * @param mixed $value
     * @param mixed $min
     * @param mixed $max
     */
    public static function checkinteger($paramname, &$value, $min = null, $max = null)
    {
        $value = trim("$value");
        if (is_numeric($value)) {
            $value = intval($value);
            if ($min !== null && $value < $min) {
                throw new PHPImageException("$paramname should be >= $min");
            }
            if ($max !== null && $value > $max) {
                throw new PHPImageException("$paramname should be <= $max");
            }
        } else {
            throw new PHPImageException("$paramname should be a correct numeric value");
        }
    }

    /**
     * @param mixed $paramname
     * @param mixed $value
     * @param mixed $x
     * @param mixed $y
     * @param mixed $w
     * @param mixed $h
     * @return mixed
     */
    public static function checkposition($paramname, $value, &$x, &$y, $w, $h)
    {
        if ($value == '') {
            return true;
        }
        $value = preg_replace('/[^a-z0-9 ]/s', '', strtolower(trim("$value")));
        $value = preg_replace('/\s+/', ' ', $value);
        $value = " $value ";
        $items = explode(' ', $value);
        foreach ($items as $item) {
            switch ($item) {
                case 'center':
                case 'top':
                case 'left':
                case 'right':
                case 'bottom':
                case '':
                    break;
                default:
                    throw new PHPImageException("$paramname <u>$item</u> is unknown !");
            }
        }
        $pX = 'left';
        $pY = 'top';
        if (preg_match('/ center /', $value)) {
            $pX = 'center';
            $pY = 'center';
        }
        if (preg_match('/ left /', $value)) {
            $pX = 'left';
        }
        if (preg_match('/ right /', $value)) {
            $pX = 'right';
        }
        if (preg_match('/ bottom /', $value)) {
            $pY = 'bottom';
        }
        if (preg_match('/ top /', $value)) {
            $pY = 'top';
        }
        switch ($pX) {
            case 'right':
                $x -= $w;
                break;
            case 'center':
                $x -= $w / 2;
                break;
        }
        switch ($pY) {
            case 'bottom':
                $y -= $h;
                break;
            case 'center':
                $y -= $h / 2;
                break;
        }
    }

    /**
     * @param mixed $paramname
     * @param mixed $value
     * @param mixed $maxsize
     */
    public static function checksize($paramname, &$value, $maxsize)
    {
        $value = trim("$value");
        if (is_numeric($value)) {
            $value = round(floatval($value));
        } elseif (preg_match('/^\s*([0-9]+)\s*(px)?\s*$/si', $value, $m)) {
            $value = $m[1];
        } elseif (preg_match('/^\s*([0-9]+(\.[0-9]+)?)\s*%\s*$/si', $value, $m)) {
            $value = round(floatval($m[1]) * $maxsize / 100);
        } else {
            throw new PHPImageException("$paramname should be a correct size value ($value)");
        }
    }

    /**
     * @param mixed $filename
     * @return mixed
     */
    public static function imagesize($filename)
    {
        if (file_exists($filename)) {
            list($width, $height, $type, $attr) = getimagesize($filename);
            return array($width, $height);
        }
        return array(0, 0);
    }

    /**
     * @param mixed $srcw
     * @param mixed $srch
     * @param mixed $dstw
     * @param mixed $dsth
     */
    public static function resizefit($srcw, $srch, &$dstw, &$dsth)
    {
        if ($srcw / $srch > $dstw / $dsth) {
            $w = $dstw;
            $h = $dstw * $srch / $srcw;
        } else {
            $w = $dsth * $srcw / $srch;
            $h = $dsth;
        }
        $dstw = round($w);
        $dsth = round($h);
    }

    /**
     * @param mixed $filename
     * @return mixed
     */
    public static function imageheight($filename)
    {
        if (file_exists($filename)) {
            list($width, $height, $type, $attr) = getimagesize($filename);
            return $height;
        }
        return 0;
    }

    /**
     * @param mixed $filename
     * @return mixed
     */
    public static function imagewidth($filename)
    {
        if (file_exists($filename)) {
            list($width, $height, $type, $attr) = getimagesize($filename);
            return $width;
        }
        return 0;
    }

    /**
     * @param mixed $angle
     * @param mixed $angle2
     */
    public static function setangle360(&$angle, &$angle2 = null)
    {
        if ($angle2 === null) {
            while ($angle < 0) {
                $angle += 360;
            }
            while ($angle >= 360) {
                $angle -= 360;
            }
        } else {
            while ($angle < 0) {
                $angle += 360;
                $angle2 += 360;
            }
            while ($angle >= 360) {
                $angle -= 360;
                $angle2 -= 360;
            }
            while ($angle2 < 0) {
                $angle += 360;
                $angle2 += 360;
            }
        }
    }

    /**
     * @param mixed $img
     * @return mixed
     */
    public static function getimageresource(&$img)
    {
        if (is_resource($img)) {
            return $img;
        }
        $class = get_class($img);
        if ($class !== false) {
            if (property_exists($img, 'img')) {
                return $img->img;
            }
        }
        // switch(get_class($img)) {
        // case 'PHPImage':
        // case 'PHPImageGD': return $img->img; break;
        // }
        return $img;
    }

    /**
     *
     */
    public static function readfile($filename)
    {
        $fh = fopen($filename, 'rb');
        while (!feof($fh)) {
            echo fread($fh, 409600);
        }
        fclose($fh);
    }

    /**
     * @param string $filename
     * @return string
     */
    public static function getfileext($filename)
    {
        $ext = '';
        $i = strrpos($filename, '.');
        if ($i) {
            $ext = strtolower(substr($filename, $i + 1, strlen($filename) - $i - 1));
        }
        return $ext;
    }

    /**
     * @param mixed $img
     * @param mixed $linestyle
     * @return mixed
     */
    public static function getlinestylelist($img, $linestyle)
    {
        $list = array();
        foreach ($linestyle as $item) {
            if ($item === '') {
                $list[] = IMG_COLOR_TRANSPARENT;
            } else {
                $list[] = $img->colorallocate($item);
            }
        }
        return $list;
    }

    /**
     * @param mixed $x1
     * @param mixed $y1
     * @param mixed $x2
     * @param mixed $y2
     * @return mixed
     */
    public static function linelength($x1, $y1, $x2, $y2)
    {
        return sqrt(($x2 - $x1) * ($x2 - $x1) + ($y2 - $y1) * ($y2 - $y1));
    }

    /**
     * @param float $deg
     * @return float
     */
    public static function deg2rad($deg)
    {
        return M_PI * $deg / 180;
    }

    /**
     * @param float $rad
     * @return float
     */
    public static function rad2deg($rad)
    {
        return $rad * 180 / M_PI;
    }

    /**
     * @param mixed $cx
     * @param mixed $cy
     * @param mixed $x
     * @param mixed $y
     * @param mixed $angle
     */
    public static function rotatepoint($cx, $cy, &$x, &$y, $angle)
    {
        $a = PHPImageTools::deg2rad($angle);
        $dx = $x - $cx;
        $dy = $y - $cy;
        $x = round($cx + $dx * cos($a) - $dy * sin($a), 0);
        $y = round($cy + $dx * sin($a) + $dy * cos($a), 0);
    }

    /**
     * @param mixed $x1
     * @param mixed $y1
     * @param mixed $x2
     * @param mixed $y2
     * @return mixed
     */
    public static function quadran($x1, $y1, $x2, $y2)
    {
        if ($x1 < $x2) {
            if ($y1 < $y2) {
                return 'Q4';
            } elseif ($y1 == $y2) {
                return 'H1';
            } else {
                return 'Q1';
            }
        } elseif ($x1 == $x2) {
            if ($y1 < $y2) {
                return 'V2';
            } elseif ($y1 == $y2) {
                return 'O';
            } else {
                return 'V1';
            }
        } else {
            if ($y1 < $y2) {
                return 'Q3';
            } elseif ($y1 == $y2) {
                return 'H2';
            } else {
                return 'Q2';
            }
        }
    }

    /**
     * @param mixed $x1
     * @param mixed $y1
     * @param mixed $x2
     * @param mixed $y2
     * @return mixed
     */
    public static function lineangle($x1, $y1, $x2, $y2)
    {
        if ($x1 < $x2) {
            return atan(($y2 - $y1) / ($x2 - $x1)) * 180 / M_PI;
        } elseif ($x1 == $x2) {
            if ($y1 < $y2) {
                return 90;
            } else {
                return -90;
            }
        } else {
            if ($y1 < $y2) {
                return 180 + atan(($y2 - $y1) / ($x2 - $x1)) * 180 / M_PI;
            } else {
                return -180 + atan(($y2 - $y1) / ($x2 - $x1)) * 180 / M_PI;
            }
        }
    }

    /**
     * @param mixed $width
     * @param mixed $height
     * @return mixed
     */
    public static function ellipseperimeter($width, $height)
    {
        return 2 * M_PI * sqrt($width * $width / 8 + $height * $height / 8);
    }

    /**
     * @param mixed $v1
     * @param mixed $v2
     */
    public static function switchvar(&$v1, &$v2)
    {
        $tmp = $v2;
        $v2 = $v1;
        $v1 = $tmp;
    }
}
