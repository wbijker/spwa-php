<?php

namespace Spwa\VNode;

use Spwa\State\StateManager;
use Spwa\UI\UIElement;

abstract class App extends Component
{
    /** @var StateManager[] */
    private array $stateManagers = [];

    abstract public function title(): string;

    abstract protected function view(): UIElement;

    /**
     * Register a state manager for this app.
     * Call in initialize() before useState() calls.
     */
    protected function addState(StateManager $state): void
    {
        $this->stateManagers[] = $state;
    }

    /**
     * Get all registered state managers.
     * @return StateManager[]
     */
    public function getStateManagers(): array
    {
        return $this->stateManagers;
    }

    /**
     * Get the primary (first registered) state manager.
     */
    public function getDefaultState(): StateManager
    {
        return $this->stateManagers[0];
    }

    /**
     * Bootstrap the app: calls initialize() to register state managers.
     * Called by Spwa::run() before rendering.
     */
    public function boot(): void
    {
        $this->ensureInitialized();
    }

    protected function build(): VNode
    {
        return $this->view();
    }
}
