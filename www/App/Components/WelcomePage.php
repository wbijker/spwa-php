<?php

namespace App\Components;

use Spwa\Template\ElementNode;
use Spwa\Template\Page;
use function Spwa\Template\{_class, bind, button, div, input, meta, onClick, script, src, text, title};

class WelcomePage extends Page
{
    function head(): array
    {
        return [
            title("Hello SPWA"),
            meta(["viewport", "width=device-width, initial-scale=1"]),
            script(src("https://cdn.tailwindcss.com"))
        ];
    }

    var Counter $counter1;
    var Counter $counter2;

    var string $input1 = "";

    function init(): void
    {
        $this->counter1 = new Counter();
        $this->counter2 = new Counter();
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
                $this->counter1,
                $this->counter2,
                div(
                    input("text",
                        _class("border-2 border-gray-300 p-2 rounded-lg"),
                        bind($this->input1),
                    ),
                )
            ),
        );
    }
}

