<?php

namespace Spwa\VNode;

use Spwa\UI\UIElement;

abstract class App extends Component
{
    abstract public function title(): string;

    abstract protected function view(): UIElement;

    protected function build(): VNode
    {
        return $this->view();
    }
}
