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
 * PHPImage class
 */
class PHPImage
{
    /**
     * @var resource
     */
    public $img = null;
    /**
     * @var int
     */
    public $width = 0;
    /**
     * @var int
     */
    public $height = 0;
    /**
     * @var string
     */
    public $format = 'png';
    /**
     * @var int
     */
    public $quality = 85;
    /**
     * @var string
     */
    public $bgcolor = 'white 127';
    /**
     * @var int
     */
    public $cachetime = 0;
    /**
     * @var int
     */
    public $cachecontrol = 86400;
    /**
     * @var int
     */
    protected $headers_sent = false;
    /**
     * @var int
     */
    public $fontfile = 2;
    /**
     * @var int
     */
    public $fontsize = 12;
    /**
     * @var string
     */
    public $fillcolor = 'white';
    /**
     * @var int
     */
    public $thickness = 1;
    /**
     * @var string
     */
    public $linecolor = 'black';
    /**
     * @var string
     */
    public $linestyle = 'solid';
    /**
     * @var bool
     */
    public $realcopy = false;
    /**
     * @var array
     */
    protected $colors = array();

    /**
     * @param int $w
     * @param int $h
     */
    public function __construct($w = 0, $h = 0)
    {
        if (is_numeric($w) && is_numeric($h)) {
            if ($w > 0 && $h > 0) {
                $this->create($w, $h);
            }
        } elseif (is_string($w)) { // filename
            PHPImageTools::checkfile("image <u>$w</u>", $w);
            $this->loadfromfile($w);
        }
    }

