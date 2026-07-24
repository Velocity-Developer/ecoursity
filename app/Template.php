<?php

declare(strict_types=1);

namespace Ecoursity\App;

class Template
{
    private const THEME_TEMPLATE_DIRECTORY = 'template-ecoursity';

    public static function view(string $view, array $data = []): bool|null
    {
        extract($data);

        $file = self::resolve($view);

        if ($file === null) {
            return false;
        }

        require $file;

        return null;
    }

    public static function get(string $view): string|false
    {
        return self::resolve($view) ?: false;
    }

    public static function component(string $component, array $data = []): bool|null
    {
        return self::view('components/' . $component, $data);
    }

    private static function resolve(string $view): ?string
    {
        $relativePath = self::relativePath($view);

        foreach (self::templatePaths($relativePath) as $file) {
            if (file_exists($file)) {
                return $file;
            }
        }

        return null;
    }

    private static function relativePath(string $view): string
    {
        $view = str_replace(['\\', '.'], '/', $view);
        $view = trim($view, '/');
        $segments = array_filter(
            explode('/', $view),
            static fn (string $segment): bool => $segment !== '' && $segment !== '..'
        );

        return implode('/', $segments) . '.php';
    }

    /**
     * @return array<int, string>
     */
    private static function templatePaths(string $relativePath): array
    {
        $paths = [];

        if (function_exists('get_stylesheet_directory')) {
            $paths[] = trailingslashit(get_stylesheet_directory())
                . self::THEME_TEMPLATE_DIRECTORY
                . '/'
                . $relativePath;
        }

        if (
            function_exists('get_template_directory')
            && get_template_directory() !== get_stylesheet_directory()
        ) {
            $paths[] = trailingslashit(get_template_directory())
                . self::THEME_TEMPLATE_DIRECTORY
                . '/'
                . $relativePath;
        }

        $paths[] = ECOURSITY_PATH . 'templates/' . $relativePath;

        return $paths;
    }
}
