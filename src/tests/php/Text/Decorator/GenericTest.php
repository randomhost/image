<?php

declare(strict_types=1);

namespace randomhost\Image\Tests\Text\Decorator;

use PHPUnit\Framework\TestCase;
use randomhost\Image\Color;
use randomhost\Image\Image;
use randomhost\Image\Text\Decorator\Border;
use randomhost\Image\Text\Decorator\Generic as GenericDecorator;
use randomhost\Image\Text\Generic as GenericText;
use randomhost\Image\Text\Text;

/**
 * Unit test for {@see GenericDecorator}.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2022 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 *
 * @internal
 *
 * @covers \randomhost\Image\Text\Decorator\Generic
 */
class GenericTest extends TestCase
{
    /**
     * Tests {@see Generic::setImage()} and {@see Generic::getImage()}.
     */
    public function testSetGetImage()
    {
        // dependencies
        $image = $this->getImageInstance();
        $textMock = $this->createMock(GenericText::class);

        // configure mock objects
        $textMock->expects($this->once())
            ->method('setImage')
            ->with($this->identicalTo($image))
            ->will($this->returnSelf())
        ;

        $textMock->expects($this->once())
            ->method('getImage')
            ->will($this->returnValue($image))
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            GenericDecorator::class,
            [$textMock]
        );

        $this->assertInstanceOf(
            Text::class,
            $generic->setImage($image)
        );

        $this->assertSame($image, $generic->getImage());
    }

    /**
     * Tests {@see Generic::setTextColor()} and {@see Generic::getTextColor()}.
     */
    public function testSetGetTextColor()
    {
        // dependencies
        $textMock = $this->createMock(GenericText::class);
        $colorMock = $this->createMock(Color::class);

        // configure mock objects
        $textMock->expects($this->once())
            ->method('setTextColor')
            ->with($this->identicalTo($colorMock))
            ->will($this->returnSelf())
        ;

        $textMock->expects($this->once())
            ->method('getTextColor')
            ->will($this->returnValue($colorMock))
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            GenericDecorator::class,
            [$textMock]
        );

        $this->assertInstanceOf(
            Text::class,
            $generic->setTextColor($colorMock)
        );

        $this->assertSame($colorMock, $generic->getTextColor());
    }

    /**
     * Tests {@see Generic::setTextFont()} and {@see Generic::getTextFont()}.
     */
    public function testSetGetTextFont()
    {
        // test value
        $font = '/path/to/font.ttf';

        // dependencies
        $textMock = $this->createMock(GenericText::class);

        // configure mock objects
        $textMock->expects($this->once())
            ->method('setTextFont')
            ->with($this->identicalTo($font))
            ->will($this->returnSelf())
        ;

        $textMock->expects($this->once())
            ->method('getTextFont')
            ->will($this->returnValue($font))
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            GenericDecorator::class,
            [$textMock]
        );

        $this->assertInstanceOf(
            Text::class,
            $generic->setTextFont($font)
        );

        $this->assertSame($font, $generic->getTextFont());
    }

    /**
     * Tests {@see Generic::setTextSize()} and {@see Generic::getTextSize()}.
     */
    public function testSetGetTextSize()
    {
        // test value
        $size = 14.0;

        // dependencies
        $textMock = $this->createMock(GenericText::class);

        // configure mock objects
        $textMock->expects($this->once())
            ->method('setTextSize')
            ->with($this->identicalTo($size))
            ->will($this->returnSelf())
        ;

        $textMock->expects($this->once())
            ->method('getTextSize')
            ->will($this->returnValue($size))
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            GenericDecorator::class,
            [$textMock]
        );

        $this->assertInstanceOf(
            Text::class,
            $generic->setTextSize($size)
        );

        $this->assertSame($size, $generic->getTextSize());
    }

    /**
     * Tests {@see Generic::insertText()}.
     */
    public function testInsertText()
    {
        // test values
        $xPosition = 20;
        $yPosition = 40;
        $text = 'test';

        // dependencies
        $textMock = $this->createMock(GenericText::class);

        // configure mock objects
        $textMock->expects($this->once())
            ->method('insertText')
            ->with(
                $this->identicalTo($xPosition),
                $this->identicalTo($yPosition),
                $this->identicalTo($text)
            )
            ->will($this->returnSelf())
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            GenericDecorator::class,
            [$textMock]
        );

        $this->assertInstanceOf(
            Text::class,
            $generic->insertText($xPosition, $yPosition, $text)
        );
    }

    /**
     * Tests {@see Generic::providesMethod()}.
     */
    public function testProvidesMethod()
    {
        // dependencies
        $textMock = $this->createMock(GenericText::class);

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            GenericDecorator::class,
            [$textMock]
        );

        $this->assertTrue($generic->providesMethod('insertText'));

        $this->assertFalse($generic->providesMethod('doesNotExist'));
    }

    /**
     * Tests {@see Generic::providesMethod()} with a tree of decorators.
     */
    public function testProvidesMethodWithDecoratorTree()
    {
        // test values
        $existingMethod = 'setBorderColor';
        $missingMethod = 'doesNotExist';

        // dependencies
        $textMock = $this->createMock(GenericText::class);
        $borderMock = $this
            ->getMockBuilder(Border::class)
            ->setConstructorArgs([$textMock])
            ->getMock()
        ;

        // configure mock objects
        $borderMock->expects($this->exactly(2))
            ->method('providesMethod')
            ->withConsecutive(
                [$this->identicalTo($existingMethod)],
                [$this->identicalTo($missingMethod)]
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue(true),
                $this->returnValue(false)
            )
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            GenericDecorator::class,
            [$borderMock]
        );

        $this->assertTrue($generic->providesMethod('setBorderColor'));

        $this->assertFalse($generic->providesMethod('doesNotExist'));
    }

    /**
     * Tests {@see Generic::__call()} with a tree of decorators.
     */
    public function testCallWithDecoratorTree()
    {
        // dependencies
        $textMock = $this->createMock(GenericText::class);
        $borderMock = $this
            ->getMockBuilder(Border::class)
            ->setConstructorArgs([$textMock])
            ->getMock()
        ;
        $colorMock = $this->createMock(Color::class);

        // configure mock objects
        $borderMock->expects($this->once())
            ->method('setBorderColor')
            ->with($this->identicalTo($colorMock))
            ->will($this->returnSelf())
        ;

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            GenericDecorator::class,
            [$borderMock]
        );

        $this->assertInstanceOf(
            Text::class,
            $generic->setBorderColor($colorMock)
        );
    }

    /**
     * Tests {@see Generic::__call()} with an undefined method.
     */
    public function testCallUndefinedMethod()
    {
        // dependencies
        $textMock = $this->createMock(GenericText::class);

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            GenericDecorator::class,
            [$textMock]
        );

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Failed to call');

        /** @noinspection PhpUndefinedMethodInspection */
        $generic->doesNotExist();
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
