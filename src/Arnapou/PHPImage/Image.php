<?php
/*
 * This file is part of the Arnapou PHPImage package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PHPImage;

use Arnapou\PHPImage\Component\Color;
use Arnapou\PHPImage\Component\Point;
use Arnapou\PHPImage\Component\Size;
use Arnapou\PHPImage\Exception\FileNotFoundException;
use Arnapou\PHPImage\Exception\UnknownFileTypeException;
use Arnapou\PHPImage\Helper\HelperTrait;

class Image
{
    use HelperTrait;

    const FILETYPE_PNG = 'png';
    const FILETYPE_JPG = 'jpg';
    const FILETYPE_GIF = 'gif';

    /**
     * @var int
     */
    protected $width;
    /**
     * @var int
     */
    protected $height;
    /**
     * @var resource
     */
    protected $img;
    /**
     * @var Color
     */
    protected $backgroundColor;
    /**
     * @var array
     */
    protected $gdAllocatedColors;
    /**
     * @var string
     */
    protected $fileType = self::FILETYPE_PNG;

    /**
     * Image constructor.
     * @param int        $width
     * @param int        $height
     * @param Color|null $backgroundColor
     */
    public function __construct($width, $height, Color $backgroundColor = null)
    {
        $this->typeChecker()->checkInteger($width, 1);
        $this->typeChecker()->checkInteger($height, 1);
        $this->width = $width;
        $this->height = $height;
        if ($backgroundColor) {
            $this->backgroundColor = $backgroundColor;
        } else {
            $this->backgroundColor = new Color([255, 255, 255, 0]); // white full transparent
        }
        $this->clear();
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->gdDestroy();
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->img;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     *
     */
    public function clear()
    {
        $this->gdDestroy();
        $this->gdAllocatedColors = [];
        $this->img = $this->gd()->createImage($this->width, $this->height, $this->gdColor($this->backgroundColor));
    }

    public function copy($source, $dstXY = null, $srcXY = null, $srcWH = null,
                         $dstPos = null, $srcPos = null, $realCopy = false, $forcedAlpha = null)
    {
        $this->typeChecker()->checkResource($source);
        if($forcedAlpha !== null) {
            $this->typeChecker()->checkAlpha($forcedAlpha);
            $transparency = Color::MAX_ALPHA - $forcedAlpha;
        }
        else {
        $transparency = null;

        }

        $dstXY = new Point($dstXY);
        $dstx = $dstXY->getX($this->width - 1);
        $dsty = $dstXY->getY($this->height - 1);

        $srcXY = new Point($srcXY);
        $srcx = $srcXY->getX(\imagesx($source));
        $srcy = $srcXY->getY(\imagesy($source));

        $srcWH = new Size($srcWH);
        $srcw = $srcWH->getW(\imagesx($source));
        $srch = $srcWH->getH(\imagesy($source));

        if ($realCopy) {
            $this->gd()->realCopy($this->img, $source, $dstx, $dsty, $srcx, $srcy, $srcw, $srch, $transparency);
        } elseif($forcedAlpha) {
            imagecopymerge($this->img, $source, $dstx, $dsty, $srcx, $srcy, $srcw, $srch, floor(100 * $transparency / 127));
        }
        else {
            imagecopy($this->img, $source, $dstx, $dsty, $srcx, $srcy, $srcw, $srch);
        }
//
//        $srcimg = PHPImageTools::getimageresource($src);
//        PHPImageTools::checkresource('Source image', $srcimg);
//        $srcw = $srcw == 0 ? imagesx($srcimg) : $srcw;
//        $srch = $srch == 0 ? imagesy($srcimg) : $srch;
//        PHPImageTools::checksize('Destination X', $dstx, $this->width);
//        PHPImageTools::checksize('Destination Y', $dsty, $this->height);
//        PHPImageTools::checksize('Source X', $srcx, imagesx($srcimg));
//        PHPImageTools::checksize('Source X', $srcy, imagesy($srcimg));
//        PHPImageTools::checksize('Source Width', $srcw, imagesx($srcimg));
//        PHPImageTools::checksize('Source Height', $srch, imagesy($srcimg));
//        PHPImageTools::checksize('Alpha', $alpha, 127);
//        PHPImageTools::checkinteger('Alpha', $alpha, -1, 127);
//        PHPImageTools::checkposition('Destination position', $dstpos, $dstx, $dsty, $srcw, $srch);
//        PHPImageTools::checkposition('Source position', $srcpos, $srcx, $srcy, $srcw, $srch);
//
//        if ($this->realcopy) {
//            $this->realcopy($srcimg, $dstx, $dsty, $srcx, $srcy, $srcw, $srch, $alpha);
//        } elseif ($alpha < 127) {
//            if ($alpha < 0) {
//                imagecopy($this->img, $srcimg, $dstx, $dsty, $srcx, $srcy, $srcw, $srch);
//            } else {
//                imagecopymerge($this->img, $srcimg, $dstx, $dsty, $srcx, $srcy, $srcw, $srch, floor(100 * (127 - $alpha) / 127));
//            }
//        }
    }

    /**
     * @param $position
     * @param $color
     */
    public function fill($position, $color)
    {
        $position = new Point($position);
        $x = $position->getX($this->width - 1);
        $y = $position->getY($this->height - 1);
        \imagefill($this->img, $x, $y, $this->gdColor($color));
    }

    /**
     * @param $position
     * @param $color
     */
    public function setPixel($position, $color)
    {
        $position = new Point($position);
        $x = $position->getX($this->width - 1);
        $y = $position->getY($this->height - 1);
        $this->typeChecker()->checkInteger($x, 0, $this->width - 1);
        $this->typeChecker()->checkInteger($y, 0, $this->height - 1);
        \imagesetpixel($this->img, $x, $y, $this->gdColor($color));
    }

    /**
     * @param $color
     * @return int
     */
    protected function gdColor($color)
    {
        if (!($color instanceof Color)) {
            $color = new Color($color);
        }
        $key = (string)$color;
        if (!isset($this->gdAllocatedColors[$key])) {
            $red = $color->getRed();
            $green = $color->getGreen();
            $blue = $color->getBlue();
            $alpha = $color->getTransparency();
            $this->gdAllocatedColors[$key] = \imagecolorallocatealpha($this->img, $red, $green, $blue, $alpha);
        }
        return $this->gdAllocatedColors[$key];
    }

    /**
     *
     */
    protected function gdDestroy()
    {
        if ($this->img) {
            \imagedestroy($this->img);
        }
        $this->img = null;
    }

    /**
     * @param $filename
     * @return string
     * @throws UnknownFileTypeException
     */
    public static function fileTypeFromFilename($filename)
    {
        $ext = \strtolower(\substr($filename, -4));
        $extensions = [
            '.png' => self::FILETYPE_PNG,
            '.jpg' => self::FILETYPE_JPG,
            '.gif' => self::FILETYPE_GIF,
        ];
        if (!isset($extensions[$ext])) {
            throw new UnknownFileTypeException($ext);
        }
        return $extensions[$ext];
    }

    /**
     * @param $filename
     * @return Image
     * @throws FileNotFoundException
     */
    public static function createFromFile($filename)
    {
        if (!\is_file($filename)) {
            throw new FileNotFoundException();
        }
        $fileType = self::fileTypeFromFilename($filename);
        if (self::FILETYPE_JPG === $fileType) {
            $tmp = \imagecreatefromjpeg($filename);
        }
        if (self::FILETYPE_GIF === $fileType) {
            $tmp = \imagecreatefromgif($filename);
        }
        if (self::FILETYPE_PNG === $fileType) {
            $tmp = \imagecreatefrompng($filename);
        }
        $image = new static(\imagesx($tmp), \imagesy($tmp));
        $image->copy($tmp);
        return $image;
    }

}