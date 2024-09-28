<?php

namespace App\Components;

use Spwa\Template\ElementNode;
use Spwa\Template\Page;
use Spwa\Template\SessionStateHandler;
use function Spwa\Template\{_class, bind, button, component, div, input, meta, onClick, script, span, src, text, title};

class WelcomePage extends Page
{
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

    var string $input1 = "";
    var int $last = 0;

    function body(): ElementNode
    {
        return div(
            _class("w-full h-screen flex bg-gray-200"),
            div(
                _class("m-auto bg-white p-4 rounded-lg"),
                div(
                    text("Welcome to the page"),
                ),
                div(text("Last counter value: " . $this->last)),

                Counter::component(fn($value) => $this->last = 0),

                div(
                    input("text",
                        _class("border-2 border-gray-300 p-2 rounded-lg"),
                        bind($this->input1),
                    ),
                ),
                div(
                    span(text("You typed: ")),
                    span(text($this->input1))
                )
            ),

        // <BookingDetailsStep data={data()} wrongDate={() => setIndex(0)} onChange={details => makeBooking(details); }}/>

//            boookingDetailsStep::create(
//                data(),
//                fn() => setIndex(0),
//                fn($details) => makeBooking($details)
//            ),

//            steps(
//                step(),
//                step(),
//                step(),
//            )

//            Steps::create(0, [
//                Step::create("First step", div(text("First step content"))),
//                Step::create("Second step", div(text("Second step content"))),
//                Step::create(step("Third step", div(text("Third step content"))))
//            ]),

//            $this->steps
//                ->addStep("First step", div(text("First step content")))
//                ->addStep("Second step", div(text("Second step content")))
//                ->addStep("Third step", div(text("Third step content")))
//                ->setIndex(1)
        );
    }
}
