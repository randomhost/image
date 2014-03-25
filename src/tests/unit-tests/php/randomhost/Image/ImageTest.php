<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ImageTest unit test definition
 *
 * PHP version 5
 *
 * @category  Image
 * @package   PHP_Image
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2014 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      https://pear.random-host.com/
 */
namespace randomhost\Image;

/**
 * Unit test for Image
 *
 * @category  Image
 * @package   PHP_Image
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2014 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   Release: @package_version@
 * @link      https://pear.random-host.com/
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Path to test data directory
     *
     * @var string
     */
    const TEST_DATA_DIR = '../../testdata';

    /**
     * Data provider for test image and associated mimetype values.
     *
     * @return array
     */
    public function providerImageData()
    {
        return array(
            // test image, mimetype, width, height
            array('test.gif', 'image/gif', 128, 128),
            array('test.jpg', 'image/jpeg', 128, 128),
            array('test.png', 'image/png', 128, 128),
        );
    }

    /**
     * Data provider for image merging.
     *
     * @return array
     */
    public function providerMerge()
    {
        return array(
            // first image name, second image name, merge strategy
            array('test.jpg', 'test.png', Image::MERGE_SCALE_DST),
            array('test.jpg', 'test.png', Image::MERGE_SCALE_DST_NO_UPSCALE),
            array('test.jpg', 'test.png', Image::MERGE_SCALE_SRC),
        );
    }

    /**
     * Data provider for image merging with alpha value.
     *
     * @return array
     */
    public function providerMergeAlpha()
    {
        return array(
            // first image name, second image name, alpha value
            array('test.jpg', 'test.png', 127),
            array('test.jpg', 'test.png', 0),
            array('test.jpg', 'test.png', 64),
        );
    }
    
    /**
     * Tests Image::getInstanceByPath() with a GIF image.
     *
     * @return void
     */
    public function testGetInstanceByPathGif()
    {
        $imagePath = $this->getTestDataPath('test.gif');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertInternalType('resource', $image->image);
    }

    /**
     * Tests Image::getInstanceByPath() with a JPEG image.
     *
     * @return void
     */
    public function testGetInstanceByPathJpeg()
    {
        $imagePath = $this->getTestDataPath('test.jpg');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertInternalType('resource', $image->image);
    }

    /**
     * Tests Image::getInstanceByPath() with a PNG image.
     *
     * @return void
     */
    public function testGetInstanceByPathPng()
    {
        $imagePath = $this->getTestDataPath('test.png');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertInternalType('resource', $image->image);
    }

    /**
     * Tests Image::getInstanceByPath() with an unsupported image format.
     *
     * @return void
     */
    public function testGetInstanceByPathUnsupportedFormat()
    {
        $imagePath = $this->getTestDataPath('test.tif');

        $this->setExpectedException(
            '\UnexpectedValueException',
            'Image type image/tiff not supported'
        );

        Image::getInstanceByPath($imagePath);
    }

    /**
     * Tests Image::getInstanceByPath() with an empty image file.
     *
     * @return void
     */
    public function testGetInstanceByPathEmptyImage()
    {
        $imagePath = $this->getTestDataPath('empty.gif');

        $this->setExpectedException(
            '\RuntimeException',
            'Couldn\'t read image at'
        );

        Image::getInstanceByPath($imagePath);
    }

    /**
     * Tests Image::getInstanceByPath() with a broken image file.
     *
     * @return void
     */
    public function testGetInstanceByPathBrokenImage()
    {
        $imagePath = $this->getTestDataPath('broken.gif');

        $this->setExpectedException(
            '\RuntimeException',
            'Couldn\'t read image at'
        );

        Image::getInstanceByPath($imagePath);
    }

    /**
     * Tests Image::getInstanceByPath() with a GIF image.
     *
     * @return void
     */
    public function testGetInstanceByPathCache()
    {
        $imageName = 'test.png';

        // create temporary image copy so we can do whatever we want with it
        $originalPath = $this->getTestDataPath($imageName);
        $dummyPath = dirname($originalPath) . DIRECTORY_SEPARATOR .
            'tmp_' . $imageName;
        copy($originalPath, $dummyPath);
        $this->assertTrue(
            is_file($dummyPath),
            'Test dummy file not found'
        );
        $this->assertTrue(
            is_readable($dummyPath),
            'Test dummy file not readable'
        );

        // prepare cache directory
        $cacheDir = sys_get_temp_dir();
        $cachePath = $cacheDir . DIRECTORY_SEPARATOR . 'tmp_' . $imageName;
        if (is_file($cachePath) && is_writable($cachePath)) {
            unlink($cachePath);
        }
        $this->assertFalse(
            is_file($cachePath),
            'Cached test dummy file was not deleted'
        );

        // build image from test dummy
        $image = Image::getInstanceByPath($dummyPath, $cacheDir);
        $this->assertInstanceOf('randomhost\\Image\\Image', $image);
        $this->assertInternalType('resource', $image->image);
        unset($image);

        // ensure image is cached
        $this->assertTrue(
            is_file($cachePath),
            'Cached test dummy file not found'
        );
        $this->assertTrue(
            is_readable($cachePath),
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
        $this->assertInstanceOf('randomhost\\Image\\Image', $image);
        $this->assertInternalType('resource', $image->image);
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
     * Tests Image::getInstanceByCreate().
     *
     * @return void
     */
    public function testGetInstanceByCreate()
    {
    }

    /**
     * Tests Image::setCacheDir().
     *
     * @return void
     */
    public function testSetCacheDir()
    {
        $cacheDir = sys_get_temp_dir();

        $imagePath = $this->getTestDataPath('test.png');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertSame($image, $image->setCacheDir($cacheDir));
    }

    /**
     * Tests Image::setCacheDir().
     *
     * @return void
     */
    public function testSetCacheDirInvalidPath()
    {
        $cacheDir = 'doesNotExists';

        $imagePath = $this->getTestDataPath('test.png');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->setExpectedException(
            '\InvalidArgumentException',
            'Cache directory at ' . $cacheDir . ' could not be found'
        );

        $this->assertSame($image, $image->setCacheDir($cacheDir));
    }

    /**
     * Tests Image::merge().
     *
     * @param string $firstImageName  First image name.
     * @param string $secondImageName Second image name.
     * @param int    $strategy        Merge strategy.
     * 
     * @dataProvider providerMerge
     * 
     * @return void
     */
    public function testMerge($firstImageName, $secondImageName, $strategy)
    {
        $firstImagePath = $this->getTestDataPath($firstImageName);
        $secondImagePath = $this->getTestDataPath($secondImageName);

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $firstImage);
        $this->assertInstanceOf('randomhost\\Image\\Image', $secondImage);

        $this->assertInternalType('resource', $firstImage->image);
        $this->assertInternalType('resource', $secondImage->image);

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
     * Tests Image::merge() with an unset first image resource.
     *
     * @return void
     */
    public function testMergeInvalidFirstResource()
    {
        $firstImagePath = $this->getTestDataPath('test.jpg');
        $secondImagePath = $this->getTestDataPath('test.png');

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $firstImage);
        $this->assertInstanceOf('randomhost\\Image\\Image', $secondImage);

        $this->assertInternalType('resource', $firstImage->image);
        $this->assertInternalType('resource', $secondImage->image);

        $firstImage->image = null;
        $this->assertNull($firstImage->image);

        $this->setExpectedException(
            '\RuntimeException',
            'Attempt to merge image data using an invalid image resource.'
        );

        $firstImage->merge($secondImage, 0, 0);
    }

    /**
     * Tests Image::merge() with an unset second image resource.
     *
     * @return void
     */
    public function testMergeInvalidSecondResource()
    {
        $firstImagePath = $this->getTestDataPath('test.jpg');
        $secondImagePath = $this->getTestDataPath('test.png');

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $firstImage);
        $this->assertInstanceOf('randomhost\\Image\\Image', $secondImage);

        $this->assertInternalType('resource', $firstImage->image);
        $this->assertInternalType('resource', $secondImage->image);

        $secondImage->image = null;
        $this->assertNull($secondImage->image);

        $this->setExpectedException(
            '\RuntimeException',
            'Attempt to merge image data using an invalid image resource.'
        );

        $firstImage->merge($secondImage, 0, 0);
    }
    
    /**
     * Tests Image::mergeAlpha().
     * 
     * @param string $firstImageName  First image name.
     * @param string $secondImageName Second image name.
     * @param int    $alpha           Alpha value.
     *
     * @dataProvider providerMergeAlpha
     * @return void
     */
    public function testMergeAlpha($firstImageName, $secondImageName, $alpha)
    {
        $firstImagePath = $this->getTestDataPath($firstImageName);
        $secondImagePath = $this->getTestDataPath($secondImageName);

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $firstImage);
        $this->assertInstanceOf('randomhost\\Image\\Image', $secondImage);

        $this->assertInternalType('resource', $firstImage->image);
        $this->assertInternalType('resource', $secondImage->image);

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
     * Tests Image::mergeAlpha() with an unset first image resource.
     *
     * @return void
     */
    public function testMergeAlphaInvalidFirstResource()
    {
        $firstImagePath = $this->getTestDataPath('test.jpg');
        $secondImagePath = $this->getTestDataPath('test.png');

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $firstImage);
        $this->assertInstanceOf('randomhost\\Image\\Image', $secondImage);

        $this->assertInternalType('resource', $firstImage->image);
        $this->assertInternalType('resource', $secondImage->image);

        $firstImage->image = null;
        $this->assertNull($firstImage->image);

        $this->setExpectedException(
            '\RuntimeException',
            'Attempt to merge image data using an invalid image resource.'
        );

        $firstImage->mergeAlpha($secondImage, 0, 0);
    }

    /**
     * Tests Image::mergeAlpha() with an unset second image resource.
     *
     * @return void
     */
    public function testMergeAlphaInvalidSecondResource()
    {
        $firstImagePath = $this->getTestDataPath('test.jpg');
        $secondImagePath = $this->getTestDataPath('test.png');

        $firstImage = Image::getInstanceByPath($firstImagePath);
        $secondImage = Image::getInstanceByPath($secondImagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $firstImage);
        $this->assertInstanceOf('randomhost\\Image\\Image', $secondImage);

        $this->assertInternalType('resource', $firstImage->image);
        $this->assertInternalType('resource', $secondImage->image);

        $secondImage->image = null;
        $this->assertNull($secondImage->image);

        $this->setExpectedException(
            '\RuntimeException',
            'Attempt to merge image data using an invalid image resource.'
        );

        $firstImage->mergeAlpha($secondImage, 0, 0);
    }
    
    /**
     * Tests Image::render().
     *
     * @runInSeparateProcess
     *
     * @return void
     */
    public function testRender()
    {
        $imagePath = $this->getTestDataPath('test.png');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertInternalType('resource', $image->image);

        $this->assertSame($image, $image->render());
    }

    /**
     * Tests Image::render() with an unset image resource.
     *
     * @runInSeparateProcess
     *
     * @return void
     */
    public function testRenderInvalidResource()
    {
        $imagePath = $this->getTestDataPath('test.png');

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertInternalType('resource', $image->image);

        $image->image = null;

        $this->assertNull($image->image);

        $this->setExpectedException(
            '\RuntimeException',
            'Attempt to render invalid resource as image.'
        );

        $image->render();
    }

    /**
     * Tests Image::getMimetype().
     *
     * @param string $imageName Test image name.
     * @param string $mimeType  Expected mime type.
     *
     * @dataProvider providerImageData
     *
     * @return void
     */
    public function testGetMimetype($imageName, $mimeType)
    {
        $imagePath = $this->getTestDataPath($imageName);

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertInternalType('resource', $image->image);

        $this->assertSame($mimeType, $image->getMimetype());
    }

    /**
     * Tests Image::getModified().
     *
     * @param string $imageName Test image name.
     *
     * @dataProvider providerImageData
     *
     * @return void
     */
    public function testGetModified($imageName)
    {
        $imagePath = $this->getTestDataPath($imageName);

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertInternalType('resource', $image->image);

        $this->assertSame(filemtime($imagePath), $image->getModified());
    }

    /**
     * Tests Image::getWidth().
     *
     * @param string $imageName Test image name.
     * @param string $mimeType  Expected mime type.
     * @param int    $width     Expected image width.
     * @param int    $height    Expected image height.
     *
     * @dataProvider providerImageData
     *
     * @return void
     */
    public function testGetWidth($imageName, $mimeType, $width, $height)
    {
        /*
         * This is done to shut up IDEs which complain about the parameters
         * not being used so we can re-use the data provider.
         */
        unset($mimeType, $height);
        
        $imagePath = $this->getTestDataPath($imageName);

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertInternalType('resource', $image->image);

        $this->assertSame($width, $image->getWidth());
    }

    /**
     * Tests Image::getHeight().
     *
     * @param string $imageName Test image name.
     * @param string $mimeType  Expected mime type.
     * @param int    $width     Expected image width.
     * @param int    $height    Expected image height.
     *
     * @dataProvider providerImageData
     *
     * @return void
     */
    public function testGetHeight($imageName, $mimeType, $width, $height)
    {
        /*
         * This is done to shut up IDEs which complain about the parameters
         * not being used so we can re-use the data provider.
         */
        unset($mimeType, $width);
        
        $imagePath = $this->getTestDataPath($imageName);

        $image = Image::getInstanceByPath($imagePath);

        $this->assertInstanceOf('randomhost\\Image\\Image', $image);

        $this->assertInternalType('resource', $image->image);

        $this->assertSame($height, $image->getHeight());
    }

    /**
     * Returns the path to the given test data file.
     *
     * @param string $fileName Test data file name.
     *
     * @return string
     * @throws \Exception Thrown in case the test data file could not be read.
     */
    protected function getTestDataPath($fileName)
    {
        $dir = __DIR__ . '/' . self::TEST_DATA_DIR;
        if (!is_dir($dir) || !is_readable($dir)) {
            throw new \Exception(
                sprintf(
                    'Test data directory %s not found',
                    $dir
                )
            );
        }

        $path = realpath($dir) . '/' . $fileName;
        if (!is_file($path) || !is_readable($path)) {
            throw new \Exception(
                sprintf(
                    'Test feed %s not found',
                    $path
                )
            );
        }

        return realpath($path);
    }
}
 