    /**
     * @param PHPImage $mask
     */
    public function maskalpha(&$mask)
    {
        $masksrc = PHPImageTools::getimageresource($mask);
        $w = $this->width;
        $h = $this->height;
        $tmp = new PHPImage($w, $h);
        $tmp->copyresampled($masksrc);
        $this->alphablending(false);
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $val = imagecolorat($tmp->img, $x, $y);
                $A = $val >> 24;
                $val = imagecolorat($this->img, $x, $y);
                $R = ($val >> 16) & 0xFF;
                $G = ($val >> 8) & 0xFF;
                $B = $val & 0xFF;
                $color = imagecolorallocatealpha($this->img, $R, $G, $B, $A);
                imagesetpixel($this->img, $x, $y, $color);
            }
        }
        $this->alphablending(true);
        $tmp->destroy();
    }

    /**
     * @param int $cx Center X
     * @param int $cy Center Y
     * @param int $r Radius
     * @param float $start Start angle
     * @param float $end End angle
     * @param string $color Color
     */
    public function drawfilledcirclearc($cx, $cy, $r, $start, $end, $color = '')
    {
        if ($color == '') {
            $color = $this->fillcolor;
        } else {
            $this->fillcolor = $color;
        }
        PHPImageTools::checksize('Center X', $cx, $this->width);
        PHPImageTools::checksize('Center Y', $cy, $this->height);
        PHPImageTools::checksize('Radius', $r, $this->width);
        PHPImageTools::checkfloat('start angle', $start);
        PHPImageTools::checkfloat('end angle', $end);
        $color = PHPImageTools::checkcolor($color);
        $this->drawfilledellipsearc($cx, $cy, 2 * $r, 2 * $r, $start, $end, $color);
    }

    /**
     * @param int $cx Center X
     * @param int $cy Center Y
     * @param int $w Width
     * @param int $h height
     * @param float $start Start angle
     * @param float $end End angle
     * @param string $color Color
     * @return mixed
     */
    public function drawfilledellipsearc($cx, $cy, $w, $h, $start, $end, $color = '')
    {
        if ($color == '') {
            $color = $this->fillcolor;
        } else {
            $this->fillcolor = $color;
        }
        PHPImageTools::checksize('Center X', $cx, $this->width);
        PHPImageTools::checksize('Center Y', $cy, $this->height);
        PHPImageTools::checksize('Width', $w, $this->width);
        PHPImageTools::checksize('Height', $h, $this->height);
        PHPImageTools::checkfloat('start angle', $start);
        PHPImageTools::checkfloat('end angle', $end);
        PHPImageTools::checkcolor($color);

        $start = -$start;
        $end = -$end;
        PHPImageTools::switchvar($start, $end);
        PHPImageTools::setangle360($start, $end);
        if ($start == $end) {
            return;
        }

        $scolor = implode(' ', $color);
        $alpha = $color[3];
        $color[3] = 0;

        if ($alpha < 127) {
            $middle = PHPImageTools::deg2rad(($start + $end) / 2);
            $x1 = $w / 2 + 1 + $w / 2 * cos($middle);
            $y1 = $h / 2 + 1 + $h / 2 * sin($middle);
            $x = ($x1 + $w / 2 + 1) / 2;
            $y = ($y1 + $h / 2 + 1) / 2;

            $tmp = new PHPImage($w + 2, $h + 2);
            imagefilledarc(
                $tmp->img,
                $w / 2 + 1,
                $h / 2 + 1,
                $w,
                $h,
                $start,
                $end,
                $tmp->colorallocate($color),
                IMG_ARC_PIE
            );
            if ($this->realcopy) {
                $tmp->setalpha($alpha);
                $this->realcopy($tmp, $cx - $tmp->width / 2, $cy - $tmp->height / 2, 0, 0, 0, 0);
            } else {
                $this->copy($tmp, $cx - $tmp->width / 2, $cy - $tmp->height / 2, 0, 0, 0, 0, $alpha);
            }
            $tmp->destroy();
        }
    }

    /**
     * @param int $x1 Point X
     * @param int $y1 Point Y
     * @param int $w Width
     * @param int $h Height
     * @param string $linecolor Line color
     * @param int $thickness thickness
     * @param string $linestyle line style
     */
    public function drawlinewh($x1, $y1, $w, $h, $linecolor = '', $thickness = 0, $linestyle = '')
    {
        if ($linecolor == '') {
            $linecolor = $this->linecolor;
        } else {
            $this->linecolor = $linecolor;
        }
        if ($thickness == 0) {
            $thickness = $this->thickness;
        } else {
            $this->thickness = $thickness;
        }
        if ($linestyle == '') {
            $linestyle = $this->linestyle;
        } else {
            $this->linestyle = $linestyle;
        }
        PHPImageTools::checksize('X1', $x1, $this->width);
        PHPImageTools::checksize('Y1', $y1, $this->height);
        PHPImageTools::checksize('Width', $w, $this->width);
        PHPImageTools::checksize('Height', $h, $this->height);
        $linecolor = PHPImageTools::checkcolor($linecolor);
        PHPImageTools::checkinteger('Thickness', $thickness, 1);
        $linestyle = PHPImageTools::checklinestyle('Line style', $linestyle);
        $x2 = $x1 + ($w == 0 ? 1 : $w) - 1;
        $y2 = $y1 + ($h == 0 ? 1 : $h) - 1;
        $this->drawline($x1, $y1, $x2, $y2, $linecolor, $thickness, $linestyle);
    }

    /**
     * @param Array $values
     * @param string $linecolor
     * @param int $thickness
     * @param string $linestyle
     */
    public function drawpolygon($values, $linecolor = '', $thickness = 0, $linestyle = '')
    {
        if ($linecolor == '') {
            $linecolor = $this->linecolor;
        } else {
            $this->linecolor = $linecolor;
        }
        if ($thickness == 0) {
            $thickness = $this->thickness;
        } else {
            $this->thickness = $thickness;
        }
        if ($linestyle == '') {
            $linestyle = $this->linestyle;
        } else {
            $this->linestyle = $linestyle;
        }
        $savecolor = PHPImageTools::checkcolor($linecolor);
        PHPImageTools::checkinteger('Thickness', $thickness, 1);
        PHPImageTools::checklinestyle('Line style', $linestyle);

        $n = count($values);
        if ($n > 2) {
            for ($i = 0; $i < $n - 1; $i++) {
                $x1 = $values[$i][0];
                $y1 = $values[$i][1];
                $x2 = $values[$i + 1][0];
                $y2 = $values[$i + 1][1];
                $this->drawline($x1, $y1, $x2, $y2, $savecolor, $thickness, $linestyle);
            }
            $x1 = $values[$n - 1][0];
            $y1 = $values[$n - 1][1];
            $x2 = $values[0][0];
            $y2 = $values[0][1];
            $this->drawline($x1, $y1, $x2, $y2, $savecolor, $thickness, $linestyle);
        }
    }

    /**
     * @param array $values
     * @param string $fillcolor
     */
    public function drawfilledpolygon($values, $fillcolor = '')
    {
        if ($fillcolor == '') {
            $fillcolor = $this->fillcolor;
        } else {
            $this->fillcolor = $fillcolor;
        }
        PHPImageTools::checkcolor($fillcolor);
        $color = $this->colorallocate($fillcolor);
        PHPImageTools::imagefilledpolygon($this->img, $values, $color);
    }

    /**
     * @param mixed $x1
     * @param mixed $y1
     * @param mixed $x2
     * @param mixed $y2
     * @param mixed $linecolor
     * @param mixed $thickness
     * @param mixed $linestyle
     * @return mixed
     */
    public function drawline($x1, $y1, $x2, $y2, $linecolor = '', $thickness = 0, $linestyle = '')
    {
        if ($linecolor == '') {
            $linecolor = $this->linecolor;
        } else {
            $this->linecolor = $linecolor;
        }
        if ($thickness == 0) {
            $thickness = $this->thickness;
        } else {
            $this->thickness = $thickness;
        }
        if ($linestyle == '') {
            $linestyle = $this->linestyle;
        } else {
            $this->linestyle = $linestyle;
        }
        PHPImageTools::checksize('X1', $x1, $this->width);
        PHPImageTools::checksize('Y1', $y1, $this->height);
        PHPImageTools::checksize('X2', $x2, $this->width);
        PHPImageTools::checksize('Y2', $y2, $this->height);
        PHPImageTools::checkcolor($linecolor);
        PHPImageTools::checkinteger('Thickness', $thickness, 1);
        PHPImageTools::checklinestyle('Line style', $linestyle);

        $color = $this->colorallocate($linecolor);

        if ($x1 == $x2 && $y1 == $y2) {
            imagefilledellipse($this->img, $x1, $y1, $thickness, $thickness, $color);
            return false;
        }
        if ($x1 > $x2 || ($x1 == $x2 && $y1 > $y2)) {
            PHPImageTools::switchvar($x1, $x2);
            PHPImageTools::switchvar($y1, $y2);
        }
        $length = PHPImageTools::linelength($x1, $y1, $x2, $y2);
        PHPImageTools::getn1n2($n1, $n2, $nb, $n, $linestyle, $thickness, $length);

        switch ($linestyle) {
            case 'dot':
                if ($n > 1) {
                    if ($x1 != $x2) {
                        $p = ($y2 - $y1) / ($x2 - $x1);
                        for ($i = 0; $i < $n; $i++) {
                            $x = $x1 + $i * ($x2 - $x1) / ($n - 1);
                            $y = $p * $x + $y1 - $p * $x1;
                            imagefilledellipse($this->img, $x, $y, $thickness, $thickness, $color);
                        }
                    } else {
                        for ($i = 0; $i < $n; $i++) {
                            $y = $y1 + $i * ($y2 - $y1) / ($n - 1);
                            imagefilledellipse($this->img, $x1, $y, $thickness, $thickness, $color);
                        }
                    }
                } else {
                    PHPImageTools::imageline($this->img, $x1, $y1, $x2, $y2, $color, $thickness);
                }
                break;
            case 'square':
            case 'dash':
            case 'bigdash':
                if ($n > 1) {
                    if ($x1 != $x2) {
                        $p = ($y2 - $y1) / ($x2 - $x1);
                        for ($i = 0; $i < $n; $i++) {
                            $x = $x1 + $i * ($x2 - $x1) * ($n1 + $n2) / $nb;
                            $xx = $x + ($x2 - $x1) * ($n1) / $nb;
                            $y = $p * $x + $y1 - $p * $x1;
                            $yy = $p * $xx + $y1 - $p * $x1;
                            PHPImageTools::imageline($this->img, $x, $y, $xx, $yy, $color, $thickness);
                        }
                    } else {
                        for ($i = 0; $i < $n; $i++) {
                            $y = $y1 + $i * ($y2 - $y1) * ($n1 + $n2) / $nb;
                            $yy = $y + ($y2 - $y1) * $n1 / $nb;
                            PHPImageTools::imageline($this->img, $x1, $y, $x1, $yy, $color, $thickness);
                        }
                    }
                } else {
                    PHPImageTools::imageline($this->img, $x1, $y1, $x2, $y2, $color, $thickness);
                }
                break;
            case 'double':
                $e = $thickness / 3;
                if ($y1 == $y2) {
                    PHPImageTools::imageline($this->img, $x1, round($y1 + $e), $x2, round($y2 + $e), $color, ceil($e));
                    PHPImageTools::imageline($this->img, $x1, round($y1 - $e), $x2, round($y2 - $e), $color, ceil($e));
                } elseif ($x1 == $x2) {
                    PHPImageTools::imageline($this->img, round($x1 + $e), $y1, round($x2 + $e), $y2, $color, ceil($e));
                    PHPImageTools::imageline($this->img, round($x1 - $e), $y1, round($x2 - $e), $y2, $color, ceil($e));
                } else {
                    $k = ($y2 - $y1) / ($x2 - $x1); // y = kx + q
                    $dx = $e / sqrt(1 + $k * $k);
                    $dy = $e / sqrt(1 + 1 / $k * $k);
                    $dy *= ($y2 - $y1) / abs($y2 - $y1);
                    PHPImageTools::imageline($this->img, round($x1 - $dy), round($y1 + $dx), round($x2 - $dy), round($y2 + $dx), $color, ceil($e));
                    PHPImageTools::imageline($this->img, round($x1 + $dy), round($y1 - $dx), round($x2 + $dy), round($y2 - $dx), $color, ceil($e));
                }
                break;
            case 'triple':
                $e = $thickness / 5;
                if ($y1 == $y2) {
                    PHPImageTools::imageline($this->img, $x1, $y1, $x2, $y2, $color, ceil($e));
                    PHPImageTools::imageline($this->img, $x1, round($y1 + 2 * $e), $x2, round($y2 + 2 * $e), $color, ceil($e));
                    PHPImageTools::imageline($this->img, $x1, round($y1 - 2 * $e), $x2, round($y2 - 2 * $e), $color, ceil($e));
                } elseif ($x1 == $x2) {
                    PHPImageTools::imageline($this->img, $x1, $y1, $x2, $y2, $color, ceil($e));
                    PHPImageTools::imageline($this->img, round($x1 + 2 * $e), $y1, round($x2 + 2 * $e), $y2, $color, ceil($e));
                    PHPImageTools::imageline($this->img, round($x1 - 2 * $e), $y1, round($x2 - 2 * $e), $y2, $color, ceil($e));
                } else {
                    $k = ($y2 - $y1) / ($x2 - $x1); // y = kx + q
                    $dx = 2 * $e / sqrt(1 + $k * $k);
                    $dy = 2 * $e / sqrt(1 + 1 / $k * $k);
                    $dy *= ($y2 - $y1) / abs($y2 - $y1);
                    PHPImageTools::imageline($this->img, $x1, $y1, $x2, $y2, $color, ceil($e));
                    PHPImageTools::imageline($this->img, round($x1 - $dy), round($y1 + $dx), round($x2 - $dy), round($y2 + $dx), $color, ceil($e));
                    PHPImageTools::imageline($this->img, round($x1 + $dy), round($y1 - $dx), round($x2 + $dy), round($y2 - $dx), $color, ceil($e));
                }
                break;
            case 'solid':
                PHPImageTools::imageline($this->img, $x1, $y1, $x2, $y2, $color, $thickness);
                break;
            default:
                if (is_array($linestyle)) {
                    $list = PHPImageTools::getlinestylelist($this, $linestyle);
                    imagesetthickness($this->img, $thickness);
                    imagesetstyle($this->img, $list);
                    imageline($this->img, $x1, $y1, $x2, $y2, IMG_COLOR_STYLED);
                    imagesetthickness($this->img, 1);
                }
                break;
        }
    }

    /**
     * @param mixed $x1
     * @param mixed $y1
     * @param mixed $w
     * @param mixed $h
     * @param mixed $linecolor
     * @param mixed $thickness
     * @param mixed $linestyle
     * @param mixed $shapestyle
     */
    public function drawrectanglewh($x1, $y1, $w, $h, $linecolor = '', $thickness = 0, $linestyle = '', $shapestyle = '')
    {
        if ($linecolor == '') {
            $linecolor = $this->linecolor;
        } else {
            $this->linecolor = $linecolor;
        }
        if ($thickness == 0) {
            $thickness = $this->thickness;
        } else {
            $this->thickness = $thickness;
        }
        if ($linestyle == '') {
            $linestyle = $this->linestyle;
        } else {
            $this->linestyle = $linestyle;
        }
        PHPImageTools::checksize('X1', $x1, $this->width);
        PHPImageTools::checksize('Y1', $y1, $this->height);
        PHPImageTools::checksize('Width', $w, $this->width);
        PHPImageTools::checksize('Height', $h, $this->height);
        $savecolor = PHPImageTools::checkcolor($linecolor);
        PHPImageTools::checkinteger('Thickness', $thickness, 1);
        $savelinestyle = PHPImageTools::checklinestyle('Line style', $linestyle);
        $saveshapestyle = PHPImageTools::checkshapestyle('Shape style', $shapestyle);
        $x2 = $x1 + ($w == 0 ? 1 : $w) - 1;
        $y2 = $y1 + ($h == 0 ? 1 : $h) - 1;
        $this->drawrectangle($x1, $y1, $x2, $y2, $savecolor, $thickness, $savelinestyle, $saveshapestyle);
    }

    /**
     * @param mixed $x1
     * @param mixed $y1
     * @param mixed $w
     * @param mixed $h
     * @param mixed $fillcolor
     * @param mixed $shapestyle
     */
    public function drawfilledrectanglewh($x1, $y1, $w, $h, $fillcolor = '', $shapestyle = '')
    {
        if ($fillcolor == '') {
            $fillcolor = $this->fillcolor;
        } else {
            $this->fillcolor = $fillcolor;
        }
        PHPImageTools::checksize('X1', $x1, $this->width);
        PHPImageTools::checksize('Y1', $y1, $this->height);
        PHPImageTools::checksize('Width', $w, $this->width);
        PHPImageTools::checksize('Height', $h, $this->height);
        $savecolor = PHPImageTools::checkcolor($fillcolor);
        $saveshapestyle = PHPImageTools::checkshapestyle('Shape style', $shapestyle);
        $x2 = $x1 + ($w == 0 ? 1 : $w) - 1;
        $y2 = $y1 + ($h == 0 ? 1 : $h) - 1;
        $this->drawfilledrectangle($x1, $y1, $x2, $y2, $savecolor, $saveshapestyle);
    }

    /**
     * @param mixed $x1
     * @param mixed $y1
     * @param mixed $x2
     * @param mixed $y2
     * @param mixed $fillcolor
     * @param mixed $shapestyle
     * @return mixed
     */
    public function drawfilledrectangle($x1, $y1, $x2, $y2, $fillcolor = '', $shapestyle = '')
    {
        if ($fillcolor == '') {
            $fillcolor = $this->fillcolor;
        } else {
            $this->fillcolor = $fillcolor;
        }
        PHPImageTools::checksize('X1', $x1, $this->width);
        PHPImageTools::checksize('Y1', $y1, $this->height);
        PHPImageTools::checksize('X2', $x2, $this->width);
        PHPImageTools::checksize('Y2', $y2, $this->height);
        $savecolor = PHPImageTools::checkcolor($fillcolor);
        $saveshapestyle = PHPImageTools::checkshapestyle('Shape style', $shapestyle);
        $c = $this->colorallocate($savecolor);

        if ($x1 > $x2) {
            PHPImageTools::switchvar($x1, $x2);
        }
        if ($y1 > $y2) {
            PHPImageTools::switchvar($y1, $y2);
        }
        $w = $x2 - $x1 + 1;
        $h = $y2 - $y1 + 1;
        $xtl = $shapestyle['tl']['w'];
        $ytl = $shapestyle['tl']['h'];
        $xtr = $shapestyle['tr']['w'];
        $ytr = $shapestyle['tr']['h'];
        $xbl = $shapestyle['bl']['w'];
        $ybl = $shapestyle['bl']['h'];
        $xbr = $shapestyle['br']['w'];
        $ybr = $shapestyle['br']['h'];
        $l_min = min($xtl, $xbl);
        $r_min = min($xtr, $xbr);
        $t_min = min($ytl, $ytr);
        $b_min = min($ybl, $ybr);
        $l_max = max($xtl, $xbl);
        $r_max = max($xtr, $xbr);
        $t_max = max($ytl, $ytr);
        $b_max = max($ybl, $ybr);
        foreach ($shapestyle as $corner => $style) {
            switch ($corner) {
                case 'tl':
                    $this->drawfilledcorner($x1, $y1, $savecolor, $corner, $style['forme'], $xtl, $ytl);
                    break;
                case 'tr':
                    $this->drawfilledcorner($x2 - $xtr + 1, $y1, $savecolor, $corner, $style['forme'], $xtr, $ytr);
                    break;
                case 'bl':
                    $this->drawfilledcorner($x1, $y2 - $ybl + 1, $savecolor, $corner, $style['forme'], $xbl, $ybl);
                    break;
                case 'br':
                    $this->drawfilledcorner($x2 - $xbr + 1, $y2 - $ybr + 1, $savecolor, $corner, $style['forme'], $xbr, $ybr);
                    break;
            }
        }
        // checks
        if ($xtl + $xtr > $w) {
            throw new PHPImageException("les largeurs des coins TL et TR d�passent la largeur du rectangle !");
        }
        if ($xbl + $xbr > $w) {
            throw new PHPImageException("les largeurs des coins BL et BR d�passent la largeur du rectangle !");
        }
        if ($ytl + $ybl > $h) {
            throw new PHPImageException("les largeurs des coins TL et BL d�passent la hauteur du rectangle !");
        }
        if ($ytr + $ybr > $h) {
            throw new PHPImageException("les largeurs des coins TR et BR d�passent la hauteur du rectangle !");
        }
        $cases = array();
        $cases[1] = array(0, 0, $l_min, $t_min);
        $cases[2] = array($l_min, 0, $l_max, $t_min);
        $cases[3] = array($l_max, 0, $w - $r_max, $t_min);
        $cases[4] = array($w - $r_max, 0, $w - $r_min, $t_min);
        $cases[5] = array($w - $r_min, 0, $w, $t_min);
        $cases[6] = array($cases[1][0], $t_min, $cases[1][2], $t_max);
        $cases[7] = array($cases[2][0], $t_min, $cases[2][2], $t_max);
        $cases[8] = array($cases[3][0], $t_min, $cases[3][2], $t_max);
        $cases[9] = array($cases[4][0], $t_min, $cases[4][2], $t_max);
        $cases[10] = array($cases[5][0], $t_min, $cases[5][2], $t_max);
        $cases[11] = array($cases[1][0], $t_max, $cases[1][2], $h - $b_max);
        $cases[12] = array($cases[2][0], $t_max, $cases[2][2], $h - $b_max);
        $cases[13] = array($cases[3][0], $t_max, $cases[3][2], $h - $b_max);
        $cases[14] = array($cases[4][0], $t_max, $cases[4][2], $h - $b_max);
        $cases[15] = array($cases[5][0], $t_max, $cases[5][2], $h - $b_max);
        $cases[16] = array($cases[1][0], $h - $b_max, $cases[1][2], $h - $b_min);
        $cases[17] = array($cases[2][0], $h - $b_max, $cases[2][2], $h - $b_min);
        $cases[18] = array($cases[3][0], $h - $b_max, $cases[3][2], $h - $b_min);
        $cases[19] = array($cases[4][0], $h - $b_max, $cases[4][2], $h - $b_min);
        $cases[20] = array($cases[5][0], $h - $b_max, $cases[5][2], $h - $b_min);
        $cases[21] = array($cases[1][0], $h - $b_min, $cases[1][2], $h);
        $cases[22] = array($cases[2][0], $h - $b_min, $cases[2][2], $h);
        $cases[23] = array($cases[3][0], $h - $b_min, $cases[3][2], $h);
        $cases[24] = array($cases[4][0], $h - $b_min, $cases[4][2], $h);
        $cases[25] = array($cases[5][0], $h - $b_min, $cases[5][2], $h);
        // gestion du chevauchement
        $chevx = false;
        $chevy = false;
        $diag = 0;
        if ($ytl + $ybr > $h) {
            $diag = 1;
            $chevy = true;
        }
        if ($ybl + $ytr > $h) {
            $diag = 2;
            $chevy = true;
        }
        if ($xtl + $xbr > $w) {
            $diag = 1;
            $chevx = true;
        }
        if ($xbl + $xtr > $w) {
            $diag = 2;
            $chevx = true;
        }
        if ($chevx && $chevy) {
            $tx1 = $l_min;
            $ty1 = $t_min;
            $tx2 = $w - $r_min;
            $ty2 = $h - $b_min;
            if ($tx2 != $tx1 && $ty2 != $ty1) {
                imagefilledrectangle($this->img, $x1 + $tx1, $y1 + $ty1, $x1 + $tx2 - 1, $y1 + $ty2 - 1, $c);
            }
        } elseif ($chevx) {
            $tx1 = $w - $r_max;
            $tx2 = $l_max;
            if ($diag == 1) {
                if ($ytl < $ytr) {
                    $ty1 = $t_min;
                } else {
                    $ty1 = $t_max;
                }
                if ($ybr < $ybl) {
                    $ty2 = $h - $b_min;
                } else {
                    $ty2 = $h - $b_max;
                }
            } else {
                if ($ytl > $ytr) {
                    $ty1 = $t_min;
                } else {
                    $ty1 = $t_max;
                }
                if ($ybr > $ybl) {
                    $ty2 = $h - $b_min;
                } else {
                    $ty2 = $h - $b_max;
                }
            }
            if ($tx2 != $tx1 && $ty2 != $ty1) {
                imagefilledrectangle($this->img, $x1 + $tx1, $y1 + $ty1, $x1 + $tx2 - 1, $y1 + $ty2 - 1, $c);
            }
        } elseif ($chevy) {
            $ty1 = $h - $b_max;
            $ty2 = $t_max;
            if ($diag == 1) {
                if ($xtl < $xtr) {
                    $tx1 = $l_min;
                } else {
                    $tx1 = $l_max;
                }
                if ($xbr < $xbl) {
                    $tx2 = $w - $r_min;
                } else {
                    $tx2 = $w - $r_max;
                }
            } else {
                if ($xtl > $xtr) {
                    $tx1 = $l_min;
                } else {
                    $tx1 = $l_max;
                }
                if ($xbr > $xbl) {
                    $tx2 = $w - $r_min;
                } else {
                    $tx2 = $w - $r_max;
                }
            }
            if ($tx2 != $tx1 && $ty2 != $ty1) {
                imagefilledrectangle($this->img, $x1 + $tx1, $y1 + $ty1, $x1 + $tx2 - 1, $y1 + $ty2 - 1, $c);
            }
        }
        // affiche les cases
        for ($i = 1; $i <= 25; $i++) {
            $tx1 = $cases[$i][0];
            $ty1 = $cases[$i][1];
            $tx2 = $cases[$i][2];
            $ty2 = $cases[$i][3];
            if ($chevy) {
                if ($i > 5 && $i < 11) {
                    $ty2 = $h - $b_max;
                } elseif ($i > 15 && $i < 21) {
                    $ty1 = $t_max;
                }
            }
            if ($chevx) {
                if (($i - 1) % 5 == 1) {
                    $tx2 = $w - $r_max;
                } elseif (($i - 1) % 5 == 3) {
                    $tx1 = $l_max;
                }
            }
            $ok = true;
            $ok &= $tx2 > $tx1;
            $ok &= $ty2 > $ty1;
            if (($i - 1) % 5 < 4 && $i < 21) {
                $ok &= ($tx1 >= $xtl || $ty1 >= $ytl);
            }
            if (($i - 1) % 5 > 0 && $i < 21) {
                $ok &= ($w - $tx2 >= $xtr || $ty1 >= $ytr);
            }
            if (($i - 1) % 5 < 4 && $i > 5) {
                $ok &= ($tx1 >= $xbl || $h - $ty2 >= $ybl);
            }
            if (($i - 1) % 5 > 0 && $i > 5) {
                $ok &= ($w - $tx2 >= $xbr || $h - $ty2 >= $ybr);
            }
            $cases[$i] = array($tx1, $ty1, $tx2, $ty2);
            if ($ok) {
                imagefilledrectangle($this->img, $x1 + $tx1, $y1 + $ty1, $x1 + $tx2 - 1, $y1 + $ty2 - 1, $c);
            }
        }
        return true;
    }

    /**
     * @param mixed $x1
     * @param mixed $y1
     * @param mixed $x2
     * @param mixed $y2
     * @param mixed $linecolor
     * @param mixed $thickness
     * @param mixed $linestyle
     * @param mixed $shapestyle
     * @return mixed
     */
    public function drawrectangle($x1, $y1, $x2, $y2, $linecolor = '', $thickness = 0, $linestyle = '', $shapestyle = '')
    {
        if ($linecolor == '') {
            $linecolor = $this->linecolor;
        } else {
            $this->linecolor = $linecolor;
        }
        if ($thickness == 0) {
            $thickness = $this->thickness;
        } else {
            $this->thickness = $thickness;
        }
        if ($linestyle == '') {
            $linestyle = $this->linestyle;
        } else {
            $this->linestyle = $linestyle;
        }
        PHPImageTools::checksize('X1', $x1, $this->width);
        PHPImageTools::checksize('Y1', $y1, $this->height);
        PHPImageTools::checksize('X2', $x2, $this->width);
        PHPImageTools::checksize('Y2', $y2, $this->height);
        $savecolor = PHPImageTools::checkcolor($linecolor);
        PHPImageTools::checkinteger('Thickness', $thickness, 1);
        $savelinestyle = PHPImageTools::checklinestyle('Line style', $linestyle);
        $saveshapestyle = PHPImageTools::checkshapestyle('Shape style', $shapestyle);

        if ($x1 > $x2) {
            PHPImageTools::switchvar($x1, $x2);
        }
        if ($y1 > $y2) {
            PHPImageTools::switchvar($y1, $y2);
        }

        $c = $this->colorallocate($linecolor);
        imagesetthickness($this->img, 1);
        $w = $x2 - $x1 + 1;
        $h = $y2 - $y1 + 1;
        PHPImageTools::gete1e2($e1, $e2, $thickness);

        if ($saveshapestyle == '') {
            if ($thickness % 2 == 1) {
                $d = 0;
            } else {
                $d = 1;
            }
            switch ($linestyle) {
                case 'dot':
                    imagefilledellipse($this->img, $x1, $y1, $thickness, $thickness, $c);
                    imagefilledellipse($this->img, $x1, $y2, $thickness, $thickness, $c);
                    imagefilledellipse($this->img, $x2, $y1, $thickness, $thickness, $c);
                    imagefilledellipse($this->img, $x2, $y2, $thickness, $thickness, $c);
                    PHPImageTools::getn1n2($n1, $n2, $nb, $n, $linestyle, $thickness, $w + 2 * $e1);
                    if ($n > 1) {
                        $r = (($w + 2 * $e1) - $n * $thickness) / ($n - 1);
                        for ($i = 1; $i < $n - 1; $i++) {
                            $x = floor($x1 + $i * $thickness + $i * $r);
                            imagefilledellipse($this->img, $x, $y1, $thickness, $thickness, $c);
                            imagefilledellipse($this->img, $x, $y2, $thickness, $thickness, $c);
                        }
                    }
                    PHPImageTools::getn1n2($n1, $n2, $nb, $n, $linestyle, $thickness, $h + 2 * $e1);
                    if ($n > 1) {
                        $r = (($h + 2 * $e1) - $n * $thickness) / ($n - 1);
                        for ($i = 1; $i < $n - 1; $i++) {
                            $y = floor($y1 + $i * $thickness + $i * $r);
                            imagefilledellipse($this->img, $x1, $y, $thickness, $thickness, $c);
                            imagefilledellipse($this->img, $x2, $y, $thickness, $thickness, $c);
                        }
                    }
                    break;
                case 'square':
                    imagefilledrectangle($this->img, $x1 - $e1, $y1 - $e1, $x1 + $e2 - 1, $y1 + $e2 - 1, $c);
                    imagefilledrectangle($this->img, $x1 - $e1, $y2 - $e2 + 1, $x1 + $e2 - 1, $y2 + $e1, $c);
                    imagefilledrectangle($this->img, $x2 - $e2 + 1, $y1 - $e1, $x2 + $e1, $y1 + $e2 - 1, $c);
                    imagefilledrectangle($this->img, $x2 - $e2 + 1, $y2 - $e2 + 1, $x2 + $e1, $y2 + $e1, $c);
                    PHPImageTools::getn1n2($n1, $n2, $nb, $n, $linestyle, $thickness, $w + 2 * $e1);
                    if ($n > 1) {
                        $r = (($w + 2 * $e1) - $n * $thickness) / ($n - 1);
                        for ($i = 1; $i < $n - 1; $i++) {
                            $x = floor($x1 - $e1 + $i * ($thickness + $r));
                            imagefilledrectangle($this->img, $x, $y1 - $e1, $x + $thickness - 1, $y1 + $e2 - 1, $c);
                            imagefilledrectangle($this->img, $x, $y2 - $e2 + 1, $x + $thickness - 1, $y2 + $e1, $c);
                        }
                    }
                    PHPImageTools::getn1n2($n1, $n2, $nb, $n, $linestyle, $thickness, $h + 2 * $e1);
                    if ($n > 1) {
                        $r = (($h + 2 * $e1) - $n * $thickness) / ($n - 1);
                        for ($i = 1; $i < $n - 1; $i++) {
                            $y = floor($y1 - $e1 + $i * $thickness + $i * $r);
                            imagefilledrectangle($this->img, $x1 - $e1, $y, $x1 + $e2 - 1, $y + $thickness - 1, $c);
                            imagefilledrectangle($this->img, $x2 - $e2 + 1, $y, $x2 + $e1, $y + $thickness - 1, $c);
                        }
                    }
                    break;
                case 'bigdash':
                case 'dash':
                    if ($thickness % 2 == 1) {
                        $d = 0;
                    } else {
                        $d = 1;
                    }
                    $this->drawline($x1 - $e1, $y1 - $d, $x2 + $e1, $y1 - $d, $savecolor, $thickness, $savelinestyle);
                    $this->drawline($x1 - $e1, $y2, $x2 + $e1, $y2, $savecolor, $thickness, $savelinestyle);
                    PHPImageTools::getn1n2($n1, $n2, $nb, $n, $linestyle, $thickness, $h + 2 * $e1);
                    if ($n > 1) {
                        for ($i = 0; $i < $n; $i++) {
                            $y = $y1 - $e1 + $i * ($y2 - $y1 + 2 * $e1) * ($n1 + $n2) / $nb;
                            $yy = $y + ($y2 - $y1 + 2 * $e1) * $n1 / $nb;
                            if ($i == 0) {
                                $y += $thickness;
                            }
                            if ($i == $n - 1) {
                                $yy -= $thickness;
                            }
                            PHPImageTools::imageline($this->img, $x1 - $d, $y, $x1 - $d, $yy, $c, $thickness);
                            PHPImageTools::imageline($this->img, $x2, $y, $x2, $yy, $c, $thickness);
                        }
                    } else {
                        imagefilledrectangle($this->img, $x1 - $e1, $y1 + $e2, $x1 + $e2 - 1, $y2 - $e2, $c);
                        imagefilledrectangle($this->img, $x2 - $e2 + 1, $y1 + $e2, $x2 + $e1, $y2 - $e2, $c);
                    }
                    break;
                case 'double':
                    $e = ceil($thickness / 3);
                    $ok = false;
                    if (2 * $e <= $thickness) {
                        $ok = true;
                    } elseif ($e > 1 && 2 * $e - 2 <= $thickness) {
                        $ok = true;
                        $e--;
                    }
                    if ($ok) {
                        for ($i = 0; $i < $e; $i++) {
                            imagerectangle($this->img, $x1 - $e1 + $i, $y1 - $e1 + $i, $x2 + $e1 - $i, $y2 + $e1 - $i, $c);
                            imagerectangle($this->img, $x1 + $e2 - 1 - $i, $y1 + $e2 - 1 - $i, $x2 - $e2 + 1 + $i, $y2 - $e2 + 1 + $i, $c);
                        }
                    } else {
                        imagesetthickness($this->img, $thickness);
                        imagerectangle($this->img, $x1, $y1, $x2, $y2, $c);
                    }
                    break;
                case 'triple':
                    $e = ceil($thickness / 5);
                    $v = ceil(($thickness - 3 * $e) / 2);
                    $ok = false;
                    if (3 * $e + $v <= $thickness) {
                        $ok = true;
                    } elseif ($e > 1 && 3 * $e + $v - 3 <= $thickness) {
                        $ok = true;
                        $e--;
                    }
                    if ($ok) {
                        for ($i = 0; $i < $e; $i++) {
                            imagerectangle($this->img, $x1 - $e1 + $i, $y1 - $e1 + $i, $x2 + $e1 - $i, $y2 + $e1 - $i, $c);
                            imagerectangle(
                                $this->img,
                                $x1 - $e1 + $e + $v + $i,
                                $y1 - $e1 + $e + $v + $i,
                                $x2 + $e1 - $e - $v - $i,
                                $y2 + $e1 - $e - $v - $i,
                                $c
                            );
                            imagerectangle($this->img, $x1 + $e2 - 1 - $i, $y1 + $e2 - 1 - $i, $x2 - $e2 + 1 + $i, $y2 - $e2 + 1 + $i, $c);
                        }
                    } else {
                        imagesetthickness($this->img, $thickness);
                        imagerectangle($this->img, $x1, $y1, $x2, $y2, $c);
                    }
                    break;
                default:
                    if (is_array($linestyle)) {
                        $list = PHPImageTools::getlinestylelist($this, $linestyle);
                        imagesetstyle($this->img, $list);
                        imagerectangle($this->img, $x1, $y1, $x2, $y2, IMG_COLOR_STYLED);
                        imagesetthickness($this->img, 1);
                    } elseif ($thickness == 1) {
                        imagerectangle($this->img, $x1, $y1, $x2, $y2, $c);
                    } else {
                        imagefilledrectangle($this->img, $x1 + $e2, $y1 - $e1, $x2 - $e2, $y1 + $e2 - 1, $c);
                        imagefilledrectangle($this->img, $x1 + $e2, $y2 - $e2 + 1, $x2 - $e2, $y2 + $e1, $c);
                        imagefilledrectangle($this->img, $x1 - $e1, $y1 - $e1, $x1 + $e2 - 1, $y2 + $e1, $c);
                        imagefilledrectangle($this->img, $x2 - $e2 + 1, $y1 - $e1, $x2 + $e1, $y2 + $e1, $c);
                    }
                    break;
            }
        } else { // shapestyle != ''
            $xtl = $shapestyle['tl']['w'];
            $ytl = $shapestyle['tl']['h'];
            $xtr = $shapestyle['tr']['w'];
            $ytr = $shapestyle['tr']['h'];
            $xbl = $shapestyle['bl']['w'];
            $ybl = $shapestyle['bl']['h'];
            $xbr = $shapestyle['br']['w'];
            $ybr = $shapestyle['br']['h'];
            switch ($linestyle) {
                case 'double':
                    $e = ceil($thickness / 3);
                    $ok = false;
                    if (2 * $e < $thickness) {
                        $ok = true;
                    } elseif ($e > 1 && 2 * $e - 2 < $thickness) {
                        $ok = true;
                        $e--;
                    }
                    if ($ok) {
                        $this->drawrectangle($x1 - $e, $y1 - $e, $x2 + $e, $y2 + $e, $savecolor, $e, 'solid', $saveshapestyle);
                        $this->drawrectangle($x1 + $e, $y1 + $e, $x2 - $e, $y2 - $e, $savecolor, $e, 'solid', $saveshapestyle);
                    } else {
                        $this->drawrectangle($x1, $y1, $x2, $y2, $savecolor, $thickness, 'solid', $saveshapestyle);
                    }
                    $this->linestyle = $savelinestyle;
                    break;
                case 'triple':
                    $e = ceil($thickness / 5);
                    $v = ceil($thickness - 3 * $e) / 2;
                    $ok = false;
                    if (3 * $e + $v < $thickness) {
                        $ok = true;
                    } elseif ($e > 1 && 3 * $e + $v - 3 < $thickness) {
                        $ok = true;
                        $e--;
                    }
                    if ($ok) {
                        $this->drawrectangle($x1 - 2 * $e, $y1 - 2 * $e, $x2 + 2 * $e, $y2 + 2 * $e, $savecolor, $e, 'solid', $saveshapestyle);
                        $this->drawrectangle($x1, $y1, $x2, $y2, $savecolor, $e, 'solid', $saveshapestyle);
                        $this->drawrectangle($x1 + 2 * $e, $y1 + 2 * $e, $x2 - 2 * $e, $y2 - 2 * $e, $savecolor, $e, 'solid', $saveshapestyle);
                    } else {
                        $this->drawrectangle($x1, $y1, $x2, $y2, $savecolor, $thickness, 'solid', $saveshapestyle);
                    }
                    $this->linestyle = $savelinestyle;
                    break;
                default:
                    foreach ($shapestyle as $corner => $style) {
                        switch ($corner) {
                            case 'tl':
                                $this->drawcorner($x1, $y1, $savecolor, $corner, $style['forme'], $xtl, $ytl, $thickness, $savelinestyle);
                                break;
                            case 'tr':
                                $this->drawcorner($x2 - $xtr + 1, $y1, $savecolor, $corner, $style['forme'], $xtr, $ytr, $thickness, $savelinestyle);
                                break;
                            case 'bl':
                                $this->drawcorner($x1, $y2 - $ybl + 1, $savecolor, $corner, $style['forme'], $xbl, $ybl, $thickness, $savelinestyle);
                                break;
                            case 'br':
                                $this->drawcorner($x2 - $xbr + 1, $y2 - $ybr + 1, $savecolor, $corner, $style['forme'], $xbr, $ybr, $thickness, $savelinestyle);
                                break;
                        }
                    }
                    if ($thickness % 2 == 1) {
                        $d = 0;
                    } else {
                        $d = 1;
                    }
                    if ($x1 + $xtl < $x2 - $xtr) {
                        $this->drawline($x1 + $xtl, $y1, $x2 - $xtr, $y1, $savecolor, $thickness, $savelinestyle);
                    }
                    if ($y1 + $ytl < $y2 - $ybl) {
                        $this->drawline($x1, $y1 + $ytl, $x1, $y2 - $ybl, $savecolor, $thickness, $savelinestyle);
                    }
                    if ($x1 + $xbl < $x2 - $xbr) {
                        $this->drawline($x1 + $xbl, $y2 - $d, $x2 - $xbr, $y2 - $d, $savecolor, $thickness, $savelinestyle);
                    }
                    if ($y1 + $ytr < $y2 - $ybr) {
                        $this->drawline($x2 - $d, $y1 + $ytr, $x2 - $d, $y2 - $ybr, $savecolor, $thickness, $savelinestyle);
                    }
                    break;
            }
        }
        return true;
    }

    /**
     * @param mixed $sx
     * @param mixed $sy
     * @param mixed $linecolor
     * @param mixed $corner
     * @param mixed $form
     * @param mixed $w
     * @param mixed $h
     */
    public function drawfilledcorner($sx, $sy, $linecolor, $corner, $form, $w, $h = 0)
    {
        if ($linecolor == '') {
            $linecolor = $this->linecolor;
        } else {
            $this->linecolor = $linecolor;
        }
        PHPImageTools::checksize('X', $sx, $this->width);
        PHPImageTools::checksize('Y', $sy, $this->height);
        PHPImageTools::checksize('Width', $w, $this->width);
        PHPImageTools::checksize('Height', $h, $this->height);
        $color = PHPImageTools::checkcolor($linecolor);

        $c = $this->colorallocate($color);

        if ($h == 0) {
            $h = $w;
        }
        $a = $w - 1;
        $b = $h - 1;
        $forms = explode('+', $form);
        foreach ($forms as $form) {
            $has_modulo = false;
            $modulo = 1;
            if (preg_match('/^(.*)\%([0-9]+)$/', $form, $m)) {
                $form = $m[1];
                $modulo = $m[2];
                $has_modulo = true;
            }
            $lengths = array();
            switch ($form) {
                case 'round':
                case 'round1':
                case 'round2':
                case 'curve':
                case 'curve1':
                case 'curve2':
                case 'curve3':
                case 'curve4':
                case 'curve5':
                case 'curve6':
                    $coords = array();
                    for ($i = 0; $i < $h; $i++) {
                        $coords[] = $a * sqrt(1 - $i * $i / ($b * $b));
                    }
                    break;
            }
            switch ($form) {
                case 'round':
                case 'round1':
                    switch ($corner) {
                        case 'tl':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, ceil($sx + $a - $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'tr':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $x), $sy + $i, $c);
                            }
                            break;
                        case 'bl':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, ceil($sx + $a - $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $x), $sy + $i, $c);
                            }
                            break;
                    }
                    break;
                case 'round2':
                    switch ($corner) {
                        case 'tl':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $a - $x), $sy + $i, $c);
                            }
                            break;
                        case 'tr':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, ceil($sx + $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'bl':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $a - $x), $sy + $i, $c);
                            }
                            break;
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, ceil($sx + $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                    }
                    break;
                case 'curve':
                case 'curve1':
                    switch ($corner) {
                        case 'tl':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, ceil($sx + $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'tr':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $a - $x), $sy + $i, $c);
                            }
                            break;
                        case 'bl':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, ceil($sx + $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $a - $x), $sy + $i, $c);
                            }
                            break;
                    }
                    break;
                case 'curve2':
                    switch ($corner) {
                        case 'tl':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $x), $sy + $i, $c);
                            }
                            break;
                        case 'tr':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, ceil($sx + $a - $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'bl':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $x), $sy + $i, $c);
                            }
                            break;
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, ceil($sx + $a - $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                    }
                    break;
                case 'curve3':
                    switch ($corner) {
                        case 'tl':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $x), $sy + $i, $c);
                            }
                            break;
                        case 'tr':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, ceil($sx + $a - $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'bl':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $x), $sy + $i, $c);
                            }
                            break;
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, ceil($sx + $a - $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                    }
                    break;
                case 'curve4':
                    switch ($corner) {
                        case 'tl':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, ceil($sx + $a - $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'tr':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $x), $sy + $i, $c);
                            }
                            break;
                        case 'bl':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, ceil($sx + $a - $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $x), $sy + $i, $c);
                            }
                            break;
                    }
                    break;
                case 'curve5':
                    switch ($corner) {
                        case 'tl':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $a - $x), $sy + $i, $c);
                            }
                            break;
                        case 'tr':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, ceil($sx + $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'bl':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $a - $x), $sy + $i, $c);
                            }
                            break;
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, ceil($sx + $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                    }
                    break;
                case 'curve6':
                    switch ($corner) {
                        case 'tl':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, ceil($sx + $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'tr':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                $x = $coords[$h - 1 - $i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $a - $x), $sy + $i, $c);
                            }
                            break;
                        case 'bl':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, ceil($sx + $x), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                $x = $coords[$i];
                                imageline($this->img, $sx, $sy + $i, floor($sx + $a - $x), $sy + $i, $c);
                            }
                            break;
                    }
                    break;
                case 'biseau':
                case 'biseau1':
                    switch ($corner) {
                        case 'tl':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                imageline($this->img, floor($sx + $a - $a * $i / $b), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'tr':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                imageline($this->img, $sx, $sy + $i, ceil($sx + $a * $i / $b), $sy + $i, $c);
                            }
                            break;
                        case 'bl':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                imageline($this->img, floor($sx + $a - $a * $i / $b), $sy + $b - $i, $sx + $a, $sy + $b - $i, $c);
                            }
                            break;
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                imageline($this->img, $sx, $sy + $b - $i, ceil($sx + $a * $i / $b), $sy + $b - $i, $c);
                            }
                            break;
                    }
                    break;
                case 'biseau2':
                    switch ($corner) {
                        case 'tl':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                imageline($this->img, $sx, $sy + $i, ceil($sx + $a - $a * $i / $b), $sy + $i, $c);
                            }
                            break;
                        case 'tr':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                imageline($this->img, floor($sx + $a * $i / $b), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'bl':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                imageline($this->img, $sx, $sy + $b - $i, ceil($sx + $a - $a * $i / $b), $sy + $b - $i, $c);
                            }
                            break;
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                imageline($this->img, floor($sx + $a * $i / $b), $sy + $b - $i, $sx + $a, $sy + $b - $i, $c);
                            }
                            break;
                    }
                    break;
                case 'biseau3':
                    switch ($corner) {
                        case 'tl':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                imageline($this->img, $sx, $sy + $b - $i, ceil($sx + $a - $a * $i / $b), $sy + $b - $i, $c);
                            }
                            break;
                        case 'tr':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                imageline($this->img, floor($sx + $a * $i / $b), $sy + $b - $i, $sx + $a, $sy + $b - $i, $c);
                            }
                            break;
                        case 'bl':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                imageline($this->img, $sx, $sy + $i, ceil($sx + $a - $a * $i / $b), $sy + $i, $c);
                            }
                            break;
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                imageline($this->img, floor($sx + $a * $i / $b), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                    }
                    break;
                case 'biseau4':
                    switch ($corner) {
                        case 'tl':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                imageline($this->img, floor($sx + $a - $a * $i / $b), $sy + $b - $i, $sx + $a, $sy + $b - $i, $c);
                            }
                            break;
                        case 'tr':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                imageline($this->img, $sx, $sy + $b - $i, ceil($sx + $a * $i / $b), $sy + $b - $i, $c);
                            }
                            break;
                        case 'bl':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                imageline($this->img, floor($sx + $a - $a * $i / $b), $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                imageline($this->img, $sx, $sy + $i, ceil($sx + $a * $i / $b), $sy + $i, $c);
                            }
                            break;
                    }
                    break;
                case 'trait':
                case 'trait1':
                    if (!$has_modulo) {
                        $modulo = 2;
                    }
                    switch ($corner) {
                        case 'tl':
                        case 'tr':
                            for ($i = 0; $i < $h; $i += $modulo) {
                                imageline($this->img, $sx, $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                        case 'bl':
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                imageline($this->img, $sx, $sy + $i, $sx + $a, $sy + $i, $c);
                            }
                            break;
                    }
                    break;
                case 'trait2':
                    if (!$has_modulo) {
                        $modulo = 2;
                    }
                    switch ($corner) {
                        case 'tl':
                        case 'bl':
                            for ($i = 0; $i < $w; $i += $modulo) {
                                imageline($this->img, $sx + $i, $sy, $sx + $i, $sy + $b, $c);
                            }
                            break;
                        case 'tr':
                        case 'br':
                            for ($i = $h - 1; $i >= 0; $i -= $modulo) {
                                imageline($this->img, $sx + $i, $sy, $sx + $i, $sy + $b, $c);
                            }
                            break;
                    }
                    break;
                case 'trait3':
                    $lmax = max($w, $h) - 1;
                    $lmin = min($w, $h) - 1;
                    if ($w > $h) {
                        for ($i = 0; $i < $h; $i = $i + 2) {
                            imageline($this->img, $sx, $sy + $i, $sx + $lmin - $i, $sy + $lmin, $c);
                        }
                        for ($i = 2; $i < $w; $i = $i + 2) {
                            if ($i + $lmin < $w) {
                                imageline($this->img, $sx + $i, $sy, $sx + $i + $lmin, $sy + $lmin, $c);
                            } else {
                                imageline($this->img, $sx + $i, $sy, $sx + $lmax, $sy + $lmax - $i, $c);
                            }
                        }
                    } else {
                        for ($i = 2; $i < $h; $i = $i + 2) {
                            if ($i + $lmin < $h) {
                                imageline($this->img, $sx, $sy + $i, $sx + $lmin, $sy + $i + $lmin, $c);
                            } else {
                                imageline($this->img, $sx, $sy + $i, $sx + $lmax - $i, $sy + $lmax, $c);
                            }
                        }
                        for ($i = 0; $i < $w; $i = $i + 2) {
                            imageline($this->img, $sx + $i, $sy, $sx + $lmin, $sy + $lmin - $i, $c);
                        }
                    }
                    $form = 'empty';
                    break;
            }
        }
    }

    /**
     * @param mixed $sx
     * @param mixed $sy
     * @param mixed $linecolor
     * @param mixed $corner
     * @param mixed $form
     * @param mixed $w
     * @param mixed $h
     * @param mixed $thickness
     * @param mixed $linestyle
     * @return mixed
     */
    public function drawcorner($sx, $sy, $linecolor, $corner, $form, $w, $h = 0, $thickness = 0, $linestyle = '')
    {
        if ($linecolor == '') {
            $linecolor = $this->linecolor;
        } else {
            $this->linecolor = $linecolor;
        }
        if ($thickness == 0) {
            $thickness = $this->thickness;
        } else {
            $this->thickness = $thickness;
        }
        if ($linestyle == '') {
            $linestyle = $this->linestyle;
        } else {
            $this->linestyle = $linestyle;
        }
        PHPImageTools::checksize('X', $sx, $this->width);
        PHPImageTools::checksize('Y', $sy, $this->height);
        PHPImageTools::checksize('Width', $w, $this->width);
        PHPImageTools::checksize('Height', $h, $this->height);
        $color = PHPImageTools::checkcolor($linecolor);
        PHPImageTools::checkinteger('Thickness', $thickness, 1);
        $savelinestyle = PHPImageTools::checklinestyle('Line style', $linestyle);

        $c = $this->colorallocate($linecolor);
        if ($thickness % 2 == 1) {
            $d = 0;
        } else {
            $d = -1;
        }
        if ($h == 0) {
            $h = $w;
        }
        $ww = 2 * $w;
        $hh = 2 * $h;
        $w--;
        $h--;
        if ($w > 0 && $h > 0) {
            $forms = explode('+', $form);
            foreach ($forms as $form) {
                $modulo = 0;
                if (preg_match('/^(.*)\%([0-9]+)$/', $form, $m)) {
                    $form = $m[1];
                    $modulo = $m[2];
                }
                // trac� des parties horizontales internes
                switch ($form) {
                    case 'round2':
                    case 'biseau2':
                    case 'curve2':
                    case 'trait':
                    case 'trait1':
                    case 'trait2':
                    case 'trait3':
                    case 'empty':

                    case 'biseau4':
                    case 'curve4':
                    case 'curve6':
                        switch ($corner) {
                            case 'tl':
                                $this->drawline($sx, $sy + $h, $sx + $w, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'tr':
                                $this->drawline($sx, $sy + $h, $sx + $w, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'bl':
                                $this->drawline($sx, $sy, $sx + $w, $sy, $color, $thickness, $savelinestyle);
                                break;
                            case 'br':
                                $this->drawline($sx, $sy, $sx + $w, $sy, $color, $thickness, $savelinestyle);
                                break;
                        }
                        break;
                }
                // trac� des parties verticales internes
                switch ($form) {
                    case 'round2':
                    case 'biseau2':
                    case 'curve2':
                    case 'trait':
                    case 'trait1':
                    case 'trait2':
                    case 'trait3':
                    case 'empty':

                    case 'biseau3':
                    case 'curve3':
                    case 'curve5':
                        switch ($corner) {
                            case 'tl':
                                $this->drawline($sx + $w, $sy, $sx + $w, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'tr':
                                $this->drawline($sx, $sy, $sx, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'bl':
                                $this->drawline($sx + $w, $sy, $sx + $w, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'br':
                                $this->drawline($sx, $sy, $sx, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                        }
                        break;
                }
                // trac� des parties horizontales externes
                switch ($form) {
                    case 'round2':
                    case 'biseau2':
                    case 'biseau4':
                    case 'curve2':
                    case 'curve4':
                    case 'curve6':
                        switch ($corner) {
                            case 'tl':
                                $this->drawline($sx, $sy, $sx + $w, $sy, $color, $thickness, $savelinestyle);
                                break;
                            case 'tr':
                                $this->drawline($sx, $sy, $sx + $w, $sy, $color, $thickness, $savelinestyle);
                                break;
                            case 'bl':
                                $this->drawline($sx, $sy + $h + $d, $sx + $w, $sy + $h + $d, $color, $thickness, $savelinestyle);
                                break;
                            case 'br':
                                $this->drawline($sx, $sy + $h + $d, $sx + $w, $sy + $h + $d, $color, $thickness, $savelinestyle);
                                break;
                        }
                        break;
                }
                // trac� des parties verticales externes
                switch ($form) {
                    case 'biseau2':
                    case 'biseau3':
                    case 'round2':
                    case 'curve2':
                    case 'curve3':
                    case 'curve5':
                        switch ($corner) {
                            case 'tl':
                                $this->drawline($sx, $sy, $sx, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'tr':
                                $this->drawline($sx + $w + $d, $sy, $sx + $w + $d, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'bl':
                                $this->drawline($sx, $sy, $sx, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'br':
                                $this->drawline($sx + $w + $d, $sy, $sx + $w + $d, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                        }
                        break;
                }
                // trac� des diagonales externes
                switch ($form) {
                    case 'biseau':
                    case 'biseau1':
                    case 'biseau2':
                        switch ($corner) {
                            case 'tl':
                                $this->drawline($sx + $w, $sy, $sx, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'tr':
                                $this->drawline($sx, $sy, $sx + $w, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'bl':
                                $this->drawline($sx, $sy, $sx + $w, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'br':
                                $this->drawline($sx + $w, $sy, $sx, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                        }
                        break;
                }
                // trac� des diagonales interne
                switch ($form) {
                    case 'biseau3':
                    case 'biseau4':
                        switch ($corner) {
                            case 'tr':
                                $this->drawline($sx + $w, $sy, $sx, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'tl':
                                $this->drawline($sx, $sy, $sx + $w, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'br':
                                $this->drawline($sx, $sy, $sx + $w, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                            case 'bl':
                                $this->drawline($sx + $w, $sy, $sx, $sy + $h, $color, $thickness, $savelinestyle);
                                break;
                        }
                        break;
                }
                // sp�cial
                switch ($form) {
                    case 'trait':
                    case 'trait1':
                    case 'trait2':
                    case 'trait3':
                        switch ($corner) {
                            case 'tl':
                                $this->drawfilledcorner($sx, $sy, $color, $corner, $form, $w, $h);
                                break;
                            case 'tr':
                                $this->drawfilledcorner($sx + 1, $sy, $color, $corner, $form, $w, $h);
                                break;
                            case 'bl':
                                $this->drawfilledcorner($sx, $sy + 1, $color, $corner, $form, $w, $h);
                                break;
                            case 'br':
                                $this->drawfilledcorner($sx + 1, $sy + 1, $color, $corner, $form, $w, $h);
                                break;
                        }
                        break;
                }
                // trac� des arcs de cercle 1
                switch ($form) {
                    case 'round':
                    case 'round1':
                    case 'round2':
                        switch ($corner) {
                            case 'tl':
                                $this->drawellipsearc($sx + $w, $sy + $h, $ww, $hh, 90, 180, $color, $thickness, $savelinestyle, false);
                                break;
                            case 'tr':
                                $this->drawellipsearc($sx, $sy + $h, $ww, $hh, 0, 90, $color, $thickness, $savelinestyle, false);
                                break;
                            case 'bl':
                                $this->drawellipsearc($sx + $w, $sy, $ww, $hh, 180, 270, $color, $thickness, $savelinestyle, false);
                                break;
                            case 'br':
                                $this->drawellipsearc($sx, $sy, $ww, $hh, 270, 360, $color, $thickness, $savelinestyle, false);
                                break;
                        }
                        break;
                }
                // trac� des arcs de cercle 2
                switch ($form) {
                    case 'curve':
                    case 'curve1':
                    case 'curve2':
                        switch ($corner) {
                            case 'tl':
                                $this->drawellipsearc($sx, $sy, $ww, $hh, 270, 360, $color, $thickness, $savelinestyle, false);
                                break;
                            case 'tr':
                                $this->drawellipsearc($sx + $w, $sy, $ww, $hh, 180, 270, $color, $thickness, $savelinestyle, false);
                                break;
                            case 'bl':
                                $this->drawellipsearc($sx, $sy + $h, $ww, $hh, 0, 90, $color, $thickness, $savelinestyle, false);
                                break;
                            case 'br':
                                $this->drawellipsearc($sx + $w, $sy + $h, $ww, $hh, 90, 180, $color, $thickness, $savelinestyle, false);
                                break;
                        }
                        break;
                }
                // trac� des arcs de cercle 3
                switch ($form) {
                    case 'curve3':
                    case 'curve4':
                        switch ($corner) {
                            case 'tl':
                                $this->drawellipsearc($sx, $sy + $h, $ww, $hh, 0, 90, $color, $thickness, $savelinestyle, false);
                                break;
                            case 'tr':
                                $this->drawellipsearc($sx + $w, $sy + $h, $ww, $hh, 90, 180, $color, $thickness, $savelinestyle, false);
                                break;
                            case 'bl':
                                $this->drawellipsearc($sx, $sy, $ww, $hh, 270, 360, $color, $thickness, $savelinestyle, false);
                                break;
                            case 'br':
                                $this->drawellipsearc($sx + $w, $sy, $ww, $hh, 180, 270, $color, $thickness, $savelinestyle, false);
                                break;
                        }
                        break;
                }
                // trac� des arcs de cercle 4
                switch ($form) {
                    case 'curve5':
                    case 'curve6':
                        switch ($corner) {
                            case 'tl':
                                $this->drawellipsearc($sx + $w, $sy, $ww, $hh, 180, 270, $color, $thickness, $savelinestyle, false);
                                break;
                            case 'tr':
                                $this->drawellipsearc($sx, $sy, $ww, $hh, 270, 360, $color, $thickness, $savelinestyle, false);
                                break;
                            case 'bl':
                                $this->drawellipsearc($sx + $w, $sy + $h, $ww, $hh, 90, 180, $color, $thickness, $savelinestyle, false);
                                break;
                            case 'br':
                                $this->drawellipsearc($sx, $sy + $h, $ww, $hh, 0, 90, $color, $thickness, $savelinestyle, false);
                                break;
                        }
                        break;
                }
            }
        }
        return true;
    }

    /**
     * @param mixed $src
     * @param mixed $alpha
     */
    public function setbackgroundimage(&$src, $alpha = -1)
    {
        $srcimg = PHPImageTools::getimageresource($src);
        PHPImageTools::checkresource('Source image', $srcimg);
        PHPImageTools::checksize('Alpha', $alpha, 127);
        PHPImageTools::checkinteger('Alpha', $alpha, -1, 127);
        $img = new PHPImage();
        $img->create(imagesx($srcimg), imagesy($srcimg));
        $img->copy($srcimg);
        $img->resample($this->width, $this->height);
        $this->copy($img->img, 0, 0, 0, 0, 0, 0, $alpha, '', '');
        $img->destroy();
    }

    /**
     * @param mixed $src
     * @param mixed $dstx
     * @param mixed $dsty
     * @param mixed $srcx
     * @param mixed $srcy
     * @param mixed $srcw
     * @param mixed $srch
     * @param mixed $alpha
     * @param mixed $dstpos
     * @param mixed $srcpos
     * @param mixed $shapestyle
     * @param mixed $watermark
     * @param mixed $creux
     */
    public function drawimage(
        &$src,
        $dstx,
        $dsty,
        $srcx = 0,
        $srcy = 0,
        $srcw = 0,
        $srch = 0,
        $alpha = -1,
        $dstpos = '',
        $srcpos = '',
        $shapestyle = '',
        $watermark = false,
        $creux = true
    ) {
        $srcimg = PHPImageTools::getimageresource($src);
        PHPImageTools::checkresource('Source image', $srcimg);

        $img = new PHPImage();
        $img->create(imagesx($srcimg), imagesy($srcimg));
        $img->copy($srcimg);

        $srcw = $srcw == 0 ? imagesx($srcimg) : $srcw;
        $srch = $srch == 0 ? imagesy($srcimg) : $srch;
        PHPImageTools::checksize('Destination X', $dstx, $this->width);
        PHPImageTools::checksize('Destination Y', $dsty, $this->height);
        PHPImageTools::checksize('Source X', $srcx, imagesx($srcimg));
        PHPImageTools::checksize('Source X', $srcy, imagesy($srcimg));
        PHPImageTools::checksize('Source Width', $srcw, imagesx($srcimg));
        PHPImageTools::checksize('Source Height', $srch, imagesy($srcimg));
        PHPImageTools::checksize('Alpha', $alpha, 127);
        PHPImageTools::checkinteger('Alpha', $alpha, -1, 127);
        PHPImageTools::checkposition('Destination position', $dstpos, $dstx, $dsty, $srcw, $srch);
        PHPImageTools::checkposition('Source position', $srcpos, $srcx, $srcy, $srcw, $srch);
        $saveshapestyle = PHPImageTools::checkshapestyle('Shape style', $shapestyle);

        if ($watermark) {
            $img->effect('watermark', $creux);
        }
        if ($saveshapestyle != '') {
            $mask = new PHPImage();
            $mask->bgcolor = $mask->getnewcolor();
            $mask->create($srcw, $srch);
            $mask->alphablending(false);
            $tmpbgcolor = $mask->getnewcolor(127);
            $mask->drawfilledrectanglewh(0, 0, $srcw, $srch, $tmpbgcolor, $saveshapestyle);
            $img->copy($mask, $srcx, $srcy);
            $img->maketransparent($mask->bgcolor);
            $img->format = 'gif';
            $mask->destroy();
        }
        $this->copy($img->img, $dstx, $dsty, $srcx, $srcy, $srcw, $srch, $alpha, '', '');
        $img->destroy();
    }

    /**
     * @param mixed $cx
     * @param mixed $cy
     * @param mixed $r
     * @param mixed $start
     * @param mixed $end
     * @param mixed $linecolor
     * @param mixed $thickness
     * @param mixed $linestyle
     * @param mixed $drawborders
     */
    public function drawarc($cx, $cy, $r, $start, $end, $linecolor = '', $thickness = 0, $linestyle = '', $drawborders = false)
    {
        if ($linecolor == '') {
            $linecolor = $this->linecolor;
        } else {
            $this->linecolor = $linecolor;
        }
        if ($thickness == 0) {
            $thickness = $this->thickness;
        } else {
            $this->thickness = $thickness;
        }
        if ($linestyle == '') {
            $linestyle = $this->linestyle;
        } else {
            $this->linestyle = $linestyle;
        }
        PHPImageTools::checksize('Center X', $cx, $this->width);
        PHPImageTools::checksize('Center Y', $cy, $this->height);
        PHPImageTools::checksize('Radius', $r, max($this->width, $this->height));
        PHPImageTools::checkfloat('start angle', $start);
        PHPImageTools::checkfloat('end angle', $end);
        $savecolor = PHPImageTools::checkcolor($linecolor);
        PHPImageTools::checkinteger('Thickness', $thickness, 1);
        $savelinestyle = PHPImageTools::checklinestyle('Line style', $linestyle);
        $this->drawellipsearc($cx, $cy, 2 * $r, 2 * $r, $start, $end, $savecolor, $thickness, $savelinestyle, $drawborders);
    }

    /**
     * @param mixed $cx
     * @param mixed $cy
     * @param mixed $w
     * @param mixed $h
     * @param mixed $start
     * @param mixed $end
     * @param mixed $linecolor
     * @param mixed $thickness
     * @param mixed $linestyle
     * @param mixed $drawborders
     * @return mixed
     */
    public function drawellipsearc($cx, $cy, $w, $h, $start, $end, $linecolor = '', $thickness = 0, $linestyle = '', $drawborders = false)
    {
        if ($linecolor == '') {
            $linecolor = $this->linecolor;
        } else {
            $this->linecolor = $linecolor;
        }
        if ($thickness == 0) {
            $thickness = $this->thickness;
        } else {
            $this->thickness = $thickness;
        }
        if ($linestyle == '') {
            $linestyle = $this->linestyle;
        } else {
            $this->linestyle = $linestyle;
        }
        PHPImageTools::checksize('Center X', $cx, $this->width);
        PHPImageTools::checksize('Center Y', $cy, $this->height);
        PHPImageTools::checksize('Width', $w, $this->width);
        PHPImageTools::checksize('Height', $h, $this->height);
        PHPImageTools::checkfloat('start angle', $start);
        PHPImageTools::checkfloat('end angle', $end);
        $savecolor = PHPImageTools::checkcolor($linecolor);
        PHPImageTools::checkinteger('Thickness', $thickness, 1);
        $savelinestyle = PHPImageTools::checklinestyle('Line style', $linestyle);

        $start = -$start;
        $end = -$end;
        PHPImageTools::switchvar($start, $end);
        PHPImageTools::setangle360($start, $end);
        if ($start == $end) {
            return;
        }

        $length = ($end - $start) * PHPImageTools::ellipseperimeter($w, $h) / 360;
        PHPImageTools::getn1n2($n1, $n2, $nb, $n, $savelinestyle, $thickness, $length);
        $c = $this->colorallocate($linecolor);

        if ($drawborders) {
            $this->drawline(
                $cx,
                $cy,
                $cx + 0.5 * $w * cos(PHPImageTools::deg2rad($start)),
                $cy + 0.5 * $h * sin(PHPImageTools::deg2rad($start)),
                $savecolor,
                $thickness,
                $savelinestyle
            );
            $this->drawline(
                $cx,
                $cy,
                $cx + 0.5 * $w * cos(PHPImageTools::deg2rad($end)),
                $cy + 0.5 * $h * sin(PHPImageTools::deg2rad($end)),
                $savecolor,
                $thickness,
                $savelinestyle
            );
        }

        switch ($linestyle) {
            case 'dot':
                if ($n > 1) {
                    for ($i = 0; $i < $n; $i++) {
                        $a = PHPImageTools::deg2rad($start + $i * ($end - $start) / ($n - 1));
                        $x = $cx + 0.5 * $w * cos($a);
                        $y = $cy + 0.5 * $h * sin($a);
                        imagefilledellipse($this->img, round($x), round($y), $thickness, $thickness, $c);
                    }
                } else {
                    PHPImageTools::imageellipsearc($this->img, $cx, $cy, $w, $h, $start, $end, $c, $thickness);
                }
                break;
            case 'square':
            case 'dash':
            case 'bigdash':
                if ($n > 1) {
                    for ($i = 0; $i < $n; $i++) {
                        $a1 = $start + $i * ($end - $start) * ($n1 + $n2) / $nb;
                        $a2 = $a1 + ($end - $start) * ($n1) / $nb;
                        PHPImageTools::imageellipsearc($this->img, $cx, $cy, $w, $h, $a1, $a2, $c, $thickness);
                    }
                } else {
                    PHPImageTools::imageellipsearc($this->img, $cx, $cy, $w, $h, $start, $end, $c, $thickness);
                }
                break;
            case 'double':
                $e = $thickness / 3;
                PHPImageTools::imageellipsearc($this->img, $cx, $cy, $w + 2 * $e, $h + 2 * $e, $start, $end, $c, $e);
                PHPImageTools::imageellipsearc($this->img, $cx, $cy, $w - 2 * $e, $h - 2 * $e, $start, $end, $c, $e);
                break;
            case 'triple':
                $e = $thickness / 5;
                PHPImageTools::imageellipsearc($this->img, $cx, $cy, $w, $h, $start, $end, $c, $e);
                PHPImageTools::imageellipsearc($this->img, $cx, $cy, $w + 4 * $e, $h + 4 * $e, $start, $end, $c, $e);
                PHPImageTools::imageellipsearc($this->img, $cx, $cy, $w - 4 * $e, $h - 4 * $e, $start, $end, $c, $e);
                break;
            case 'solid':
                PHPImageTools::imageellipsearc($this->img, $cx, $cy, $w, $h, $start, $end, $c, $thickness);
                break;
            default:
                if (is_array($linestyle)) {
                    $list = PHPImageTools::getlinestylelist($this, $linestyle);
                    imagesetthickness($this->img, $thickness);
                    imagesetstyle($this->img, $list);
                    imagearc($this->img, $cx, $cy, $w, $h, $start, $end, IMG_COLOR_STYLED);
                    imagesetthickness($this->img, 1);
                }
                break;
        }
    }

    /**
     * @param mixed $cx
     * @param mixed $cy
     * @param mixed $r
     * @param mixed $linecolor
     * @param mixed $thickness
     * @param mixed $linestyle
     */
    public function drawcircle($cx, $cy, $r, $linecolor = '', $thickness = 0, $linestyle = '')
    {
        if ($linecolor == '') {
            $linecolor = $this->linecolor;
        } else {
            $this->linecolor = $linecolor;
        }
        if ($thickness == 0) {
            $thickness = $this->thickness;
        } else {
            $this->thickness = $thickness;
        }
        if ($linestyle == '') {
            $linestyle = $this->linestyle;
        } else {
            $this->linestyle = $linestyle;
        }
        PHPImageTools::checksize('Center X', $cx, $this->width);
        PHPImageTools::checksize('Center Y', $cy, $this->height);
        PHPImageTools::checksize('Radius', $r, $this->width);
        $linecolor = PHPImageTools::checkcolor($linecolor);
        PHPImageTools::checkinteger('Thickness', $thickness, 1);
        $linestyle = PHPImageTools::checklinestyle('Line style', $linestyle);
        $this->drawellipse($cx, $cy, 2 * $r, 2 * $r, $linecolor, $thickness, $linestyle);
    }

    /**
     * @param mixed $cx
     * @param mixed $cy
     * @param mixed $w
     * @param mixed $h
     * @param mixed $linecolor
     * @param mixed $thickness
     * @param mixed $linestyle
     */
    public function drawellipse($cx, $cy, $w, $h, $linecolor = '', $thickness = 0, $linestyle = '')
    {
        if ($linecolor == '') {
            $linecolor = $this->linecolor;
        } else {
            $this->linecolor = $linecolor;
        }
        if ($thickness == 0) {
            $thickness = $this->thickness;
        } else {
            $this->thickness = $thickness;
        }
        if ($linestyle == '') {
            $linestyle = $this->linestyle;
        } else {
            $this->linestyle = $linestyle;
        }
        PHPImageTools::checksize('Center X', $cx, $this->width);
        PHPImageTools::checksize('Center Y', $cy, $this->height);
        PHPImageTools::checksize('Width', $w, $this->width);
        PHPImageTools::checksize('Height', $h, $this->height);
        PHPImageTools::checkcolor($linecolor);
        PHPImageTools::checkinteger('Thickness', $thickness, 1);
        PHPImageTools::checklinestyle('Line style', $linestyle);

        $length = PHPImageTools::ellipseperimeter($w, $h);
        PHPImageTools::getn1n2($n1, $n2, $nb, $n, $linestyle, $thickness, $length);
        $c = $this->colorallocate($linecolor);

        switch ($linestyle) {
            case 'dot':
                if ($n > 1) {
                    for ($i = 0; $i < $n - 1; $i++) {
                        $a = PHPImageTools::deg2rad($i * 360 / ($n - 1));
                        $x = $cx + 0.5 * $w * cos($a);
                        $y = $cy + 0.5 * $h * sin($a);
                        imagefilledellipse($this->img, round($x), round($y), $thickness, $thickness, $c);
                    }
                } else {
                    PHPImageTools::imageellipse($this->img, $cx, $cy, $w, $h, $c, $thickness);
                }
                break;
            case 'square':
            case 'dash':
            case 'bigdash':
                if ($n > 1) {
                    for ($i = 0; $i < $n - 1; $i++) {
                        $a1 = $i * 360 / ($n - 1);
                        $a2 = $a1 + 360 * ($n1) / $nb;
                        PHPImageTools::imageellipsearc($this->img, $cx, $cy, $w, $h, $a1, $a2, $c, $thickness);
                    }
                } else {
                    PHPImageTools::imageellipse($this->img, $cx, $cy, $w, $h, $c, $thickness);
                }
                break;
            case 'double':
                $e = $thickness / 3;
                PHPImageTools::imageellipse($this->img, $cx, $cy, $w + 2 * $e, $h + 2 * $e, $c, $e);
                PHPImageTools::imageellipse($this->img, $cx, $cy, $w - 2 * $e, $h - 2 * $e, $c, $e);
                break;
            case 'triple':
                $e = $thickness / 5;
                PHPImageTools::imageellipse($this->img, $cx, $cy, $w, $h, $c, $e);
                PHPImageTools::imageellipse($this->img, $cx, $cy, $w + 4 * $e, $h + 4 * $e, $c, $e);
                PHPImageTools::imageellipse($this->img, $cx, $cy, $w - 4 * $e, $h - 4 * $e, $c, $e);
                break;
            case 'solid':
                PHPImageTools::imageellipse($this->img, $cx, $cy, $w, $h, $c, $thickness);
                break;
            default:
                if (is_array($linestyle)) {
                    $list = PHPImageTools::getlinestylelist($this, $linestyle);
                    imagesetthickness($this->img, $thickness);
                    imagesetstyle($this->img, $list);
                    imageellipse($this->img, $cx, $cy, $w, $h, IMG_COLOR_STYLED);
                    imagesetthickness($this->img, 1);
                }
                break;
        }
    }

    /**
     * @param mixed $cx
     * @param mixed $cy
     * @param mixed $r
     * @param mixed $color
     */
    public function drawfilledcircle($cx, $cy, $r, $color = '')
    {
        if ($color == '') {
            $color = $this->fillcolor;
        } else {
            $this->fillcolor = $color;
        }
        PHPImageTools::checksize('Center X', $cx, $this->width);
        PHPImageTools::checksize('Center Y', $cy, $this->height);
        PHPImageTools::checksize('Radius', $r, $this->width);
        $color = PHPImageTools::checkcolor($color);
        $this->drawfilledellipse($cx, $cy, 2 * $r, 2 * $r, $color);
    }

    /**
     * @param mixed $cx
     * @param mixed $cy
     * @param mixed $w
     * @param mixed $h
     * @param mixed $color
     */
    public function drawfilledellipse($cx, $cy, $w, $h, $color = '')
    {
        if ($color == '') {
            $color = $this->fillcolor;
        } else {
            $this->fillcolor = $color;
        }
        PHPImageTools::checksize('Center X', $cx, $this->width);
        PHPImageTools::checksize('Center Y', $cy, $this->height);
        PHPImageTools::checksize('Width', $w, $this->width);
        PHPImageTools::checksize('Height', $h, $this->height);
        PHPImageTools::checkcolor($color);
        imagefilledellipse($this->img, $cx, $cy, $w, $h, $this->colorallocate($color));
    }

    /**
     * @param mixed $alpha
     * @param mixed $forcetransparentpixels
     */
    public function setalpha($alpha, $forcetransparentpixels = false)
    {
        PHPImageTools::checksize('Alpha', $alpha, 127);
        PHPImageTools::checkinteger('Alpha', $alpha, 0, 127);
        $w = $this->width;
        $h = $this->height;
        $this->alphablending(false);
        if ($forcetransparentpixels) {
            for ($y = 0; $y < $h; $y++) {
                for ($x = 0; $x < $w; $x++) {
                    $val = imagecolorat($this->img, $x, $y);
                    $R = ($val >> 16) & 0xFF;
                    $G = ($val >> 8) & 0xFF;
                    $B = $val & 0xFF;
                    $color = imagecolorallocatealpha($this->img, $R, $G, $B, $alpha);
                    imagesetpixel($this->img, $x, $y, $color);
                }
            }
        } else {
            for ($y = 0; $y < $h; $y++) {
                for ($x = 0; $x < $w; $x++) {
                    $val = imagecolorat($this->img, $x, $y);
                    $A = $val >> 24;
                    if ($A != 127) {
                        $R = ($val >> 16) & 0xFF;
                        $G = ($val >> 8) & 0xFF;
                        $B = $val & 0xFF;
                        $color = imagecolorallocatealpha($this->img, $R, $G, $B, $alpha);
                        imagesetpixel($this->img, $x, $y, $color);
                    }
                }
            }
        }
        $this->alphablending(true);
    }

    /**
     * @param mixed $srccolor
     * @param mixed $dstcolor
     * @param mixed $keeptransparency
     */
    public function replacecoloralpha($srccolor, $dstcolor, $keeptransparency = false)
    {
        PHPImageTools::checkcolor($srccolor);
        PHPImageTools::checkcolor($dstcolor);
        list($SR, $SG, $SB, $SA) = $srccolor;
        list($DR, $DG, $DB, $DA) = $dstcolor;
        $w = $this->width;
        $h = $this->height;
        $this->alphablending(false);
        if ($keeptransparency) {
            for ($y = 0; $y < $h; $y++) {
                for ($x = 0; $x < $w; $x++) {
                    $val = imagecolorat($this->img, $x, $y);
                    $A = $val >> 24;
                    $R = ($val >> 16) & 0xFF;
                    $G = ($val >> 8) & 0xFF;
                    $B = $val & 0xFF;
                    if ($R == $SR && $G == $SG && $B == $SB && $A == $SA) {
                        $color = imagecolorallocatealpha($this->img, $DR, $DG, $DB, $A);
                        imagesetpixel($this->img, $x, $y, $color);
                    }
                }
            }
        } else {
            for ($y = 0; $y < $h; $y++) {
                for ($x = 0; $x < $w; $x++) {
                    $val = imagecolorat($this->img, $x, $y);
                    $A = $val >> 24;
                    $R = ($val >> 16) & 0xFF;
                    $G = ($val >> 8) & 0xFF;
                    $B = $val & 0xFF;
                    if ($R == $SR && $G == $SG && $B == $SB && $A == $SA) {
                        $color = imagecolorallocatealpha($this->img, $DR, $DG, $DB, $DA);
                        imagesetpixel($this->img, $x, $y, $color);
                    }
                }
            }
        }
        $this->alphablending(true);
    }

    /**
     * @param mixed $srccolor
     * @param mixed $dstcolor
     * @param mixed $keeptransparency
     */
    public function replacecolor($srccolor, $dstcolor, $keeptransparency = true)
    {
        PHPImageTools::checkcolor($srccolor);
        PHPImageTools::checkcolor($dstcolor);
        list($SR, $SG, $SB, $SA) = $srccolor;
        list($DR, $DG, $DB, $DA) = $dstcolor;
        $w = $this->width;
        $h = $this->height;
        $this->alphablending(false);
        if ($keeptransparency) {
            for ($y = 0; $y < $h; $y++) {
                for ($x = 0; $x < $w; $x++) {
                    $val = imagecolorat($this->img, $x, $y);
                    $A = $val >> 24;
                    $R = ($val >> 16) & 0xFF;
                    $G = ($val >> 8) & 0xFF;
                    $B = $val & 0xFF;
                    if ($R == $SR && $G == $SG && $B == $SB) {
                        $color = imagecolorallocatealpha($this->img, $DR, $DG, $DB, $A);
                        imagesetpixel($this->img, $x, $y, $color);
                    }
                }
            }
        } else {
            for ($y = 0; $y < $h; $y++) {
                for ($x = 0; $x < $w; $x++) {
                    $val = imagecolorat($this->img, $x, $y);
                    $A = $val >> 24;
                    $R = ($val >> 16) & 0xFF;
                    $G = ($val >> 8) & 0xFF;
                    $B = $val & 0xFF;
                    if ($R == $SR && $G == $SG && $B == $SB) {
                        $color = imagecolorallocatealpha($this->img, $DR, $DG, $DB, $DA);
                        imagesetpixel($this->img, $x, $y, $color);
                    }
                }
            }
        }
        $this->alphablending(true);
    }

    /**
     * @param mixed $mask
     */
    public function mask(&$mask)
    {
        $masksrc = PHPImageTools::getimageresource($mask);
        $w = $this->width;
        $h = $this->height;
        $tmp = new PHPImage($w, $h);
        $tmp->copyresampled($masksrc);
        $tmp->effect('grayscale');
        $this->alphablending(false);
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $val = imagecolorat($tmp->img, $x, $y);
                $A = intval(($val & 0xFF) / 2);
                $val = imagecolorat($this->img, $x, $y);
                $R = ($val >> 16) & 0xFF;
                $G = ($val >> 8) & 0xFF;
                $B = $val & 0xFF;
                $color = imagecolorallocatealpha($this->img, $R, $G, $B, $A);
                imagesetpixel($this->img, $x, $y, $color);
            }
        }
        $this->alphablending(true);
        $tmp->destroy();
    }

    /**
     * @param mixed $matrix
     * @param mixed $offset
     * @param mixed $usealpha
     * @param mixed $divAlpha
     */
    public function convolution($matrix, $offset = 0, $usealpha = false, $divAlpha = null)
    {
        $dim = PHPImageTools::checkmatrix('[convolution]', $matrix);
        $div = 0;
        foreach ($matrix as $row) {
            $div += array_sum($row);
        }
        PHPImageTools::checkfloat('divisor ', $div);
        PHPImageTools::checkfloat('offset ', $offset);
        if ($dim == 3 && !$usealpha) {
            imageconvolution($this->img, $matrix, $div, $offset);
        } elseif (!$usealpha) {
            $tmp = $this->getclone();
            $w = $this->width;
            $h = $this->height;
            $n = ($dim - 1) / 2;
            $this->alphablending(false);
            for ($j = 0; $j < $h; ++$j) {
                for ($i = 0; $i < $w; ++$i) {
                    $RR = $GG = $BB = $AA = 0;
                    for ($k = 0; $k < $dim; $k++) {
                        for ($l = 0; $l < $dim; $l++) {
                            $coeff = $matrix[$k][$l];
                            if ($coeff != 0) {
                                $x = $i + $l - $n;
                                $y = $j + $k - $n;
                                if ($x < 0) {
                                    $x = 0;
                                } elseif ($x > $w - 1) {
                                    $x = $w - 1;
                                }
                                if ($y < 0) {
                                    $y = 0;
                                } elseif ($y > $h - 1) {
                                    $y = $h - 1;
                                }
                                $val = imagecolorat($tmp->img, $x, $y);
                                $R = ($val >> 16) & 0xFF;
                                $G = ($val >> 8) & 0xFF;
                                $B = $val & 0xFF;
                                $RR += $R * $coeff;
                                $GG += $G * $coeff;
                                $BB += $B * $coeff;
                            }
                        }
                    }
                    if ($div == 0) {
                        $RR += 128;
                        $GG += 128;
                        $BB += 128;
                    } elseif ($div != 1) {
                        $RR = intval($RR / $div);
                        $GG = intval($GG / $div);
                        $BB = intval($BB / $div);
                    }
                    if ($offset != 0) {
                        $RR += $offset;
                        $GG += $offset;
                        $BB += $offset;
                        if ($RR > 255) {
                            $RR = 255;
                        } elseif ($RR < 0) {
                            $RR = 0;
                        }
                        if ($GG > 255) {
                            $GG = 255;
                        } elseif ($GG < 0) {
                            $GG = 0;
                        }
                        if ($BB > 255) {
                            $BB = 255;
                        } elseif ($BB < 0) {
                            $BB = 0;
                        }
                    }
                    $color = imagecolorallocate($this->img, $RR, $GG, $BB);
                    imagesetpixel($this->img, $i, $j, $color);
                }
            }
            $this->alphablending(true);
            $tmp->destroy();
        } else {
            $tmp = $this->getclone();
            $w = $this->width;
            $h = $this->height;
            $n = ($dim - 1) / 2;
            if ($divAlpha === null || $divAlpha === 0) {
                $divAlpha = $div;
            }
            $this->alphablending(false);
            for ($j = 0; $j < $h; ++$j) {
                for ($i = 0; $i < $w; ++$i) {
                    $RR = $GG = $BB = $AA = 0;
                    for ($k = 0; $k < $dim; $k++) {
                        for ($l = 0; $l < $dim; $l++) {
                            $coeff = $matrix[$k][$l];
                            if ($coeff != 0) {
                                $x = $i + $l - $n;
                                $y = $j + $k - $n;
                                if ($x < 0) {
                                    $x = 0;
                                } elseif ($x > $w - 1) {
                                    $x = $w - 1;
                                }
                                if ($y < 0) {
                                    $y = 0;
                                } elseif ($y > $h - 1) {
                                    $y = $h - 1;
                                }
                                $val = imagecolorat($tmp->img, $x, $y);
                                $A = $val >> 24;
                                $R = ($val >> 16) & 0xFF;
                                $G = ($val >> 8) & 0xFF;
                                $B = $val & 0xFF;
                                $RR += $R * $coeff;
                                $GG += $G * $coeff;
                                $BB += $B * $coeff;
                                $AA += (127 - $A) * $coeff;
                            }
                        }
                    }
                    if ($div == 0) {
                        $RR += 128;
                        $GG += 128;
                        $BB += 128;
                    } elseif ($div != 1) {
                        $RR = intval($RR / $div);
                        $GG = intval($GG / $div);
                        $BB = intval($BB / $div);
                    }
                    if ($divAlpha == 0) {
                        $AA = 64 - $AA;
                    } else {
                        $AA = 127 - intval($AA / $divAlpha);
                    }
                    if ($AA > 127) {
                        $AA = 127;
                    } elseif ($AA < 0) {
                        $AA = 0;
                    }
                    if ($offset != 0) {
                        $RR += $offset;
                        $GG += $offset;
                        $BB += $offset;
                        if ($RR > 255) {
                            $RR = 255;
                        } elseif ($RR < 0) {
                            $RR = 0;
                        }
                        if ($GG > 255) {
                            $GG = 255;
                        } elseif ($GG < 0) {
                            $GG = 0;
                        }
                        if ($BB > 255) {
                            $BB = 255;
                        } elseif ($BB < 0) {
                            $BB = 0;
                        }
                    }
                    $color = imagecolorallocatealpha($this->img, $RR, $GG, $BB, $AA);
                    //$color = imagecolorallocate($this->img, $RR, $GG, $BB);
                    imagesetpixel($this->img, $i, $j, $color);
                }
            }
            $this->alphablending(true);
            $this->savealpha(false);
            $tmp->destroy();
        }
    }

    /**
     * @param mixed $effect
     * @param mixed $arg1
     * @param mixed $arg2
     * @param mixed $arg3
     */
    public function effect($effect, $arg1 = null, $arg2 = null, $arg3 = null)
    {
        $effect = strtolower(trim($effect));
        // filters
        switch ($effect) {
            case 'edgedetect':
                $filter = IMG_FILTER_EDGEDETECT;
                break;
            case 'emboss':
                $filter = IMG_FILTER_EMBOSS;
                break;
            case 'invert':
            case 'negate':
                $filter = IMG_FILTER_NEGATE;
                break;
            case 'grayscale':
                $filter = IMG_FILTER_GRAYSCALE;
                break;
            case 'blur':
            case 'gaussian_blur' :
                $filter = IMG_FILTER_GAUSSIAN_BLUR;
                break;
            case 'selective_blur':
                $filter = IMG_FILTER_SELECTIVE_BLUR;
                break;
            case 'sharpen':
            case 'mean_removal':
                $filter = IMG_FILTER_MEAN_REMOVAL;
                break;
            case 'brightness':
                $filter = IMG_FILTER_BRIGHTNESS;
                break;
            case 'contrast':
                $filter = IMG_FILTER_CONTRAST;
                break;
            case 'smooth':
                $filter = IMG_FILTER_SMOOTH;
                break;
            case 'colorize':
                $filter = IMG_FILTER_COLORIZE;
                break;
            case 'watermark':
            case 'points':
            case 'sepia':
            case 'mosaic':
            case 'flipv':
            case 'fliph':
            case 'threshold':
                break;
            default:
                throw new PHPImageException("L'effet '$effect' n'existe pas !");
        }
        // arguments
        switch ($effect) {
            case 'blur':
            case 'gaussian_blur':
                $arg1 = $arg1 === null ? 1 : $arg1;
                PHPImageTools::checkinteger('Effect gaussian_blur argument 1', $arg1, 1, 3);
                break;
            case 'selective_blur':
                $arg1 = $arg1 === null ? 1 : $arg1;
                PHPImageTools::checkinteger('Effect selective_blur argument 1', $arg1, 1, 3);
                break;
            case 'sharpen':
            case 'mean_removal':
                $arg1 = $arg1 === null ? 1 : $arg1;
                PHPImageTools::checkinteger('Effect mean_removal argument 1', $arg1, 1, 3);
                break;
            case 'points':
            case 'threshold':
                $arg1 = $arg1 === null ? 127 : $arg1;
                PHPImageTools::checkinteger('Effect threshold argument 1', $arg1, 1, 254);
                break;
            case 'brightness':
                $arg1 = $arg1 === null ? 10 : $arg1;
                PHPImageTools::checkinteger('Effect brightness argument 1', $arg1);
                break;
            case 'contrast':
                $arg1 = $arg1 === null ? -10 : -$arg1;
                PHPImageTools::checkinteger('Effect contrast argument 1', $arg1);
                break;
            case 'smooth':
                $arg1 = $arg1 === null ? 1 : $arg1;
                PHPImageTools::checkinteger('Effect smooth argument 1', $arg1);
                break;
            case 'mosaic':
                $arg1 = $arg1 === null ? 5 : $arg1;
                $arg2 = $arg2 === null ? $arg1 : $arg2;
                PHPImageTools::checkinteger('Effect mosaic argument 1 (width)', $arg1, 2);
                PHPImageTools::checkinteger('Effect mosaic argument 2 (height)', $arg2, 2);
                break;
            case 'colorize':
                $arg1 = $arg1 === null ? 0 : $arg1;
                $arg2 = $arg2 === null ? 0 : $arg2;
                $arg3 = $arg3 === null ? 0 : $arg3;
                PHPImageTools::checkinteger('Effect colorize argument 1 (Red)', $arg1, -255, 255);
                PHPImageTools::checkinteger('Effect colorize argument 2 (Green)', $arg2, -255, 255);
                PHPImageTools::checkinteger('Effect colorize argument 3 (Blue)', $arg3, -255, 255);
                break;
        }
        // do the effect
        switch ($effect) {
            case 'edgedetect':
            case 'emboss':
            case 'invert':
            case 'negate':
            case 'grayscale':
                imagefilter($this->img, $filter);
                break;
            case 'blur':
            case 'gaussian_blur':
            case 'selective_blur':
            case 'sharpen':
            case 'mean_removal':
                for ($i = 0; $i < $arg1; $i++) {
                    imagefilter($this->img, $filter);
                }
                break;
            case 'brightness':
            case 'smooth':
            case 'contrast':
                imagefilter($this->img, $filter, $arg1);
                break;
            case 'colorize':
                imagefilter($this->img, $filter, $arg1, $arg2, $arg3);
                break;
            case 'watermark':
                $arg1 = $arg1 === null ? false : ($arg1 === true);
                $arg2 = $arg2 === null ? false : ($arg2 === true);
                $tmp = new PHPImage();
                $tmp->bgcolor = '0 white';
                $tmp->create($this->width, $this->height);
                $tmp->copy($this->img);
                $tmp->effect('grayscale');
                $tmp->effect('emboss');
                if ($arg2) {
                    $tmp->effect('blur');
                }
                if ($arg1) {
                    $tmp->effect('invert');
                    $tmp->maketransparent('128 128 128 0');
                } else {
                    $tmp->maketransparent('127 127 127 0');
                }
                $this->cleanall();
                $realcopy = $this->realcopy;
                $this->realcopy = false;
                $tmp->savealpha(false);
                $this->copy($tmp, 0, 0, 0, 0, 0, 0, 0);
                $this->realcopy = $realcopy;
                $tmp->destroy();
                break;
            case 'sepia':
                $this->effect('grayscale');
                $this->effect('colorize', 112, 66, 20);
                $this->effect('brightness', -15);
                break;
            case 'mosaic':
                $w = $this->width;
                $h = $this->height;
                $this->resize(floor($w / $arg1), floor($h / $arg2));
                $this->resize($w, $h);
                break;
            case 'flipv':
                $w = $this->width;
                $h = $this->height;
                $tmp = $this->getclone();
                $this->cleanall();
                for ($i = 0; $i < $h; ++$i) {
                    imagecopy($this->img, $tmp->img, 0, $h - 1 - $i, 0, $i, $w, 1);
                }
                $tmp->destroy();
                break;
            case 'fliph':
                $w = $this->width;
                $h = $this->height;
                $tmp = $this->getclone();
                $this->cleanall();
                for ($i = 0; $i < $w; ++$i) {
                    imagecopy($this->img, $tmp->img, $w - 1 - $i, 0, $i, 0, 1, $h);
                }
                $tmp->destroy();
                break;
            case 'threshold':
                $this->effect('grayscale');
                $w = $this->width;
                $h = $this->height;
                for ($j = 0; $j < $h; ++$j) {
                    for ($i = 0; $i < $w; ++$i) {
                        $gray = imagecolorat($this->img, $i, $j);
                        $alpha = $gray >> 24;
                        $gray = $gray & 0xFF;
                        $nb = $gray > $arg1 ? 255 : 0;
                        $color = imagecolorallocatealpha($this->img, $nb, $nb, $nb, $alpha);
                        imagesetpixel($this->img, $i, $j, $color);
                    }
                }
                break;
            case 'points':
                $arg1 = $arg1 === null ? 127 : $arg1;
                $this->effect('grayscale');
                $w = $this->width;
                $h = $this->height;
                for ($j = 0; $j < $h; ++$j) {
                    for ($i = 0; $i < $w; ++$i) {
                        $gray = imagecolorat($this->img, $i, $j);
                        $alpha = $gray >> 24;
                        $gray = $gray & 0xFF;
                        $nb = $gray > $arg1 ? 255 : 0;
                        $color = imagecolorallocatealpha($this->img, $nb, $nb, $nb, $alpha);
                        imagesetpixel($this->img, $i, $j, $color);

                        $err = ($gray - $nb) / 16;
                        if ($j + 1 < $h) {
                            if ($i > 0) {
                                $gray = imagecolorat($this->img, $i - 1, $j + 1);
                                $alpha = $gray >> 24;
                                $gray = $gray & 0xFF;
                                $gray += 3 * $err;
                                $gray = intval($gray);
                                if ($gray > 255) {
                                    $gray = 255;
                                } elseif ($gray < 0) {
                                    $gray = 0;
                                }
                                $color = imagecolorallocatealpha($this->img, $gray, $gray, $gray, $alpha);
                                imagesetpixel($this->img, $i - 1, $j + 1, $color);
                            }

                            $gray = imagecolorat($this->img, $i, $j + 1);
                            $alpha = $gray >> 24;
                            $gray = $gray & 0xFF;
                            $gray += 5 * $err;
                            $gray = intval($gray);
                            if ($gray > 255) {
                                $gray = 255;
                            } elseif ($gray < 0) {
                                $gray = 0;
                            }
                            $color = imagecolorallocatealpha($this->img, $gray, $gray, $gray, $alpha);
                            imagesetpixel($this->img, $i, $j + 1, $color);

                            if ($i + 1 < $w) {
                                $gray = imagecolorat($this->img, $i + 1, $j + 1);
                                $alpha = $gray >> 24;
                                $gray = $gray & 0xFF;
                                $gray += $err;
                                $gray = intval($gray);
                                if ($gray > 255) {
                                    $gray = 255;
                                } elseif ($gray < 0) {
                                    $gray = 0;
                                }
                                $color = imagecolorallocate($this->img, $gray, $gray, $gray);
                                imagesetpixel($this->img, $i + 1, $j + 1, $color);
                            }
                        }
                        if ($i + 1 < $w) {
                            $gray = imagecolorat($this->img, $i + 1, $j);
                            $alpha = $gray >> 24;
                            $gray = $gray & 0xFF;
                            $gray += 7 * $err;
                            $gray = intval($gray);
                            if ($gray > 255) {
                                $gray = 255;
                            } elseif ($gray < 0) {
                                $gray = 0;
                            }
                            $color = imagecolorallocatealpha($this->img, $gray, $gray, $gray, $alpha);
                            imagesetpixel($this->img, $i + 1, $j, $color);
                        }
                    }
                }
                break;
        }
    }

    /**
     * @param mixed $width
     * @param mixed $height
     */
    public function create($width, $height)
    {
        PHPImageTools::checkinteger('width', $width, 1, 4000);
        PHPImageTools::checkinteger('height', $height, 1, 4000);
        $this->destroy();
        $this->width = $width;
        $this->height = $height;
        $this->img = imagecreatetruecolor($width, $height);
        $this->savealpha(true);
        $this->alphablending(true);
        $bgcolor = new PHPImageColor($this->bgcolor);
        $this->fill(0, 0, $this->bgcolor);
        if ($bgcolor->A == 127) {
            $this->maketransparent($this->bgcolor);
        }
    }

    /**
     *
     */
    public function destroy()
    {
        if (isset($this->img)) {
            if ($this->img != null) {
                imagedestroy($this->img);
                $this->img = null;
            }
        }
        $this->colors = array();
        $this->height = 0;
        $this->width = 0;
    }

    /**
     * @param mixed $color
     */
    public function maketransparent($color)
    {
        imagecolortransparent($this->img, $this->colorallocate($color));
    }

    /**
     * @param mixed $x
     * @param mixed $y
     * @param mixed $color
     */
    public function fill($x, $y, $color)
    {
        PHPImageTools::checksize('X', $x, $this->width);
        PHPImageTools::checksize('Y', $y, $this->height);
        imagefill($this->img, round($x), round($y), $this->colorallocate($color));
    }

    /**
     * @param mixed $bool
     */
    public function alphablending($bool = true)
    {
        imagealphablending($this->img, $bool === true);
    }

    /**
     * @param mixed $bool
     */
    public function antialias($bool = true)
    {
        imageantialias($this->img, $bool === true);
    }

    /**
     * @param mixed $color
     * @return mixed
     */
    public function colorallocate($color)
    {
        if (is_array($color)) {
            $color = implode(' ', $color);
        }
        $C = new PHPImageColor($color);
        $key = $C->getvalues();
        if (!isset($this->colors[$key])) {
            $val = imagecolorallocatealpha($this->img, $C->R, $C->G, $C->B, $C->A);
            $this->colors[$key] = $val;
        }
        return $this->colors[$key];
    }

    /**
     * @param mixed $alpha
     * @return mixed
     */
    public function getnewcolor($alpha = 0)
    {
        $color = rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255);
        while (array_key_exists($color . ',' . $alpha, $this->colors)) {
            $color = rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255);
        }
        if ($alpha == 0) {
            return $color;
        } else {
            return $color . ',' . $alpha;
        }
    }

    /**
     * @param mixed $x
     * @param mixed $y
     * @return mixed
     */
    public function colorat($x, $y)
    {
        $val = imagecolorat($this->img, $x, $y);
        $A = $val >> 24;
        $R = $val >> 16 & 0xFF;
        $G = $val >> 8 & 0xFF;
        $B = $val & 0xFF;
        return new PHPImageColor("$R,$G,$B,$A");
    }

    /**
     * @param mixed $x
     * @param mixed $y
     * @param mixed $color
     */
    public function setpixel($x, $y, $color)
    {
        imagesetpixel($this->img, $x, $y, $this->colorallocate($color));
    }

    /**
     * @param mixed $bool
     */
    public function savealpha($bool = true)
    {
        imagesavealpha($this->img, $bool === true);
    }

    /**
     * @param mixed $filename
     * @param mixed $quality
     * @return mixed
     */
    public function savetofile($filename, $quality = -1)
    {
        $quality = $quality < 0 ? $this->quality : $quality;
        $ext = PHPImageTools::getfileext($filename);
        if ($this->format != 'simplepng' || $ext != 'png') {
            $this->format = $ext;
        }
        switch ($this->format) {
            case 'gif':
            case 'simplepng':
                $this->savealpha(false);
        }
        switch ($this->format) {
            case 'gif':
                imagegif($this->img, $filename);
                break;
            case 'jpg':
                imagejpeg($this->img, $filename, $quality);
                break;
            default:
                imagepng($this->img, $filename);
        }
        switch ($this->format) {
            case 'gif':
            case 'simplepng':
                $this->savealpha(true);
        }
        return true;
    }

    /**
     * @param mixed $filename
     * @param mixed $source
     */
    public function cacheok($source, $cache)
    {
        if (!file_exists($cache)) {
            PHPImageTools::createfolderpath(dirname($cache));
            return false;
        }
        if (file_exists($cache) && filemtime($cache) < filemtime($source)) {
            @unlink($cache);
            return false;
        }
        return true;
    }

    /**
     * @param mixed $filename
     */
    public function headers($filename = '')
    {
        if (!empty($filename) && file_exists($filename)) {
            $last_modified = filemtime($filename);
        } else {
            $last_modified = time();
        }

        $etag = dechex($last_modified);

        // headers
        header('Expires: ' . gmdate('D, d M Y H:i:s', $last_modified + $this->cachetime) . ' GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');
        header('ETag: ' . $etag);
        if ($this->cachetime <= 0) {
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
        } else {
            header('Cache-Control: max-age=' . $this->cachecontrol . ', s-maxage=' . $this->cachecontrol);
            header('Pragma:');
            if (function_exists('http_match_etag') && function_exists('http_match_modified')) {
                if (http_match_etag($etag) || http_match_modified($last_modified)) {
                    header('HTTP/1.1 304 Not Modified');
                    exit;
                }
            }
        }
        if (!empty($filename)) {
            $ext = PHPImageTools::getfileext($filename);
            if ($this->format != 'simplepng' || $ext != 'png') {
                $this->format = $ext;
            }
        }
        switch ($this->format) {
            case 'gif':
                header('Content-type: image/gif');
                break;
            case 'simplepng':
                header('Content-type: image/png');
                break;
            case 'jpg':
                header('Content-type: image/jpeg');
                break;
            default:
                header('Content-type: image/png');
                break;
        }

        $this->headers_sent = true;
    }

    /**
     * @param mixed $filename
     * @param mixed $quality
     * @return mixed
     */
    public function display($filename = '', $quality = -1)
    {
        $quality = $quality < 0 ? $this->quality : $quality;

        if (!$this->headers_sent) {
            $this->headers($filename);
        }

        // return content file if exists
        if (!empty($filename) && file_exists($filename)) {
            PHPImageTools::readfile($filename);
            return true;
        }

        if (!empty($filename)) {
            $this->savetofile($filename);
            PHPImageTools::readfile($filename);
            return true;
        }

        // directly display
        switch ($this->format) {
            case 'gif':
            case 'simplepng':
                $this->savealpha(false);
        }
        switch ($this->format) {
            case 'gif':
                imagegif($this->img);
                break;
            case 'jpg':
                imagejpeg($this->img, null, $quality);
                break;
            default:
                imagepng($this->img);
        }
        switch ($this->format) {
            case 'gif':
            case 'simplepng':
                $this->savealpha(true);
        }
        return true;
    }

    /**
     * @param mixed $filename
     * @return mixed
     */
    public function loadfromfile($filename = '')
    {
        if (!file_exists($filename)) {
            throw new PHPImageException("the image file '$filename' doesn't exists !");
        }
        $ext = PHPImageTools::getfileext($filename);
        switch ($ext) {
            case 'gif':
                $tmp = imagecreatefromgif($filename);
                break;
            case 'jpg':
                $tmp = imagecreatefromjpeg($filename);
                break;
            case 'png':
                $tmp = imagecreatefrompng($filename);
                break;
            default:
                throw new PHPImageException("'$ext' file extension is not supported");
        }
        $this->format = $ext;
        switch ($ext) {
            case 'gif':
            case 'jpg':
            case 'png':
                $this->create(imagesx($tmp), imagesy($tmp));
                $this->copy($tmp);
                return true;
        }
        $this->destroy();
        return false;
    }

    /**
     * @param mixed $x
     * @param mixed $y
     */
    public function cleanat($x, $y)
    {
        $this->fill($x, $y, $this->bgcolor);
    }

    /**
     *
     */
    public function cleanall()
    {
        $w = $this->width;
        $h = $this->height;
        $this->destroy();
        $this->create($w, $h);
    }

    /**
     * @param mixed $x1
     * @param mixed $y1
     * @param mixed $x2
     * @param mixed $y2
     */
    public function cleanrectangle($x1, $y1, $x2, $y2)
    {
        $this->drawfilledrectangle($x1, $y1, $x2, $y2, $this->getnewcolor());
        $this->fill(floor(($x1 + $x2) / 2), floor(($y1 + $y2) / 2), $this->bgcolor);
    }

    /**
     * @param mixed $x1
     * @param mixed $y1
     * @param mixed $w
     * @param mixed $h
     */
    public function cleanrectanglewh($x1, $y1, $w, $h)
    {
        $x2 = $x1 + ($w == 0 ? 1 : $w) - 1;
        $y2 = $y1 + ($w == 0 ? 1 : $w) - 1;
        $this->cleanrectangle($x1, $x2, $y1, $y2);
    }

    /**
     * @param mixed $src
     * @param mixed $dstx
     * @param mixed $dsty
     * @param mixed $srcx
     * @param mixed $srcy
     * @param mixed $dstw
     * @param mixed $dsth
     * @param mixed $srcw
     * @param mixed $srch
     * @param mixed $alpha
     * @param mixed $dstpos
     * @param mixed $srcpos
     * @return mixed
     */
    public function copyresampledfit(
        &$src,
        $dstx = 0,
        $dsty = 0,
        $srcx = 0,
        $srcy = 0,
        $dstw = 0,
        $dsth = 0,
        $srcw = 0,
        $srch = 0,
        $alpha = -1,
        $dstpos = '',
        $srcpos = ''
    ) {
        $srcimg = PHPImageTools::getimageresource($src);
        PHPImageTools::checkresource('Source image', $srcimg);
        $srcw = $srcw == 0 ? imagesx($srcimg) : $srcw;
        $srch = $srch == 0 ? imagesy($srcimg) : $srch;
        $dstw = $dstw == 0 ? $this->width : $dstw;
        $dsth = $dsth == 0 ? $this->height : $dsth;
        PHPImageTools::checksize('Destination X', $dstx, $this->width);
        PHPImageTools::checksize('Destination Y', $dsty, $this->height);
        PHPImageTools::checksize('Destination Width', $dstw, $this->width);
        PHPImageTools::checksize('Destination Height', $dsth, $this->height);
        PHPImageTools::checksize('Source X', $srcx, imagesx($srcimg));
        PHPImageTools::checksize('Source X', $srcy, imagesy($srcimg));
        PHPImageTools::checksize('Source Width', $srcw, imagesx($srcimg));
        PHPImageTools::checksize('Source Height', $srch, imagesy($srcimg));
        PHPImageTools::checksize('Alpha', $alpha, 127);
        PHPImageTools::checkinteger('Alpha', $alpha, -1, 127);
        PHPImageTools::resizefit($srcw, $srch, $dstw, $dsth);
        PHPImageTools::checkposition('Destination position', $dstpos, $dstx, $dsty, $dstw, $dsth);
        PHPImageTools::checkposition('Source position', $srcpos, $srcx, $srcy, $srcw, $srch);

        $tmp = new PHPImage($dstw, $dsth);
        imagecopyresampled($tmp->img, $srcimg, 0, 0, $srcx, $srcy, $dstw, $dsth, $srcw, $srch);
        $this->copy($tmp, $dstx, $dsty, 0, 0, $dstw, $dsth, $alpha);
        $tmp->destroy();
        return true;
    }

    /**
     * @param mixed $src
     * @param mixed $dstx
     * @param mixed $dsty
     * @param mixed $srcx
     * @param mixed $srcy
     * @param mixed $dstw
     * @param mixed $dsth
     * @param mixed $srcw
     * @param mixed $srch
     * @param mixed $alpha
     * @param mixed $dstpos
     * @param mixed $srcpos
     * @return mixed
     */
    public function copyresizedfit(
        &$src,
        $dstx = 0,
        $dsty = 0,
        $srcx = 0,
        $srcy = 0,
        $dstw = 0,
        $dsth = 0,
        $srcw = 0,
        $srch = 0,
        $alpha = -1,
        $dstpos = '',
        $srcpos = ''
    ) {
        $srcimg = PHPImageTools::getimageresource($src);
        PHPImageTools::checkresource('Source image', $srcimg);
        $srcw = $srcw == 0 ? imagesx($srcimg) : $srcw;
        $srch = $srch == 0 ? imagesy($srcimg) : $srch;
        $dstw = $dstw == 0 ? $this->width : $dstw;
        $dsth = $dsth == 0 ? $this->height : $dsth;
        PHPImageTools::checksize('Destination X', $dstx, $this->width);
        PHPImageTools::checksize('Destination Y', $dsty, $this->height);
        PHPImageTools::checksize('Destination Width', $dstw, $this->width);
        PHPImageTools::checksize('Destination Height', $dsth, $this->height);
        PHPImageTools::checksize('Source X', $srcx, imagesx($srcimg));
        PHPImageTools::checksize('Source X', $srcy, imagesy($srcimg));
        PHPImageTools::checksize('Source Width', $srcw, imagesx($srcimg));
        PHPImageTools::checksize('Source Height', $srch, imagesy($srcimg));
        PHPImageTools::checksize('Alpha', $alpha, 127);
        PHPImageTools::checkinteger('Alpha', $alpha, -1, 127);
        PHPImageTools::resizefit($srcw, $srch, $dstw, $dsth);
        PHPImageTools::checkposition('Destination position', $dstpos, $dstx, $dsty, $dstw, $dsth);
        PHPImageTools::checkposition('Source position', $srcpos, $srcx, $srcy, $srcw, $srch);

        $tmp = new PHPImage($dstw, $dsth);
        imagecopyresized($tmp->img, $srcimg, 0, 0, $srcx, $srcy, $dstw, $dsth, $srcw, $srch);
        $this->copy($tmp, $dstx, $dsty, 0, 0, $dstw, $dsth, $alpha);
        $tmp->destroy();
        return true;
    }

    /**
     * @param mixed $src
     * @param mixed $dstx
     * @param mixed $dsty
     * @param mixed $srcx
     * @param mixed $srcy
     * @param mixed $dstw
     * @param mixed $dsth
     * @param mixed $srcw
     * @param mixed $srch
     * @param mixed $alpha
     * @param mixed $dstpos
     * @param mixed $srcpos
     * @return mixed
     */
    public function copyresampled(
        &$src,
        $dstx = 0,
        $dsty = 0,
        $srcx = 0,
        $srcy = 0,
        $dstw = 0,
        $dsth = 0,
        $srcw = 0,
        $srch = 0,
        $alpha = -1,
        $dstpos = '',
        $srcpos = ''
    ) {
        $srcimg = PHPImageTools::getimageresource($src);
        PHPImageTools::checkresource('Source image', $srcimg);
        $srcw = $srcw == 0 ? imagesx($srcimg) : $srcw;
        $srch = $srch == 0 ? imagesy($srcimg) : $srch;
        $dstw = $dstw == 0 ? $this->width : $dstw;
        $dsth = $dsth == 0 ? $this->height : $dsth;
        PHPImageTools::checksize('Destination X', $dstx, $this->width);
        PHPImageTools::checksize('Destination Y', $dsty, $this->height);
        PHPImageTools::checksize('Destination Width', $dstw, $this->width);
        PHPImageTools::checksize('Destination Height', $dsth, $this->height);
        PHPImageTools::checksize('Source X', $srcx, imagesx($srcimg));
        PHPImageTools::checksize('Source X', $srcy, imagesy($srcimg));
        PHPImageTools::checksize('Source Width', $srcw, imagesx($srcimg));
        PHPImageTools::checksize('Source Height', $srch, imagesy($srcimg));
        PHPImageTools::checksize('Alpha', $alpha, 127);
        PHPImageTools::checkinteger('Alpha', $alpha, -1, 127);
        PHPImageTools::checkposition('Destination position', $dstpos, $dstx, $dsty, $dstw, $dsth);
        PHPImageTools::checkposition('Source position', $srcpos, $srcx, $srcy, $srcw, $srch);

        $tmp = new PHPImage($dstw, $dsth);
        imagecopyresampled($tmp->img, $srcimg, 0, 0, $srcx, $srcy, $dstw, $dsth, $srcw, $srch);
        $this->copy($tmp, $dstx, $dsty, 0, 0, $dstw, $dsth, $alpha);
        $tmp->destroy();
        return true;
    }

    /**
     * @param mixed $src
     * @param mixed $dstx
     * @param mixed $dsty
     * @param mixed $srcx
     * @param mixed $srcy
     * @param mixed $dstw
     * @param mixed $dsth
     * @param mixed $srcw
     * @param mixed $srch
     * @param mixed $alpha
     * @param mixed $dstpos
     * @param mixed $srcpos
     * @return mixed
     */
    public function copyresized(
        &$src,
        $dstx = 0,
        $dsty = 0,
        $srcx = 0,
        $srcy = 0,
        $dstw = 0,
        $dsth = 0,
        $srcw = 0,
        $srch = 0,
        $alpha = -1,
        $dstpos = '',
        $srcpos = ''
    ) {
        $srcimg = PHPImageTools::getimageresource($src);
        PHPImageTools::checkresource('Source image', $srcimg);
        $srcw = $srcw == 0 ? imagesx($srcimg) : $srcw;
        $srch = $srch == 0 ? imagesy($srcimg) : $srch;
        $dstw = $dstw == 0 ? $this->width : $dstw;
        $dsth = $dsth == 0 ? $this->height : $dsth;
        PHPImageTools::checksize('Destination X', $dstx, $this->width);
        PHPImageTools::checksize('Destination Y', $dsty, $this->height);
        PHPImageTools::checksize('Destination Width', $dstw, $this->width);
        PHPImageTools::checksize('Destination Height', $dsth, $this->height);
        PHPImageTools::checksize('Source X', $srcx, imagesx($srcimg));
        PHPImageTools::checksize('Source X', $srcy, imagesy($srcimg));
        PHPImageTools::checksize('Source Width', $srcw, imagesx($srcimg));
        PHPImageTools::checksize('Source Height', $srch, imagesy($srcimg));
        PHPImageTools::checksize('Alpha', $alpha, 127);
        PHPImageTools::checkinteger('Alpha', $alpha, -1, 127);
        PHPImageTools::checkposition('Destination position', $dstpos, $dstx, $dsty, $dstw, $dsth);
        PHPImageTools::checkposition('Source position', $srcpos, $srcx, $srcy, $srcw, $srch);

        $tmp = new PHPImage($dstw, $dsth);
        imagecopyresized($tmp->img, $srcimg, 0, 0, $srcx, $srcy, $dstw, $dsth, $srcw, $srch);
        $this->copy($tmp, $dstx, $dsty, 0, 0, $dstw, $dsth, $alpha);
        $tmp->destroy();
        return true;
    }

    /**
     * @param mixed $src
     * @param mixed $dstx
     * @param mixed $dsty
     * @param mixed $srcx
     * @param mixed $srcy
     * @param mixed $srcw
     * @param mixed $srch
     * @param mixed $alpha
     * @param mixed $dstpos
     * @param mixed $srcpos
     */
    public function copy(&$src, $dstx = 0, $dsty = 0, $srcx = 0, $srcy = 0, $srcw = 0, $srch = 0, $alpha = -1, $dstpos = '', $srcpos = '')
    {
        $srcimg = PHPImageTools::getimageresource($src);
        PHPImageTools::checkresource('Source image', $srcimg);
        $srcw = $srcw == 0 ? imagesx($srcimg) : $srcw;
        $srch = $srch == 0 ? imagesy($srcimg) : $srch;
        PHPImageTools::checksize('Destination X', $dstx, $this->width);
        PHPImageTools::checksize('Destination Y', $dsty, $this->height);
        PHPImageTools::checksize('Source X', $srcx, imagesx($srcimg));
        PHPImageTools::checksize('Source X', $srcy, imagesy($srcimg));
        PHPImageTools::checksize('Source Width', $srcw, imagesx($srcimg));
        PHPImageTools::checksize('Source Height', $srch, imagesy($srcimg));
        PHPImageTools::checksize('Alpha', $alpha, 127);
        PHPImageTools::checkinteger('Alpha', $alpha, -1, 127);
        PHPImageTools::checkposition('Destination position', $dstpos, $dstx, $dsty, $srcw, $srch);
        PHPImageTools::checkposition('Source position', $srcpos, $srcx, $srcy, $srcw, $srch);

        if ($this->realcopy) {
            $this->realcopy($srcimg, $dstx, $dsty, $srcx, $srcy, $srcw, $srch, $alpha);
        } elseif ($alpha < 127) {
            if ($alpha < 0) {
                imagecopy($this->img, $srcimg, $dstx, $dsty, $srcx, $srcy, $srcw, $srch);
            } else {
                imagecopymerge($this->img, $srcimg, $dstx, $dsty, $srcx, $srcy, $srcw, $srch, floor(100 * (127 - $alpha) / 127));
            }
        }
    }

    /**
     * @param mixed $src
     * @param mixed $dstx
     * @param mixed $dsty
     * @param mixed $srcx
     * @param mixed $srcy
     * @param mixed $srcw
     * @param mixed $srch
     * @param mixed $alpha
     * @param mixed $dstpos
     * @param mixed $srcpos
     * @return mixed
     */
    public function realcopy(&$src, $dstx = 0, $dsty = 0, $srcx = 0, $srcy = 0, $srcw = 0, $srch = 0, $alpha = -1, $dstpos = '', $srcpos = '')
    {
        $srcimg = PHPImageTools::getimageresource($src);
        PHPImageTools::checkresource('Source image', $srcimg);
        $srcw = $srcw == 0 ? imagesx($srcimg) : $srcw;
        $srch = $srch == 0 ? imagesy($srcimg) : $srch;
        PHPImageTools::checksize('Destination X', $dstx, $this->width);
        PHPImageTools::checksize('Destination Y', $dsty, $this->height);
        PHPImageTools::checksize('Source X', $srcx, imagesx($srcimg));
        PHPImageTools::checksize('Source X', $srcy, imagesy($srcimg));
        PHPImageTools::checksize('Source Width', $srcw, imagesx($srcimg));
        PHPImageTools::checksize('Source Height', $srch, imagesy($srcimg));
        PHPImageTools::checksize('Alpha', $alpha, 127);
        PHPImageTools::checkinteger('Alpha', $alpha, -1, 127);
        PHPImageTools::checkposition('Destination position', $dstpos, $dstx, $dsty, $srcw, $srch);
        PHPImageTools::checkposition('Source position', $srcpos, $srcx, $srcy, $srcw, $srch);

        $tmp = new PHPImage($srcw, $srch);
        $tmp->alphablending(false);
        imagecopy($tmp->img, $this->img, 0, 0, $dstx, $dsty, $srcw, $srch);
        $tmp->alphablending(true);
        imagecopy($tmp->img, $srcimg, 0, 0, $srcx, $srcy, $srcw, $srch);
        $this->alphablending(false);
        if ($alpha < 0) {
            for ($x = 0; $x < $srcw; $x++) {
                for ($y = 0; $y < $srch; $y++) {
                    $c = imagecolorat($tmp->img, $x, $y);
                    $R = ($c >> 16) & 0xFF;
                    $G = ($c >> 8) & 0xFF;
                    $B = $c & 0xFF;
                    $A = (imagecolorat($srcimg, $x + $srcx, $y + $srcy) >> 24) - 127;
                    if ($A != 0) {
                        if ($x + $dstx < $this->width && $x + $dstx >= 0 && $y + $dsty < $this->height && $y + $dsty >= 0) {
                            $A += imagecolorat($this->img, $x + $dstx, $y + $dsty) >> 24;
                            if ($A > 127) {
                                $A = 127;
                            } elseif ($A < 0) {
                                $A = 0;
                            }
                            $c = imagecolorallocatealpha($this->img, $R, $G, $B, $A);
                            imagesetpixel($this->img, $x + $dstx, $y + $dsty, $c);
                        }
                    }
                }
            }
        } else {
            for ($x = 0; $x < $srcw; $x++) {
                for ($y = 0; $y < $srch; $y++) {
                    $c = imagecolorat($tmp->img, $x, $y);
                    $R = ($c >> 16) & 0xFF;
                    $G = ($c >> 8) & 0xFF;
                    $B = $c & 0xFF;
                    $t = imagecolorat($srcimg, $x + $srcx, $y + $srcy) >> 24;
                    if ($t != 127) {
                        if ($x + $dstx < $this->width && $x + $dstx >= 0 && $y + $dsty < $this->height && $y + $dsty >= 0) {
                            $c = imagecolorallocatealpha($this->img, $R, $G, $B, $alpha);
                            imagesetpixel($this->img, $x + $dstx, $y + $dsty, $c);
                        }
                    }
                }
            }
        }
        $tmp->destroy();
        $this->alphablending(true);
        return true;
    }

    /**
     * @return mixed
     */
    public function getclone()
    {
        $tmp = new PHPImage();
        $tmp->clonepropertiesfrom($this);
        $tmp->create($this->width, $this->height);
        $tmp->copy($this);
        return $tmp;
    }

    /**
     *
     */
    public function clonepropertiesfrom($obj)
    {
        $this->bgcolor = $obj->bgcolor;
        $this->fontfile = $obj->fontfile;
        $this->fontsize = $obj->fontsize;
        $this->realcopy = $obj->realcopy;
        $this->format = $obj->format;
        $this->quality = $obj->quality;
    }

    /**
     * @param mixed $width
     * @param mixed $height
     */
    public function resamplefit($width, $height = null)
    {
        $this->resample($width, $height, true);
    }

    /**
     * @param mixed $width
     * @param mixed $height
     * @param mixed $fit
     */
    public function resample($width, $height = null, $fit = false)
    {
        $height = $height === null ? $width : $height;
        PHPImageTools::checksize('width', $width, $this->width);
        PHPImageTools::checksize('height', $height, $this->height);
        if ($fit) {
            PHPImageTools::resizefit($this->width, $this->height, $width, $height);
        }
        // differents methods if destination is slower or greater than source
        // the goal is to optimize memory usage
        if ($width * $height < $this->width * $this->height) { // reduce
            $tmp = new PHPImage();
            $tmp->clonepropertiesfrom($this);
            $tmp->create($width, $height);
            imagecopyresampled($tmp->img, $this->img, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
            $this->destroy();
            $this->create($width, $height);
            imagecopy($this->img, $tmp->img, 0, 0, 0, 0, $width, $height);
            $tmp->destroy();
        } else { // enlarge
            $tmp = $this->getclone();
            $this->destroy();
            $this->create($width, $height);
            imagecopyresampled($this->img, $tmp->img, 0, 0, 0, 0, $width, $height, $tmp->width, $tmp->height);
            $tmp->destroy();
        }
    }

    /**
     * @param mixed $width
     * @param mixed $height
     */
    public function resizefit($width, $height = null)
    {
        $this->resize($width, $height, true);
    }

    /**
     * @param mixed $width
     * @param mixed $height
     * @param mixed $fit
     */
    public function resize($width, $height = null, $fit = false)
    {
        $height = $height === null ? $width : $height;
        PHPImageTools::checksize('width', $width, $this->width);
        PHPImageTools::checksize('height', $height, $this->height);
        if ($fit) {
            PHPImageTools::resizefit($this->width, $this->height, $width, $height);
        }
        // differents methods if destination is slower or greater than source
        // the goal is to optimize memory usage
        if ($width * $height < $this->width * $this->height) { // reduce
            $tmp = new PHPImage();
            $tmp->clonepropertiesfrom($this);
            $tmp->create($width, $height);
            imagecopyresized($tmp->img, $this->img, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
            $this->destroy();
            $this->create($width, $height);
            imagecopy($this->img, $tmp->img, 0, 0, 0, 0, $width, $height);
            $tmp->destroy();
        } else { // enlarge
            $tmp = $this->getclone();
            $this->destroy();
            $this->create($width, $height);
            imagecopyresized($this->img, $tmp->img, 0, 0, 0, 0, $width, $height, $tmp->width, $tmp->height);
            $tmp->destroy();
        }
    }

    /**
     * @param mixed $img
     */
    public function setresource(&$img)
    {
        if (!is_resource($img)) {
            throw new PHPImageException("supplied argument is not a valid image resource !");
        }
        $this->destroy();
        $this->img = $img;
        $this->width = imagesx($img);
        $this->height = imagesy($img);
        $this->savealpha(true);
        $this->alphablending(true);
    }

    /**
     * @param mixed $angle
     * @param mixed $enlarge
     * @return mixed
     */
    public function rotate($angle, $enlarge = true)
    {
        PHPImageTools::checkfloat('Angle', $angle);
        PHPImageTools::checkbool('Enlarge', $enlarge);
        if ($angle % 360 != 0) {
            $tmp = imagerotate($this->img, $angle, $this->colorallocate($this->bgcolor));
            if ($enlarge) {
                $this->destroy();
                $this->create(imagesx($tmp), imagesy($tmp));
                $this->copy($tmp);
                imagedestroy($tmp);
            } else {
                $w = $this->width;
                $h = $this->height;
                $tmpx = imagesx($tmp);
                $tmpy = imagesy($tmp);
                $this->destroy();
                $this->create($w, $h);
                $this->copy($tmp, 0, 0, ($tmpx - $w) / 2, ($tmpy - $h) / 2, $w, $h);
                imagedestroy($tmp);
            }
        }
        return true;
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
     * @return mixed
     */
    public function gettextposition($cx, $cy, $text, $fontsize = 12, $angle = 0, $position = 'top left', $linespacing = '', $font = '')
    {
        if ($font === '') {
            $font = $this->fontfile;
        }
        if ($fontsize === 0) {
            $fontsize = $this->fontsize;
        }
        $angle = -$angle;
        PHPImageTools::checksize('Center X', $cx, $this->width);
        PHPImageTools::checksize('Center Y', $cy, $this->height);
        PHPImageTools::checkinteger('Font Size', $fontsize, 1);
        PHPImageTools::checkfloat('Angle', $angle);
        PHPImageTools::writetext_init($cx, $cy, $text, $fontsize, $angle, $position, $linespacing, $font, $lines, $sizes, $W, $H, $tlx, $tly, $lineH);
        $trx = $tlx + $W;
        $try = $tly;
        $blx = $tlx;
        $bly = $tly + $H;
        $brx = $tlx + $W;
        $bry = $tly + $H;
        PHPImageTools::rotatepoint($cx, $cy, $tlx, $tly, $angle);
        PHPImageTools::rotatepoint($cx, $cy, $trx, $try, $angle);
        PHPImageTools::rotatepoint($cx, $cy, $blx, $bly, $angle);
        PHPImageTools::rotatepoint($cx, $cy, $brx, $bry, $angle);
        return array(
            min($tlx, $trx, $blx, $brx),
            min($tly, $try, $bly, $bry)
        );
    }

    /**
     * @param mixed $text
     * @param mixed $fontsize
     * @param mixed $angle
     * @param mixed $linespacing
     * @param mixed $font
     * @return mixed
     */
    public function gettextbox($text, $fontsize = 0, $angle = 0, $linespacing = '', $font = '')
    {
        if ($font === '') {
            $font = $this->fontfile;
        }
        if ($fontsize === 0) {
            $fontsize = $this->fontsize;
        }
        $angle = -$angle;
        PHPImageTools::checkinteger('Font Size', $fontsize, 1);
        PHPImageTools::checkfloat('Angle', $angle);
        $position = 'top left';
        PHPImageTools::writetext_init(0, 0, $text, $fontsize, $angle, $position, $linespacing, $font, $lines, $sizes, $W, $H, $tlx, $tly, $lineH);
        $trx = $tlx + $W;
        $try = $tly;
        $blx = $tlx;
        $bly = $tly + $H;
        $brx = $tlx + $W;
        $bry = $tly + $H;
        PHPImageTools::rotatepoint(0, 0, $tlx, $tly, $angle);
        PHPImageTools::rotatepoint(0, 0, $trx, $try, $angle);
        PHPImageTools::rotatepoint(0, 0, $blx, $bly, $angle);
        PHPImageTools::rotatepoint(0, 0, $brx, $bry, $angle);
        return array(
            max($tlx, $trx, $blx, $brx) - min($tlx, $trx, $blx, $brx),
            max($tly, $try, $bly, $bry) - min($tly, $try, $bly, $bry),
        );
    }

    /**
     * @param mixed $cx
     * @param mixed $cy
     * @param mixed $text
     * @param mixed $fontsize
     * @param mixed $angle
     * @param mixed $creux
     * @param mixed $style
     * @param mixed $position
     * @param mixed $linespacing
     * @param mixed $font
     * @param mixed $alpha
     */
    public function writetextwatermark(
        $cx,
        $cy,
        $text,
        $fontsize = 0,
        $angle = 0,
        $creux = true,
        $style = 'left',
        $position = 'top left',
        $linespacing = '',
        $font = '',
        $alpha = 70
    ) {
        if ($font === '') {
            $font = $this->fontfile;
        }
        if ($fontsize === 0) {
            $fontsize = $this->fontsize;
        }
        $angle = -$angle;
        PHPImageTools::checkfont($font);
        PHPImageTools::checksize('Center X', $cx, $this->width);
        PHPImageTools::checksize('Center Y', $cy, $this->height);
        PHPImageTools::checkinteger('Font Size', $fontsize, 1);
        PHPImageTools::checkfloat('Angle', $angle);
        PHPImageTools::checksize('Alpha', $alpha, 127);
        PHPImageTools::checkinteger('Alpha', $alpha, -1, 127);
        list($w, $h) = $this->gettextbox($text, $fontsize, -$angle, $linespacing, $font);
        list($ptx, $pty) = $this->gettextposition($cx, $cy, $text, $fontsize, -$angle, $position, $linespacing, $font);
        $w += 40;
        $h += 40;
        $tmp = new PHPImage();
        $tmp->fontfile = $font;
        $tmp->create($w, $h);
        $tmp->fill(0, 0, 'white');
        $tmp->writetext('50%', '50%', $text, $fontsize, -$angle, 'black', $style, 'center center', $linespacing);
        $tmp->effect('watermark', $creux);
        $realcopy = $this->realcopy;
        $this->realcopy = false;
        $this->copy($tmp->img, $ptx - 21, $pty - 21, 0, 0, $w, $h, $alpha);
        $this->realcopy = $realcopy;
        $tmp->destroy();
    }

    /**
     * @param mixed $text
     * @param mixed $fontsize
     * @param mixed $angle
     * @param mixed $color
     * @param mixed $style
     * @param mixed $linespacing
     * @param mixed $font
     * @param mixed $padL
     * @param mixed $padT
     * @param mixed $padR
     * @param mixed $padB
     */
    public function imagetext(
        $text,
        $fontsize = 0,
        $angle = 0,
        $color = 'black',
        $style = 'left',
        $linespacing = '',
        $font = '',
        $padL = 1,
        $padT = null,
        $padR = null,
        $padB = null
    ) {
        if ($padT === null) {
            $padT = $padL;
        }
        if ($padR === null) {
            $padR = $padL;
        }
        if ($padB === null) {
            $padB = $padT;
        }
        PHPImageTools::checkinteger('Padding left', $padL, 0);
        PHPImageTools::checkinteger('Padding top', $padT, 0);
        PHPImageTools::checkinteger('Padding right', $padR, 0);
        PHPImageTools::checkinteger('Padding bottom', $padB, 0);
        list($w, $h) = $this->gettextbox($text, $fontsize, $angle, $linespacing, $font);
        $this->create($w + $padL + $padR, $h + $padT + $padB);
        list($cx, $cy) = $this->gettextposition(0, 0, $text, $fontsize, $angle, 'top left', $linespacing, $font);
        $this->writetext(abs($cx) + $padL, abs($cy) + $padT, $text, $fontsize, $angle, $color, $style, 'top left', $linespacing, $font);
    }

    /**
     * @param mixed $cx
     * @param mixed $cy
     * @param mixed $text
     * @param mixed $fontsize
     * @param mixed $angle
     * @param mixed $color
     * @param mixed $style
     * @param mixed $position
     * @param mixed $linespacing
     * @param mixed $font
     */
    public function writetext(
        $cx,
        $cy,
        $text,
        $fontsize = 0,
        $angle = 0,
        $color = 'black',
        $style = 'left',
        $position = 'top left',
        $linespacing = '',
        $font = ''
    ) {
        if ($font === '') {
            $font = $this->fontfile;
        }
        if ($fontsize === 0) {
            $fontsize = $this->fontsize;
        }
        $angle = -$angle;
        PHPImageTools::checkfont($font);
        PHPImageTools::checksize('Center X', $cx, $this->width);
        PHPImageTools::checksize('Center Y', $cy, $this->height);
        PHPImageTools::checkinteger('Font Size', $fontsize, 1);
        PHPImageTools::checkfloat('Angle', $angle);
        $color = PHPImageTools::checkcolor($color);
        PHPImageTools::writetext_init($cx, $cy, $text, $fontsize, $angle, $position, $linespacing, $font, $lines, $sizes, $W, $H, $tlx, $tly, $lineH);
        $c = $this->colorallocate($color);
        $posx = $tlx;
        $posy = $tly;
        $y = 0;
        $tmp = new PHPImageColor($color);
        PHPImageTools::writetext_style($color, $tmp->A, $this->bgcolor, $style, $align);
        PHPImageTools::writetext_style_shadow(
            $color,
            $tmp->A,
            $this->bgcolor,
            $style,
            $shadow,
            $shadow_x,
            $shadow_y,
            $shadow_color,
            $shadow_bgcolor,
            $shadow_alpha,
            $shadow_blur
        );
        PHPImageTools::writetext_style_underline(
            $color,
            $tmp->A,
            $this->bgcolor,
            $style,
            $underline,
            $underline_style,
            $underline_thickness,
            $underline_color,
            $underline_alpha
        );
        if ($shadow) {
            $tmpcolor = new PHPImageColor($shadow_color);
            $tmpcolor->A = $shadow_alpha;
            list($w, $h) = $this->gettextbox($text, $fontsize, -$angle, $linespacing, $font);
            $w += 40;
            $h += 40;
            $tmp = new PHPImage();
            $tmp->bgcolor = $shadow_bgcolor;
            $tmp->create($w, $h);
            $tmp->writetext(
                '50%',
                '50%',
                $text,
                $fontsize,
                -$angle,
                $tmpcolor->getvalues(),
                preg_replace('/shadow/i', '', preg_replace('/shadow\(.*?\)/i', '', $style)),
                'center center',
                $linespacing,
                $font
            );
            list($ptx, $pty) = $this->gettextposition($cx, $cy, $text, $fontsize, -$angle, $position, $linespacing, $font);
            if ($shadow_blur) {
                // $tmp->convolution('1 2 1  2 4 2  1 2 1', 0, true, 10);
                $tmp->convolution('1 1 1  1 2 1  1 1 1', 0, true, 7);
            }
            $this->copy($tmp->img, $ptx + $shadow_x - 21, $pty + $shadow_y - 21);
            $tmp->destroy();
        }
        if (is_int($font)) {
            switch ($font) {
                case 1:
                    $d = 0;
                    break;
                case 2:
                    $d = -2;
                    break;
                case 3:
                    $d = -1;
                    break;
                case 4:
                    $d = -2;
                    break;
                case 5:
                    $d = -2;
                    break;
            }
            if ($angle == 0) {
                for ($i = 0; $i < count($lines); $i++) {
                    $posx = $tlx - $sizes[$i]->tlx;
                    $posy = $tly + $y;
                    switch ($align) {
                        case 'right':
                            $posx += $W - $sizes[$i]->width;
                            break;
                        case 'center':
                            $posx += floor(($W - $sizes[$i]->width) / 2);
                            break;
                    }
                    imagestring($this->img, $font, $posx, $posy + $d, $lines[$i], $c);
                    $y += $lineH + $linespacing;
                }
            } else {
                $tmp = new PHPImage($W, $H);
                $posy = 0;
                for ($i = 0; $i < count($lines); $i++) {
                    $posx = 0;
                    switch ($align) {
                        case 'right':
                            $posx = $W - $sizes[$i]->width;
                            break;
                        case 'center':
                            $posx = floor(($W - $sizes[$i]->width) / 2);
                            break;
                    }
                    imagestring($tmp->img, $font, $posx, $posy + $d, $lines[$i], $c);
                    $posy += $lineH + $linespacing;
                }
                $newtmp = PHPImageTools::imagerotation($tmp->img, -$angle, $tmp->colorallocate($this->bgcolor));
                $tmp->setresource($newtmp->img);
                list($ptx, $pty) = $this->gettextposition($cx, $cy, $text, $fontsize, -$angle, $position, $linespacing, $font);
                $this->copy($tmp, $ptx, $pty);
                $tmp->destroy();
            }
        } else {
            for ($i = 0; $i < count($lines); $i++) {
                $posx = $tlx - $sizes[$i]->tlx;
                $posy = $tly + $y + 0.75 * $lineH;
                switch ($align) {
                    case 'right':
                        $posx += $W - $sizes[$i]->width;
                        break;
                    case 'center':
                        $posx += floor(($W - $sizes[$i]->width) / 2);
                        break;
                }
                if ($underline && $lines[$i] != '' && $underline_thickness > 0) {
                    $p1x = $posx;
                    $p1y = $posy + ceil($underline_thickness / 2);
                    $p2x = $posx + $sizes[$i]->width;
                    $p2y = $posy + ceil($underline_thickness / 2);
                    PHPImageTools::rotatepoint($cx, $cy, $p1x, $p1y, $angle);
                    PHPImageTools::rotatepoint($cx, $cy, $p2x, $p2y, $angle);
                    $tmp = new PHPImageColor($underline_color);
                    $tmp->A = $underline_alpha;
                    $this->drawline($p1x, $p1y, $p2x, $p2y, $tmp->getvalues(), $underline_thickness, $underline_style);
                }
                PHPImageTools::rotatepoint($cx, $cy, $posx, $posy, $angle);
                imagettftext($this->img, $fontsize, -$angle, $posx, $posy, $c, $font, $lines[$i]);
                $y += $lineH + $linespacing;
            }
        }
    }
}
