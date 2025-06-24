<?php

namespace FlexibleApp\Panel\Components;

use FlexibleApp\Panel\Panel;

class Dialog extends Component
{
    public string $name;
    public array $schema = [];
    public bool $open = false;

    public static array $registry = [];

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->register();
    }

    public static function make(...$args): static
    {
        return new static(...$args); // accepts anything, passes to constructor
    }

    public function schema(array $schema): static
    {
        $this->schema = $schema;
        return $this;
    }

    public function open(bool $state = true): static
    {
        $this->open = $state;
        return $this;
    }

    protected function register(): void
    {
        static::$registry[$this->name] = $this;
    }

    public static function show(string $name)
    {
        return [
            'show' => $name
        ];
        // if (isset(static::$registry[$name])) {
        //     static::$registry[$name]->open(true);
        // }
    }

    public function registerRoutes(string $slug, Panel $panel): void
    {
        foreach ($this->schema as $component) {
            $component->registerRoutes($slug, $panel);
        }
    }

    public function toArray(): array
    {
        return [
            'type' => 'Dialog',
            'name' => $this->name,
            'open' => $this->open,
            'schema' => array_map(fn($child) => $child->toArray(), $this->schema),
            'visible' => $this->visible,
        ];
    }
}
