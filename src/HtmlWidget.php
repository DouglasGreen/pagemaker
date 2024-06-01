<?php

declare(strict_types=1);

namespace DouglasGreen\PageMaker;

/**
 * A widget that stores plain HTML.
 */
class HtmlWidget extends AbstractWidget
{
    protected string $html;

    public function render(): string
    {
        return $this->html;
    }

    public function setHtml(string $html): void
    {
        $this->html = $html;
    }
}
