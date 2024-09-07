<?php

namespace App\Components;

use Spwa\Template\Component;
use Spwa\Template\ElementNode;
use function Spwa\Template\{_class, button, component, div, onClick, text};

class WelcomePage extends Component
{
    var $counter = 0;

    function view(): ElementNode
    {
        return div(
            div(
                text("Welcome to the home page"),
                _class("text-red-500")
            ),
            div(
                _class("bg-blue-500"),
                text("Another text node"),
            ),
            div(
                text("Counter: " . $this->counter),
                button(
                    text("Increment"),
                    _class("bg-green-500"),
                    onClick(fn() => $this->counter++)
                ),
                button(
                    text("Decrement"),
                    _class("bg-red-500"),
                    onClick(fn() => $this->counter--)
                ),
            ),
//            TodoList::create(["Drink Coffee", "Sleep", "Code"]),
        );
    }
}

