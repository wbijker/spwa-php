<?php

namespace Spwa\VNode;

use Spwa\State\StateManager;
use Spwa\UI\UIElement;

abstract class App extends Component
{
    /** @var string[] Custom JS snippets registered by components */
    private array $customJs = [];

    /** @var string[] Custom CSS snippets registered by components */
    private array $customCss = [];

    abstract public function title(): string;

    abstract protected function view(): VNode;

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

    /**
     * Add a custom JavaScript snippet.
     */
    public function addJs(string $js): void
    {
        $this->customJs[] = $js;
    }

    /**
     * Add a custom CSS snippet.
     */
    public function addCss(string $css): void
    {
        $this->customCss[] = $css;
    }

    /**
     * Get all registered custom JS snippets.
     * @return string[]
     */
    public function getCustomJs(): array
    {
        return $this->customJs;
    }

    /**
     * Get all registered custom CSS snippets.
     * @return string[]
     */
    public function getCustomCss(): array
    {
        return $this->customCss;
    }

    protected function build(): VNode
    {
        return $this->view();
    }
}
