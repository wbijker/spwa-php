<?php

namespace App\Components;

use Spwa\Html\Div;
use Spwa\Html\ExternalScript;
use Spwa\Html\Img;
use Spwa\Html\Meta;
use Spwa\Html\Title;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;
use Spwa\Nodes\Page;
use Spwa\Route\Route;
use Spwa\Route\Router;

class WelcomePage extends Page
{
    function renderBody(): Node
    {
        return new Div(class: "flex w-screen h-screen", children: [

            new Div(class: "m-auto", children: [
                new Div(class: "bg-white p-8 border-y", children: [
                    new Img(src: "/assets/images/logo.png", alt: "b", style: ['width' => '200px']),
                    new Router(routes: [
                        new Route(path: Routes::$about, component: new AboutPage()),
                        new Route(path: Routes::$product, component: fn(ProductRoute $product) => new ProductsPage($product)),
                    ], fallback: new AboutPage())
                ]),

                new Div(class: "text-right text-gray-400 text-xs py-2", children: [
                    new HtmlText("BrickPHP v1.0.0")
                ])
            ])
        ]);
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

