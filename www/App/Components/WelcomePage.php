<?php

namespace App\Components;

use Spwa\Html\Meta;
use Spwa\Html\ExternalScript;
use Spwa\Html\Title;
use Spwa\Nodes\Node;
use Spwa\Nodes\Page;
use Spwa\Route\Route;
use Spwa\Route\Router;

class WelcomePage extends Page
{
    function renderBody(): Node
    {
        return new Router(routes: [
            new Route(path: Routes::$about, component: new AboutPage()),
            new Route(path: Routes::$product, component: fn(ProductRoute $product) => new ProductsPage($product)),
        ], fallback: new AboutPage());
    }

    function header(): array
    {
        return [
            new Title("Some document"),
            new Meta(charset: "UTF-8"),
            new Meta(name: "viewport", content: "width=device-width, initial-scale=1.0"),
            new ExternalScript(src: "https://cdn.tailwindcss.com"),
        ];
    }

}

