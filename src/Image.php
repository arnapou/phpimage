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
use Arnapou\PHPImage\Exception\DestroyedImageException;
use Arnapou\PHPImage\Exception\FileNotFoundException;
use Arnapou\PHPImage\Exception\InvalidFileTypeException;
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
    protected $fileType;

    /**
     * Image constructor.
     * @param int    $width
     * @param int    $height
     * @param null   $backgroundColor
     * @param string $fileType
     */
    public function __construct($width, $height, $backgroundColor = null, $fileType = self::FILETYPE_PNG)
    {
        $this->type()->checkInteger($width, 1);
        $this->type()->checkInteger($height, 1);
        $this->width = $width;
        $this->height = $height;
        $this->setBackgroundColor($backgroundColor ?: new Color([255, 255, 255, 0]));
        $this->setFileType($fileType);
        $this->clear();
    }

    
    public function __destruct()
    {
        $this->destroy();
    }

    /**
     * @return Image
     */
    public function getClone()
    {
        $clone = new Image($this->getWidth(), $this->getHeight(), $this->getBackgroundColor());
        $clone->setFileType($this->getFileType());
        $clone->copy($this);
        return $clone;
    }

    /**
     * @return Color
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * @param Color $color
     */
    public function setBackgroundColor($color)
    {
        $this->backgroundColor = new Color($color);
    }

    /**
     * @param string $fileType
     * @throws InvalidFileTypeException
     */
    public function setFileType($fileType)
    {
        if (!\in_array($fileType, [self::FILETYPE_GIF, self::FILETYPE_JPG, self::FILETYPE_PNG])) {
            throw new InvalidFileTypeException("Type $fileType not valid.");
        }
        $this->fileType = $fileType;
    }


    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
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
     * Destroy the image to free memory.
     * Note that any drawing method after that will generate a php error : that's normal !
     */
    public function destroy()
    {
        if ($this->img && \is_resource($this->img)) {
            imagedestroy($this->img);
        }
        $this->img = null;
    }

    /**
     * @return bool
     */
    public function isDestroyed()
    {
        return $this->img === null;
    }

    /**
     * @throws DestroyedImageException
     */
    protected function checkNotDestroyed()
    {
        if ($this->isDestroyed()) {
            throw new DestroyedImageException('The image was already destroyed.');
        }
    }

    
    public function clear()
    {
        $this->destroy();
        $this->gdAllocatedColors = [];
        $this->img = $this->gd()->createImage($this->width, $this->height, $this->backgroundColor->toArray());
    }

    /**
     * @param resource $source
     * @param null     $dstXY
     * @param null     $srcXY
     * @param null     $srcWH
     * @param null     $srcPos
     * @param bool     $realCopy
     * @param null     $forcedAlpha
     */
    public function copy(
        $source,
        $dstXY = null,
        $srcXY = null,
        $srcWH = null,
        $srcPos = null,
        $realCopy = false,
        $forcedAlpha = null
    ) {
        $this->checkNotDestroyed();
        $source = $this->type()->checkResource($source);
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        $this->type()->checkPoint($dstXY, $dstx, $dsty, $this->width - 1, $this->height - 1);
        $this->type()->checkPoint($srcXY, $srcx, $srcy, $sourceWidth, $sourceHeight);
        $this->type()->checkSize($srcWH, $srcw, $srch, $sourceWidth, $sourceHeight);
        $this->type()->checkPosition($srcPos, $dstx, $dsty, $srcw, $srch);

        if ($forcedAlpha !== null) {
            $this->type()->checkAlpha($forcedAlpha);
            $transparency = Color::MAX_ALPHA - $forcedAlpha;
        } else {
            $transparency = null;
        }

        if ($realCopy) {
            $this->gd()->realCopy($this->img, $source, $dstx, $dsty, $srcx, $srcy, $srcw, $srch, $transparency);
        } elseif ($transparency !== null) {
            imagecopymerge($this->img, $source, $dstx, $dsty, $srcx, $srcy, $srcw, $srch, round(100 * $transparency / 127));
        } else {
            imagecopy($this->img, $source, $dstx, $dsty, $srcx, $srcy, $srcw, $srch);
        }
    }

    /**
     * @param $point
     * @param $color
     */
    public function fill($point, $color)
    {
        $this->checkNotDestroyed();
        $this->type()->checkPoint($point, $x, $y, $this->width - 1, $this->height - 1);
        imagefill($this->img, $x, $y, $this->gdColor($color));
    }

    /**
     * @param $point
     * @param $color
     */
    public function setPixel($point, $color)
    {
        $this->checkNotDestroyed();
        $this->type()->checkPoint($point, $x, $y, $this->width - 1, $this->height - 1);
        imagesetpixel($this->img, $x, $y, $this->gdColor($color));
    }

    /**
     * Use this method with caution : it not a fast method to get a lots pixel colors.
     * Look at other ways to get what you want if you need performance.
     *
     * @param $point
     * @return Color $color
     */
    public function getPixel($point)
    {
        $this->checkNotDestroyed();
        $this->type()->checkPoint($point, $x, $y, $this->width - 1, $this->height - 1);
        $RGBA = $this->gd()->getPixel($this->img, $x, $y);
        return new Color($RGBA);
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
            $this->gdAllocatedColors[$key] = imagecolorallocatealpha($this->img, $red, $green, $blue, $alpha);
        }
        return $this->gdAllocatedColors[$key];
    }


    /**
     * @param $filename
     * @return string
     * @throws UnknownFileTypeException
     */
    public static function fileTypeFromFilename($filename)
    {
        $ext = strtolower(substr($filename, -4));
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
        if (!is_file($filename)) {
            throw new FileNotFoundException();
        }
        $fileType = self::fileTypeFromFilename($filename);
        if (self::FILETYPE_JPG === $fileType) {
            $tmp = imagecreatefromjpeg($filename);
        }
        if (self::FILETYPE_GIF === $fileType) {
            $tmp = imagecreatefromgif($filename);
        }
        if (self::FILETYPE_PNG === $fileType) {
            $tmp = imagecreatefrompng($filename);
        }
        $image = new static(imagesx($tmp), imagesy($tmp));
        $image->copy($tmp);
        imagedestroy($tmp);
        return $image;
    }
}
