<?php

namespace FlexibleApp\Panel;

use Illuminate\Support\ServiceProvider;

abstract class PanelServiceProvider extends ServiceProvider
{
    abstract public function panel(Panel $panel): Panel;

    public function register(): void
    {
        PanelManager::registerPanel(fn () => $this->panel(new Panel('panel')));
    }

    public function boot(): void
    {
        PanelManager::bootPanels();
    }
}