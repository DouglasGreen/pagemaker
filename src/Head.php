<?php

declare(strict_types=1);

namespace DouglasGreen\PageMaker;

/*
 * @class Page head builder
 */
class Head
{
    public $version;

    protected string $charset = 'UTF-8';

    protected string $favicon;

    /**
     * @var array Page metadata
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
     * @var array Top-level containers (parts) that hold widgets
     */
    protected $widgets = [
        'pmHeader' => [],
        'pmMain' => [],
        'pmFooter' => [],
    ];

    /**
     * @var array Named script URLs
     */
    protected $scripts = [];

    /**
     * @var array Named style URLs
     */
    protected $styles = [];

    /**
     * Set the page title.
     */
    public function __construct(
        protected string $title
    ) {}

    public function setCharset(string $charset): void
    {
        $this->charset = $charset;
    }

    public function setFavicon(string $favicon): void
    {
        $this->favicon = $favicon;
    }

    public function setMeta(string $name, string $content): void
    {
        if (array_key_exists($this->metadata['http-equiv'], $name)) {
            $this->metadata['http-equiv'][$name] = $content;
            return;
        }

        if (array_key_exists($this->metadata['name'], $name)) {
            $this->metadata['name'][$name] = $content;
            return;
        }

        throw new Exception('Unrecognized name of meta tag attribute');
    }

    public function setScript(string $name, string $src): void
    {
        if (str_contains($src, '?') === false) {
            $src .= '?version=' . $this->version;
        }

        $this->scripts[$name] = $src;
    }

    public function setStyle(string $name, string $href): void
    {
        if (str_contains($href, '?') === false) {
            $href .= '?version=' . $this->version;
        }

        $this->styles[$name] = $href;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function render(): string
    {
        $output = "<head>\n";
        $output .= "<title>{$this->title}</title>\n";
        $output .= "<meta charset='{$this->charset}'>\n";

        foreach ($this->metadata as $type => $values) {
            foreach ($values as $name => $content) {
                if ($content !== null) {
                    $output .= "<meta {$type}='{$name}' content='{$content}'>\n";
                }
            }
        }

        if ($this->favicon !== null) {
            $output .= sprintf('<link rel="icon" href="%s" type="image/x-icon">', $this->favicon);
        }

        foreach ($this->styles as $name => $href) {
            $output .= "<link id='{$name}' rel='stylesheet' type='text/css' href='{$href}'>\n";
        }

        foreach ($this->scripts as $name => $src) {
            $output .= "<script id='{$name}' src='{$src}'></script>\n";
        }

        return $output . "</head>\n";
    }
}
