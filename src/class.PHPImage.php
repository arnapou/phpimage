<?php
/**
 * PHPImage - PHP Drawing class
 *
 * @author     Arnaud BUATHIER
 * @license    http://gnu.org/copyleft/gpl.html GNU GPL
 * @require    PHP5, GD2
 * @version    1.3 (02/05/2009)
 * @link       http://arnapou.net
 */

/**
 * PHPImageException class
 */
class PHPImageException extends Exception
{
    /**
     *
     * @param string $message
     * @param int    $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    /**
     *
     */
    public function display_image()
    {
        $traces = $this->getTrace();
        $trace = array_pop($traces);
        $method = $trace['function'];
        $file = $this->getFile();
        $line = $this->getLine();
        $msg = $this->getMessage();
        if (strpos($file, '/') !== false) {
            $SEP = '/';
        } else {
            $SEP = '\\';
        }
        if (!headers_sent()) {
            $w2 = imagefontwidth(2);
            $w3 = imagefontwidth(3);
            $fileP = 'file   : ';
            $lineP = 'line   : ';
            $methodP = 'method : ';
            $msgP = 'msg	: ';
            $lineH = 17;
            $widths = [];
            $widths[] = $w2 * strlen($fileP) + $w2 * strlen(dirname($file) . $SEP) + $w3 * strlen(basename($file));
            $widths[] = $w2 * strlen($lineP) + $w3 * strlen($line);
            $widths[] = $w2 * strlen($methodP) + $w3 * strlen($method);
            $widths[] = $w2 * strlen($msgP) + $w2 * strlen($msg);
            $dx = 5;
            $dy = 4;
            $width = max($widths) + 2 * $dx;
            $height = count($widths) * $lineH + $lineH + 2 * $dy;
            $im = @imagecreate($width, $height) or die("Impossible d'initialiser la biblioth�que GD");
            $black = imagecolorallocate($im, 0, 0, 0);
            $red = imagecolorallocate($im, 255, 0, 0);
            $blue = imagecolorallocate($im, 0, 0, 255);
            $green = imagecolorallocate($im, 0, 130, 0);
            $white = imagecolorallocate($im, 255, 255, 255);
            $dy += $lineH;

            imagefill($im, 0, 0, $white);
            imagerectangle($im, 0, $lineH, $width - 1, $height - 1, $red);
            imagestring($im, 3, $dx, 0, '** PHPImage ERROR **', $red);

            imagestring($im, 2, $dx, $dy, $fileP . dirname($file) . $SEP, $red);
            imagestring($im, 3, $dx + $w2 * strlen($fileP) + $w2 * strlen(dirname($file) . $SEP), $dy, basename($file), $red);

            imagestring($im, 2, $dx, $dy + $lineH, $lineP, $green);
            imagestring($im, 3, $dx + $w2 * strlen($lineP), $dy + $lineH, $line, $green);

            imagestring($im, 2, $dx, $dy + 2 * $lineH, $methodP, $blue);
            imagestring($im, 3, $dx + $w2 * strlen($methodP), $dy + 2 * $lineH, $method, $blue);

            imagestring($im, 2, $dx, $dy + 3 * $lineH, $msgP, $black);
            imagestring($im, 2, $dx + $w2 * strlen($msgP), $dy + 3 * $lineH, $msg, $black);

            header('Content-type: image/gif');
            imagegif($im);
            imagedestroy($im);
            exit;
        }
    }
}

/**
 * PHPImage class
 */
class PHPImage
{
    /**
     *
     * @var Resource
     */
    public $img = null;
    /**
     *
     * @var int
     */
    public $width = 0;
    /**
     *
     * @var int
     */
    public $height = 0;
    /**
     *
     * @var string
     */
    public $format = 'png';
    /**
     *
     * @var int
     */
    public $quality = 85;
    /**
     *
     * @var string
     */
    public $bgcolor = 'white 127';
    /**
     *
     * @var int
     */
    public $cachetime = 0;
    /**
     *
     * @var int
     */
    public $cachecontrol = 86400;
    /**
     *
     * @var int
     */
    protected $headers_sent = false;
    /**
     *
     * @var int
     */
    public $fontfile = 2;
    /**
     *
     * @var int
     */
    public $fontsize = 12;
    /**
     *
     * @var string
     */
    public $fillcolor = 'white';
    /**
     *
     * @var int
     */
    public $thickness = 1;
    /**
     *
     * @var string
     */
    public $linecolor = 'black';
    /**
     *
     * @var string
     */
    public $linestyle = 'solid';
    /**
     *
     * @var bool
     */
    public $realcopy = false;
    /**
     *
     * @var array
     */
    protected $colors = [];

