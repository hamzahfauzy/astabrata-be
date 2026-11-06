<?php

namespace Libraries;

use RuntimeException;

class RouteCache
{
    protected string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function exists(): bool
    {
        return is_file($this->path);
    }

    public function load(): ?array
    {
        if (!$this->exists()) {
            return null;
        }

        $routes = require $this->path;

        return is_array($routes) ? $routes : null;
    }

    public function save(array $routes): void
    {
        $directory = dirname($this->path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $content = "<?php\n\nreturn "
            . var_export($routes, true)
            . ";\n";

        $temp = $this->path . '.tmp';

        if (file_put_contents($temp, $content, LOCK_EX) === false) {
            throw new RuntimeException('Gagal membuat route cache.');
        }

        if (!rename($temp, $this->path)) {
            @unlink($temp);

            throw new RuntimeException('Gagal menyimpan route cache.');
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($this->path, true);
        }
    }

    public function clear(): void
    {
        if ($this->exists()) {
            unlink($this->path);
        }
    }
}