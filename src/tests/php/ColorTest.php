<?php

declare(strict_types=1);

namespace randomhost\Image\Tests;

use PHPUnit\Framework\TestCase;
use randomhost\Image\Color;

/**
 * Unit test for {@see Color}.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2022 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 *
 * @internal
 *
 * @covers \randomhost\Image\Color
 */
class ColorTest extends TestCase
{
    /**
     * Data provider for color values.
     */
    public function providerColor(): \Generator
    {
        // color, exception, type error
        yield [128, false, false];

        yield [0, false, false];

        yield [255, false, false];

        yield [0x80, false, false];

        yield [0x00, false, false];

        yield [0xFF, false, false];

        yield [-1, true, false];

        yield [256, true, false];

        yield [0x100, true, false];

        yield [128.5, false, true];

        yield ['128', false, true];

        yield ['notanumber', false, true];
    }

    /**
     * Data provider for alpha values.
     */
    public function providerAlpha(): \Generator
    {
        // alpha, exception
        yield [64, false, false];

        yield [0, false, false];

        yield [127, false, false];

        yield [-1, true, false];

        yield [128, true, false];

        yield [64.0, false, true];

        yield ['64', false, true];

        yield ['notanumber', false, true];
    }

    /**
     * Tests {@see Color::setRed()} and {@see Color::getRed()}.
     *
     * @param mixed $red       Red component (0-255 or 0x00-0xFF).
     * @param bool  $exception Exception expected.
     * @param bool  $typeError Type error expected.
     *
     * @dataProvider providerColor
     */
    public function testSetGetRed($red, bool $exception, bool $typeError)
    {
        $color = new Color();

        if ($exception) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage(
                'Color is expected to be an integer value between 0 and 255 '.
                'or a hexadecimal value between 0x00 and 0xFF'
            );
        }

        if ($typeError) {
            $this->expectException(\TypeError::class);
        }

        $this->assertSame($color, $color->setRed($red));

        $this->assertSame($red, $color->getRed());
    }

    /**
     * Tests {@see Color::setGreen()} and {@see Color::getGreen()}.
     *
     * @param mixed $green     Green component (0-255 or 0x00-0xFF).
     * @param bool  $exception Exception expected.
     * @param bool  $typeError Type error expected.
     *
     * @dataProvider providerColor
     */
    public function testSetGetGreen($green, bool $exception, bool $typeError)
    {
        $color = new Color();

        if ($exception) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage(
                'Color is expected to be an integer value between 0 and 255 '.
                'or a hexadecimal value between 0x00 and 0xFF'
            );
        }

        if ($typeError) {
            $this->expectException(\TypeError::class);
        }

        $this->assertSame($color, $color->setGreen($green));

        $this->assertSame($green, $color->getGreen());
    }

    /**
     * Tests {@see Color::setBlue()} and {@see Color::getBlue()}.
     *
     * @param mixed $blue      Blue component (0-255 or 0x00-0xFF).
     * @param bool  $exception Exception expected.
     * @param bool  $typeError Type error expected.
     *
     * @dataProvider providerColor
     */
    public function testSetGetBlue($blue, bool $exception, bool $typeError)
    {
        $color = new Color();

        if ($exception) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage(
                'Color is expected to be an integer value between 0 and 255 '.
                'or a hexadecimal value between 0x00 and 0xFF'
            );
        }

        if ($typeError) {
            $this->expectException(\TypeError::class);
        }

        $this->assertSame($color, $color->setBlue($blue));

        $this->assertSame($blue, $color->getBlue());
    }

    /**
     * Tests {@see Color::setAlpha()} and {@see Color::getAlpha()}.
     *
     * @param mixed $alpha     Alpha value (0-127).
     * @param bool  $exception Exception expected.
     * @param bool  $typeError Type error expected.
     *
     * @dataProvider providerAlpha
     */
    public function testSetGetAlpha($alpha, bool $exception, bool $typeError)
    {
        $color = new Color();

        if ($exception) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage(
                'Alpha is expected to be an integer value between 0 and 127'
            );
        }

        if ($typeError) {
            $this->expectException(\TypeError::class);
        }

        $this->assertSame($color, $color->setAlpha($alpha));

        $this->assertSame($alpha, $color->getAlpha());
    }

    /**
     * Tests {@see Color::validateColor()}.
     *
     * @param mixed $color     Color component (0-255 or 0x00-0xFF).
     * @param bool  $exception Exception expected.
     * @param bool  $typeError Type error expected.
     *
     * @dataProvider providerColor
     */
    public function testValidateColor($color, bool $exception, bool $typeError)
    {
        if ($exception) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage(
                'Color is expected to be an integer value between 0 and 255 '.
                'or a hexadecimal value between 0x00 and 0xFF'
            );
        }

        if ($typeError) {
            $this->expectException(\TypeError::class);
        }

        $this->assertTrue(Color::validateColor($color));
    }

    /**
     * Tests {@see Color::validateAlpha()}.
     *
     * @param mixed $alpha     Alpha value (0-127).
     * @param bool  $exception Exception expected.
     * @param bool  $typeError Type error expected.
     *
     * @dataProvider providerAlpha
     */
    public function testValidateAlpha($alpha, bool $exception, bool $typeError)
    {
        if ($exception) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage(
                'Alpha is expected to be an integer value between 0 and 127'
            );
        }

        if ($typeError) {
            $this->expectException(\TypeError::class);
        }

        $this->assertTrue(Color::validateAlpha($alpha));
    }
}
