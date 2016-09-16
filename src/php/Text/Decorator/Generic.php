<?php
namespace randomhost\Image\Text\Decorator;

use randomhost\Image;

/**
 * Decorates a generic image overlay text with additional functionality
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://php-image.random-host.com
 */
abstract class Generic implements Image\Text\Text
{
    /**
     * Text object instance
     *
     * @var Image\Text\Text
     */
    protected $text = null;

    /**
     * Constructor for this class.
     *
     * @param Image\Text\Text $text randomhost\Image\Text\Text instance.
     */
    public function __construct(Image\Text\Text $text)
    {
        $this->text = $text;
    }

    /**
     * Sets the Image object instance.
     *
     * @param Image\Image $image Image object instance.
     *
     * @return $this
     */
    public function setImage(Image\Image $image)
    {
        return $this->text->setImage($image);
    }

    /**
     * Returns the Image object instance.
     *
     * @return Image\Image
     */
    public function getImage()
    {
        return $this->text->getImage();
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
        return $this->text->setTextColor($color);
    }

    /**
     * Returns the text color used for rendering text overlays onto the image.
     *
     * @return Image\Color
     */
    public function getTextColor()
    {
        return $this->text->getTextColor();
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
        return $this->text->setTextFont($path);
    }

    /**
     * Returns the path to the font file used for rendering text overlays onto
     * the image.
     *
     * @return string
     */
    public function getTextFont()
    {
        return $this->text->getTextFont();
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
        return $this->text->setTextSize($size);
    }

    /**
     * Returns the text size used for rendering text overlays onto the image.
     *
     * @return float
     */
    public function getTextSize()
    {
        return $this->text->getTextSize();
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
        return $this->text->insertText($xPosition, $yPosition, $text);
    }

    /**
     * Returns if the given method is implemented by one of the decorators.
     *
     * @param string $name Method name.
     *
     * @return bool
     */
    public function providesMethod($name)
    {
        if (method_exists($this, $name)) {
            return true;
        }
        if ($this->text instanceof Generic) {
            return $this->text->providesMethod($name);
        }
        return false;
    }

    /**
     * Passes calls to unknown methods to the decorated object as they might be
     * implemented by a preceding decorator.
     *
     * @param string $method Called method name.
     * @param array  $args   Method arguments.
     *
     * @return mixed
     */
    public function __call($method, array $args)
    {
        return call_user_func_array(
            array($this->text, $method),
            $args
        );
    }

    /**
     * Returns an Image\Image instance.
     *
     * @param int $width  Image width.
     * @param int $height Image height.
     *
     * @return Image\Image
     */
    protected function getTempImage($width, $height)
    {
        return Image\Image::getInstanceByCreate(
            $width,
            $height
        );
    }
}
