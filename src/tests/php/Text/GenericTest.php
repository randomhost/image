<?php

declare(strict_types=1);

namespace randomhost\Image\Tests\Text;

use PHPUnit\Framework\TestCase;
use randomhost\Image\Color;
use randomhost\Image\Image;
use randomhost\Image\Tests\TestData;
use randomhost\Image\Text\Generic;

/**
 * Unit test for {@see Generic}.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2024 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 *
 * @internal
 *
 * @covers \randomhost\Image\Text\Generic
 */
class GenericTest extends TestCase
{
    /**
     * Tests {@see Generic::setImage()} and {@see Generic::getImage()}.
     */
    public function testSetGetImage()
    {
        $image = $this->getImageInstance();

        $generic = new Generic();

        $this->assertSame($generic, $generic->setImage($image));

        $this->assertSame($image, $generic->getImage());
    }

    /**
     * Tests {@see Generic::setTextColor()} and {@see Generic::getTextColor()}.
     */
    public function testSetGetTextColor()
    {
        $colorMock = $this->createMock(Color::class);

        $generic = new Generic();

        $this->assertSame($generic, $generic->setTextColor($colorMock));

        $this->assertSame($colorMock, $generic->getTextColor());
    }

    /**
     * Tests {@see Generic::setTextFont()} and {@see Generic::getTextFont()}.
     *
     * @throws \Exception
     */
    public function testSetGetTextFont()
    {
        // test value
        $font = TestData::getPath('vera.ttf');

        $generic = new Generic();

        $this->assertSame($generic, $generic->setTextFont($font));

        $this->assertSame($font, $generic->getTextFont());
    }

    /**
     * Tests {@see Generic::setTextFont()} with an invalid font path.
     */
    public function testSetTextFontInvalidFontPath()
    {
        // test value
        $font = 'doesnotexist.ttf';

        $generic = new Generic();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unable to load font file at '.$font
        );

        $generic->setTextFont($font);
    }

    /**
     * Tests {@see Generic::setTextSize()} and Generic::getTextSize().
     */
    public function testSetGetTextSize()
    {
        // test value
        $size = 14.0;

        $generic = new Generic();

        $this->assertSame($generic, $generic->setTextSize($size));

        $this->assertSame($size, $generic->getTextSize());
    }

    /**
     * Tests {@see Generic::insertText()}.
     *
     * @throws \Exception
     */
    public function testInsertText()
    {
        // dependencies
        $image = $this->getImageInstance();
        $font = TestData::getPath('vera.ttf');
        $colorMock = $this->createMock(Color::class);

        $generic = new Generic($image);

        $this->assertSame($generic, $generic->setTextFont($font));

        $this->assertSame($generic, $generic->setTextColor($colorMock));

        $this->assertSame($generic, $generic->insertText(0, 0, ''));
    }

    /**
     * Tests {@see Generic::insertText()} with an unset Image object.
     *
     * @throws \Exception
     */
    public function testInsertTextMissingImageObject()
    {
        // dependencies
        $font = TestData::getPath('vera.ttf');
        $colorMock = $this->createMock(Color::class);

        $generic = new Generic();

        $this->assertSame($generic, $generic->setTextFont($font));

        $this->assertSame($generic, $generic->setTextColor($colorMock));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Attempt to render text onto invalid image resource'
        );

        $generic->insertText(0, 0, '');
    }

    /**
     * Tests {@see Generic::insertText()} with an unset {@see Image} object image resource.
     *
     * @throws \Exception
     */
    public function testInsertTextUnsetImageResource()
    {
        // dependencies
        $image = $this->getImageInstance();
        $image->image = null;
        $font = TestData::getPath('vera.ttf');
        $colorMock = $this->createMock(Color::class);

        $generic = new Generic($image);

        $this->assertSame($generic, $generic->setTextFont($font));

        $this->assertSame($generic, $generic->setTextColor($colorMock));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Attempt to render text onto invalid image resource'
        );

        $generic->insertText(0, 0, '');
    }

    /**
     * Tests {@see Generic::insertText()} with an unset Color object.
     *
     * @throws \Exception
     */
    public function testInsertTextMissingColorObject()
    {
        // dependencies
        $image = $this->getImageInstance();
        $font = TestData::getPath('vera.ttf');

        $generic = new Generic($image);

        $this->assertSame($generic, $generic->setTextFont($font));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Attempt to render text without setting a color'
        );

        $generic->insertText(0, 0, '');
    }

    /**
     * Tests {@see Generic::insertText()} with an unset font path.
     */
    public function testInsertTextUnsetFont()
    {
        // dependencies
        $image = $this->getImageInstance();
        $colorMock = $this->createMock(Color::class);

        $generic = new Generic($image);

        $this->assertSame($generic, $generic->setTextColor($colorMock));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'No font file selected for rendering text overlay'
        );

        $generic->insertText(0, 0, '');
    }

    /**
     * Tests {@see Generic::insertText()} with a missing font file.
     *
     * @throws \Exception
     */
    public function testInsertTextMissingFontFile()
    {
        // dependencies
        $image = $this->getImageInstance();
        $font = 'vera.ttf';
        $colorMock = $this->createMock(Color::class);

        $generic = new Generic($image);

        $this->assertSame($generic, $generic->setTextColor($colorMock));

        // move font file to a temporary path
        $tmpDir = sys_get_temp_dir();
        $fontPath = realpath($tmpDir).DIRECTORY_SEPARATOR.$font;
        copy(TestData::getPath($font), $fontPath);

        // set font and let setTextFont() validate the path
        $this->assertSame($generic, $generic->setTextFont($fontPath));

        // remove font copy to trigger the expected exception
        unlink($fontPath);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Failed to read font file \''.$fontPath.'\''
        );

        $generic->insertText(0, 0, '');
    }

    /**
     * Returns a real image object as mocking this is a little too complicated
     * for now.
     */
    protected function getImageInstance(): Image
    {
        return Image::getInstanceByCreate(100, 100);
    }
}
