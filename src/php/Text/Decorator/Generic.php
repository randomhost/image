<?php

declare(strict_types=1);

namespace randomhost\Image\Text\Decorator;

use randomhost\Image\Color;
use randomhost\Image\Image;
use randomhost\Image\Text\Text;

/**
 * Decorates a generic image overlay text with additional functionality.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2022 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
abstract class Generic implements Text
{
    /**
     * Text object instance.
     *
     * @var Text
     */
    protected $text;

    /**
     * Constructor for this class.
     *
     * @param Text $text Text instance.
     */
    public function __construct(Text $text)
    {
        $this->text = $text;
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
    public function __call(string $method, array $args)
    {
        $callable = [$this->text, $method];

        if (!is_callable($callable, false, $callableName)) {
            throw new \BadMethodCallException('Failed to call '.$callableName.'()');
        }

        return call_user_func_array($callable, $args);
    }

    /**
     * Sets the Image object instance.
     *
     * @param Image $image Image object instance.
     */
    public function setImage(Image $image): Text
    {
        return $this->text->setImage($image);
    }

    /**
     * Returns the Image object instance.
     */
    public function getImage(): ?Image
    {
        return $this->text->getImage();
    }

    /**
     * Sets the text color used for rendering text overlays onto the image.
     *
     * @param Color $color Color object instance.
     */
    public function setTextColor(Color $color): Text
    {
        return $this->text->setTextColor($color);
    }

    /**
     * Returns the text color used for rendering text overlays onto the image.
     */
    public function getTextColor(): ?Color
    {
        return $this->text->getTextColor();
    }

    /**
     * Sets the path to the font file used for rendering text overlays onto the
     * image.
     *
     * @param string $path File system path to TTF font file to be used.
     *
     * @throws \InvalidArgumentException Thrown if the font file could not be loaded.
     */
    public function setTextFont(string $path): Text
    {
        return $this->text->setTextFont($path);
    }

    /**
     * Returns the path to the font file used for rendering text overlays onto
     * the image.
     */
    public function getTextFont(): string
    {
        return $this->text->getTextFont();
    }

    /**
     * Sets the text size used for rendering text overlays onto the image.
     *
     * @param float $size Font size.
     */
    public function setTextSize(float $size): Text
    {
        return $this->text->setTextSize($size);
    }

    /**
     * Returns the text size used for rendering text overlays onto the image.
     */
    public function getTextSize(): float
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
     * @throws \RuntimeException Thrown if $this->image is not a valid image
     *                           resource or the font file isn't set.
     */
    public function insertText(int $xPosition, int $yPosition, string $text): Text
    {
        return $this->text->insertText($xPosition, $yPosition, $text);
    }

    /**
     * Returns if the given method is implemented by one of the decorators.
     *
     * @param string $name Method name.
     */
    public function providesMethod(string $name): bool
    {
        if (method_exists($this, $name)) {
            return true;
        }
        if ($this->text instanceof Generic) {
            return $this->text->providesMethod($name);
        }

        return false;
    }
}
