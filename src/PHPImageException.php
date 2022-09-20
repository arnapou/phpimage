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
 * PHPImageException class
 */
class PHPImageException extends Exception
{
    /**
     * @param string $message
     * @param int $code
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
            $widths = array();
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
