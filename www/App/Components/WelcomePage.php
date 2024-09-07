<?php

namespace App\Components;

use Spwa\Template\ElementNode;
use Spwa\Template\Page;
use function Spwa\Template\{_class, button, div, meta, onClick, script, src, text, title};

class WelcomePage extends Page
{
    var $counter = 0;

    function head(): array
    {
        return [
            title("Hello SPWA"),
            meta(["viewport", "width=device-width, initial-scale=1"]),
            script(src("https://cdn.tailwindcss.com"))
        ];
    }

    function body(): ElementNode
    {
        return div(
            _class("w-full h-screen flex bg-gray-200"),
            div(
                _class("m-auto bg-white p-4 rounded-lg"),
                div(
                    text("Welcome to the page")
                ),
                div(
                    _class("text-center"),
                    text("Counter: " . $this->counter),
                ),
                div(
                    button(
                        text("inc"),
                        _class("m-1 px-2 border shadow cursor-pointer"),
                        onClick(fn() => $this->counter++)
                    )
                ),
                div(
                    button(
                        text("dec"),
                        _class("m-1 px-2 border shadow cursor-pointer"),
                        onClick(fn() => $this->counter--)
                    )
                )
            )
        );
    }
}

