<?php

namespace App\Components;

use Spwa\Template\Component;
use Spwa\Template\ElementNode;
use function Spwa\Template\div;

class Steps extends Component
{
    private array $steps = [];
    private int $index = 0;

    function setIndex($index): Steps {
        $this->index = $index;
        return $this;
    }

    function addStep(string $tab, ElementNode $content): Steps {
//        $this->steps[] = $render;
        return $this;
    }

    function view(): ElementNode
    {
        // <Steps index={index()} steps={steps}/>
        return div();
    }
}