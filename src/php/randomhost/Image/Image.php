<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Image class definition
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
 * This class represents an image in a (remote) filesystem or created in memory.
 *
 * It supports rendering of text messages on top of the image and merging other
 * images into the image by passing in other Image objects.
 *
 * @category  Image
 * @package   PHP_Image
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2014 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   Release: @package_version@
 * @link      https://pear.random-host.com/
 */
class Image
{
    /**
     * Image cache time in minutes
     *
     * @var int
     */
    const CACHE_TIME = 60;

    /**
     * Merge images using the source image size
     *
     * @var int
     */
    const MERGE_SCALE_SRC = 1;

    /**
     * Merge images using the destination image size
     *
     * @var int
     */
    const MERGE_SCALE_DST = 2;

    /**
     * Merge images using the destination image size, do not upscale
     *
     * @var int
     */
    const MERGE_SCALE_DST_NO_UPSCALE = 3;

    /**
     * Image resource identifier
     *
     * @var resource
     */
    public $image = false;

    /**
     * Timestamp at call time
     *
     * @var int
     */
    protected $time;

    /**
     * File system path for image caching
     *
     * @var string
     */
    protected $cacheDir = '';

    /**
     * Local path or remote URL of the image file
     *
     * @var string
     */
    protected $pathFile = '';

    /**
     * Local cache path of the image file
     *
     * @var string
     */
    protected $pathCache = '';

    /**
     * Width of the image in pixels
     *
     * @var int
     */
    protected $width = 0;

    /**
     * Height of the image in pixels
     *
     * @var int
     */
    protected $height = 0;

    /**
     * Mimetype of the image
     *
     * @var string
     */
    protected $mimetype = '';

    /**
     * Last modified timestamp of the image
     *
     * @var int
     */
    protected $modified = 0;

    /**
     * ImageText object instance
     *
     * @var ImageText
     */
    protected $imageText = null;

    /**
     * Constructor for this class.
     */
    private function __construct()
    {
        $this->time = time();
        $this->imageText = new ImageText($this);
    }

    /**
     * Creates a new image from file or URL.
     *
     * @param string $path     Path to the image.
     * @param string $cacheDir Optional: Directory path for caching image files.
     *
     * @return $this
     */
    public static function getInstanceByPath($path, $cacheDir = '')
    {
        $instance = new self();

        $instance->pathFile = $path;

        if (!empty($cacheDir)) {
            $instance->setCacheDir($cacheDir);
            $instance->setCachePath();
        }

        $instance->readImage();

        return $instance;
    }

    /**
     * Creates a new image in memory.
     *
     * @param int $width  Image width.
     * @param int $height Image height.
     *
     * @return $this
     */
    public static function getInstanceByCreate($width, $height)
    {
        $instance = new self();

        // set image dimensions
        $instance->width = (int)$width;
        $instance->height = (int)$height;

        $instance->createImage();

        return $instance;
    }

    /**
     * Destructor for this class.
     */
    public function __destruct()
    {
        if (is_resource($this->image)) {
            // free up memory
            imagedestroy($this->image);
        }
    }

    /**
     * Sets the path to the cache directory.
     *
     * @param string $path Path to cache directory
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCacheDir($path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cache directory at "%s" could not be found',
                    $path
                )
            );
        }
        if (!is_readable($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cache directory at "%s" is not readable',
                    $path
                )
            );
        }
        if (!is_writable($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cache directory at "%s" is not writable',
                    $path
                )
            );
        }

        $this->cacheDir = realpath($path);

        return $this;
    }

    /**
     * Sets the text color used for rendering text overlays onto the image.
     *
     * @param array $rgb Text color in format array( red, green, blue ).
     *
     * @return $this
     *
     * @throws \RuntimeException Thrown if $this->image is not a valid image
     * resource.
     * @throws \InvalidArgumentException Thrown if $rgb has an invalid array format.
     */
    public function setTextColor(array $rgb)
    {
        $this->imageText->setTextColor($rgb);

        return $this;
    }

