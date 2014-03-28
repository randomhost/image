<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * BorderTest unit test definition
 *
 * PHP version 5
 *
 * @category  Image
 * @package   PHP_Image
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2014 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      https://pear.random-host.com/
 */
namespace randomhost\Image\Text\Decorator;

use randomhost\Image\Image;

/**
 * Unit test for Border
 *
 * @category  Image
 * @package   PHP_Image
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2014 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   Release: @package_version@
 * @link      https://pear.random-host.com/
 */
class BorderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Border::setBorderColor() and Border::getBorderColor().
     *
     * @return void
     */
    public function testSetGetBorderColor()
    {
        // mock dependencies
        $text = $this->getMock('randomhost\\Image\\Text\\Generic');
        $color = $this->getMock('randomhost\\Image\\Color');

        $border = new Border($text);

        $this->assertSame(
            $border,
            $border->setBorderColor($color)
        );

        $this->assertSame($color, $border->getBorderColor());
    }

    /**
     * Tests Border::setInsertText().
     *
     * @return void
     */
    public function testInsertText()
    {
        // test values
        $alpha = 75;
        $xPosition = 20;
        $yPosition = 40;
        $text = 'test';

        /*
         * Create a real image object as mocking this is a little too
         * complicated for now.
         */
        $image = Image::getInstanceByCreate(100, 100);
        
        // mock dependencies
        $textMock = $this->getMock('randomhost\\Image\\Text\\Generic');
        $textColorMock = $this->getMock('randomhost\\Image\\Color');
        $borderColorMock = $this->getMock('randomhost\\Image\\Color');

        // configure mock objects
        $borderColorMock->expects($this->at(0))
            ->method('getAlpha')
            ->will($this->returnValue($alpha));

        $borderColorMock->expects($this->at(1))
            ->method('setAlpha')
            ->with($this->identicalTo(0))
            ->will($this->returnSelf());

        $textMock->expects($this->at(0))
            ->method('getTextColor')
            ->will($this->returnValue($textColorMock));

        $textMock->expects($this->at(1))
            ->method('setTextColor')
            ->with($this->identicalTo($borderColorMock))
            ->will($this->returnSelf());

        $textMock->expects($this->at(2))
            ->method('getImage')
            ->will($this->returnValue($image));

        $textMock->expects($this->at(3))
            ->method('setImage')
            ->with($this->isInstanceOf('randomhost\\Image\\Image'))
            ->will($this->returnSelf());

        $textMock->expects($this->at(4))
            ->method('insertText')
            ->with(
                $this->identicalTo($xPosition - 1),
                $this->identicalTo($yPosition - 1),
                $this->identicalTo($text)
            )
            ->will($this->returnSelf());

        $textMock->expects($this->at(5))
            ->method('insertText')
            ->with(
                $this->identicalTo($xPosition - 1),
                $this->identicalTo($yPosition),
                $this->identicalTo($text)
            )
            ->will($this->returnSelf());

        $textMock->expects($this->at(6))
            ->method('insertText')
            ->with(
                $this->identicalTo($xPosition - 1),
                $this->identicalTo($yPosition + 1),
                $this->identicalTo($text)
            )
            ->will($this->returnSelf());

        $textMock->expects($this->at(7))
            ->method('insertText')
            ->with(
                $this->identicalTo($xPosition),
                $this->identicalTo($yPosition - 1),
                $this->identicalTo($text)
            )
            ->will($this->returnSelf());

        $textMock->expects($this->at(8))
            ->method('insertText')
            ->with(
                $this->identicalTo($xPosition),
                $this->identicalTo($yPosition + 1),
                $this->identicalTo($text)
            )
            ->will($this->returnSelf());

        $textMock->expects($this->at(9))
            ->method('insertText')
            ->with(
                $this->identicalTo($xPosition + 1),
                $this->identicalTo($yPosition - 1),
                $this->identicalTo($text)
            )
            ->will($this->returnSelf());

        $textMock->expects($this->at(10))
            ->method('insertText')
            ->with(
                $this->identicalTo($xPosition + 1),
                $this->identicalTo($yPosition),
                $this->identicalTo($text)
            )
            ->will($this->returnSelf());

        $textMock->expects($this->at(11))
            ->method('insertText')
            ->with(
                $this->identicalTo($xPosition + 1),
                $this->identicalTo($yPosition + 1),
                $this->identicalTo($text)
            )
            ->will($this->returnSelf());

        $textMock->expects($this->at(12))
            ->method('setImage')
            ->with($this->identicalTo($image))
            ->will($this->returnSelf());

        $textMock->expects($this->at(13))
            ->method('getImage')
            ->will($this->returnValue($image));

        $textMock->expects($this->at(14))
            ->method('setTextColor')
            ->with($this->identicalTo($textColorMock))
            ->will($this->returnSelf());

        $border = new Border($textMock);

        $border->setBorderColor($borderColorMock);
        
        $this->assertSame(
            $border,
            $border->insertText($xPosition, $yPosition, $text)
        );
    }

    /**
     * Tests Border::setInsertText() with an unset border color.
     *
     * @return void
     */
    public function testInsertTextMissingBorderColor()
    {
        // mock dependencies
        $textMock = $this->getMock('randomhost\\Image\\Text\\Generic');
        
        $border = new Border($textMock);

        $this->setExpectedException(
            '\RuntimeException',
            'Attempt to render text border without setting a color'
        );
        
        $border->insertText(0, 0, '');
    }
}
 
