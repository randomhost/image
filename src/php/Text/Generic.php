<?php

declare(strict_types=1);

namespace randomhost\Image\Text;

use randomhost\Image\Color;
use randomhost\Image\Image;

/**
 * This class represents a generic image overlay text.
 *
 * It supports rendering of text messages onto Image objects.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2022 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
class Generic implements Text
{
    /**
     * Image object instance.
     *
     * @var Image
     */
    protected $image;

    /**
     * Text size for rendering text overlays onto the image.
     *
     * @var float
     */
    protected $textSize = 7.0;

    /**
     * Text angle for rendering text overlays onto the image.
     *
     * @var float
     */
    protected $textAngle = 0.0;

    /**
     * Text color identifier.
     *
     * @var null|Color
     */
    protected $textColor;

    /**
     * Path to the font file used for rendering text overlays onto the image.
     *
     * @var string
     */
    protected $textFontPath = '';

    /**
     * Constructor for this class.
     *
     * @param Image $image Optional: randomhost\Image instance.
     */
    public function __construct(Image $image = null)
    {
        $this->image = $image;
    }

    /**
     * Sets the Image object instance.
     *
     * @param Image $image Image object instance.
     */
    public function setImage(Image $image): Text
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Returns the Image object instance.
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * Sets the text color used for rendering text overlays onto the image.
     *
     * @param Color $color Color object instance.
     */
    public function setTextColor(Color $color): Text
    {
        $this->textColor = $color;

        return $this;
    }

    /**
     * Returns the text color used for rendering text overlays onto the image.
     */
    public function getTextColor(): ?Color
    {
        return $this->textColor;
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
        if (!is_file($path) || !is_readable($path)) {
            throw new \InvalidArgumentException(
                'Unable to load font file at '.$path
            );
        }

        $this->textFontPath = realpath($path);

        return $this;
    }

    /**
     * Returns the path to the font file used for rendering text overlays onto
     * the image.
     */
    public function getTextFont(): string
    {
        return $this->textFontPath;
    }

    /**
     * Sets the text size used for rendering text overlays onto the image.
     *
     * @param float $size Font size.
     */
    public function setTextSize(float $size): Text
    {
        $this->textSize = (float) $size;

        return $this;
    }

    /**
     * Returns the text size used for rendering text overlays onto the image.
     */
    public function getTextSize(): float
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
     * @throws \RuntimeException Thrown if $this->image is not a valid image
     *                           resource or the font file isn't set.
     */
    public function insertText(int $xPosition, int $yPosition, string $text): Text
    {
        if (!$this->getImage() instanceof Image
            || !is_resource($this->getImage()->image)
        ) {
            throw new \RuntimeException(
                'Attempt to render text onto invalid image resource'
            );
        }

        if (!$this->textColor instanceof Color) {
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
