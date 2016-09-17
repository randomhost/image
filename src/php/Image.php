<?php
namespace randomhost\Image;

/**
 * Represents an image in a (remote) filesystem or created in memory.
 *
 * It supports merging other images into the image by passing in other Image objects.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://php-image.random-host.com
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
     * Mime type of the image
     *
     * @var string
     */
    protected $mimeType = '';

    /**
     * Last modified timestamp of the image
     *
     * @var int
     */
    protected $modified = 0;

    /**
     * Constructor for this class.
     */
    private function __construct()
    {
        $this->time = time();
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
     * Copies the given Image image stream into the image stream of the active
     * instance using a scaling strategy.
     *
     * @param Image $srcImage The source image.
     * @param int   $dstX     x-coordinate of destination point.
     * @param int   $dstY     y-coordinate of destination point.
     * @param int   $strategy Scaling strategy. Default: self::MERGE_SCALE_SRC
     *
     * @return $this
     *
     * @throws \RuntimeException Thrown if $this->image or $srcImage->image is
     * not a valid image resource.
     */
    public function merge(
        Image $srcImage,
        $dstX,
        $dstY,
        $strategy = self::MERGE_SCALE_SRC
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
     * Copies the given Image image stream into the image stream of the active
     * instance while applying the given alpha transparency.
     *
     * This method does not support scaling.
     *
     * @param Image $srcImage The source image.
     * @param int   $dstX     x-coordinate of destination point.
     * @param int   $dstY     y-coordinate of destination point.
     * @param int   $alpha    Alpha value (0-127)
     *
     * @return $this
     *
     * @throws \RuntimeException Thrown if $this->image or $srcImage->image is
     * not a valid image resource.
     */
    public function mergeAlpha(
        Image $srcImage,
        $dstX,
        $dstY,
        $alpha = 127
    ) {
        if (!is_resource($this->image) || !is_resource($srcImage->image)) {
            throw new \RuntimeException(
                'Attempt to merge image data using an invalid image resource.'
            );
        }

        $percent = 100 - min(max(round(($alpha / 127 * 100)), 1), 100);

        // copy images around
        @imagecopymerge(
            $this->image,
            $srcImage->image,
            (int)$dstX,
            (int)$dstY,
            0,
            0,
            $srcImage->width,
            $srcImage->height,
            $percent
        );

        return $this;
    }

    /**
     * Outputs the image stream to the browser.
     *
     * @return $this
     *
     * @throws \RuntimeException Thrown if $this->image is not a valid image
     *                           resource.
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
     * Returns the mime type of the image.
     *
     * @return string
     */
    public function getMimeType()
    {
        return (string)$this->mimeType;
    }

    /**
     * Returns the last modified timestamp of the image.
     *
     * @return int
     */
    public function getModified()
    {
        return (int)$this->modified;
    }

    /**
     * Returns the width of the image in pixels.
     *
     * @return int
     */
    public function getWidth()
    {
        return (int)$this->width;
    }

    /**
     * Returns the height of the image in pixels.
     *
     * @return int
     */
    public function getHeight()
    {
        return (int)$this->height;
    }

    /**
     * Sets the path to the cache directory.
     *
     * @param string $path Path to cache directory
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function setCacheDir($path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cache directory at %s could not be found',
                    $path
                )
            );
        }
        if (!is_readable($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cache directory at %s is not readable',
                    $path
                )
            );
        }
        if (!is_writable($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cache directory at %s is not writable',
                    $path
                )
            );
        }

        $this->cacheDir = realpath($path);
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
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        $remoteFile = fopen(
            $this->pathFile,
            'rb',
            null,
            stream_context_create($arrContextOptions)
        );
        if (!$remoteFile) {
            throw new \RuntimeException(
                sprintf(
                    "Couldn't open file at %s",
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
                    "Couldn't open cache file at %s",
                    $this->pathCache
                )
            );
        }

        // write file to cache
        while (!feof($remoteFile)) {
            fwrite(
                $cacheFile,
                fread($remoteFile, $chunkSize),
                $chunkSize
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
                    "Couldn't read image at %s",
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
                        image_type_to_mime_type($read[2])
                    )
                );
        }

        if (false === $this->image) {
            throw new \RuntimeException(
                sprintf(
                    "Couldn't read image at %s",
                    $path
                )
            );
        }

        // set image dimensions
        $this->width = $read[0];
        $this->height = $read[1];

        // set mime type
        $this->mimeType = $read['mime'];

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

        // set mime type
        $this->mimeType = 'image/png';

        // set modified date
        $this->modified = time();
    }
}
