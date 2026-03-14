<?php

namespace Spwa\VNode;

use Spwa\State\StateManager;
use Spwa\UI\UIElement;

abstract class App extends Component
{
    abstract public function title(): string;

    abstract protected function view(): UIElement;

    /**
     * Return the state managers used by this app.
     * @return StateManager[]
     */
    abstract public function states(): array;

    /**
     * Get the primary (first) state manager.
     */
    public function getDefaultState(): StateManager
    {
        return $this->states()[0];
    }

    protected function build(): VNode
    {
        return $this->view();
    }
}