    /**
     *
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
     *
     * @param PHPImage $mask
     */
    public function maskalpha(& $mask)
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
     *
     * @param int    $cx    Center X
     * @param int    $cy    Center Y
     * @param int    $r     Radius
     * @param float  $start Start angle
     * @param float  $end   End angle
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
     *
     * @param int    $cx    Center X
     * @param int    $cy    Center Y
     * @param int    $w     Width
     * @param int    $h     height
     * @param float  $start Start angle
     * @param float  $end   End angle
     * @param string $color Color
     * @return <type>
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
                $tmp->img, $w / 2 + 1, $h / 2 + 1, $w, $h,
                $start, $end, $tmp->colorallocate($color), IMG_ARC_PIE
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
     *
     * @param int    $x1        Point X
     * @param int    $y1        Point Y
     * @param int    $w         Width
     * @param int    $h         Height
     * @param string $linecolor Line color
     * @param int    $thickness thickness
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
     *
     * @param Array  $values
     * @param string $linecolor
     * @param int    $thickness
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
     *
     * @param array  $values
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
     *
     * @param  <type> $x1
     * @param  <type> $y1
     * @param  <type> $x2
     * @param  <type> $y2
     * @param  <type> $linecolor
     * @param  <type> $thickness
     * @param  <type> $linestyle
     * @return <type>
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
        $color = $this->colorallocate($linecolor);

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
     *
     * @param <type> $x1
     * @param <type> $y1
     * @param <type> $w
     * @param <type> $h
     * @param <type> $linecolor
     * @param <type> $thickness
     * @param <type> $linestyle
     * @param <type> $shapestyle
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
     *
     * @param <type> $x1
     * @param <type> $y1
     * @param <type> $w
     * @param <type> $h
     * @param <type> $fillcolor
     * @param <type> $shapestyle
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
     *
     * @param  <type> $x1
     * @param  <type> $y1
     * @param  <type> $x2
     * @param  <type> $y2
     * @param  <type> $fillcolor
     * @param  <type> $shapestyle
     * @return <type>
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
        $cases = [];
        $cases[1] = [0, 0, $l_min, $t_min];
        $cases[2] = [$l_min, 0, $l_max, $t_min];
        $cases[3] = [$l_max, 0, $w - $r_max, $t_min];
        $cases[4] = [$w - $r_max, 0, $w - $r_min, $t_min];
        $cases[5] = [$w - $r_min, 0, $w, $t_min];
        $cases[6] = [$cases[1][0], $t_min, $cases[1][2], $t_max];
        $cases[7] = [$cases[2][0], $t_min, $cases[2][2], $t_max];
        $cases[8] = [$cases[3][0], $t_min, $cases[3][2], $t_max];
        $cases[9] = [$cases[4][0], $t_min, $cases[4][2], $t_max];
        $cases[10] = [$cases[5][0], $t_min, $cases[5][2], $t_max];
        $cases[11] = [$cases[1][0], $t_max, $cases[1][2], $h - $b_max];
        $cases[12] = [$cases[2][0], $t_max, $cases[2][2], $h - $b_max];
        $cases[13] = [$cases[3][0], $t_max, $cases[3][2], $h - $b_max];
        $cases[14] = [$cases[4][0], $t_max, $cases[4][2], $h - $b_max];
        $cases[15] = [$cases[5][0], $t_max, $cases[5][2], $h - $b_max];
        $cases[16] = [$cases[1][0], $h - $b_max, $cases[1][2], $h - $b_min];
        $cases[17] = [$cases[2][0], $h - $b_max, $cases[2][2], $h - $b_min];
        $cases[18] = [$cases[3][0], $h - $b_max, $cases[3][2], $h - $b_min];
        $cases[19] = [$cases[4][0], $h - $b_max, $cases[4][2], $h - $b_min];
        $cases[20] = [$cases[5][0], $h - $b_max, $cases[5][2], $h - $b_min];
        $cases[21] = [$cases[1][0], $h - $b_min, $cases[1][2], $h];
        $cases[22] = [$cases[2][0], $h - $b_min, $cases[2][2], $h];
        $cases[23] = [$cases[3][0], $h - $b_min, $cases[3][2], $h];
        $cases[24] = [$cases[4][0], $h - $b_min, $cases[4][2], $h];
        $cases[25] = [$cases[5][0], $h - $b_min, $cases[5][2], $h];
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
            $cases[$i] = [$tx1, $ty1, $tx2, $ty2];
            if ($ok) {
                imagefilledrectangle($this->img, $x1 + $tx1, $y1 + $ty1, $x1 + $tx2 - 1, $y1 + $ty2 - 1, $c);
            }
        }
        return true;
    }

    /**
     *
     * @param  <type> $x1
     * @param  <type> $y1
     * @param  <type> $x2
     * @param  <type> $y2
     * @param  <type> $linecolor
     * @param  <type> $thickness
     * @param  <type> $linestyle
     * @param  <type> $shapestyle
     * @return <type>
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
                            imagerectangle($this->img, $x1 - $e1 + $e + $v + $i, $y1 - $e1 + $e + $v + $i, $x2 + $e1 - $e - $v - $i, $y2 + $e1 - $e - $v - $i, $c);
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
     *
     * @param <type> $sx
     * @param <type> $sy
     * @param <type> $linecolor
     * @param <type> $corner
     * @param <type> $form
     * @param <type> $w
     * @param <type> $h
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
            $lengths = [];
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
                    $coords = [];
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
     *
     * @param  <type> $sx
     * @param  <type> $sy
     * @param  <type> $linecolor
     * @param  <type> $corner
     * @param  <type> $form
     * @param  <type> $w
     * @param  <type> $h
     * @param  <type> $thickness
     * @param  <type> $linestyle
     * @return <type>
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
     *
     * @param <type> $src
     * @param <type> $alpha
     */
    public function setbackgroundimage(& $src, $alpha = -1)
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
     *
     * @param <type> $src
     * @param <type> $dstx
     * @param <type> $dsty
     * @param <type> $srcx
     * @param <type> $srcy
     * @param <type> $srcw
     * @param <type> $srch
     * @param <type> $alpha
     * @param <type> $dstpos
     * @param <type> $srcpos
     * @param <type> $shapestyle
     * @param <type> $watermark
     * @param <type> $creux
     */
    public function drawimage(& $src, $dstx, $dsty, $srcx = 0, $srcy = 0, $srcw = 0, $srch = 0, $alpha = -1, $dstpos = '', $srcpos = '', $shapestyle = '', $watermark = false, $creux = true)
    {
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
     *
     * @param <type> $cx
     * @param <type> $cy
     * @param <type> $r
     * @param <type> $start
     * @param <type> $end
     * @param <type> $linecolor
     * @param <type> $thickness
     * @param <type> $linestyle
     * @param <type> $drawborders
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
     *
     * @param  <type> $cx
     * @param  <type> $cy
     * @param  <type> $w
     * @param  <type> $h
     * @param  <type> $start
     * @param  <type> $end
     * @param  <type> $linecolor
     * @param  <type> $thickness
     * @param  <type> $linestyle
     * @param  <type> $drawborders
     * @return <type>
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
                $cx, $cy,
                $cx + 0.5 * $w * cos(PHPImageTools::deg2rad($start)),
                $cy + 0.5 * $h * sin(PHPImageTools::deg2rad($start)),
                $savecolor, $thickness, $savelinestyle
            );
            $this->drawline(
                $cx, $cy,
                $cx + 0.5 * $w * cos(PHPImageTools::deg2rad($end)),
                $cy + 0.5 * $h * sin(PHPImageTools::deg2rad($end)),
                $savecolor, $thickness, $savelinestyle
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
     *
     * @param <type> $cx
     * @param <type> $cy
     * @param <type> $r
     * @param <type> $linecolor
     * @param <type> $thickness
     * @param <type> $linestyle
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
     *
     * @param <type> $cx
     * @param <type> $cy
     * @param <type> $w
     * @param <type> $h
     * @param <type> $linecolor
     * @param <type> $thickness
     * @param <type> $linestyle
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
     *
     * @param <type> $cx
     * @param <type> $cy
     * @param <type> $r
     * @param <type> $color
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
     *
     * @param <type> $cx
     * @param <type> $cy
     * @param <type> $w
     * @param <type> $h
     * @param <type> $color
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
     *
     * @param <type> $alpha
     * @param <type> $forcetransparentpixels
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
     *
     * @param <type> $srccolor
     * @param <type> $dstcolor
     * @param <type> $keeptransparency
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
     *
     * @param <type> $srccolor
     * @param <type> $dstcolor
     * @param <type> $keeptransparency
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
     *
     * @param <type> $mask
     */
    public function mask(& $mask)
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
     *
     * @param <type> $matrix
     * @param <type> $offset
     * @param <type> $usealpha
     * @param <type> $divAlpha
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
     *
     * @param <type> $effect
     * @param <type> $arg1
     * @param <type> $arg2
     * @param <type> $arg3
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
     *
     * @param <type> $width
     * @param <type> $height
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
        $this->colors = [];
        $this->height = 0;
        $this->width = 0;
    }

    /**
     *
     * @param <type> $color
     */
    public function maketransparent($color)
    {
        imagecolortransparent($this->img, $this->colorallocate($color));
    }

    /**
     *
     * @param <type> $x
     * @param <type> $y
     * @param <type> $color
     */
    public function fill($x, $y, $color)
    {
        PHPImageTools::checksize('X', $x, $this->width);
        PHPImageTools::checksize('Y', $y, $this->height);
        imagefill($this->img, round($x), round($y), $this->colorallocate($color));
    }

    /**
     *
     * @param <type> $bool
     */
    public function alphablending($bool = true)
    {
        imagealphablending($this->img, $bool === true);
    }

    /**
     *
     * @param <type> $bool
     */
    public function antialias($bool = true)
    {
        imageantialias($this->img, $bool === true);
    }

    /**
     *
     * @param  <type> $color
     * @return <type>
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
     *
     * @param  <type> $alpha
     * @return <type>
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
     *
     * @param  <type> $x
     * @param  <type> $y
     * @return <type>
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
     *
     * @param <type> $x
     * @param <type> $y
     * @param <type> $color
     */
    public function setpixel($x, $y, $color)
    {
        imagesetpixel($this->img, $x, $y, $this->colorallocate($color));
    }

    /**
     *
     * @param <type> $bool
     */
    public function savealpha($bool = true)
    {
        imagesavealpha($this->img, $bool === true);
    }

    /**
     *
     * @param  <type> $filename
     * @param  <type> $quality
     * @return <type>
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
     *
     * @param <type> $filename
     * @param <type> $source
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
     *
     * @param <type> $filename
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
     *
     * @param  <type> $filename
     * @param  <type> $quality
     * @return <type>
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
     *
     * @param  <type> $filename
     * @return <type>
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
     *
     * @param <type> $x
     * @param <type> $y
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
     *
     * @param <type> $x1
     * @param <type> $y1
     * @param <type> $x2
     * @param <type> $y2
     */
    public function cleanrectangle($x1, $y1, $x2, $y2)
    {
        $this->drawfilledrectangle($x1, $y1, $x2, $y2, $this->getnewcolor());
        $this->fill(floor(($x1 + $x2) / 2), floor(($y1 + $y2) / 2), $this->bgcolor);
    }

    /**
     *
     * @param <type> $x1
     * @param <type> $y1
     * @param <type> $w
     * @param <type> $h
     */
    public function cleanrectanglewh($x1, $y1, $w, $h)
    {
        $x2 = $x1 + ($w == 0 ? 1 : $w) - 1;
        $y2 = $y1 + ($w == 0 ? 1 : $w) - 1;
        $this->cleanrectangle($x1, $x2, $y1, $y2);
    }

    /**
     *
     * @param  <type> $src
     * @param  <type> $dstx
     * @param  <type> $dsty
     * @param  <type> $srcx
     * @param  <type> $srcy
     * @param  <type> $dstw
     * @param  <type> $dsth
     * @param  <type> $srcw
     * @param  <type> $srch
     * @param  <type> $alpha
     * @param  <type> $dstpos
     * @param  <type> $srcpos
     * @return <type>
     */
    public function copyresampledfit(& $src, $dstx = 0, $dsty = 0, $srcx = 0, $srcy = 0, $dstw = 0, $dsth = 0, $srcw = 0, $srch = 0, $alpha = -1, $dstpos = '', $srcpos = '')
    {
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
     *
     * @param  <type> $src
     * @param  <type> $dstx
     * @param  <type> $dsty
     * @param  <type> $srcx
     * @param  <type> $srcy
     * @param  <type> $dstw
     * @param  <type> $dsth
     * @param  <type> $srcw
     * @param  <type> $srch
     * @param  <type> $alpha
     * @param  <type> $dstpos
     * @param  <type> $srcpos
     * @return <type>
     */
    public function copyresizedfit(& $src, $dstx = 0, $dsty = 0, $srcx = 0, $srcy = 0, $dstw = 0, $dsth = 0, $srcw = 0, $srch = 0, $alpha = -1, $dstpos = '', $srcpos = '')
    {
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
     *
     * @param  <type> $src
     * @param  <type> $dstx
     * @param  <type> $dsty
     * @param  <type> $srcx
     * @param  <type> $srcy
     * @param  <type> $dstw
     * @param  <type> $dsth
     * @param  <type> $srcw
     * @param  <type> $srch
     * @param  <type> $alpha
     * @param  <type> $dstpos
     * @param  <type> $srcpos
     * @return <type>
     */
    public function copyresampled(& $src, $dstx = 0, $dsty = 0, $srcx = 0, $srcy = 0, $dstw = 0, $dsth = 0, $srcw = 0, $srch = 0, $alpha = -1, $dstpos = '', $srcpos = '')
    {
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
     *
     * @param  <type> $src
     * @param  <type> $dstx
     * @param  <type> $dsty
     * @param  <type> $srcx
     * @param  <type> $srcy
     * @param  <type> $dstw
     * @param  <type> $dsth
     * @param  <type> $srcw
     * @param  <type> $srch
     * @param  <type> $alpha
     * @param  <type> $dstpos
     * @param  <type> $srcpos
     * @return <type>
     */
    public function copyresized(& $src, $dstx = 0, $dsty = 0, $srcx = 0, $srcy = 0, $dstw = 0, $dsth = 0, $srcw = 0, $srch = 0, $alpha = -1, $dstpos = '', $srcpos = '')
    {
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
     *
     * @param <type> $src
     * @param <type> $dstx
     * @param <type> $dsty
     * @param <type> $srcx
     * @param <type> $srcy
     * @param <type> $srcw
     * @param <type> $srch
     * @param <type> $alpha
     * @param <type> $dstpos
     * @param <type> $srcpos
     */
    public function copy(& $src, $dstx = 0, $dsty = 0, $srcx = 0, $srcy = 0, $srcw = 0, $srch = 0, $alpha = -1, $dstpos = '', $srcpos = '')
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
     *
     * @param  <type> $src
     * @param  <type> $dstx
     * @param  <type> $dsty
     * @param  <type> $srcx
     * @param  <type> $srcy
     * @param  <type> $srcw
     * @param  <type> $srch
     * @param  <type> $alpha
     * @param  <type> $dstpos
     * @param  <type> $srcpos
     * @return <type>
     */
    public function realcopy(& $src, $dstx = 0, $dsty = 0, $srcx = 0, $srcy = 0, $srcw = 0, $srch = 0, $alpha = -1, $dstpos = '', $srcpos = '')
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
     *
     * @return <type>
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
     *
     * @param <type> $width
     * @param <type> $height
     */
    public function resamplefit($width, $height = null)
    {
        $this->resample($width, $height, true);
    }

    /**
     *
     * @param <type> $width
     * @param <type> $height
     * @param <type> $fit
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
     *
     * @param <type> $width
     * @param <type> $height
     */
    public function resizefit($width, $height = null)
    {
        $this->resize($width, $height, true);
    }

    /**
     *
     * @param <type> $width
     * @param <type> $height
     * @param <type> $fit
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
     *
     * @param <type> $img
     */
    public function setresource(& $img)
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
     *
     * @param  <type> $angle
     * @param  <type> $enlarge
     * @return <type>
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
     *
     * @param  <type> $cx
     * @param  <type> $cy
     * @param  <type> $text
     * @param  <type> $fontsize
     * @param  <type> $angle
     * @param  <type> $position
     * @param  <type> $linespacing
     * @param  <type> $font
     * @return <type>
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
        return [
            min($tlx, $trx, $blx, $brx),
            min($tly, $try, $bly, $bry),
        ];
    }

    /**
     *
     * @param  <type> $text
     * @param  <type> $fontsize
     * @param  <type> $angle
     * @param  <type> $linespacing
     * @param  <type> $font
     * @return <type>
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
        return [
            max($tlx, $trx, $blx, $brx) - min($tlx, $trx, $blx, $brx),
            max($tly, $try, $bly, $bry) - min($tly, $try, $bly, $bry),
        ];
    }

    /**
     *
     * @param <type> $cx
     * @param <type> $cy
     * @param <type> $text
     * @param <type> $fontsize
     * @param <type> $angle
     * @param <type> $creux
     * @param <type> $style
     * @param <type> $position
     * @param <type> $linespacing
     * @param <type> $font
     * @param <type> $alpha
     */
    public function writetextwatermark($cx, $cy, $text, $fontsize = 0, $angle = 0, $creux = true, $style = 'left', $position = 'top left', $linespacing = '', $font = '', $alpha = 70)
    {
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
     *
     * @param <type> $text
     * @param <type> $fontsize
     * @param <type> $angle
     * @param <type> $color
     * @param <type> $style
     * @param <type> $linespacing
     * @param <type> $font
     * @param <type> $padL
     * @param <type> $padT
     * @param <type> $padR
     * @param <type> $padB
     */
    public function imagetext($text, $fontsize = 0, $angle = 0, $color = 'black', $style = 'left', $linespacing = '', $font = '', $padL = 1, $padT = null, $padR = null, $padB = null)
    {
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
     *
     * @param <type> $cx
     * @param <type> $cy
     * @param <type> $text
     * @param <type> $fontsize
     * @param <type> $angle
     * @param <type> $color
     * @param <type> $style
     * @param <type> $position
     * @param <type> $linespacing
     * @param <type> $font
     */
    public function writetext($cx, $cy, $text, $fontsize = 0, $angle = 0, $color = 'black', $style = 'left', $position = 'top left', $linespacing = '', $font = '')
    {
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
        PHPImageTools::writetext_style_shadow($color, $tmp->A, $this->bgcolor, $style, $shadow, $shadow_x, $shadow_y, $shadow_color, $shadow_bgcolor, $shadow_alpha, $shadow_blur);
        PHPImageTools::writetext_style_underline($color, $tmp->A, $this->bgcolor, $style, $underline, $underline_style, $underline_thickness, $underline_color, $underline_alpha);
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
                '50%', '50%', $text, $fontsize, -$angle, $tmpcolor->getvalues(),
                preg_replace('/shadow/i', '', preg_replace('/shadow\(.*?\)/i', '', $style)),
                'center center', $linespacing, $font
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
                case 1 :
                    $d = 0;
                    break;
                case 2 :
                    $d = -2;
                    break;
                case 3 :
                    $d = -1;
                    break;
                case 4 :
                    $d = -2;
                    break;
                case 5 :
                    $d = -2;
                    break;
            }
            if ($angle == 0) {
                for ($i = 0; $i < count($lines); $i++) {
                    $posx = $tlx - $sizes[$i]->tlx;
                    $posy = $tly + $y;
                    switch ($align) {
                        case 'right' :
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
                        case 'right' :
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
                    case 'right' :
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


/**
 * PHPImageTTFBox class
 */
class PHPImageTTFBox
{
    /**
     * @var int
     */
    public $width = 0;
    /**
     * @var int
     */
    public $height = 0;
    /**
     * Top Left X
     * @var int
     */
    public $tlx = null;
    /**
     * Top Left Y
     * @var int
     */
    public $tly = null;
    /**
     * Top Right X
     * @var int
     */
    public $trx = null;
    /**
     * Top Right Y
     * @var int
     */
    public $try = null;
    /**
     * Bottom Left X
     * @var int
     */
    public $blx = null;
    /**
     * Bottom Left Y
     * @var int
     */
    public $bly = null;
    /**
     * Bottom Right X
     * @var int
     */
    public $brx = null;
    /**
     * Bottom Right Y
     * @var int
     */
    public $bry = null;

    /**
     *
     * @param string $text
     * @param int    $size
     * @param string $font
     * @param float  $angle
     */
    public function __construct($text, $size, $font, $angle = 0)
    {
        if (is_int($font)) {
            switch ($font) {
                case 1 :
                    $this->height = 9;
                    break;
                case 2 :
                    $this->height = 12;
                    break;
                case 3 :
                    $this->height = 13;
                    break;
                case 4 :
                    $this->height = 15;
                    break;
                case 5 :
                    $this->height = 14;
                    break;
            }
            $this->width = imagefontwidth($font) * strlen($text);
            $this->blx = 0;
            $this->bly = $this->height;
            $this->brx = $this->width;
            $this->bry = $this->height;
            $this->trx = $this->width;
            $this->try = 0;
            $this->tlx = 0;
            $this->tly = 0;
        } else {
            $b = imagettfbbox($size, $angle, $font, $text);
            $this->width = $b[2] - $b[0];
            $this->height = $b[1] - $b[5];
            $this->blx = $b[0];
            $this->bly = $b[1];
            $this->brx = $b[2];
            $this->bry = $b[3];
            $this->trx = $b[4];
            $this->try = $b[5];
            $this->tlx = $b[6];
            $this->tly = $b[7];
        }
    }
}

/**
 * PHPImageTools class
 */
class PHPImageTools
{
    /**
     *
     * @return array
     */
    static function linestylenames()
    {
        return ['solid', 'dot', 'square', 'dash', 'bigdash', 'double', 'triple'];
    }

    /**
     *
     * @return array
     */
    static function shapestylenames()
    {
        return [
            'biseau', 'biseau1', 'biseau2', 'biseau3', 'biseau4',
            'round', 'round1', 'round2',
            'curve', 'curve1', 'curve2', 'curve3', 'curve4', 'curve5', 'curve6',
            'trait', 'trait1', 'trait2', 'trait3',
            'empty', 'none',
        ];
    }

    /**
     *
     * @param  <type> $img
     * @param  <type> $angle
     * @param  <type> $bgcolor
     * @return <type>
     */
    static function imagerotation(& $img, $angle, $bgcolor = null)
    {
        $angle = -$angle;
        PHPImageTools::setangle360($angle);
        switch ($angle) {
            case 0 :
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
            case 270 :
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
            case 180 :
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
            case 90 :
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
            default :
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
     * @param <type> $img
     * @param <type> $points
     * @param <type> $color
     */
    static function imagefilledpolygon(& $img, $points, $color)
    {
        $scanline = 99999;
        //foreach($points as $point) { imagesetpixel($img, $point[0], $point[1], $color); }
        // compute edges and find starting scanline
        $all_edges = [];
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
                $all_edges[] = [$ymin, $ymax, $xval, $invslope];
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
        $active = [];
        $pixels = [];
        while (count($all_edges) + count($active) > 0) {
            // add edges to active array
            $tmp = [];
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
            $tmp = [];
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
            $pixels[$scanline] = [];
            $n = count($active);
            for ($i = 0; $i < $n; $i += 2) {
                if ($i + 1 < $n) {
                    if ($active[$i][2] == $active[$i + 1][2]) {
                        $x1 = intval(round($active[$i][2]));
                        $pixels[$scanline][] = [$x1, $x1];
                    } else {
                        $x1 = intval(round($active[$i][2]));
                        $x2 = intval(round($active[$i + 1][2]));
                        $pixels[$scanline][] = [$x1, $x2];
                    }
                }
            }
            // manage segments
            $ok = true;
            $tmp = [];
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
                $tmp[] = [$x1, $x2];
            }
            if ($i == $n - 1) {
                list($x1, $x2) = $pixels[$scanline][$n - 1];
                $tmp[] = [$x1, $x2];
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
                $draw = [];
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
                            $pixels[$y1][] = [$s, $x - 1];
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
                    $pixels[$y1][] = [$s, $x - 1];
                }
            }
        }
        foreach ($save_edges as $edge) {
            list($ymin, $ymax, $xval, $invslope) = $edge;
            for ($y = intval($ymin); $y <= $ymax; $y++) {
                $x = intval(round($xval));
                if (array_key_exists($y, $pixels)) {
                    $draw = [];
                    foreach ($pixels[$y] as $segment) {
                        list($xx1, $xx2) = $segment;
                        for ($k = $xx1; $k <= $xx2; $k++) {
                            $draw[$k] = true;
                        }
                    }
                    if (!array_key_exists($x, $draw)) {
                        imagesetpixel($img, $x, $y, $color);
                        $pixels[$y][] = [$x, $x];
                    }
                } else {
                    imagesetpixel($img, $x, $y, $color);
                    $pixels[$y][] = [$x, $x];
                }
                $xval += $invslope;
            }
        }
    }

    /**
     *
     * @param  <type> $w
     * @param  <type> $h
     * @param  <type> $start
     * @param  <type> $end
     * @return <type>
     */
    static function imageellipsearc_points($w, $h, $start, $end)
    {
        $q1 = $q2 = $q3 = $q4 = [];
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
        $q1[] = [$x, $y];
        $q2[] = [-$x, $y];
        $q3[] = [-$x, -$y];
        $q4[] = [$x, -$y];
        while ($a * $a * ($y - .5) > $b * $b * ($x + 1)) {
            if ($d1 < 0) {
                $d1 += $b * $b * (2 * $x + 3);
                $x++;
            } else {
                $d1 += $b * $b * (2 * $x + 3) + $a * $a * (-2 * $y + 2);
                $x++;
                $y--;
            }
            $q1[] = [$x, $y];
            $q2[] = [-$x, $y];
            $q3[] = [-$x, -$y];
            $q4[] = [$x, -$y];
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
            $q1[] = [$x, $y];
            $q2[] = [-$x, $y];
            $q3[] = [-$x, -$y];
            $q4[] = [$x, -$y];
        }
        $q = array_merge(array_reverse($q1), $q2, array_reverse($q3), $q4);
        $i = 0;
        $n = count($q);
        $tour = 0;
        $r = [];
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
        $l = [];
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
     *
     * @param <type> $color
     * @param <type> $alpha
     * @param <type> $bgcolor
     * @param <type> $style
     * @param <type> $shadow
     * @param <type> $shadow_x
     * @param <type> $shadow_y
     * @param <type> $shadow_color
     * @param <type> $shadow_bgcolor
     * @param <type> $shadow_alpha
     * @param <type> $shadow_blur
     */
    static function writetext_style_shadow($color, $alpha, $bgcolor, & $style, & $shadow, & $shadow_x, & $shadow_y, & $shadow_color, & $shadow_bgcolor, & $shadow_alpha, & $shadow_blur)
    {
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
     *
     * @param <type> $color
     * @param <type> $alpha
     * @param <type> $bgcolor
     * @param <type> $style
     * @param <type> $underline
     * @param <type> $underline_style
     * @param <type> $underline_thickness
     * @param <type> $underline_color
     * @param <type> $underline_alpha
     */
    static function writetext_style_underline($color, $alpha, $bgcolor, & $style, & $underline, & $underline_style, & $underline_thickness, & $underline_color, & $underline_alpha)
    {
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
                    if (in_array($item, ['dot', 'square', 'dash', 'bigdash', 'double', 'triple'])) {
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
     *
     * @param <type> $color
     * @param <type> $alpha
     * @param <type> $bgcolor
     * @param <type> $style
     * @param <type> $align
     */
    static function writetext_style($color, $alpha, $bgcolor, & $style, & $align)
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
     *
     * @param <type> $cx
     * @param <type> $cy
     * @param <type> $text
     * @param <type> $fontsize
     * @param <type> $angle
     * @param <type> $position
     * @param <type> $linespacing
     * @param <type> $font
     * @param <type> $lines
     * @param <type> $sizes
     * @param <type> $W
     * @param <type> $H
     * @param <type> $tlx
     * @param <type> $tly
     * @param <type> $lineH
     */
    static function writetext_init($cx, $cy, $text, $fontsize, & $angle, & $position, & $linespacing, & $font, & $lines, & $sizes, & $W, & $H, & $tlx, & $tly, & $lineH)
    {
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
        $sizes = [];
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
            case 'left top'     :
                $tlx = $cx;
                $tly = $cy;
                break;
            case 'left center'  :
                $tlx = $cx;
                $tly = $cy - $H / 2;
                break;
            case 'left bottom'  :
                $tlx = $cx;
                $tly = $cy - $H;
                break;
            case 'center top'   :
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
            case 'right top'    :
                $tlx = $cx - $W;
                $tly = $cy;
                break;
            case 'right center' :
                $tlx = $cx - $W;
                $tly = $cy - $H / 2;
                break;
            case 'right bottom' :
                $tlx = $cx - $W;
                $tly = $cy - $H;
                break;
        }
    }

    /**
     *
     * @param <type> $img
     * @param <type> $cx
     * @param <type> $cy
     * @param <type> $w
     * @param <type> $h
     * @param <type> $start
     * @param <type> $end
     * @param <type> $color
     * @param <type> $thickness
     */
    static function imageellipsearc(& $img, $cx, $cy, $w, $h, $start, $end, $color, $thickness)
    {
        PHPImageTools::setangle360($start, $end);
        $n = floor(abs($end - $start) * PHPImageTools::ellipseperimeter($w, $h) / 360);
        $w = round($w);
        $h = round($h);
        if ($n >= 2) {
            $points = [];
            if ($thickness > 1) {
                PHPImageTools::gete1e2($e1, $e2, $thickness);
                if ($thickness % 2 == 0) {
                    $e1--;
                } else {
                    $e2--;
                }
                $list = [];
                $points = PHPImageTools::imageellipsearc_points($w + 2 * $e1, $h + 2 * $e1, $start, $end);
                $n = count($points);
                for ($i = 0; $i < $n; $i++) {
                    $list[] = [$cx + $points[$i][0], $cy + $points[$i][1]];
                }
                $points = array_reverse(PHPImageTools::imageellipsearc_points($w - 2 * $e2, $h - 2 * $e2, $start, $end));
                $n = count($points);
                for ($i = 0; $i < $n; $i++) {
                    $list[] = [$cx + $points[$i][0], $cy + $points[$i][1]];
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
            imageline($img,
                $cx + 0.5 * $w * cos(PHPImageTools::deg2rad($end)),
                $cy + 0.5 * $h * sin(PHPImageTools::deg2rad($end)),
                $cx + 0.5 * $w * cos(PHPImageTools::deg2rad($start)),
                $cy + 0.5 * $h * sin(PHPImageTools::deg2rad($start)),
                $color
            );
        }
    }

    /**
     *
     * @param <type> $img
     * @param <type> $cx
     * @param <type> $cy
     * @param <type> $w
     * @param <type> $h
     * @param <type> $color
     * @param <type> $thickness
     */
    static function imageellipse(& $img, $cx, $cy, $w, $h, $color, $thickness)
    {
        $n = floor(PHPImageTools::ellipseperimeter($w, $h));
        $w = round($w);
        $h = round($h);
        if ($n >= 2) {
            if ($thickness > 1) {
                $points = [];
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
     *
     * @param  <type> $img
     * @param  <type> $x1
     * @param  <type> $y1
     * @param  <type> $x2
     * @param  <type> $y2
     * @param  <type> $color
     * @param  <type> $thickness
     * @return <type>
     */
    static function imageline(& $img, $x1, $y1, $x2, $y2, $color, $thickness)
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
                round(min($x1, $x2) - $e), round(min($y1, $y2)),
                round(max($x1, $x2) + $e), round(max($y1, $y2)),
                $color
            );
        } elseif ($y1 == $y2) {
            imagefilledrectangle(
                $img,
                round(min($x1, $x2)), round(min($y1, $y2) - $e),
                round(max($x1, $x2)), round(max($y1, $y2) + $e),
                $color
            );
        } else {
            $k = ($y2 - $y1) / ($x2 - $x1); // y = kx + q
            $dx = $e / sqrt(1 + $k * $k);
            $dy = $e / sqrt(1 + 1 / ($k * $k));
            $dy *= ($y2 - $y1) / abs($y2 - $y1);
            $points = [
                round($x1 - $dy), round($y1 + $dx),
                round($x1 + $dy), round($y1 - $dx),
                round($x2 + $dy), round($y2 - $dx),
                round($x2 - $dy), round($y2 + $dx),
            ];
            if ($points[0] == $points[2] && $points[1] == $points[3] &&
                $points[4] == $points[6] && $points[5] == $points[7]
            ) {
                imageline($img, $x1, $y1, $x2, $y2, $color);
            } else {
                imagefilledpolygon($img, $points, 4, $color);
            }
        }
    }

    /**
     *
     * @param  <type> $paramname
     * @param  <type> $value
     * @return <type>
     */
    static function checkshapestyle($paramname, & $value)
    {
        $save = $value;
        PHPImageTools::checkparams($paramname, $value);
        $S = [
            'tl' => ['forme' => 'none', 'w' => 0, 'h' => 0],
            'tr' => ['forme' => 'none', 'w' => 0, 'h' => 0],
            'bl' => ['forme' => 'none', 'w' => 0, 'h' => 0],
            'br' => ['forme' => 'none', 'w' => 0, 'h' => 0],
        ];
        foreach ($value as $param => $items) {
            $tmp = ['forme' => 'none', 'w' => 0, 'h' => 0];
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
     *
     * @param <type> $n1
     * @param <type> $n2
     * @param <type> $nb
     * @param <type> $n
     * @param <type> $stylename
     * @param <type> $thickness
     * @param <type> $length
     * @param <type> $strict
     */
    static function getn1n2(& $n1, & $n2, & $nb, & $n, $stylename, $thickness, $length, $strict = true)
    {
        switch ($stylename) {
            case 'dash'   :
                $n1 = 3;
                $n2 = 2;
                break;
            case 'bigdash':
                $n1 = 6;
                $n2 = 2;
                break;
            case 'dot'    :
            case 'square' :
            default       :
                $n1 = 1;
                $n2 = 1;
                break;
        }
        $nb = ceil($length / $thickness);
        if ($strict) {
            $n = floor(($nb + $n2) / ($n1 + $n2));
        } else {
            switch ($stylename) {
                case 'dot'    :
                    $n = floor(($nb + $n2) / ($n1 + $n2));
                    break;
                default       :
                    $n = floor(($nb + $n2) / ($n1 + $n2 + 1));
                    break;
            }
        }
        if ($n > 1) {
            $n2 = ($nb - $n * $n1) / ($n - 1);
        }
    }

    /**
     *
     * @param <type> $e1
     * @param <type> $e2
     * @param <type> $thickness
     */
    static function gete1e2(& $e1, & $e2, $thickness)
    {
        $e1 = floor($thickness / 2);
        $e2 = $thickness - $e1;
    }

    /**
     *
     * @param  <type> $paramname
     * @param  <type> $value
     * @return <type>
     */
    static function checklinestyle($paramname, & $value)
    {
        $save = $value;
        if (is_array($value)) {
            $tmp = [];
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
     *
     * @param  <type> $paramname
     * @param  <type> $value
     * @return <type>
     */
    static function checkparams($paramname, & $value)
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
            $params = [];
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
     *
     * @param <type> $paramname
     * @param <type> $value
     */
    static function checkresource($paramname, & $value)
    {
        if (!is_resource($value)) {
            throw new PHPImageException("$paramname is not a resource");
        }
    }

    /**
     *
     * @param  <type> $paramname
     * @param  <type> $matrix
     * @return <type>
     */
    static function checkmatrix($paramname, & $matrix)
    {
        if (is_string($matrix)) {
            $matrix = preg_replace('/\s+/si', ' ', trim($matrix));
            $matrix = preg_replace('/[^0-9\. -]/si', '', $matrix);
            $matrix = explode(' ', $matrix);
        } elseif (is_array($matrix)) {
            $tmp = [];
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
        $tmp = [];
        for ($i = 0; $i < $dim; $i++) {
            $tmp[] = array_slice($matrix, $i * $dim, $dim);
        }
        $matrix = $tmp;
        return $dim;
    }

    /**
     *
     * @param <type> $object
     */
    static function checkcreated($object)
    {
        if (!is_resource($object->img)) {
            throw new PHPImageException("You should run <b>create</b> or <b>loadfromfile</b> methods before drawing anything");
        }
    }

    /**
     *
     * @param <type> $paramname
     * @param <type> $value
     * @param <type> $shouldexist
     */
    static function checkfile($paramname, & $value, $shouldexist = true)
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
     *
     * @param <type> $font
     */
    static function checkfont(& $font)
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
     *
     * @param  <type> $folder
     * @param  <type> $mode
     * @return <type>
     */
    static function createfolder($folder, $mode = 0755)
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
     *
     * @param  <type> $folder
     * @param  <type> $mode
     * @return <type>
     */
    static function createfolderpath($folder, $mode = 0755)
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
     *
     * @param <type> $paramname
     * @param <type> $value
     */
    static function checkbool($paramname, $value)
    {
        if (!is_bool($value)) {
            throw new PHPImageException("$paramname is not a boolean (true or false)");
        }
    }



    /**
     *
     * @param  <type> $paramname
     * @param  <type> $value
     * @param  <type> $x
     * @param  <type> $y
     * @param  <type> $w
     * @param  <type> $h
     * @return <type>
     */
    static function checkposition($paramname, $value, & $x, & $y, $w, $h)
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
     *
     * @param <type> $paramname
     * @param <type> $value
     * @param <type> $maxsize
     */
    static function checksize($paramname, & $value, $maxsize)
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
     *
     * @param  <type> $filename
     * @return <type>
     */
    static function imagesize($filename)
    {
        if (file_exists($filename)) {
            list($width, $height, $type, $attr) = getimagesize($filename);
            return [$width, $height];
        }
        return [0, 0];
    }

    /**
     *
     * @param <type> $srcw
     * @param <type> $srch
     * @param <type> $dstw
     * @param <type> $dsth
     */
    static function resizefit($srcw, $srch, & $dstw, & $dsth)
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
     *
     * @param  <type> $filename
     * @return <type>
     */
    static function imageheight($filename)
    {
        if (file_exists($filename)) {
            list($width, $height, $type, $attr) = getimagesize($filename);
            return $height;
        }
        return 0;
    }

    /**
     *
     * @param  <type> $filename
     * @return <type>
     */
    static function imagewidth($filename)
    {
        if (file_exists($filename)) {
            list($width, $height, $type, $attr) = getimagesize($filename);
            return $width;
        }
        return 0;
    }

    /**
     *
     * @param <type> $angle
     * @param <type> $angle2
     */
    static function setangle360(& $angle, & $angle2 = null)
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
     *
     * @param  <type> $img
     * @return <type>
     */
    static function getimageresource(& $img)
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
    static function readfile($filename)
    {
        $fh = fopen($filename, 'rb');
        while (!feof($fh)) {
            echo fread($fh, 409600);
        }
        fclose($fh);
    }

    /**
     *
     * @param string $filename
     * @return string
     */
    static function getfileext($filename)
    {
        $ext = '';
        $i = strrpos($filename, '.');
        if ($i) {
            $ext = strtolower(substr($filename, $i + 1, strlen($filename) - $i - 1));
        }
        return $ext;
    }

    /**
     *
     * @param  <type> $img
     * @param  <type> $linestyle
     * @return <type>
     */
    static function getlinestylelist($img, $linestyle)
    {
        $list = [];
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
     *
     * @param  <type> $x1
     * @param  <type> $y1
     * @param  <type> $x2
     * @param  <type> $y2
     * @return <type>
     */
    static function linelength($x1, $y1, $x2, $y2)
    {
        return sqrt(($x2 - $x1) * ($x2 - $x1) + ($y2 - $y1) * ($y2 - $y1));
    }

    /**
     *
     * @param float $deg
     * @return float
     */
    static function deg2rad($deg)
    {
        return M_PI * $deg / 180;
    }

    /**
     *
     * @param float $rad
     * @return float
     */
    static function rad2deg($rad)
    {
        return $rad * 180 / M_PI;
    }

    /**
     *
     * @param <type> $cx
     * @param <type> $cy
     * @param <type> $x
     * @param <type> $y
     * @param <type> $angle
     */
    static function rotatepoint($cx, $cy, & $x, & $y, $angle)
    {
        $a = PHPImageTools::deg2rad($angle);
        $dx = $x - $cx;
        $dy = $y - $cy;
        $x = round($cx + $dx * cos($a) - $dy * sin($a), 0);
        $y = round($cy + $dx * sin($a) + $dy * cos($a), 0);
    }

    /**
     *
     * @param  <type> $x1
     * @param  <type> $y1
     * @param  <type> $x2
     * @param  <type> $y2
     * @return <type>
     */
    static function quadran($x1, $y1, $x2, $y2)
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
     *
     * @param  <type> $x1
     * @param  <type> $y1
     * @param  <type> $x2
     * @param  <type> $y2
     * @return <type>
     */
    static function lineangle($x1, $y1, $x2, $y2)
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
     *
     * @param  <type> $width
     * @param  <type> $height
     * @return <type>
     */
    static function ellipseperimeter($width, $height)
    {
        return 2 * M_PI * sqrt($width * $width / 8 + $height * $height / 8);
    }

    /**
     *
     * @param <type> $v1
     * @param <type> $v2
     */
    static function switchvar(& $v1, & $v2)
    {
        $tmp = $v2;
        $v2 = $v1;
        $v1 = $tmp;
    }
}

/**
 *
 */
global $PHPImageColors;
$PHPImageColors = [
    'aqua' => [0, 255, 255], 'lime' => [0, 255, 0],
    'teal' => [0, 128, 128], 'whitesmoke' => [245, 245, 245],
    'gainsboro' => [220, 220, 220], 'oldlace' => [253, 245, 230],
    'linen' => [250, 240, 230], 'antiquewhite' => [250, 235, 215],
    'papayawhip' => [255, 239, 213], 'blanchedalmond' => [255, 235, 205],
    'bisque' => [255, 228, 196], 'peachpuff' => [255, 218, 185],
    'navajowhite' => [255, 222, 173], 'moccasin' => [255, 228, 181],
    'cornsilk' => [255, 248, 220], 'ivory' => [255, 255, 240],
    'lemonchiffon' => [255, 250, 205], 'seashell' => [255, 245, 238],
    'mintcream' => [245, 255, 250], 'azure' => [240, 255, 255],
    'aliceblue' => [240, 248, 255], 'lavender' => [230, 230, 250],
    'lavenderblush' => [255, 240, 245], 'mistyrose' => [255, 228, 225],
    'white' => [255, 255, 255], 'black' => [0, 0, 0],
    'darkslategray' => [47, 79, 79], 'dimgray' => [105, 105, 105],
    'slategray' => [112, 128, 144], 'lightslategray' => [119, 136, 153],
    'gray' => [190, 190, 190], 'lightgray' => [211, 211, 211],
    'midnightblue' => [25, 25, 112], 'navy' => [0, 0, 128],
    'cornflowerblue' => [100, 149, 237], 'darkslateblue' => [72, 61, 139],
    'slateblue' => [106, 90, 205], 'mediumslateblue' => [123, 104, 238],
    'lightslateblue' => [132, 112, 255], 'mediumblue' => [0, 0, 205],
    'royalblue' => [65, 105, 225], 'blue' => [0, 0, 255],
    'dodgerblue' => [30, 144, 255], 'deepskyblue' => [0, 191, 255],
    'skyblue' => [135, 206, 235], 'lightskyblue' => [135, 206, 250],
    'steelblue' => [70, 130, 180], 'lightred' => [211, 167, 168],
    'lightsteelblue' => [176, 196, 222], 'lightblue' => [173, 216, 230],
    'powderblue' => [176, 224, 230], 'paleturquoise' => [175, 238, 238],
    'darkturquoise' => [0, 206, 209], 'mediumturquoise' => [72, 209, 204],
    'turquoise' => [64, 224, 208], 'cyan' => [0, 255, 255],
    'lightcyan' => [224, 255, 255], 'cadetblue' => [95, 158, 160],
    'mediumaquamarine' => [102, 205, 170], 'aquamarine' => [127, 255, 212],
    'darkgreen' => [0, 100, 0], 'darkolivegreen' => [85, 107, 47],
    'darkseagreen' => [143, 188, 143], 'seagreen' => [46, 139, 87],
    'mediumseagreen' => [60, 179, 113], 'lightseagreen' => [32, 178, 170],
    'palegreen' => [152, 251, 152], 'springgreen' => [0, 255, 127],
    'lawngreen' => [124, 252, 0], 'green' => [0, 255, 0],
    'chartreuse' => [127, 255, 0], 'mediumspringgreen' => [0, 250, 154],
    'greenyellow' => [173, 255, 47], 'limegreen' => [50, 205, 50],
    'yellowgreen' => [154, 205, 50], 'forestgreen' => [34, 139, 34],
    'olivedrab' => [107, 142, 35], 'darkkhaki' => [189, 183, 107],
    'khaki' => [240, 230, 140], 'palegoldenrod' => [238, 232, 170],
    'lightgoldenrodyellow' => [250, 250, 210], 'lightyellow' => [255, 255, 200],
    'yellow' => [255, 255, 0], 'gold' => [255, 215, 0],
    'lightgoldenrod' => [238, 221, 130], 'goldenrod' => [218, 165, 32],
    'darkgoldenrod' => [184, 134, 11], 'rosybrown' => [188, 143, 143],
    'indianred' => [205, 92, 92], 'saddlebrown' => [139, 69, 19],
    'sienna' => [160, 82, 45], 'peru' => [205, 133, 63],
    'burlywood' => [222, 184, 135], 'beige' => [245, 245, 220],
    'wheat' => [245, 222, 179], 'sandybrown' => [244, 164, 96],
    'tan' => [210, 180, 140], 'chocolate' => [210, 105, 30],
    'firebrick' => [178, 34, 34], 'brown' => [165, 42, 42],
    'darksalmon' => [233, 150, 122], 'salmon' => [250, 128, 114],
    'lightsalmon' => [255, 160, 122], 'orange' => [255, 165, 0],
    'darkorange' => [255, 140, 0], 'coral' => [255, 127, 80],
    'lightcoral' => [240, 128, 128], 'tomato' => [255, 99, 71],
    'orangered' => [255, 69, 0], 'red' => [255, 0, 0],
    'hotpink' => [255, 105, 180], 'deeppink' => [255, 20, 147],
    'pink' => [255, 192, 203], 'lightpink' => [255, 182, 193],
    'palevioletred' => [219, 112, 147], 'maroon' => [176, 48, 96],
    'mediumvioletred' => [199, 21, 133], 'violetred' => [208, 32, 144],
    'magenta' => [255, 0, 255], 'violet' => [238, 130, 238],
    'plum' => [221, 160, 221], 'orchid' => [218, 112, 214],
    'mediumorchid' => [186, 85, 211], 'darkorchid' => [153, 50, 204],
    'darkviolet' => [148, 0, 211], 'blueviolet' => [138, 43, 226],
    'purple' => [160, 32, 240], 'mediumpurple' => [147, 112, 219],
    'thistle' => [216, 191, 216], 'snow1' => [255, 250, 250],
    'snow2' => [238, 233, 233], 'snow3' => [205, 201, 201],
    'snow4' => [139, 137, 137], 'seashell1' => [255, 245, 238],
    'seashell2' => [238, 229, 222], 'seashell3' => [205, 197, 191],
    'seashell4' => [139, 134, 130], 'AntiqueWhite1' => [255, 239, 219],
    'AntiqueWhite2' => [238, 223, 204], 'AntiqueWhite3' => [205, 192, 176],
    'AntiqueWhite4' => [139, 131, 120], 'bisque1' => [255, 228, 196],
    'bisque2' => [238, 213, 183], 'bisque3' => [205, 183, 158],
    'bisque4' => [139, 125, 107], 'peachPuff1' => [255, 218, 185],
    'peachpuff2' => [238, 203, 173], 'peachpuff3' => [205, 175, 149],
    'peachpuff4' => [139, 119, 101], 'navajowhite1' => [255, 222, 173],
    'navajowhite2' => [238, 207, 161], 'navajowhite3' => [205, 179, 139],
    'navajowhite4' => [139, 121, 94], 'lemonchiffon1' => [255, 250, 205],
    'lemonchiffon2' => [238, 233, 191], 'lemonchiffon3' => [205, 201, 165],
    'lemonchiffon4' => [139, 137, 112], 'ivory1' => [255, 255, 240],
    'ivory2' => [238, 238, 224], 'ivory3' => [205, 205, 193],
    'ivory4' => [139, 139, 131], 'honeydew' => [193, 205, 193],
    'lavenderblush1' => [255, 240, 245], 'lavenderblush2' => [238, 224, 229],
    'lavenderblush3' => [205, 193, 197], 'lavenderblush4' => [139, 131, 134],
    'mistyrose1' => [255, 228, 225], 'mistyrose2' => [238, 213, 210],
    'mistyrose3' => [205, 183, 181], 'mistyrose4' => [139, 125, 123],
    'azure1' => [240, 255, 255], 'azure2' => [224, 238, 238],
    'azure3' => [193, 205, 205], 'azure4' => [131, 139, 139],
    'slateblue1' => [131, 111, 255], 'slateblue2' => [122, 103, 238],
    'slateblue3' => [105, 89, 205], 'slateblue4' => [71, 60, 139],
    'royalblue1' => [72, 118, 255], 'royalblue2' => [67, 110, 238],
    'royalblue3' => [58, 95, 205], 'royalblue4' => [39, 64, 139],
    'dodgerblue1' => [30, 144, 255], 'dodgerblue2' => [28, 134, 238],
    'dodgerblue3' => [24, 116, 205], 'dodgerblue4' => [16, 78, 139],
    'steelblue1' => [99, 184, 255], 'steelblue2' => [92, 172, 238],
    'steelblue3' => [79, 148, 205], 'steelblue4' => [54, 100, 139],
    'deepskyblue1' => [0, 191, 255], 'deepskyblue2' => [0, 178, 238],
    'deepskyblue3' => [0, 154, 205], 'deepskyblue4' => [0, 104, 139],
    'skyblue1' => [135, 206, 255], 'skyblue2' => [126, 192, 238],
    'skyblue3' => [108, 166, 205], 'skyblue4' => [74, 112, 139],
    'lightskyblue1' => [176, 226, 255], 'lightskyblue2' => [164, 211, 238],
    'lightskyblue3' => [141, 182, 205], 'lightskyblue4' => [96, 123, 139],
    'slategray1' => [198, 226, 255], 'slategray2' => [185, 211, 238],
    'slategray3' => [159, 182, 205], 'slategray4' => [108, 123, 139],
    'lightsteelblue1' => [202, 225, 255], 'lightsteelblue2' => [188, 210, 238],
    'lightsteelblue3' => [162, 181, 205], 'lightsteelblue4' => [110, 123, 139],
    'lightblue1' => [191, 239, 255], 'lightblue2' => [178, 223, 238],
    'lightblue3' => [154, 192, 205], 'lightblue4' => [104, 131, 139],
    'lightcyan1' => [224, 255, 255], 'lightcyan2' => [209, 238, 238],
    'lightcyan3' => [180, 205, 205], 'lightcyan4' => [122, 139, 139],
    'paleturquoise1' => [187, 255, 255], 'paleturquoise2' => [174, 238, 238],
    'paleturquoise3' => [150, 205, 205], 'paleturquoise4' => [102, 139, 139],
    'cadetblue1' => [152, 245, 255], 'cadetblue2' => [142, 229, 238],
    'cadetblue3' => [122, 197, 205], 'cadetblue4' => [83, 134, 139],
    'turquoise1' => [0, 245, 255], 'turquoise2' => [0, 229, 238],
    'turquoise3' => [0, 197, 205], 'turquoise4' => [0, 134, 139],
    'cyan1' => [0, 255, 255], 'cyan2' => [0, 238, 238],
    'cyan3' => [0, 205, 205], 'cyan4' => [0, 139, 139],
    'darkslategray1' => [151, 255, 255], 'darkslategray2' => [141, 238, 238],
    'darkslategray3' => [121, 205, 205], 'darkslategray4' => [82, 139, 139],
    'aquamarine1' => [127, 255, 212], 'aquamarine2' => [118, 238, 198],
    'aquamarine3' => [102, 205, 170], 'aquamarine4' => [69, 139, 116],
    'darkseagreen1' => [193, 255, 193], 'darkseagreen2' => [180, 238, 180],
    'darkseagreen3' => [155, 205, 155], 'darkseagreen4' => [105, 139, 105],
    'seagreen1' => [84, 255, 159], 'seagreen2' => [78, 238, 148],
    'seagreen3' => [67, 205, 128], 'seagreen4' => [46, 139, 87],
    'palegreen1' => [154, 255, 154], 'palegreen2' => [144, 238, 144],
    'palegreen3' => [124, 205, 124], 'palegreen4' => [84, 139, 84],
    'springgreen1' => [0, 255, 127], 'springgreen2' => [0, 238, 118],
    'springgreen3' => [0, 205, 102], 'springgreen4' => [0, 139, 69],
    'chartreuse1' => [127, 255, 0], 'chartreuse2' => [118, 238, 0],
    'chartreuse3' => [102, 205, 0], 'chartreuse4' => [69, 139, 0],
    'olivedrab1' => [192, 255, 62], 'olivedrab2' => [179, 238, 58],
    'olivedrab3' => [154, 205, 50], 'olivedrab4' => [105, 139, 34],
    'darkolivegreen1' => [202, 255, 112], 'darkolivegreen2' => [188, 238, 104],
    'darkolivegreen3' => [162, 205, 90], 'darkolivegreen4' => [110, 139, 61],
    'khaki1' => [255, 246, 143], 'khaki2' => [238, 230, 133],
    'khaki3' => [205, 198, 115], 'khaki4' => [139, 134, 78],
    'lightgoldenrod1' => [255, 236, 139], 'lightgoldenrod2' => [238, 220, 130],
    'lightgoldenrod3' => [205, 190, 112], 'lightgoldenrod4' => [139, 129, 76],
    'yellow1' => [255, 255, 0], 'yellow2' => [238, 238, 0],
    'yellow3' => [205, 205, 0], 'yellow4' => [139, 139, 0],
    'gold1' => [255, 215, 0], 'gold2' => [238, 201, 0],
    'gold3' => [205, 173, 0], 'gold4' => [139, 117, 0],
    'goldenrod1' => [255, 193, 37], 'goldenrod2' => [238, 180, 34],
    'goldenrod3' => [205, 155, 29], 'goldenrod4' => [139, 105, 20],
    'darkgoldenrod1' => [255, 185, 15], 'darkgoldenrod2' => [238, 173, 14],
    'darkgoldenrod3' => [205, 149, 12], 'darkgoldenrod4' => [139, 101, 8],
    'rosybrown1' => [255, 193, 193], 'rosybrown2' => [238, 180, 180],
    'rosybrown3' => [205, 155, 155], 'rosybrown4' => [139, 105, 105],
    'indianred1' => [255, 106, 106], 'indianred2' => [238, 99, 99],
    'indianred3' => [205, 85, 85], 'indianred4' => [139, 58, 58],
    'sienna1' => [255, 130, 71], 'sienna2' => [238, 121, 66],
    'sienna3' => [205, 104, 57], 'sienna4' => [139, 71, 38],
    'burlywood1' => [255, 211, 155], 'burlywood2' => [238, 197, 145],
    'burlywood3' => [205, 170, 125], 'burlywood4' => [139, 115, 85],
    'wheat1' => [255, 231, 186], 'wheat2' => [238, 216, 174],
    'wheat3' => [205, 186, 150], 'wheat4' => [139, 126, 102],
    'tan1' => [255, 165, 79], 'tan2' => [238, 154, 73],
    'tan3' => [205, 133, 63], 'tan4' => [139, 90, 43],
    'chocolate1' => [255, 127, 36], 'chocolate2' => [238, 118, 33],
    'chocolate3' => [205, 102, 29], 'chocolate4' => [139, 69, 19],
    'firebrick1' => [255, 48, 48], 'firebrick2' => [238, 44, 44],
    'firebrick3' => [205, 38, 38], 'firebrick4' => [139, 26, 26],
    'brown1' => [255, 64, 64], 'brown2' => [238, 59, 59],
    'brown3' => [205, 51, 51], 'brown4' => [139, 35, 35],
    'salmon1' => [255, 140, 105], 'salmon2' => [238, 130, 98],
    'salmon3' => [205, 112, 84], 'salmon4' => [139, 76, 57],
    'lightsalmon1' => [255, 160, 122], 'lightsalmon2' => [238, 149, 114],
    'lightsalmon3' => [205, 129, 98], 'lightsalmon4' => [139, 87, 66],
    'orange1' => [255, 165, 0], 'orange2' => [238, 154, 0],
    'orange3' => [205, 133, 0], 'orange4' => [139, 90, 0],
    'darkorange1' => [255, 127, 0], 'darkorange2' => [238, 118, 0],
    'darkorange3' => [205, 102, 0], 'darkorange4' => [139, 69, 0],
    'coral1' => [255, 114, 86], 'coral2' => [238, 106, 80],
    'coral3' => [205, 91, 69], 'coral4' => [139, 62, 47],
    'tomato1' => [255, 99, 71], 'tomato2' => [238, 92, 66],
    'tomato3' => [205, 79, 57], 'tomato4' => [139, 54, 38],
    'orangered1' => [255, 69, 0], 'orangered2' => [238, 64, 0],
    'orangered3' => [205, 55, 0], 'orangered4' => [139, 37, 0],
    'deeppink1' => [255, 20, 147], 'deeppink2' => [238, 18, 137],
    'deeppink3' => [205, 16, 118], 'deeppink4' => [139, 10, 80],
    'hotpink1' => [255, 110, 180], 'hotpink2' => [238, 106, 167],
    'hotpink3' => [205, 96, 144], 'hotpink4' => [139, 58, 98],
    'pink1' => [255, 181, 197], 'pink2' => [238, 169, 184],
    'pink3' => [205, 145, 158], 'pink4' => [139, 99, 108],
    'lightpink1' => [255, 174, 185], 'lightpink2' => [238, 162, 173],
    'lightpink3' => [205, 140, 149], 'lightpink4' => [139, 95, 101],
    'palevioletred1' => [255, 130, 171], 'palevioletred2' => [238, 121, 159],
    'palevioletred3' => [205, 104, 137], 'palevioletred4' => [139, 71, 93],
    'maroon1' => [255, 52, 179], 'maroon2' => [238, 48, 167],
    'maroon3' => [205, 41, 144], 'maroon4' => [139, 28, 98],
    'violetred1' => [255, 62, 150], 'violetred2' => [238, 58, 140],
    'violetred3' => [205, 50, 120], 'violetred4' => [139, 34, 82],
    'magenta1' => [255, 0, 255], 'magenta2' => [238, 0, 238],
    'magenta3' => [205, 0, 205], 'magenta4' => [139, 0, 139],
    'mediumred' => [140, 34, 34], 'orchid1' => [255, 131, 250],
    'orchid2' => [238, 122, 233], 'orchid3' => [205, 105, 201],
    'orchid4' => [139, 71, 137], 'plum1' => [255, 187, 255],
    'plum2' => [238, 174, 238], 'plum3' => [205, 150, 205],
    'plum4' => [139, 102, 139], 'mediumorchid1' => [224, 102, 255],
    'mediumorchid2' => [209, 95, 238], 'mediumorchid3' => [180, 82, 205],
    'mediumorchid4' => [122, 55, 139], 'darkorchid1' => [191, 62, 255],
    'darkorchid2' => [178, 58, 238], 'darkorchid3' => [154, 50, 205],
    'darkorchid4' => [104, 34, 139], 'purple1' => [155, 48, 255],
    'purple2' => [145, 44, 238], 'purple3' => [125, 38, 205],
    'purple4' => [85, 26, 139], 'mediumpurple1' => [171, 130, 255],
    'mediumpurple2' => [159, 121, 238], 'mediumpurple3' => [137, 104, 205],
    'mediumpurple4' => [93, 71, 139], 'thistle1' => [255, 225, 255],
    'thistle2' => [238, 210, 238], 'thistle3' => [205, 181, 205],
    'thistle4' => [139, 123, 139], 'gray1' => [10, 10, 10],
    'gray2' => [40, 40, 30], 'gray3' => [70, 70, 70],
    'gray4' => [100, 100, 100], 'gray5' => [130, 130, 130],
    'gray6' => [160, 160, 160], 'gray7' => [190, 190, 190],
    'gray8' => [210, 210, 210], 'gray9' => [240, 240, 240],
    'darkgray' => [100, 100, 100], 'darkblue' => [0, 0, 139],
    'darkcyan' => [0, 139, 139], 'darkmagenta' => [139, 0, 139],
    'darkred' => [139, 0, 0], 'silver' => [192, 192, 192],
    'eggplant' => [144, 176, 168], 'lightgreen' => [144, 238, 144],
];


/**
 * PHPImageColor class
 */
class PHPImageColor
{
    /**
     * RED
     * @var int
     */
    public $R = 0;
    /**
     * GREEN
     * @var int
     */
    public $G = 0;
    /**
     * BLUE
     * @var int
     */
    public $B = 0;
    /**
     * ALPHA / TRANSPARENCY
     * @var int
     */
    public $A = 0;
    /**
     *
     */
    public $value = '';

    /**
     *
     * @param string $color
     */
    public function __construct($color = 'black')
    {
        $this->setcolor($color);
    }

    /**
     *
     * @return string
     */
    public function getHTML()
    {
        return sprintf('#%02x%02x%02x', $this->R, $this->G, $this->B);
    }

    /**
     *
     * @param bool $invertalpha
     */
    public function invert($invertalpha = false)
    {
        $R = 255 - $this->R;
        $G = 255 - $this->G;
        $B = 255 - $this->B;
        $A = $invertalpha === true ? 127 - $this->A : $this->A;
        $this->setcolor("$R,$G,$B,$A");
    }

    /**
     *
     * @return string
     */
    public function getvalues()
    {
        return $this->R . ',' . $this->G . ',' . $this->B . ',' . $this->A;
    }

    /**
     *
     */
    public function togray()
    {
        $avg = floor(($this->R + $this->G + $this->B) / 3);
        $this->setcolor("$avg,$avg,$avg," . $this->A);
    }

    /**
     *
     * @param string $color
     */
    public function setcolor($color)
    {
        $this->value = $color;
        PHPImageTools::checkcolor($color);
        $this->R = $color[0];
        $this->G = $color[1];
        $this->B = $color[2];
        $this->A = $color[3];
    }
}

?>