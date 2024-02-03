<?php

declare(strict_types=1);

namespace randomhost\Image\Tests;

use PHPUnit\Framework\TestCase;
use randomhost\Image\Image;

/**
 * Unit test for Image.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2024 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 *
 * @internal
 *
 * @covers \randomhost\Image\Image
 */
class ImageTest extends TestCase
{
    /**
     * Data provider for test image and associated mime type values.
     */
    public function providerImageData(): \Generator
    {
        // test image, mime type, width, height
        yield ['test.gif', 'image/gif', 128, 128];

        yield ['test.jpg', 'image/jpeg', 128, 128];

        yield ['test.png', 'image/png', 128, 128];
    }

    /**
     * Data provider for image merging.
     */
    public function providerMerge(): \Generator
    {
        // first image name, second image name, merge strategy
        yield ['test.jpg', 'test.png', Image::MERGE_SCALE_DST];

        yield ['test.jpg', 'test.png', Image::MERGE_SCALE_DST_NO_UPSCALE];

        yield ['test.jpg', 'test.png', Image::MERGE_SCALE_SRC];

        yield ['test.png', 'test_small.png', Image::MERGE_SCALE_DST];

        yield ['test.png', 'test_small.png', Image::MERGE_SCALE_DST_NO_UPSCALE];

        yield ['test.png', 'test_small.png', Image::MERGE_SCALE_SRC];

        yield ['test_small.png', 'test.png', Image::MERGE_SCALE_DST];

        yield ['test_small.png', 'test.png', Image::MERGE_SCALE_DST_NO_UPSCALE];

        yield ['test_small.png', 'test.png', Image::MERGE_SCALE_SRC];
    }

    /**
     * Data provider for image merging with alpha value.
     */
    public function providerMergeAlpha(): \Generator
    {
        // first image name, second image name, alpha value
        yield ['test.jpg', 'test.png', 127];

        yield ['test.jpg', 'test.png', 0];

        yield ['test.jpg', 'test.png', 64];
    }

    /**
     * Tests {@see Image::getInstanceByPath()} with a GIF image.
     *
     * @throws \Exception
     */
    public function testGetInstanceByPathGif()
    {
        $imagePath = TestData::getPath('test.gif');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf(Image::class, $image);

        $this->assertInstanceOf(\GdImage::class, $image->image);
    }

    /**
     * Tests {@see Image::getInstanceByPath()} with a JPEG image.
     *
     * @throws \Exception
     */
    public function testGetInstanceByPathJpeg()
    {
        $imagePath = TestData::getPath('test.jpg');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf(Image::class, $image);

        $this->assertInstanceOf(\GdImage::class, $image->image);
    }

    /**
     * Tests {@see Image::getInstanceByPath()} with a PNG image.
     *
     * @throws \Exception
     */
    public function testGetInstanceByPathPng()
    {
        $imagePath = TestData::getPath('test.png');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf(Image::class, $image);

        $this->assertInstanceOf(\GdImage::class, $image->image);
    }

    /**
     * Tests {@see Image::getInstanceByPath()} with an unsupported image format.
     *
     * @throws \Exception
     */
    public function testGetInstanceByPathUnsupportedFormat()
    {
        $imagePath = TestData::getPath('test.tif');

        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage(
            'Image type image/tiff not supported'
        );

        Image::getInstanceByPath($imagePath);
    }

    /**
     * Tests {@see Image::getInstanceByPath()} with an empty image file.
     *
     * @throws \Exception
     */
    public function testGetInstanceByPathEmptyImage()
    {
        $imagePath = TestData::getPath('empty.gif');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            "Couldn't read image at"
        );

