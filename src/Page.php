<?php

declare(strict_types=1);

namespace DouglasGreen\PageMaker;

/*
 * @class Page builder
 */
class Page
{
    /**
     * @var string Language
     */
    protected $lang = 'en';

    /**
     * Set the page title.
     */
    public function __construct(
        protected Head $head,
        protected Body $body
    ) {}

    public function setLanguage(string $lang): void
    {
        $this->lang = $lang;
    }

    public function render(): string
    {
        $output = "<!DOCTYPE html>\n";
        $output .= "<html lang='{$this->lang}'>\n";
        $output .= $this->head->render();
        $output .= $this->body->render();
        return $output . "</html>\n";
    }
}