    /**
     * Sets the path to the font file used for rendering text overlays onto the
     * image.
     *
     * @param string $path File system path to TTF font file to be used.
     *
     * @return $this
     *
     * @throws \InvalidArgumentException Thrown if the font file could not be loaded.
     */
    public function setTextFont($path)
    {
        $this->imageText->setTextFont($path);

        return $this;
    }

    /**
     * Sets the text size used for rendering text overlays onto the image.
     *
     * @param float $size Font size.
     *
     * @return $this
     */
    public function setTextSize($size)
    {
        $this->imageText->setTextSize($size);

        return $this;
    }

    /**
     * Renders the given text onto the image resource, using the given coordinates.
     *
     * @param int    $xPosition The x-ordinate.
     * @param int    $yPosition The y-ordinate position of the fonts baseline.
     * @param string $text      The text string in UTF-8 encoding.
     *
     * @return $this
     *
     * @throws \RuntimeException Thrown if $this->image is not a valid image
     * resource or the font file isn't set
     */
    public function insertText($xPosition, $yPosition, $text)
    {
        $this->imageText->insertText($xPosition, $yPosition, $text);

        return $this;
    }

    /**
     * Copies the given Image image stream into the image stream of the active
     * instance.
     *
     * @param Image $srcImage The source image.
     * @param int   $dstX     x-coordinate of destination point.
     * @param int   $dstY     y-coordinate of destination point.
     * @param int   $strategy Scaling strategy. Default: self::MERGE_SCALE_SRC
     *
     * @return $this
     *
     * @throws \RuntimeException Trown if $this->image or $srcImage->image is
     * not a valid image resource.
     */
    public function merge(
        Image $srcImage, $dstX, $dstY, $strategy = self::MERGE_SCALE_SRC
    ) {
        if (!is_resource($this->image) || !is_resource($srcImage->image)) {
            throw new \RuntimeException(
                'Attempt to merge image data using an invalid image resource.'
            );
        }

        // determine re-sampling strategy
        switch ($strategy) {

        // merge using the destination image dimensions
        case self::MERGE_SCALE_DST:

            $dstWidth = $this->width;
            $dstHeight = $this->height;

            break;

        // merge using the destination image dimensions, do not upscale
        case self::MERGE_SCALE_DST_NO_UPSCALE:

            $dstWidth = $this->width;
            if ($dstWidth > $srcImage->width) {
                $dstWidth = $srcImage->width;
            }

            $dstHeight = $this->height;
            if ($dstHeight > $srcImage->height) {
                $dstHeight = $srcImage->height;
            }

            break;

        // merge using the source image dimensions
        case self::MERGE_SCALE_SRC:
        default:

            $dstWidth = $srcImage->width;
            $dstHeight = $srcImage->height;

            break;
        }

        // copy images around
        @imagecopyresampled(
            $this->image,
            $srcImage->image,
            (int)$dstX,
            (int)$dstY,
            0,
            0,
            (int)$dstWidth,
            (int)$dstHeight,
            $srcImage->width,
            $srcImage->height
        );

        return $this;
    }

    /**
     * Outputs the image stream to the browser.
     *
     * @return $this
     *
     * @throws \RuntimeException Trown if $this->image is not a valid image resource.
     */
    public function render()
    {
        if (!is_resource($this->image)) {
            throw new \RuntimeException(
                'Attempt to render invalid resource as image.'
            );
        }

        header('Content-type: image/png');
        imagepng($this->image, null, 9, PNG_ALL_FILTERS);

        return $this;
    }

    /**
     * Returns the Mimetype of the image.
     *
     * @return string
     */
    public function getMimetype()
    {
        return (string) $this->mimetype;
    }

    /**
     * Returns the last modified timestamp of the image.
     *
     * @return int
     */
    public function getModified()
    {
        return (int) $this->modified;
    }

