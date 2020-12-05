<?php

declare(strict_types=1);

namespace randomhost\Image;

/**
 * Represents a color used in images.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2022 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
class Color
{
    /**
     * Red component.
     *
     * @var int
     */
    protected $red = 0;

    /**
     * Green component.
     *
     * @var int
     */
    protected $green = 0;

    /**
     * Blue component.
     *
     * @var int
     */
    protected $blue = 0;

    /**
     * Alpha value.
     *
     * 0 indicates completely opaque while 127 indicates completely transparent.
     *
     * @var int
     */
    protected $alpha = 0;

    /**
     * Constructor.
     *
     * @param int $red   Optional: Red component (0-255 or 0x00-0xFF).
     * @param int $green Optional: Green component (0-255 or 0x00-0xFF).
     * @param int $blue  Optional: Blue component (0-255 or 0x00-0xFF).
     * @param int $alpha Optional: Alpha value (0-127)
     *
     * @throws \InvalidArgumentException Thrown if a color value or the alpha
     *                                   value is invalid.
     */
    public function __construct(int $red = 0, int $green = 0, int $blue = 0, int $alpha = 0)
    {
        $this->setRed($red);
        $this->setGreen($green);
        $this->setBlue($blue);
        $this->setAlpha($alpha);
    }

    /**
     * Sets the red component.
     *
     * @param int $red Red component (0-255 or 0x00-0xFF).
     *
     * @throws \InvalidArgumentException Thrown if the color value is invalid.
     */
    public function setRed(int $red): Color
    {
        self::validateColor($red);

        $this->red = $red;

        return $this;
    }

    /**
     * Returns the red component.
     */
    public function getRed(): int
    {
        return $this->red;
    }

    /**
     * Sets the green component.
     *
     * @param int $green Green component (0-255 or 0x00-0xFF).
     *
     * @throws \InvalidArgumentException Thrown if the color value is invalid.
     */
    public function setGreen(int $green): Color
    {
        self::validateColor($green);

        $this->green = $green;

        return $this;
    }

    /**
     * Returns the green component.
     */
    public function getGreen(): int
    {
        return $this->green;
    }

    /**
     * Sets the blue component.
     *
     * @param int $blue Blue component (0-255 or 0x00-0xFF).
     *
     * @throws \InvalidArgumentException Thrown if the color value is invalid.
     */
    public function setBlue(int $blue): Color
    {
        self::validateColor($blue);

        $this->blue = $blue;

        return $this;
    }

    /**
     * Returns the blue component.
     */
    public function getBlue(): int
    {
        return $this->blue;
    }

    /**
     * Sets the alpha value.
     *
     * @param int $alpha Alpha value (0-127).
     *
     * @throws \InvalidArgumentException Thrown if the alpha value is invalid.
     */
    public function setAlpha(int $alpha): Color
    {
        self::validateAlpha($alpha);

        $this->alpha = $alpha;

        return $this;
    }

    /**
     * Returns the alpha value.
     */
    public function getAlpha(): int
    {
        return $this->alpha;
    }

    /**
     * Validates the color value.
     *
     * @param mixed $color Color value.
     *
     * @throws \InvalidArgumentException Thrown if the color value is invalid.
     */
    public static function validateColor(int $color): bool
    {
        if (0 <= $color && 255 >= $color) {
            return true;
        }

        throw new \InvalidArgumentException(
            'Color is expected to be an integer value between 0 and 255 or '.
            'a hexadecimal value between 0x00 and 0xFF'
        );
    }

    /**
     * Validates the alpha value.
     *
     * @param mixed $alpha Alpha value.
     *
     * @throws \InvalidArgumentException Thrown if the alpha value is invalid.
     */
    public static function validateAlpha(int $alpha): bool
    {
        if (0 <= $alpha && 127 >= $alpha) {
            return true;
        }

        throw new \InvalidArgumentException(
            'Alpha is expected to be an integer value between 0 and 127'
        );
    }
}
