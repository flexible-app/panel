<?php

namespace FlexibleApp\Panel;

use Illuminate\Support\Facades\Route;

abstract class Page
{
    public static function slug(): string
    {
        return str(class_basename(static::class))->kebab();
    }

    public static function title(): string
    {
        return str(class_basename(static::class))->headline();
    }

    public static function navigationLabel(): string
    {
        return static::title();
    }

    public static function canAccess(): bool
    {
        return true;
    }

    public static function meta(): array
    {
        return [
            'slug' => static::slug(),
            'title' => static::title(),
            'navigationLabel' => static::navigationLabel(),
        ];
    }
    
    public static function schema(): array
    {
        return [];
    }

    public static function registerRoutes(Panel $panel): void
    {
        if (!static::canAccess()) return;

        Route::get(static::slug(), fn () => inertia('panel', [
            'panel' => [
                'id' => $panel->id,
                'name' => $panel->name,
                'path' => $panel->path,
                'domain' => $panel->domain,
                'assets' => $panel->assets,
            ],
            'page' => [
                'slug' => static::slug(),
                'meta' => static::meta(),
                'schema' => collect(static::schema())->map->toArray()->all(),
            ],
        ]))
        ->name($panel->id . "." . static::slug());

        // dd(static::schema());

        foreach (static::schema() as $component) {
            if ($component instanceof \FlexibleApp\Panel\Components\Form) {
                $component->registerSubmitRoute($panel, static::slug());
                $component->registerSchemaRoute($panel, static::slug());
            }
        }
    }
}