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
     * @return void
     */
    public function testMerge()
    {
        $this->markTestIncomplete('Not implemented yet');
    }

    /**
     * Tests Image::mergeAlpha().
     *
     * @return void
     */
    public function testMergeAlpha()
    {
        $this->markTestIncomplete('Not implemented yet');
    }

    /**
     * Tests Image::render().
     *
     * @return void
     */
    public function testRender()
    {
        $this->markTestIncomplete('Not implemented yet');
    }

    /**
     * Tests Image::getMimetype().
     *
     * @return void
     */
    public function testGetMimetype()
    {
        $this->markTestIncomplete('Not implemented yet');
    }

    /**
     * Tests Image::getModified().
     *
     * @return void
     */
    public function testGetModified()
    {
        $this->markTestIncomplete('Not implemented yet');
    }

    /**
     * Tests Image::getWidth().
     *
     * @return void
     */
    public function testGetWidth()
    {
        $this->markTestIncomplete('Not implemented yet');
    }

    /**
     * Tests Image::getHeight().
     *
     * @return void
     */
    public function testGetHeight()
    {
        $this->markTestIncomplete('Not implemented yet');
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
 
