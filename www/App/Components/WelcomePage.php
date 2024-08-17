<?php

namespace App\Components;

use Spwa\Template\AttributeNode;
use Spwa\Template\Component;
use Spwa\Template\ElementNode;
use Spwa\Template\TextNode;

//use function Spwa\Html\div;

class WelcomePage extends Component
{

    function render(): ElementNode
    {
        return new ElementNode("div", [
            new TextNode("Welcome to the home page"),
            new AttributeNode("style", "color: red; font-size: 2rem;"),
        ]);
    }
}
