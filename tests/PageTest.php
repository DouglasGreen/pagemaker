<?php

declare(strict_types=1);

namespace DouglasGreen\PageMaker\Tests;

use DouglasGreen\Exceptions\ValueException;
use DouglasGreen\PageMaker\Page;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    public function testConstructor(): void
    {
        $page = new Page('MyPage');
        $this->assertInstanceOf(Page::class, $page);
        $this->assertSame('MyPage', $page->getTitle());
        $this->assertSame('en', $page->getLang());
        $this->assertSame('UTF-8', $page->getCharset());
        $this->assertNull($page->getFavicon());
    }

    public function testSetMetaValid(): void
    {
        $page = new Page('MyPage');
        $page->setMeta('content-type', 'text/html');
        $this->assertSame('text/html', $page->getMeta('content-type'));
    }

    public function testSetMetaInvalid(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Unrecognized name of meta tag attribute');
        $page = new Page('MyPage');
        $page->setMeta('invalid-meta', 'some content');
    }

    public function testSetScript(): void
    {
        $page = new Page('MyPage');
        $page->setScript('jquery', 'https://code.jquery.com/jquery-3.6.0.min.js', '3.6.0');
        $this->assertTrue($page->hasScript('jquery'));
        $this->assertSame([
            'src' => 'https://code.jquery.com/jquery-3.6.0.min.js',
            'version' => '3.6.0',
        ], $page->getScript('jquery'));
    }

    public function testSetScriptAlreadySet(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Script "jquery" already set: "https://code.jquery.com/jquery-3.6.0.min.js"');
        $page = new Page('MyPage');
        $page->setScript('jquery', 'https://code.jquery.com/jquery-3.6.0.min.js', '3.6.0');
        $page->setScript('jquery', 'https://code.jquery.com/jquery-3.6.1.min.js', '3.6.1');
    }

    public function testSetStyle(): void
    {
        $page = new Page('MyPage');
        $page->setStyle(
            'bootstrap',
            'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css',
            '4.3.1'
        );
        $this->assertTrue($page->hasStyle('bootstrap'));
        $this->assertSame([
            'href' => 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css',
            'version' => '4.3.1',
        ], $page->getStyle('bootstrap'));
    }

    public function testSetStyleAlreadySet(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage(
            'Style "bootstrap" already set: "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"'
        );
        $page = new Page('MyPage');
        $page->setStyle(
            'bootstrap',
            'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css',
            '4.3.1'
        );
        $page->setStyle(
            'bootstrap',
            'https://stackpath.bootstrapcdn.com/bootstrap/4.3.2/css/bootstrap.min.css',
            '4.3.2'
        );
    }
}
