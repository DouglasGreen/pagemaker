<?php

declare(strict_types=1);

namespace DouglasGreen\PageMaker;

/*
 * @class Page body builder
 */
class Body
{
    public $widgets;

    /**
     * Add a widget to the top-level part.
     */
    public function addWidget(string $partClass, Widget $widget): void
    {
        if (! isset($this->widgets[$partClass])) {
            $goodNames = implode(', ', array_keys($this->widgets));
            throw new Exception('Bad top-level container; should be one of: ' . $goodNames);
        }

        $this->widgets[$partClass][] = $widget;
    }

    public function render(): string
    {
        $output = "<body class='pmBody'>\n";
        $output .= $this->renderSection('header', 'pmHeader');
        $output .= $this->renderSection('main', 'pmMain');
        $output .= $this->renderSection('footer', 'pmFooter');
        return $output . "</body>\n";
    }

    protected function renderSection(string $tag, string $partClass): string
    {
        if (! $this->widgets[$partClass]) {
            $goodNames = implode(', ', array_keys($this->widgets));
            throw new Exception('Bad container class; should be one of: ' . $goodNames);
        }

        $output = "<{$tag} class='{$partClass}'>\n";
        foreach ($this->widgets[$partClass] as $widget) {
            $widgetTag = $widget->getTag();
            $widgetClass = $widget->getClass();
            $output .= "<{$widgetTag} class='{$widgetClass}'>\n";

            // @todo What if the widgets have conflicting requirements with the main page?
            foreach ($widget->getScripts() as $name => $script) {
                $this->setScript($name, $script);
            }

            foreach ($widget->getStyles() as $name => $style) {
                $this->setStyle($name, $style);
            }

            try {
                $content = $widget->render();
            } catch (Throwable) {
                $content = '<p style="color: red">Error rendering ' . $widget->getName() . '</p>';
            }

            $output .= $content;
            $output .= "</{$widgetTag}>\n";
        }

        return $output . "</{$tag}>\n";
    }
}
