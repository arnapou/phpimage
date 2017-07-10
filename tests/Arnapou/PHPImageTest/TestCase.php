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

use Arnapou\PHPImage\Helper\HelperTrait;
use Arnapou\PHPImage\Image;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use HelperTrait;
    /**
     * @var string
     */
    private $testedImagePathPrefix = null;

    /**
     * @return string
     */
    public function getPathImages()
    {
        return __DIR__ . '/../../images';
    }

    /**
     * @param string $name
     * @return string
     */
    public function getPathTestedImage($name)
    {
        if (null === $this->testedImagePathPrefix) {
            $this->testedImagePathPrefix = '';
            $currentClass = \get_class($this);
            if (\strpos($currentClass, __NAMESPACE__) === 0) {
                $prefix = \substr($currentClass, \strlen(__NAMESPACE__) + 1);
                $prefix = \str_replace("\\", ".", $prefix);
                $this->testedImagePathPrefix = $prefix . '.';
            }
        }

        $path = $this->getPathImages() . '/tested';
        return $path . '/' . $this->testedImagePathPrefix . $name . '.png';
    }

    /**
     * @param resource $image
     * @param string   $name
     * @return bool
     */
    public function assertImageIdentical($image, $name)
    {
        if ($image instanceof Image) {
            $image = $image->getResource();
        }

        $image2 = null;
        $filename = $this->getPathTestedImage($name);
        if (!\is_file($filename)) {
            \imagepng($image, $filename); // generate the file at the first run supposing it is valid
            $identical = true;
            \imagedestroy($image);
        } else {
            $image2 = \imagecreatefrompng($filename);
            $identical = $this->gd()->areImagesIdentical($image, $image2);
            \imagedestroy($image);
            \imagedestroy($image2);
        }

        // asserts
        if ($identical) {
            $this->assertThat(IsImageIdentical::IDENTICAL, new IsImageIdentical(), $name);
        } else {
            $this->assertThat(IsImageIdentical::DIFFERENT, new IsImageIdentical(), $name);
        }
    }

}