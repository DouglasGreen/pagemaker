<?php

declare(strict_types=1);

namespace DouglasGreen\PageMaker\Tests;

use DouglasGreen\Exceptions\ValueException;
use DouglasGreen\PageMaker\HtmlWidget;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class HtmlWidgetTest extends TestCase
{
    public function testConstructorInvalidClass(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Invalid class name: invalid class');
        new HtmlWidget('MyWidget', '1.0.0', 'div', 'invalid class');
    }

    public function testConstructorInvalidName(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Missing name');
        new HtmlWidget('', '1.0.0', 'div', 'my-widget-class');
    }

    public function testConstructorInvalidTag(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Bad tag; should be one of: article, aside, div, nav, section');
        new HtmlWidget('MyWidget', '1.0.0', 'invalid_tag', 'my-widget-class');
    }

    public function testConstructorInvalidVersion(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Invalid semantic version: invalid_version');
        new HtmlWidget('MyWidget', 'invalid_version', 'div', 'my-widget-class');
    }

    public function testConstructorValidData(): void
    {
        $htmlWidget = new HtmlWidget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertInstanceOf(HtmlWidget::class, $htmlWidget);
    }

    public function testGetClass(): void
    {
        $htmlWidget = new HtmlWidget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertSame('my-widget-class', $htmlWidget->getClass());
    }

    public function testGetName(): void
    {
        $htmlWidget = new HtmlWidget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertSame('MyWidget', $htmlWidget->getName());
    }

    public function testGetScripts(): void
    {
        $htmlWidget = new HtmlWidget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertIsArray($htmlWidget->getScripts());
    }

    public function testGetStyles(): void
    {
        $htmlWidget = new HtmlWidget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertIsArray($htmlWidget->getStyles());
    }

    public function testGetTag(): void
    {
        $htmlWidget = new HtmlWidget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertSame('div', $htmlWidget->getTag());
    }

    public function testGetVersion(): void
    {
        $htmlWidget = new HtmlWidget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertSame('1.0.0', $htmlWidget->getVersion());
    }

    public function testHasScript(): void
    {
        $htmlWidget = new HtmlWidget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertFalse($htmlWidget->hasScript('script1'));
        $htmlWidget->setScript('script1', 'src1');
        $this->assertTrue($htmlWidget->hasScript('script1'));
    }

    public function testHasStyle(): void
    {
        $htmlWidget = new HtmlWidget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $this->assertFalse($htmlWidget->hasStyle('style1'));
        $htmlWidget->setStyle('style1', 'href1');
        $this->assertTrue($htmlWidget->hasStyle('style1'));
    }

    public function testSetScript(): void
    {
        $htmlWidget = new HtmlWidget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $htmlWidget->setScript('script1', 'src1');
        $this->assertSame([
            'script1' => 'src1',
        ], $htmlWidget->getScripts());
    }

    public function testSetScriptAlreadySet(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Script "script1" already set: "src1"');
        $htmlWidget = new HtmlWidget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $htmlWidget->setScript('script1', 'src1');
        $htmlWidget->setScript('script1', 'src2');
    }

    public function testSetStyle(): void
    {
        $htmlWidget = new HtmlWidget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $htmlWidget->setStyle('style1', 'href1');
        $this->assertSame([
            'style1' => 'href1',
        ], $htmlWidget->getStyles());
    }

    public function testSetStyleAlreadySet(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Style "style1" already set: "href1"');
        $htmlWidget = new HtmlWidget('MyWidget', '1.0.0', 'div', 'my-widget-class');
        $htmlWidget->setStyle('style1', 'href1');
        $htmlWidget->setStyle('style1', 'href2');
    }
}
