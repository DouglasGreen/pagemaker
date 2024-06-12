<?php

declare(strict_types=1);

namespace DouglasGreen\PageMaker;

use DouglasGreen\Utility\Exceptions\Data\ValueException;

class Page
{
    /**
     * @var array<string, array<string, ?string>> Page metadata
     */
    protected array $metadata = [
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
     * @var array<string, array{src: string, version: string}> Page scripts
     */
    protected array $scripts = [];

    /**
     * @var array<string, array{href: string, version: string}> Page styles
     */
    protected array $styles = [];

    /**
     * @var array<string, list<AbstractWidget>>
     */
    protected array $widgets = [
        'header' => [],
        'main' => [],
        'footer' => [],
    ];

    public function __construct(
        protected string $title,
        protected string $lang = 'en',
        protected string $charset = 'UTF-8',
        protected ?string $favicon = null,
    ) {}

    public function addFooterWidget(AbstractWidget $widget): self
    {
        $this->widgets['footer'][] = $widget;
        return $this;
    }

    public function addHeaderWidget(AbstractWidget $widget): self
    {
        $this->widgets['header'][] = $widget;
        return $this;
    }

    public function addMainWidget(AbstractWidget $widget): self
    {
        $this->widgets['main'][] = $widget;
        return $this;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function getFavicon(): ?string
    {
        return $this->favicon;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function getMeta(string $name): ?string
    {
        return $this->metadata['http-equiv'][$name] ??
            ($this->metadata['name'][$name] ?? null);
    }

    /**
     * @return array{src: string, version: string}
     */
    public function getScript(string $scriptName): ?array
    {
        return $this->scripts[$scriptName] ?? null;
    }

    /**
     * @return array{href: string, version: string}
     */
    public function getStyle(string $styleName): ?array
    {
        return $this->styles[$styleName] ?? null;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function hasScript(string $scriptName): bool
    {
        return isset($this->scripts[$scriptName]);
    }

    public function hasStyle(string $styleName): bool
    {
        return isset($this->styles[$styleName]);
    }

    public function render(): string
    {
        $output = '<!DOCTYPE html>' . PHP_EOL;
        $output .= sprintf('<html lang="%s">', $this->lang) . PHP_EOL;
        $output .= $this->renderHead();
        $output .= $this->renderBody();
        return $output . '</html>' . PHP_EOL;
    }

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
     * Set a page script.
     *
     * Common scripts like jQuery should be added by calling this function
     * directly. Scripts that are unique to each widget should be added using
     * AbstractWidget::setScript() instead.
     *
     * @throws ValueException
     */
    public function setScript(
        string $scriptName,
        string $src,
        string $version,
    ): self {
        if (preg_match('/^\\d+\\.\\d+(\\.\\d+)?$/', $version) === 0) {
            throw new ValueException('Invalid semantic version: ' . $version);
        }

        if (isset($this->scripts[$scriptName])) {
            throw new ValueException(
                sprintf(
                    'Script "%s" already set: "%s"',
                    $scriptName,
                    $this->scripts[$scriptName]['src'],
                ),
            );
        }

        $this->scripts[$scriptName] = [
            'src' => $src,
            'version' => $version,
        ];
        return $this;
    }

    /**
     * Set a page styles.
     *
     * Common styles like Bootstrap should be added by calling this function
     * directly. Styles that are unique to each widget should be added using
     * AbstractWidget::setStyle() instead.
     *
     * @throws ValueException
     */
    public function setStyle(
        string $styleName,
        string $href,
        string $version,
    ): self {
        if (preg_match('/^\\d+\\.\\d+(\\.\\d+)?$/', $version) === 0) {
            throw new ValueException('Invalid semantic version: ' . $version);
        }

        if (isset($this->styles[$styleName])) {
            throw new ValueException(
                sprintf(
                    'Style "%s" already set: "%s"',
                    $styleName,
                    $this->styles[$styleName]['href'],
                ),
            );
        }

        $this->styles[$styleName] = [
            'href' => $href,
            'version' => $version,
        ];
        return $this;
    }

    protected function addVersion(string $url, string $version): string
    {
        if (! str_contains($url, '?')) {
            $url .= '?version=' . urlencode($version);
        } else {
            $url .= '&version=' . urlencode($version);
        }

        return $url;
    }

    protected function renderBody(): string
    {
        $output = "<body id='pageMakerBody'>" . PHP_EOL;
        $output .= $this->renderSection('header', 'pageMakerHeader');
        $output .= $this->renderSection('main', 'pageMakerMain');
        $output .= $this->renderSection('footer', 'pageMakerFooter');
        return $output . '</body>' . PHP_EOL;
    }

    protected function renderHead(): string
    {
        $output = '<head>' . PHP_EOL;
        $output .= sprintf('<title>%s</title>', $this->title) . PHP_EOL;
        $output .= sprintf('<meta charset="%s">', $this->charset) . PHP_EOL;

        foreach ($this->metadata as $type => $values) {
            foreach ($values as $name => $content) {
                if ($content !== null) {
                    $output .=
                        sprintf(
                            '<meta %s="%s" content="%s">',
                            $type,
                            $name,
                            $content,
                        ) . PHP_EOL;
                }
            }
        }

        if ($this->favicon !== null) {
            $output .= sprintf(
                '<link rel="icon" href="%s" type="image/x-icon">',
                $this->favicon,
            );
        }

        foreach ($this->styles as $style) {
            $href = $this->addVersion($style['href'], $style['version']);
            $output .=
                sprintf(
                    '<link rel="stylesheet" type="text/css" href="%s">',
                    $href,
                ) . PHP_EOL;
        }

        foreach ($this->scripts as $script) {
            $src = $this->addVersion($script['src'], $script['version']);
            $output .= sprintf('<script src="%s"></script>', $src) . PHP_EOL;
        }

        return $output . '</head>' . PHP_EOL;
    }

    /**
     * @throws ValueException
     */
    protected function renderSection(
        string $sectionTag,
        string $sectionId,
    ): string {
        if (! $this->widgets[$sectionTag]) {
            $goodNames = implode(', ', array_keys($this->widgets));
            throw new ValueException(
                'Bad tag name; should be one of: ' . $goodNames,
            );
        }

        $output = sprintf('<%s id="%s">', $sectionTag, $sectionId) . PHP_EOL;

        foreach ($this->widgets[$sectionTag] as $widget) {
            $output .= $this->renderWidget($widget);
        }

        return $output . sprintf('</%s>', $sectionTag) . PHP_EOL;
    }

    protected function renderWidget(AbstractWidget $widget): string
    {
        $widgetTag = $widget->getTag();
        $widgetClass = $widget->getClass();
        $widgetVersion = $widget->getVersion();

        $content =
            sprintf('<%s class="%s">', $widgetTag, $widgetClass) . PHP_EOL;

        // Scripts are qualified by their widget class to avoid conflict.
        foreach ($widget->getScripts() as $scriptName => $src) {
            $fullName = $widgetClass . ucfirst($scriptName);
            $this->setScript($fullName, $src, $widgetVersion);
        }

        // Styles are qualified by their widget class to avoid conflict.
        foreach ($widget->getStyles() as $styleName => $href) {
            $fullName = $widgetClass . ucfirst($styleName);
            $this->setStyle($fullName, $href, $widgetVersion);
        }

        try {
            $content .= $widget->render();
        } catch (\Throwable) {
            $content .=
                '<p style="color: red">Error rendering ' .
                $widget->getName() .
                '</p>';
        }

        return $content . (sprintf('</%s>', $widgetTag) . PHP_EOL);
    }
}
