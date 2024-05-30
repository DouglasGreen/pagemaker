<?php

declare(strict_types=1);

namespace DouglasGreen\PageMaker;

use DouglasGreen\Exceptions\ValueException;

class Page
{
    /**
     * @var array<string, array<string, ?string>> Page metadata
     */
    protected $metadata = [
        'http-equiv' => [
            'content-type' => null,
            'default-style' => null,
            'refresh' => null,
        ],
        'name' => [
            'application-name' => null,
            'author' => null,
            'description' => null,
            'generator' => null,
            'keywords' => null,
        ],
    ];

    /**
     * @var array<string, string> Page scripts
     */
    protected $scripts = [];

    /**
     * @var array<string, string> Page styles
     */
    protected $styles = [];

    /**
     * @var array<string, list<Widget>>
     */
    public array $widgets = [
        'header' => [],
        'main' => [],
        'footer' => [],
    ];

    public function __construct(
        protected string $title,
        protected string $lang = 'en',
        protected string $charset = 'UTF-8',
        protected ?string $favicon = null
    ) {}

    /**
     * @throws ValueException
     */
    public function setMeta(string $name, string $content): self
    {
        if (array_key_exists($name, $this->metadata['http-equiv'])) {
            $this->metadata['http-equiv'][$name] = $content;
            return $this;
        }

        if (array_key_exists($name, $this->metadata['name'])) {
            $this->metadata['name'][$name] = $content;
            return $this;
        }

        throw new ValueException('Unrecognized name of meta tag attribute');
    }

    /**
     * @throws ValueException
     */
    protected function setScript(string $scriptName, string $src): self
    {
        if (isset($this->scripts[$scriptName])) {
            throw new ValueException(sprintf(
                'Script "%s" already set to: "%s"',
                $scriptName,
                $this->scripts[$scriptName]
            ));
        }

        $this->scripts[$scriptName] = $src;
        return $this;
    }

    /**
     * @throws ValueException
     */
    protected function setStyle(string $styleName, string $href): self
    {
        if (isset($this->styles[$styleName])) {
            throw new ValueException(sprintf(
                'Style "%s" already set to: "%s"',
                $styleName,
                $this->styles[$styleName]
            ));
        }

        $this->styles[$styleName] = $href;
        return $this;
    }

    public function addHeaderWidget(Widget $widget): self
    {
        $this->widgets['header'][] = $widget;
        return $this;
    }

    public function addMainWidget(Widget $widget): self
    {
        $this->widgets['main'][] = $widget;
        return $this;
    }

    public function addFooterWidget(Widget $widget): self
    {
        $this->widgets['footer'][] = $widget;
        return $this;
    }

    public function render(): string
    {
        $output = '<!DOCTYPE html>' . PHP_EOL;
        $output .= sprintf('<html lang="%s">', $this->lang) . PHP_EOL;
        $output .= $this->renderHead();
        $output .= $this->renderBody();
        return $output . '</html>' . PHP_EOL;
    }

    protected function renderHead(): string
    {
        $output = '<head>' . PHP_EOL;
        $output .= sprintf('<title>%s</title>', $this->title) . PHP_EOL;
        $output .= sprintf('<meta charset="%s">', $this->charset) . PHP_EOL;

        foreach ($this->metadata as $type => $values) {
            foreach ($values as $name => $content) {
                if ($content !== null) {
                    $output .= sprintf('<meta %s="%s" content="%s">', $type, $name, $content) . PHP_EOL;
                }
            }
        }

        if ($this->favicon !== null) {
            $output .= sprintf('<link rel="icon" href="%s" type="image/x-icon">', $this->favicon);
        }

        foreach ($this->styles as $style) {
            $output .= sprintf('<link rel="stylesheet" type="text/css" href="%s">', $style) . PHP_EOL;
        }

        foreach ($this->scripts as $script) {
            $output .= sprintf('<script src="%s"></script>', $script) . PHP_EOL;
        }

        return $output . '</head>' . PHP_EOL;
    }

    public function renderBody(): string
    {
        $output = "<body id='pageMakerBody'>" . PHP_EOL;
        $output .= $this->renderSection('header', 'pageMakerHeader');
        $output .= $this->renderSection('main', 'pageMakerMain');
        $output .= $this->renderSection('footer', 'pageMakerFooter');
        return $output . '</body>' . PHP_EOL;
    }

    /**
     * @throws ValueException
     */
    protected function renderSection(string $sectionTag, string $sectionId): string
    {
        if (! $this->widgets[$sectionTag]) {
            $goodNames = implode(', ', array_keys($this->widgets));
            throw new ValueException('Bad tag name; should be one of: ' . $goodNames);
        }

        $output = sprintf('<%s id="%s">', $sectionTag, $sectionId) . PHP_EOL;

        foreach ($this->widgets[$sectionTag] as $widget) {
            $output .= $this->renderWidget($widget);
        }

        return $output . sprintf('</%s>', $sectionTag) . PHP_EOL;
    }

    protected function renderWidget(Widget $widget): string
    {
        $widgetName = $widget->getName();
        $widgetTag = $widget->getTag();
        $widgetClass = $widget->getClass();

        $content = sprintf('<%s class="%s">', $widgetTag, $widgetClass) . PHP_EOL;

        foreach ($widget->getScripts() as $scriptName => $src) {
            $fullName = $widgetName . ucfirst($scriptName);
            $this->setScript($fullName, $src);
        }

        foreach ($widget->getStyles() as $styleName => $href) {
            $fullName = $widgetName . ucfirst($styleName);
            $this->setStyle($fullName, $href);
        }

        try {
            $content .= $widget->render();
        } catch (\Throwable) {
            $content .= '<p style="color: red">Error rendering ' . $widget->getName() . '</p>';
        }

        return $content . (sprintf('</%s>', $widgetTag) . PHP_EOL);
    }
}
