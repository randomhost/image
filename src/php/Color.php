<?php
namespace randomhost\Image;

/**
 * Represents a color used in images.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://github.random-host.com/image/
 */
class Color
{
    /**
     * Red component
     *
     * @var int
     */
    protected $red = 0;

    /**
     * Green component
     *
     * @var int
     */
    protected $green = 0;

    /**
     * Blue component
     *
     * @var int
     */
    protected $blue = 0;

    /**
     * Alpha value
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
    public function __construct($red = 0, $green = 0, $blue = 0, $alpha = 0)
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
     * @return $this
     * @throws \InvalidArgumentException Thrown if the color value is invalid.
     */
    public function setRed($red)
    {
        self::validateColor($red);

        $this->red = $red;

        return $this;
    }

    /**
     * Returns the red component.
     *
     * @return int
     */
    public function getRed()
    {
        return $this->red;
    }

    /**
     * Sets the green component.
     *
     * @param int $green Green component (0-255 or 0x00-0xFF).
     *
     * @return $this
     * @throws \InvalidArgumentException Thrown if the color value is invalid.
     */
    public function setGreen($green)
    {
        self::validateColor($green);

        $this->green = $green;

        return $this;
    }

    /**
     * Returns the green component.
     *
     * @return int
     */
    public function getGreen()
    {
        return $this->green;
    }

    /**
     * Sets the blue component.
     *
     * @param int $blue Blue component (0-255 or 0x00-0xFF).
     *
     * @return $this
     * @throws \InvalidArgumentException Thrown if the color value is invalid.
     */
    public function setBlue($blue)
    {
        self::validateColor($blue);

        $this->blue = $blue;

        return $this;
    }

    /**
     * Returns the blue component.
     *
     * @return int
     */
    public function getBlue()
    {
        return $this->blue;
    }

    /**
     * Sets the alpha value.
     *
     * @param int $alpha Alpha value (0-127).
     *
     * @return $this
     * @throws \InvalidArgumentException Thrown if the alpha value is invalid.
     */
    public function setAlpha($alpha)
    {
        self::validateAlpha($alpha);

        $this->alpha = $alpha;

        return $this;
    }

    /**
     * Returns the alpha value.
     *
     * @return int
     */
    public function getAlpha()
    {
        return $this->alpha;
    }


    /**
     * Validates the color value.
     *
     * @param mixed $color Color value.
     *
     * @return bool
     * @throws \InvalidArgumentException Thrown if the color value is invalid.
     */
    public static function validateColor($color)
    {
        if (is_int($color) && 0 <= $color && 255 >= $color) {
            return true;
        }

        throw new \InvalidArgumentException(
            'Color is expected to be an integer value between 0 and 255 or ' .
            'a hexadecimal value between 0x00 and 0xFF'
        );
    }

    /**
     * Validates the alpha value.
     *
     * @param mixed $alpha Alpha value.
     *
     * @return bool
     * @throws \InvalidArgumentException Thrown if the alpha value is invalid.
     */
    public static function validateAlpha($alpha)
    {
        if (is_int($alpha) && 0 <= $alpha && 127 >= $alpha) {
            return true;
        }

        throw new \InvalidArgumentException(
            'Alpha is expected to be an integer value between 0 and 127'
        );
    }
}
