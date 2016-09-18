<?php
namespace randomhost\Image;

/**
 * Unit test for Color
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://github.random-host.com/image/
 */
class ColorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for color values.
     *
     * @return array
     */
    public function providerColor()
    {
        return array(
            // color, exception
            array(128, false),
            array(0, false),
            array(255, false),
            array(0x80, false),
            array(0x00, false),
            array(0xFF, false),
            array(-1, true),
            array(256, true),
            array(0x100, true),
            array(128.0, true),
            array('128', true),
            array('notanumber', true),
        );
    }

    /**
     * Data provider for alpha values.
     *
     * @return array
     */
    public function providerAlpha()
    {
        return array(
            // alpha, exception
            array(64, false),
            array(0, false),
            array(127, false),
            array(-1, true),
            array(128, true),
            array(64.0, true),
            array('64', true),
            array('notanumber', true),
        );
    }

    /**
     * Tests Color::setRed() and Color::getRed().
     *
     * @param int  $red       Red component (0-255 or 0x00-0xFF).
     * @param bool $exception Exception expected
     *
     * @dataProvider providerColor
     *
     * @return void
     */
    public function testSetGetRed($red, $exception)
    {
        $color = new Color();

        if ($exception) {
            $this->setExpectedException(
                '\InvalidArgumentException',
                'Color is expected to be an integer value between 0 and 255 ' .
                'or a hexadecimal value between 0x00 and 0xFF'
            );
        }

        $this->assertSame($color, $color->setRed($red));

        $this->assertSame($red, $color->getRed());
    }

    /**
     * Tests Color::setGreen() and Color::getGreen().
     *
     * @param int  $green     Green component (0-255 or 0x00-0xFF).
     * @param bool $exception Exception expected
     *
     * @dataProvider providerColor
     *
     * @return void
     */
    public function testSetGetGreen($green, $exception)
    {
        $color = new Color();

        if ($exception) {
            $this->setExpectedException(
                '\InvalidArgumentException',
                'Color is expected to be an integer value between 0 and 255 ' .
                'or a hexadecimal value between 0x00 and 0xFF'
            );
        }

        $this->assertSame($color, $color->setGreen($green));

        $this->assertSame($green, $color->getGreen());
    }

    /**
     * Tests Color::setBlue() and Color::getBlue().
     *
     * @param int  $blue      Blue component (0-255 or 0x00-0xFF).
     * @param bool $exception Exception expected
     *
     * @dataProvider providerColor
     *
     * @return void
     */
    public function testSetGetBlue($blue, $exception)
    {
        $color = new Color();

        if ($exception) {
            $this->setExpectedException(
                '\InvalidArgumentException',
                'Color is expected to be an integer value between 0 and 255 ' .
                'or a hexadecimal value between 0x00 and 0xFF'
            );
        }

        $this->assertSame($color, $color->setBlue($blue));

        $this->assertSame($blue, $color->getBlue());
    }

    /**
     * Tests Color::setAlpha() and Color::getAlpha().
     *
     * @param int  $alpha     Alpha value (0-127).
     * @param bool $exception Exception expected
     *
     * @dataProvider providerAlpha
     *
     * @return void
     */
    public function testSetGetAlpha($alpha, $exception)
    {
        $color = new Color();

        if ($exception) {
            $this->setExpectedException(
                '\InvalidArgumentException',
                'Alpha is expected to be an integer value between 0 and 127'
            );
        }

        $this->assertSame($color, $color->setAlpha($alpha));

        $this->assertSame($alpha, $color->getAlpha());
    }

    /**
     * Tests Color::validateColor().
     *
     * @param int  $color     Color component (0-255 or 0x00-0xFF).
     * @param bool $exception Exception expected
     *
     * @dataProvider providerColor
     *
     * @return void
     */
    public function testValidateColor($color, $exception)
    {
        if ($exception) {
            $this->setExpectedException(
                '\InvalidArgumentException',
                'Color is expected to be an integer value between 0 and 255 ' .
                'or a hexadecimal value between 0x00 and 0xFF'
            );
        }

        $this->assertTrue(Color::validateColor($color));
    }

    /**
     * Tests Color::validateAlpha().
     *
     * @param int  $alpha     Alpha value (0-127).
     * @param bool $exception Exception expected
     *
     * @dataProvider providerAlpha
     *
     * @return void
     */
    public function testValidateAlpha($alpha, $exception)
    {
        if ($exception) {
            $this->setExpectedException(
                '\InvalidArgumentException',
                'Alpha is expected to be an integer value between 0 and 127'
            );
        }

        $this->assertTrue(Color::validateAlpha($alpha));
    }
}

