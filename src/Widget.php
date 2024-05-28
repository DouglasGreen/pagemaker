<?php

declare(strict_types=1);

namespace DouglasGreen\PageMaker;

interface Widget
{
    public function getClass(): string;

    public function getName(): string;

    public function getVersion(): string;

    public function getScripts(): array;

    public function getStyles(): array;

    public function getTag(): string;

    public function render(): string;
}
