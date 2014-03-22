<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Generic class definition
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
namespace randomhost\Image\Text;

use randomhost\Image;

/**
 * This class represents a generic image overlay text.
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
class Generic implements Text
{
    /**
     * Image object instance
     *
     * @var Image\Image
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
     * @var Image\Color|null
     */
    protected $textColor = null;

    /**
     * Path to the font file used for rendering text overlays onto the image
     *
     * @var string
     */
    protected $textFontPath = '';

    /**
     * Constructor for this class.
     *
     * @param Image\Image $image Optional: randomhost\Image\Image instance.
     */
    public function __construct(Image\Image $image = null)
    {
        $this->image = $image;
    }

    /**
     * Sets the Image object instance.
     *
     * @param Image\Image $image randomhost\Image\Image instance.
     *
     * @return $this
     */
    public function setImage(Image\Image $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Returns the Image object instance.
     *
     * @return Image\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the text color used for rendering text overlays onto the image.
     *
     * @param Image\Color $color Color object instance.
     *
     * @return $this
     */
    public function setTextColor(Image\Color $color)
    {
        $this->textColor = $color;

        return $this;
    }

    /**
     * Returns the text color used for rendering text overlays onto the image.
     *
     * @return Image\Color|null
     */
    public function getTextColor()
    {
        return $this->textColor;
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
     * Returns the path to the font file used for rendering text overlays onto
     * the image.
     *
     * @return string
     */
    public function getTextFont()
    {
        return $this->textFontPath;
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
     * Returns the text size used for rendering text overlays onto the image.
     *
     * @return float
     */
    public function getTextSize()
    {
        return $this->textSize;
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
     *                           resource or the font file isn't set.
     */
    public function insertText($xPosition, $yPosition, $text)
    {
        if (!$this->getImage() instanceof Image\Image
            || !is_resource($this->getImage()->image)
        ) {
            throw new \RuntimeException(
                'Attempt to render text onto invalid image resource'
            );
        }

        if (!$this->textColor instanceof Image\Color) {
            throw new \RuntimeException(
                'Attempt to render text without setting a color'
            );
        }

        if (empty($this->textFontPath)) {
            throw new \RuntimeException(
                'No font file selected for rendering text overlay'
            );
        }

        if (!is_file($this->textFontPath)
            || !is_readable(
                $this->textFontPath
            )
        ) {
            throw new \RuntimeException(
                sprintf(
                    'Failed to read font file \'%1$s\'',
                    $this->textFontPath
                )
            );
        }
        
        $color = imagecolorallocatealpha(
            $this->getImage()->image,
            $this->textColor->getRed(),
            $this->textColor->getGreen(),
            $this->textColor->getBlue(),
            $this->textColor->getAlpha()
        );
        
        imagettftext(
            $this->getImage()->image,
            $this->textSize,
            $this->textAngle,
            $xPosition,
            $yPosition,
            $color,
            $this->textFontPath,
            $text
        );

        return $this;
    }
}
