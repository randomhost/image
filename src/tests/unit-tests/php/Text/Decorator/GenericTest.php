<?php
namespace randomhost\Image\Text\Decorator;

use randomhost\Image\Image;

/**
 * Unit test for Generic
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://php-image.random-host.com
 */
class GenericTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Generic::setImage() and Generic::getImage().
     *
     * @return void
     */
    public function testSetGetImage()
    {
        // dependencies
        $image = $this->getImageInstance();
        $textMock = $this->getMock('randomhost\\Image\\Text\\Generic');

        // configure mock objects
        $textMock->expects($this->once())
            ->method('setImage')
            ->with($this->identicalTo($image))
            ->will($this->returnSelf());

        $textMock->expects($this->once())
            ->method('getImage')
            ->will($this->returnValue($image));

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            array($textMock)
        );

        $this->assertInstanceOf(
            'randomhost\\Image\\Text\\Text',
            $generic->setImage($image)
        );

        $this->assertSame($image, $generic->getImage());
    }

    /**
     * Tests Generic::setTextColor() and Generic::getTextColor().
     *
     * @return void
     */
    public function testSetGetTextColor()
    {
        // dependencies
        $textMock = $this->getMock('randomhost\\Image\\Text\\Generic');
        $colorMock = $this->getMock('randomhost\\Image\\Color');

        // configure mock objects
        $textMock->expects($this->once())
            ->method('setTextColor')
            ->with($this->identicalTo($colorMock))
            ->will($this->returnSelf());

        $textMock->expects($this->once())
            ->method('getTextColor')
            ->will($this->returnValue($colorMock));

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            array($textMock)
        );

        $this->assertInstanceOf(
            'randomhost\\Image\\Text\\Text',
            $generic->setTextColor($colorMock)
        );

        $this->assertSame($colorMock, $generic->getTextColor());
    }

    /**
     * Tests Generic::setTextFont() and Generic::getTextFont().
     *
     * @return void
     */
    public function testSetGetTextFont()
    {
        // test value
        $font = '/path/to/font.ttf';

        // dependencies
        $textMock = $this->getMock('randomhost\\Image\\Text\\Generic');

        // configure mock objects
        $textMock->expects($this->once())
            ->method('setTextFont')
            ->with($this->identicalTo($font))
            ->will($this->returnSelf());

        $textMock->expects($this->once())
            ->method('getTextFont')
            ->will($this->returnValue($font));

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            array($textMock)
        );

        $this->assertInstanceOf(
            'randomhost\\Image\\Text\\Text',
            $generic->setTextFont($font)
        );

        $this->assertSame($font, $generic->getTextFont());
    }

    /**
     * Tests Generic::setTextSize() and Generic::getTextSize().
     *
     * @return void
     */
    public function testSetGetTextSize()
    {
        // test value
        $size = 14;

        // dependencies
        $textMock = $this->getMock('randomhost\\Image\\Text\\Generic');

        // configure mock objects
        $textMock->expects($this->once())
            ->method('setTextSize')
            ->with($this->identicalTo($size))
            ->will($this->returnSelf());

        $textMock->expects($this->once())
            ->method('getTextSize')
            ->will($this->returnValue($size));

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            array($textMock)
        );

        $this->assertInstanceOf(
            'randomhost\\Image\\Text\\Text',
            $generic->setTextSize($size)
        );

        $this->assertSame($size, $generic->getTextSize());
    }

    /**
     * Tests Generic::insertText().
     *
     * @return void
     */
    public function testInsertText()
    {
        // test values
        $xPosition = 20;
        $yPosition = 40;
        $text = 'test';

        // dependencies
        $textMock = $this->getMock('randomhost\\Image\\Text\\Generic');

        // configure mock objects
        $textMock->expects($this->once())
            ->method('insertText')
            ->with(
                $this->identicalTo($xPosition),
                $this->identicalTo($yPosition),
                $this->identicalTo($text)
            )
            ->will($this->returnSelf());

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            array($textMock)
        );

        $this->assertInstanceOf(
            'randomhost\\Image\\Text\\Text',
            $generic->insertText($xPosition, $yPosition, $text)
        );
    }

    /**
     * Tests Generic::providesMethod().
     *
     * @return void
     */
    public function testProvidesMethod()
    {
        // dependencies
        $textMock = $this->getMock('randomhost\\Image\\Text\\Generic');

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            array($textMock)
        );

        $this->assertTrue($generic->providesMethod('insertText'));

        $this->assertFalse($generic->providesMethod('doesNotExist'));
    }

    /**
     * Tests Generic::providesMethod() with a tree of decorators.
     *
     * @return void
     */
    public function testProvidesMethodWithDecoratorTree()
    {
        // test values
        $existingMethod = 'setBorderColor';
        $missingMethod = 'doesNotExist';

        // dependencies
        $textMock = $this->getMock('randomhost\\Image\\Text\\Generic');
        $borderMock = $this->getMock(
            'randomhost\\Image\\Text\\Decorator\\Border',
            array(),
            array($textMock)
        );

        // configure mock objects
        $borderMock->expects($this->at(0))
            ->method('providesMethod')
            ->with($this->identicalTo($existingMethod))
            ->will($this->returnValue(true));

        $borderMock->expects($this->at(1))
            ->method('providesMethod')
            ->with($this->identicalTo($missingMethod))
            ->will($this->returnValue(false));

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            array($borderMock)
        );

        $this->assertTrue($generic->providesMethod('setBorderColor'));

        $this->assertFalse($generic->providesMethod('doesNotExist'));
    }

    /**
     * Tests Generic::__call() with a tree of decorators.
     *
     * @return void
     */
    public function testCallWithDecoratorTree()
    {
        // dependencies
        $textMock = $this->getMock('randomhost\\Image\\Text\\Generic');
        $borderMock = $this->getMock(
            'randomhost\\Image\\Text\\Decorator\\Border',
            array(),
            array($textMock)
        );
        $colorMock = $this->getMock('randomhost\\Image\\Color');

        // configure mock objects
        $borderMock->expects($this->at(0))
            ->method('setBorderColor')
            ->with($this->identicalTo($colorMock))
            ->will($this->returnSelf());

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            array($borderMock)
        );

        $this->assertInstanceOf(
            'randomhost\\Image\\Text\\Text',
            $generic->setBorderColor($colorMock)
        );
    }

    /**
     * Tests Generic::__call() with an undefined method.
     *
     * @return void
     */
    public function testCallUndefinedMethod()
    {
        // dependencies
        $textMock = $this->getMock('randomhost\\Image\\Text\\Generic');

        // mock abstract class
        $generic = $this->getMockForAbstractClass(
            'randomhost\\Image\\Text\\Decorator\\Generic',
            array($textMock)
        );

        $this->setExpectedException(
            'PHPUnit_Framework_Error_Warning',
            'call_user_func_array() expects parameter 1 to be a valid callback'
        );

        $generic->doesNotExist();
    }

    /**
     * Returns a real image object as mocking this is a little too complicated
     * for now.
     *
     * @return Image
     */
    protected function getImageInstance()
    {
        return Image::getInstanceByCreate(100, 100);
    }
}

