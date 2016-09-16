<?php
namespace randomhost\Image\Text;

use randomhost\Image;

/**
 * Interface for rendering text messages onto Image objects.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://php-image.random-host.com
 */
interface Text
{
    /**
     * Sets the Image object instance.
     *
     * @param Image\Image $image Image object instance.
     *
     * @return $this
     */
    public function setImage(Image\Image $image);

    /**
     * Returns the Image object instance.
     *
     * @return Image\Image
     */
    public function getImage();

    /**
     * Sets the text color used for rendering text overlays onto the image.
     *
     * @param Image\Color $color Color object instance.
     *
     * @return $this
     */
    public function setTextColor(Image\Color $color);

    /**
     * Returns the text color used for rendering text overlays onto the image.
     *
     * @return Image\Color
     */
    public function getTextColor();

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
    public function setTextFont($path);

    /**
     * Returns the path to the font file used for rendering text overlays onto
     * the image.
     *
     * @return string
     */
    public function getTextFont();

    /**
     * Sets the text size used for rendering text overlays onto the image.
     *
     * @param float $size Font size.
     *
     * @return $this
     */
    public function setTextSize($size);

    /**
     * Returns the text size used for rendering text overlays onto the image.
     *
     * @return float
     */
    public function getTextSize();

    /**
     * Renders the given text onto the image resource using the given coordinates.
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
    public function insertText($xPosition, $yPosition, $text);
}
