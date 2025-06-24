<?php

namespace FlexibleApp\Panel\Components;

use Illuminate\Support\Facades\Route;
use FlexibleApp\Panel\Panel;

abstract class Component
{
    public bool $visible = true;

    public static function make(...$args): static
    {
        return new static(...$args);
    }

    public function visible(bool $condition = true): static
    {
        $this->visible = $condition;
        return $this;
    }

    public function when(bool $condition): static
    {
        return $this->visible($condition);
    }

    public function registerRoutes(string $slug, Panel $panel): void
    {
        // Route::post("$slug/forms/{$this->name}", function ($name) {
            
        // })->name("panel.form.submit.{$this->name}");
    }

    abstract public function toArray(): array;
}
