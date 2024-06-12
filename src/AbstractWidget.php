<?php

declare(strict_types=1);

namespace DouglasGreen\PageMaker;

use DouglasGreen\Utility\Exceptions\Data\ValueException;

/**
 * A widget can extend this class.
 */
abstract class AbstractWidget
{
    /**
     * @var list<string>
     */
    protected static array $validTags = [
        'article',
        'aside',
        'div',
        'nav',
        'section',
    ];

    /**
     * @var array<string, string>
     */
    protected array $scripts = [];

    /**
     * @var array<string, string>
     */
    protected array $styles = [];

    /**
     * Subclass and override this function for custom widgets.
     */
    abstract public function render(): string;

    /**
     * @param string $name Name of the widget
     * @param string $version Semantic version of this class and its CSS/JS files
     * @param string $tag One of the valid tags that contains the widget
     * @param string $class Class name that will be applied to the widget tag
     *
     * @throws ValueException
     */
    public function __construct(
        protected string $name,
        protected string $version,
        protected string $tag,
        protected string $class,
    ) {
        $this->name = trim($this->name);
        if ($this->name === '') {
            throw new ValueException('Missing name');
        }

        if (preg_match('/^\\d+\\.\\d+(\\.\\d+)?$/', $this->version) === 0) {
            throw new ValueException(
                'Invalid semantic version: ' . $this->version,
            );
        }

        $this->tag = strtolower($this->tag);
        if (! in_array($this->tag, self::$validTags, true)) {
            $validTags = implode(', ', self::$validTags);
            throw new ValueException(
                'Bad tag; should be one of: ' . $validTags,
            );
        }

        if (preg_match('/^\\w+(-\\w+)*$/', $this->class) === 0) {
            throw new ValueException('Invalid class name: ' . $this->class);
        }
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, string>
     */
    public function getScripts(): array
    {
        return $this->scripts;
    }

    /**
     * @return array<string, string>
     */
    public function getStyles(): array
    {
        return $this->styles;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function hasScript(string $name): bool
    {
        return isset($this->scripts[$name]);
    }

    public function hasStyle(string $name): bool
    {
        return isset($this->styles[$name]);
    }

    public function setScript(string $name, string $src): self
    {
        if (isset($this->scripts[$name])) {
            throw new ValueException(
                'Script "' .
                    $name .
                    '" already set: "' .
                    $this->scripts[$name] .
                    '"',
            );
        }

        $this->scripts[$name] = $src;
        return $this;
    }

    public function setStyle(string $name, string $href): self
    {
        if (isset($this->styles[$name])) {
            throw new ValueException(
                'Style "' .
                    $name .
                    '" already set: "' .
                    $this->styles[$name] .
                    '"',
            );
        }

        $this->styles[$name] = $href;
        return $this;
    }
}
