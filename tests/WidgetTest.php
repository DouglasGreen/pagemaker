<?php

declare(strict_types=1);

namespace DouglasGreen\PageMaker\Tests;

use DouglasGreen\Exceptions\ValueException;
use DouglasGreen\PageMaker\Widget;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class WidgetTest extends TestCase
{
    public function testConstructorValidData(): void
    {
        $widget = new Widget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertInstanceOf(Widget::class, $widget);
    }

    public function testConstructorInvalidName(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Missing name');
        new Widget('', '1.0.0', 'div', 'my-widget-class');
    }

    public function testConstructorInvalidVersion(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Invalid semantic version: invalid_version');
        new Widget('MyWidget', 'invalid_version', 'div', 'my-widget-class');
    }

    public function testConstructorInvalidTag(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Bad tag; should be one of: article, aside, div, nav, section');
        new Widget('MyWidget', '1.0.0', 'invalid_tag', 'my-widget-class');
    }

    public function testConstructorInvalidClass(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Invalid class name: invalid class');
        new Widget('MyWidget', '1.0.0', 'div', 'invalid class');
    }

    public function testGetClass(): void
    {
        $widget = new Widget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertSame('my-widget-class', $widget->getClass());
    }

    public function testGetName(): void
    {
        $widget = new Widget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertSame('MyWidget', $widget->getName());
    }

    public function testGetScripts(): void
    {
        $widget = new Widget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertIsArray($widget->getScripts());
    }

    public function testGetStyles(): void
    {
        $widget = new Widget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertIsArray($widget->getStyles());
    }

    public function testGetTag(): void
    {
        $widget = new Widget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertSame('div', $widget->getTag());
    }

    public function testGetVersion(): void
    {
        $widget = new Widget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertSame('1.0.0', $widget->getVersion());
    }

    public function testHasScript(): void
    {
        $widget = new Widget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertFalse($widget->hasScript('script1'));
        $widget->setScript('script1', 'src1');
        $this->assertTrue($widget->hasScript('script1'));
    }

    public function testHasStyle(): void
    {
        $widget = new Widget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertFalse($widget->hasStyle('style1'));
        $widget->setStyle('style1', 'href1');
        $this->assertTrue($widget->hasStyle('style1'));
    }

    public function testSetScript(): void
    {
        $widget = new Widget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $widget->setScript('script1', 'src1');
        $this->assertSame([
            'script1' => 'src1',
        ], $widget->getScripts());
    }

    public function testSetScriptAlreadySet(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Script "script1" already set: "src1"');
        $widget = new Widget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $widget->setScript('script1', 'src1');
        $widget->setScript('script1', 'src2');
    }

    public function testSetStyle(): void
    {
        $widget = new Widget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $widget->setStyle('style1', 'href1');
        $this->assertSame([
            'style1' => 'href1',
        ], $widget->getStyles());
    }

    public function testSetStyleAlreadySet(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Style "style1" already set: "href1"');
        $widget = new Widget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $widget->setStyle('style1', 'href1');
        $widget->setStyle('style1', 'href2');
    }
}
