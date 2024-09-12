<?php

namespace App\Components;

use Spwa\Template\ElementNode;
use Spwa\Template\Page;
use function Spwa\Template\{_class, button, div, meta, onClick, script, src, text, title};

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

    function init()
    {
        $this->counter1 = new Counter(0, "Counter 1");
        $this->counter2 = new Counter(0, "Counter 2");
    }

    function save(): array
    {
        return [
            "counter1" => $this->counter1->save(),
            "counter2" => $this->counter2->save()
        ];
    }

    function restore($props): void
    {
        $this->counter1->restore($props["counter1"]);
        $this->counter2->restore($props["counter2"]);
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
            )
        );
    }
}

