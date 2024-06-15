<?php

declare(strict_types=1);

namespace DouglasGreen\PageMaker;

/**
 * Example usage:
 * $router = new Router('http://example.com', '/api/v1/resource', 'handleRequest', ['id' => 123, 'action' => 'view']);
 * $router->dispatch();
 *
 * Example function to handle the request
 * function handleRequest($args) {
 *   echo "Handling request with args: ";
 *   print_r($args);
 * }
 */
class Router {
    /**
     * @var array<string, mixed>
     */
    protected array $args;
    protected string $base;
    protected string $name;
    protected string $path;

    /**
     * @param array<string, mixed> $args
     */
    public function __construct(string $base, string $path, string $name, array $args = []) {
        $this->base = $base;
        $this->path = $path;
        $this->name = $name;
        $this->args = $args;
    }

    public function dispatch(): void {
        $url = $this->getFullUrl();
        $name = $this->getName();
        $args = $this->getArgs();

        // Here you would include the logic to call the appropriate program/service
        // For example, you might include a file or call a function based on $name
        // and pass $args to it.

        // Example:
        if (function_exists($name)) {
            call_user_func($name, $args);
        } else {
            echo "Service $name not found.";
        }
    }

    public function getFullUrl(): string {
        return rtrim($this->base, '/') . '/' . ltrim($this->path, '/');
    }

    public function getName(): string {
        return $this->name;
    }

    /**
     * @return array<string, mixed>
     */
    public function getArgs(): array {
        return $this->args;
    }
}
