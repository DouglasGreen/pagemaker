<?php

declare(strict_types=1);

namespace DouglasGreen\PageMaker;

/**
 * A widget extends this class. It adds scripts, add styles, and output.
 * The purpose of widget architecture is separation of features and error checking
 * @todo Add section builder to add divs to section?
 */
abstract class AbstractWidget implements Widget
{
    protected static $validTags = ['article', 'aside', 'div', 'nav', 'section'];

    protected string $tag;

    protected $scripts = [];

    protected $styles = [];

    /**
     * @var string Semantic version of this class and its CSS/JS files
     */
    protected $version = '0.1.0';

    public function __construct(
        protected string $name,
        string $tag,
        protected string $class,
        protected ?array $data = null
    ) {
        $this->tag = strtolower($tag);
        if (! in_array($this->tag, self::$validTags, true)) {
            $validTags = implode(', ', self::$validTags);
            throw new Exception('Bad tag; should be one of: ' . $validTags);
        }
    }

    public function setScript(string $name, string $src): void
    {
        if (str_contains($src, '?') === false) {
            $src .= '?version=' . $this->version;
        }

        $this->scripts[$name] = $src;
    }

    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }

    public function setStyle(string $name, string $href): void
    {
        if (str_contains($href, '?') === false) {
            $href .= '?version=' . $this->version;
        }

        $this->styles[$name] = $href;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getScripts(): array
    {
        return $this->scripts;
    }

    public function getStyles(): array
    {
        return $this->styles;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * Subclass and override this function for custom widgets.
     */
    public function render(): string
    {
        $output = "Debug Mode<br>\n";
        foreach ($this->data as $key => $value) {
            $output .= $key . ': ' . var_export($value, true) . "<br>\n";
        }

        return $output;
    }
}
