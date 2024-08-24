<?php

namespace App\Components;

use Spwa\Template\Component;
use Spwa\Template\ElementNode;
use function Spwa\Template\{_class, div, text};

class WelcomePage extends Component
{
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
            new TodoList(['Drink Coffee', 'Write Code', 'Drink More Coffee'])
        );
    }
}

