<?php

namespace Samples\News;

use Spwa\State\SessionStateManager;
use Spwa\State\StateManager;
use Spwa\UI\Router;
use Spwa\VNode\App;
use Spwa\VNode\VNode;

class NewsApp extends App
{
    public function title(): string
    {
        return 'News';
    }

    public function state(): StateManager
    {
        return new SessionStateManager();
    }

    protected function registerAssets(App $app): void
    {
        Router::registerAssets($app);
        Leaflet::registerAssets($app);
    }

    protected function view(): VNode
    {
        return new News();
    }



}
