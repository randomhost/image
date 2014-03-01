<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ImageText class definition
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
 * This class represents an image overlay text.
 *
 * It supports rendering of text messages onto Image objects.
 *
 * @category  Image
 * @package   PHP_Image
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2014 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   Release: @package_version@
 * @link      https://pear.random-host.com/
 */
class ImageText
{
    /**
     * Image object instance
     *
     * @var Image
     */
    protected $image = null;

    /**
     * Text size for rendering text overlays onto the image
     *
     * @var float
     */
    protected $textSize = 7.0;

    /**
     * Text angle for rendering text overlays onto the image
     *
     * @var float
     */
    protected $textAngle = 0.0;

    /**
     * Text color identifier.
     *
     * @var int
     */
    protected $textColor = 0;

    /**
     * Path to the font file used for rendering text overlays onto the image
     *
     * @var string
     */
    protected $textFontPath = '';

    /**
     * Constructor for this class.
     *
     * @param Image $image randomhost\Image\Image instance.
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
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
        if (!is_resource($this->image->image)) {
            throw new \RuntimeException(
                'Attempt to allocate color for invalid image resource.'
            );
        }

        if (!isset($rgb[0]) || !isset($rgb[1]) || !isset($rgb[2])) {
            throw new \InvalidArgumentException('Invalid text color array format.');
        }

        $this->textColor = ImageColorAllocate(
            $this->image->image,
            (int)$rgb[0],
            (int)$rgb[1],
            (int)$rgb[2]
        );

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
        if (!is_file($path) || !is_readable($path)) {
            throw new \InvalidArgumentException(
                'Unable to load font file at ' . $path
            );
        }

        $this->textFontPath = realpath($path);

        return $this;
    }

    /**
     * Sets the text size used for rendering text overlays onto the image.
     *
     * @param float $size Font size.
     *
     * @return $this;
     */
    public function setTextSize($size)
    {
        $this->textSize = (float)$size;

        return $this;
    }

    /**
     * Renders the given text onto the image resource, using the given coordinates.
     *
     * @param int    $xPosition The x-ordinate.
     * @param int    $yPosition The y-ordinate position of the fonts baseline.
     * @param string $text      The text string in UTF-8 encoding.
     *
     * @return $this;
     *
     * @throws \RuntimeException Thrown if $this->image is not a valid image
     * resource or the font file isn't set.
     */
    public function insertText($xPosition, $yPosition, $text)
    {
        if (!is_resource($this->image->image)) {
            throw new \RuntimeException(
                'Attempt to render text onto invalid image resource.'
            );
        }

        if (empty($this->textFontPath)) {
            throw new \RuntimeException(
                'No font file selected for rendering text overlay.'
            );
        }

        if (!is_file($this->textFontPath) || !is_readable($this->textFontPath)) {
            throw new \RuntimeException(
                sprintf(
                    'Failed to read font file: \'%1$s\'',
                    $this->textFontPath
                )
            );
        }

        imagettftext(
            $this->image->image,
            $this->textSize,
            $this->textAngle,
            $xPosition,
            $yPosition,
            $this->textColor,
            $this->textFontPath,
            $text
        );

        return $this;
    }
}
