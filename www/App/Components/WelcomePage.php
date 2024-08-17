<?php

namespace App\Components;

use Spwa\Template\Component;
use Spwa\Template\ElementNode;
use function Spwa\Template\{_class, div, text};

class WelcomePage extends Component
{

    function render(): ElementNode
    {
        return div(
            text("Welcome to the home page"),
            _class("text-red-500"),
            _class("bg-blue-500"),
            text("Another text node"),
        );
    }
}
