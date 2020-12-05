<?php

declare(strict_types=1);

namespace randomhost\Image\Tests\Text\Decorator;

use PHPUnit\Framework\TestCase;
use randomhost\Image\Color;
use randomhost\Image\Text\Decorator\Border;
use randomhost\Image\Text\Generic as Text;

/**
 * Unit test for {@see Border}.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2022 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 *
 * @internal
 *
 * @covers \randomhost\Image\Text\Decorator\Border
 */
class BorderTest extends TestCase
{
    /**
     * Tests {@see Border::setBorderColor()} and {@see Border::getBorderColor()}.
     */
    public function testSetGetBorderColor()
    {
        // mock dependencies
        $text = $this->createMock(Text::class);
        $color = $this->createMock(Color::class);

        $border = new Border($text);

        $this->assertSame(
            $border,
            $border->setBorderColor($color)
        );

        $this->assertSame($color, $border->getBorderColor());
    }

    /**
     * Tests {@see Border::setInsertText()}.
     */
    public function testInsertText()
    {
        // test values
        $alpha = 75;
        $xPosition = 20;
        $yPosition = 40;
        $text = 'test';

        // mock dependencies
        $textMock = $this->createMock(Text::class);
        $textColorMock = $this->createMock(Color::class);
        $borderColorMock = $this->createMock(Color::class);

        // configure mock objects
        $borderColorMock->expects($this->once())
            ->method('getAlpha')
            ->will($this->returnValue($alpha))
        ;

        $borderColorMock->expects($this->atLeastOnce())
            ->method('setAlpha')
            ->withConsecutive([$this->identicalTo(0)], [$this->identicalTo(75)])
            ->will($this->returnSelf())
        ;

        $textMock->expects($this->once())
            ->method('getTextColor')
            ->will($this->returnValue($textColorMock))
        ;

        $textMock->expects($this->exactly(2))
            ->method('setTextColor')
            ->withConsecutive(
                [$this->identicalTo($borderColorMock)],
                [$this->identicalTo($textColorMock)]
            )
            ->will($this->returnSelf())
        ;

        $textMock->expects($this->atLeastOnce())
            ->method('insertText')
            ->withConsecutive(
                [
                    $this->identicalTo($xPosition - 1),
                    $this->identicalTo($yPosition - 1),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition - 1),
                    $this->identicalTo($yPosition),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition - 1),
                    $this->identicalTo($yPosition + 1),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition),
                    $this->identicalTo($yPosition - 1),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition),
                    $this->identicalTo($yPosition + 1),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition + 1),
                    $this->identicalTo($yPosition - 1),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition + 1),
                    $this->identicalTo($yPosition),
                    $this->identicalTo($text),
                ],
                [
                    $this->identicalTo($xPosition + 1),
                    $this->identicalTo($yPosition + 1),
                    $this->identicalTo($text),
                ],
            )
            ->will($this->returnSelf())
        ;

        $border = new Border($textMock);

        $border->setBorderColor($borderColorMock);

        $this->assertSame(
            $border,
            $border->insertText($xPosition, $yPosition, $text)
        );
    }

    /**
     * Tests {@see Border::setInsertText()} with an unset border color.
     */
    public function testInsertTextMissingBorderColor()
    {
        // mock dependencies
        $textMock = $this->createMock(Text::class);

        $border = new Border($textMock);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Attempt to render text border without setting a color');

        $border->insertText(0, 0, '');
    }
}