        Image::getInstanceByPath($imagePath);
    }

    /**
     * Tests {@see Image::getInstanceByPath()} with a broken image file.
     *
     * @throws \Exception
     */
    public function testGetInstanceByPathBrokenImage()
    {
        $imagePath = TestData::getPath('broken.gif');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            "Couldn't read image at"
        );

        Image::getInstanceByPath($imagePath);
    }

    /**
     * Tests {@see Image::getInstanceByPath()} with a GIF image.
     *
     * @throws \Exception
     */
    public function testGetInstanceByPathCache()
    {
        $imageName = 'test.png';

        // create temporary image copy, so we can do whatever we want with it
        $originalPath = TestData::getPath($imageName);
        $dummyPath = dirname($originalPath).DIRECTORY_SEPARATOR.
            'tmp_'.$imageName;
        copy($originalPath, $dummyPath);
        $this->assertTrue(
            is_file($dummyPath),
            'Test dummy file not found'
        );
        $this->assertIsReadable(
            $dummyPath,
            'Test dummy file not readable'
        );

        // prepare cache directory
        $cacheDir = sys_get_temp_dir();
        $cachePath = $cacheDir.DIRECTORY_SEPARATOR.'tmp_'.$imageName;
        if (is_file($cachePath) && is_writable($cachePath)) {
            unlink($cachePath);
        }
        $this->assertFalse(
            is_file($cachePath),
            'Cached test dummy file was not deleted'
        );

        // build image from test dummy
        $image = Image::getInstanceByPath($dummyPath, $cacheDir);
        $this->assertInstanceOf(Image::class, $image);
        $this->assertInstanceOf(\GdImage::class, $image->image);
        unset($image);

        // ensure image is cached
        $this->assertTrue(
            is_file($cachePath),
            'Cached test dummy file not found'
        );
        $this->assertIsReadable(
            $cachePath,
            'Cached test dummy file not readable'
        );

        // delete test dummy
        unlink($dummyPath);
        $this->assertFalse(
            is_file($dummyPath),
            'Test dummy file was not deleted'
        );

        // build image from cache
        $image = Image::getInstanceByPath($dummyPath, $cacheDir);
        $this->assertInstanceOf(Image::class, $image);
        $this->assertInstanceOf(\GdImage::class, $image->image);
        unset($image);

        // delete cache file
        if (is_file($cachePath) && is_writable($cachePath)) {
            unlink($cachePath);
        }
        $this->assertFalse(
            is_file($cachePath),
            'Cached test dummy file was not deleted'
        );
    }

    /**
     * Tests {@see Image::getInstanceByPath()} with an invalid cache path.
     *
     * @throws \Exception
     */
    public function testGetInstanceByPathInvalidCachePath()
    {
        $cacheDir = 'doesNotExists';

        $imagePath = TestData::getPath('test.png');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Cache directory at '.$cacheDir.' could not be found'
        );

        Image::getInstanceByPath($imagePath, $cacheDir);
    }

    /**
     * Tests {@see Image::getInstanceByCreate()}.
     */
    public function testGetInstanceByCreate()
    {
        $image = Image::getInstanceByCreate(10, 10);

        $this->assertInstanceOf(Image::class, $image);

        $this->assertInstanceOf(\GdImage::class, $image->image);
    }

    /**
     * Tests {@see Image::merge()}.
     *
     * @param string $firstImageName  First image name.
     * @param string $secondImageName Second image name.
     * @param int    $strategy        Merge strategy.
     *
     * @dataProvider providerMerge
     *
     * @throws \Exception
     */
    public function testMerge(string $firstImageName, string $secondImageName, int $strategy)
    {
        $firstImagePath = TestData::getPath($firstImageName);
        $secondImagePath = TestData::getPath($secondImageName);

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf(Image::class, $firstImage);
        $this->assertInstanceOf(Image::class, $secondImage);

        $this->assertInstanceOf(\GdImage::class, $firstImage->image);
        $this->assertInstanceOf(\GdImage::class, $secondImage->image);

        $this->assertSame(
            $firstImage,
            $firstImage->merge(
                $secondImage,
                0,
                0,
                $strategy
            )
        );
    }

    /**
     * Tests {@see Image::merge()} with an unset first image resource.
     *
     * @throws \Exception
     */
    public function testMergeInvalidFirstResource()
    {
        $firstImagePath = TestData::getPath('test.jpg');
        $secondImagePath = TestData::getPath('test.png');

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf(Image::class, $firstImage);
        $this->assertInstanceOf(Image::class, $secondImage);

        $this->assertInstanceOf(\GdImage::class, $firstImage->image);
        $this->assertInstanceOf(\GdImage::class, $secondImage->image);

        $firstImage->image = null;
        $this->assertNull($firstImage->image);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Attempt to merge image data using an invalid image resource.'
        );

        $firstImage->merge($secondImage, 0, 0);
    }

    /**
     * Tests {@see Image::merge()} with an unset second image resource.
     *
     * @throws \Exception
     */
    public function testMergeInvalidSecondResource()
    {
        $firstImagePath = TestData::getPath('test.jpg');
        $secondImagePath = TestData::getPath('test.png');

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf(Image::class, $firstImage);
        $this->assertInstanceOf(Image::class, $secondImage);

        $this->assertInstanceOf(\GdImage::class, $firstImage->image);
        $this->assertInstanceOf(\GdImage::class, $secondImage->image);

        $secondImage->image = null;
        $this->assertNull($secondImage->image);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Attempt to merge image data using an invalid image resource.'
        );

        $firstImage->merge($secondImage, 0, 0);
    }

    /**
     * Tests {@see Image::mergeAlpha()}.
     *
     * @param string $firstImageName  First image name.
     * @param string $secondImageName Second image name.
     * @param int    $alpha           Alpha value.
     *
     * @dataProvider providerMergeAlpha
     *
     * @throws \Exception
     */
    public function testMergeAlpha(string $firstImageName, string $secondImageName, int $alpha)
    {
        $firstImagePath = TestData::getPath($firstImageName);
        $secondImagePath = TestData::getPath($secondImageName);

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf(Image::class, $firstImage);
        $this->assertInstanceOf(Image::class, $secondImage);

        $this->assertInstanceOf(\GdImage::class, $firstImage->image);
        $this->assertInstanceOf(\GdImage::class, $secondImage->image);

        $this->assertSame(
            $firstImage,
            $firstImage->mergeAlpha(
                $secondImage,
                0,
                0,
                $alpha
            )
        );
    }

    /**
     * Tests {@see Image::mergeAlpha()} with an unset first image resource.
     *
     * @throws \Exception
     */
    public function testMergeAlphaInvalidFirstResource()
    {
        $firstImagePath = TestData::getPath('test.jpg');
        $secondImagePath = TestData::getPath('test.png');

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf(Image::class, $firstImage);
        $this->assertInstanceOf(Image::class, $secondImage);

        $this->assertInstanceOf(\GdImage::class, $firstImage->image);
        $this->assertInstanceOf(\GdImage::class, $secondImage->image);

        $firstImage->image = null;
        $this->assertNull($firstImage->image);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Attempt to merge image data using an invalid image resource.'
        );

        $firstImage->mergeAlpha($secondImage, 0, 0);
    }

    /**
     * Tests {@see Image::mergeAlpha()} with an unset second image resource.
     *
     * @throws \Exception
     */
    public function testMergeAlphaInvalidSecondResource()
    {
        $firstImagePath = TestData::getPath('test.jpg');
        $secondImagePath = TestData::getPath('test.png');

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf(Image::class, $firstImage);
        $this->assertInstanceOf(Image::class, $secondImage);

        $this->assertInstanceOf(\GdImage::class, $firstImage->image);
        $this->assertInstanceOf(\GdImage::class, $secondImage->image);

        $secondImage->image = null;
        $this->assertNull($secondImage->image);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Attempt to merge image data using an invalid image resource.'
        );

        $firstImage->mergeAlpha($secondImage, 0, 0);
    }

    /**
     * Tests {@see Image::render()}.
     *
     * @runInSeparateProcess
     *
     * @throws \Exception
     */
    public function testRender()
    {
        $imagePath = TestData::getPath('test.png');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf(Image::class, $image);

        $this->assertInstanceOf(\GdImage::class, $image->image);

        ob_start();

        $result = $image->render();
        ob_get_contents();
        ob_end_clean();

        $this->assertSame($image, $result);
    }

    /**
     * Tests {@see Image::render()} with an unset image resource.
     *
     * @runInSeparateProcess
     *
     * @throws \Exception
     */
    public function testRenderInvalidResource()
    {
        $imagePath = TestData::getPath('test.png');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf(Image::class, $image);

        $this->assertInstanceOf(\GdImage::class, $image->image);

        $image->image = null;

        $this->assertNull($image->image);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Attempt to render invalid resource as image.'
        );

        $image->render();
    }

    /**
     * Tests {@see Image::getMimetype()}.
     *
     * @param string $imageName Test image name.
     * @param string $mimeType  Expected mime type.
     *
     * @dataProvider providerImageData
     *
     * @throws \Exception
     */
    public function testGetMimeType(string $imageName, string $mimeType)
    {
        $imagePath = TestData::getPath($imageName);

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf(Image::class, $image);

        $this->assertInstanceOf(\GdImage::class, $image->image);

        $this->assertSame($mimeType, $image->getMimeType());
    }

    /**
     * Tests {@see Image::getModified()}.
     *
     * @param string $imageName Test image name.
     *
     * @dataProvider providerImageData
     *
     * @throws \Exception
     */
    public function testGetModified(string $imageName)
    {
        $imagePath = TestData::getPath($imageName);

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf(Image::class, $image);

        $this->assertInstanceOf(\GdImage::class, $image->image);

        $this->assertSame(filemtime($imagePath), $image->getModified());
    }

    /**
     * Tests {@see Image::getWidth()}.
     *
     * @param string $imageName Test image name.
     * @param string $mimeType  Expected mime type.
     * @param int    $width     Expected image width.
     * @param int    $height    Expected image height.
     *
     * @dataProvider providerImageData
     *
     * @throws \Exception
     */
    public function testGetWidth(string $imageName, string $mimeType, int $width, int $height)
    {
        /*
         * This is done to shut up IDEs which complain about the parameters
         * not being used, so we can re-use the data provider.
         */
        unset($mimeType, $height);

        $imagePath = TestData::getPath($imageName);

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf(Image::class, $image);

        $this->assertInstanceOf(\GdImage::class, $image->image);

        $this->assertSame($width, $image->getWidth());
    }

    /**
     * Tests {@see Image::getHeight()}.
     *
     * @param string $imageName Test image name.
     * @param string $mimeType  Expected mime type.
     * @param int    $width     Expected image width.
     * @param int    $height    Expected image height.
     *
     * @dataProvider providerImageData
     *
     * @throws \Exception
     */
    public function testGetHeight(string $imageName, string $mimeType, int $width, int $height)
    {
        /*
         * This is done to shut up IDEs which complain about the parameters
         * not being used, so we can re-use the data provider.
         */
        unset($mimeType, $width);

        $imagePath = TestData::getPath($imageName);

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf(Image::class, $image);

        $this->assertInstanceOf(\GdImage::class, $image->image);

        $this->assertSame($height, $image->getHeight());
    }
}