    /**
     * Returns the width of the image in pixels.
     *
     * @return int
     */
    public function getWidth()
    {
        return (int) $this->width;
    }

    /**
     * Returns the height of the image in pixels.
     *
     * @return int
     */
    public function getHeight()
    {
        return (int) $this->height;
    }

    /**
     * Sets $this->pathCache based on the given $this->pathFile.
     *
     * @return void
     */
    protected function setCachePath()
    {
        $filename = basename($this->pathFile);

        $this->pathCache = $this->cacheDir . '/' . $filename;
    }

    /**
     * Checks if the image file exists in the cache.
     *
     * @return bool
     */
    protected function isCached()
    {
        $cacheTime = self::CACHE_TIME * 60;

        if (!is_file($this->pathCache)) {
            return false;
        }

        $cachedTime = filemtime($this->pathCache);
        $cacheAge = $this->time - $cachedTime;

        return $cacheAge < $cacheTime;
    }

    /**
     * Attempts to write the image file located at $this->pathFile to a local
     * file at $this->pathCache.
     *
     * @return void
     *
     * @throws \RuntimeException Thrown if the cache file could not be written.
     */
    protected function writeCache()
    {
        $chunkSize = 1024 * 8;

        // open remote file
        $remoteFile = @fopen($this->pathFile, 'rb');
        if (!$remoteFile) {
            throw new \RuntimeException(
                sprintf(
                    'Couldn\'t open file at %s',
                    $this->pathFile
                )
            );
        }

        // open cache file
        $cacheFile = fopen($this->pathCache, 'wb');
        if (!$cacheFile) {
            fclose($remoteFile);
            throw new \RuntimeException(
                sprintf(
                    'Couldn\'t open cache file at %s',
                    $this->pathCache
                )
            );
        }

        // write file to cache
        while (!feof($remoteFile)) {
            fwrite(
                $cacheFile, fread($remoteFile, $chunkSize), $chunkSize
            );
        }

        // close cache file
        fclose($cacheFile);

        // close remote file
        fclose($remoteFile);
    }

    /**
     * Reads the image file.
     *
     * @return void
     *
     * @throws \RuntimeException Thrown if the image could not be processed.
     * @throws \UnexpectedValueException Thrown if the image type is not supported.
     */
    protected function readImage()
    {
        // set default path
        $path = $this->pathFile;

        // handle caching
        if (!empty($this->pathCache)) {

            // write / refresh cache file if necessary
            if (!$this->isCached()) {
                $this->writeCache();
            }

            // replace path if file is cached
            if ($this->isCached()) {
                $path = $this->pathCache;
            }

        }

        // get image information
        $read = @getimagesize($path);
        if (false === $read) {
            throw new \RuntimeException(
                sprintf(
                    'Couldn\'t read image at %s',
                    $path
                )
            );
        }

        // detect image type
        switch ($read[2]) {

        case IMAGETYPE_GIF:
            $this->image = @imagecreatefromgif($path);
            break;

        case IMAGETYPE_JPEG:
            $this->image = @imagecreatefromjpeg($path);
            break;

        case IMAGETYPE_PNG:
            $this->image = @imagecreatefrompng($path);
            break;

        default:
            // image type not supported
            throw new \UnexpectedValueException(
                sprintf(
                    'Image type %s not supported',
                    $read[2]
                )
            );
        }

        if (false === $this->image) {
            throw new \RuntimeException(
                sprintf(
                    'Couldn\'t read image at %s',
                    $path
                )
            );
        }

        // set image dimensions
        $this->width = $read[0];
        $this->height = $read[1];

        // set mimetype
        $this->mimetype = $read['mime'];

        // set modified date
        $this->modified = @filemtime($path);
    }

    /**
     * Creates a new true color image.
     *
     * @return void
     */
    protected function createImage()
    {
        // create image
        $this->image = @imagecreatetruecolor($this->width, $this->height);

        // set mimetype
        $this->mimetype = 'image/png';

        // set modified date
        $this->modified = time();
    }
}
