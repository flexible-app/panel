<?php

namespace FlexibleApp\Panel;

class PanelManager
{
    protected static array $panelFactories = [];
    protected static array $panels = [];

    public static function registerPanel(callable $callback): void
    {
        static::$panelFactories[] = $callback;
    }

    public static function bootPanels(): void
    {
        foreach (static::$panelFactories as $factory) {
            $panel = $factory();
            $panel->registerRoutes();
            static::$panels[] = $panel;
        }
    }

    public static function all(): array
    {
        return static::$panels;
    }
}
