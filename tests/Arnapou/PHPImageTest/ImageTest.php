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

use Arnapou\PHPImage\Exception\DestroyedImageException;
use Arnapou\PHPImage\Exception\FileNotFoundException;
use Arnapou\PHPImage\Exception\InvalidFileTypeException;
use Arnapou\PHPImage\Exception\UnknownFileTypeException;
use Arnapou\PHPImage\Image;

/**
 * @coversDefaultClass Arnapou\PHPImage\Image
 */
class ImageTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::getWidth
     * @covers ::getHeight
     */
    public function testConstructor()
    {
        $image = new Image(10, 20);
        $this->assertInstanceOf(Image::class, $image);

        $image = new Image(10, 20, 'blue 50%');
        $this->assertInstanceOf(Image::class, $image);

        $this->assertEquals(10, $image->getWidth());
        $this->assertEquals(20, $image->getHeight());

    }

    /**
     * @covers ::__construct
     * @covers ::createFromFile
     * @covers ::fileTypeFromFilename
     */
    public function testStaticValid()
    {
        foreach (['php.gif', 'mask.png', 'tulipes.jpg'] as $file) {
            $filename = $this->getPathImages() . '/assets/' . $file;
            $image = Image::createFromFile($filename);
            $this->assertInstanceOf(Image::class, $image);
        }
    }

    /**
     * @covers ::__construct
     * @covers ::createFromFile
     * @covers ::fileTypeFromFilename
     */
    public function testStaticFileNotFound()
    {
        $this->expectException(FileNotFoundException::class);
        $filename = $this->getPathImages() . '/assets/file_does_not_exist.jpg';
        $image = Image::createFromFile($filename);
    }

    /**
     * @covers ::__construct
     * @covers ::createFromFile
     * @covers ::fileTypeFromFilename
     */
    public function testStaticUnknownExtension()
    {
        $this->expectException(UnknownFileTypeException::class);
        $filename = $this->getPathImages() . '/assets/arial.ttf';
        $image = Image::createFromFile($filename);
    }

    /**
     * @covers ::getBackgroundColor
     * @covers ::setBackgroundColor
     */
    public function testBackgroundColor()
    {
        $color = [100, 150, 200, 50];
        $image = new Image(10, 10, $color);
        $this->assertEquals($color, $image->getBackgroundColor()->toArray());
    }

    /**
     * @covers ::getFileType
     * @covers ::setFileType
     */
    public function testFileType()
    {
        $image = new Image(10, 10);
        $this->assertEquals(Image::FILETYPE_PNG, $image->getFileType());
    }

    /**
     * @covers ::setFileType
     */
    public function testFileTypeInvalid()
    {
        $this->expectException(InvalidFileTypeException::class);
        $image = new Image(10, 10);
        $image->setFileType('xxx');
    }

    /**
     * @covers ::getClone
     */
    public function testClone()
    {
        $filename = $this->getPathImages() . '/assets/php.gif';
        $image = Image::createFromFile($filename);
        $clone = $image->getClone();
        $this->assertTrue($this->gd()->areImagesIdentical($image->getResource(), $clone->getResource()), 'clone test');
    }

    /**
     * @covers ::clear
     * @covers ::getPixel
     * @covers ::setPixel
     * @covers ::gdColor
     * @covers ::clear
     * @covers ::fill
     * @covers ::checkNotDestroyed
     */
    public function testPixel()
    {
        $image = new Image(20, 20);
        $image->setPixel([10, 10], 'azure1 50%');

        $color = $image->getPixel([10, 10]);
        $this->assertEquals([240, 255, 255, 64], $color->toArray());

        $image->clear();

        $color = $image->getPixel([10, 10]);
        $this->assertEquals([255, 255, 255, 0], $color->toArray());
    }

    /**
     * @covers ::__destruct
     * @covers ::destroy
     * @covers ::isDestroyed
     * @covers ::getResource
     */
    public function testDestructor()
    {
        $image = new Image(10, 20);
        $image->__destruct();

        $this->assertEquals(null, $image->getResource());
        $this->assertEquals(true, $image->isDestroyed());
    }

    /**
     * @covers ::checkNotDestroyed
     * @covers ::destroy
     */
    public function testCheckDestroyed()
    {
        $this->expectException(DestroyedImageException::class);

        $image = new Image(10, 20);
        $image->destroy();
        $image->getPixel([0, 0]);
    }

    /**
     * @covers ::copy
     */
    public function testCopy()
    {

        $tests = [
            'copy1' => function () {
                $image = new Image(10, 10, 'red 30%');
                $copied = new Image(5, 5, 'blue 50%');

                $image->copy($copied, [1, 1], [0, 0], [3, 3]);

                $copied->__destruct();
                return $image;
            },
            'copy2' => function () {
                $image = new Image(10, 10, 'red 30%');
                $copied = new Image(5, 5, 'blue 50%');

                $image->copy($copied, 'right center', [0, 0], [4, 4], 'center right');

                $copied->__destruct();
                return $image;
            },
            'copy3' => function () {
                $image = new Image(10, 10, 'red 30%');
                $copied = new Image(5, 5, 'blue 50%');

                $image->copy($copied, 'center center', [0, 0], [4, 4], 'top right', true);

                $copied->__destruct();
                return $image;
            },
            'copy4' => function () {
                $image = new Image(10, 10, 'red');
                $copied = new Image(5, 5, 'blue 80%');

                $image->copy($copied, null, null, [4, 4], null, false, '50%');

                $copied->__destruct();
                return $image;
            },
        ];

        foreach ($tests as $name => $factory) {
            $image = $factory();
            $this->assertImageIdentical($image, $name);
        }
    }

    /**
     * @covers ::fill
     */
    public function testFill()
    {
        $image = new Image(10, 10, 'red');

        for ($y = 0; $y < 10; $y++) {
            $image->setPixel([4, $y], 'blue');
        }

        $image->fill([2, 5], 'green');
        $this->assertImageIdentical($image, 'fill1');
    }

}