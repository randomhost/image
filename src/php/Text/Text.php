<?php

declare(strict_types=1);

namespace randomhost\Image\Text;

use randomhost\Image\Color;
use randomhost\Image\Image;

/**
 * Interface for rendering text messages onto Image objects.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2024 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
interface Text
{
    /**
     * Sets the Image object instance.
     *
     * @param Image $image Image object instance.
     */
    public function setImage(Image $image): Text;

    /**
     * Returns the Image object instance.
     */
    public function getImage(): ?Image;

    /**
     * Sets the text color used for rendering text overlays onto the image.
     *
     * @param Color $color Color object instance.
     */
    public function setTextColor(Color $color): Text;

    /**
     * Returns the text color used for rendering text overlays onto the image.
     */
    public function getTextColor(): ?Color;

    /**
     * Sets the path to the font file used for rendering text overlays onto the
     * image.
     *
     * @param string $path File system path to TTF font file to be used.
     *
     * @throws \InvalidArgumentException Thrown if the font file could not be loaded.
     */
    public function setTextFont(string $path): Text;

    /**
     * Returns the path to the font file used for rendering text overlays onto
     * the image.
     */
    public function getTextFont(): string;

    /**
     * Sets the text size used for rendering text overlays onto the image.
     *
     * @param float $size Font size.
     */
    public function setTextSize(float $size): Text;

    /**
     * Returns the text size used for rendering text overlays onto the image.
     */
    public function getTextSize(): float;

    /**
     * Renders the given text onto the image resource using the given coordinates.
     *
     * @param int    $xPosition The x-ordinate.
     * @param int    $yPosition The y-ordinate position of the font's baseline.
     * @param string $text      The text string in UTF-8 encoding.
     *
     * @throws \RuntimeException Thrown if $this->image is not a valid image
     *                           resource or the font file isn't set.
     */
    public function insertText(int $xPosition, int $yPosition, string $text): Text;
}
