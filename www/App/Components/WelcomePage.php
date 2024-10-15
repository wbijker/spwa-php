<?php

namespace App\Components;

use Spwa\Page;
use Spwa\Template\ElementNode;
use Spwa\Template\SessionStateHandler;
use function Spwa\Template\{_class, bind, div, input, meta, script, span, src, title};

class WelcomePageState
{
    public Counter $counter1;
    public Counter $counter2;
    public string $input1 = "";
    public int $last = 0;

    public function __construct()
    {
        $this->counter1 = new Counter(1);
        $this->counter2 = new Counter(11);
    }

}

class WelcomePage extends Page
{
    public function __construct()
    {
        $this->state = new WelcomePageState();
    }

    public function stateHandler(): \Spwa\Template\StateHandler
    {
        return new SessionStateHandler();
    }

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
                    "Welcome to the page"
                ),
                div("Last counter value: " . $this->state->last),

                $this->state->counter1
                    ->onChange(fn($val) => $this->state->last = $val),

                $this->state->counter2
                    ->onChange(fn($val) => $this->state->last = $val),

                div(
                    input("text",
                        _class("border-2 border-gray-300 p-2 rounded-lg"),
                        bind($this->state->input1),
                    ),
                ),
                div(
                    span("You typed: "),
                    span($this->state->input1)
                )
            ),
        );
    }
}
