<?php

namespace FlexibleApp\Panel;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class Panel
{
    public string $name;
    public string $path = '/admin';
    public ?string $domain = null;
    public array $middleware = ['web'];
    public array $assets = [];
    protected array $pages = [];

    public function __construct(public string $id)
    {
        $this->name = ucfirst($id);
    }

    public function path(string $path): static
    {
        $this->path = $path;
        return $this;
    }

    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function domain(?string $domain): static
    {
        $this->domain = $domain;
        return $this;
    }

    public function middleware(array $middleware): static
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function assets(array $assets): static
    {
        $this->assets = $assets;
        return $this;
    }

    public function pages(array $pages): static
    {
        $this->pages = [...$this->pages, ...$pages];
        return $this;
    }

    public function registerRoutes(): void
    {
        Route::middleware($this->middleware)
            ->prefix($this->path)
            ->domain($this->domain)
            ->group(function () {
                Route::get('/', fn () => inertia('panel', [
                    'panel' => [
                        'id' => $this->id,
                        'name' => $this->name,
                        'path' => $this->path,
                        'domain' => $this->domain,
                        'assets' => $this->assets
                    ],
                ]))
                ->name($this->name);

                foreach ($this->pages as $page) {
                    $page::registerRoutes($this);
                }
            });
    }
}
