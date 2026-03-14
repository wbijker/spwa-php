<?php

namespace Spwa\VNode;

use Spwa\State\StateManager;
use Spwa\UI\DomNode;
use Spwa\UI\NoOpDomNode;

/**
 * A component virtual node that can have state and lifecycle.
 */
abstract class Component extends VNode
{
    /** @var array<int, mixed> References to state variables */
    private array $stateRefs = [];

    /** @var StateManager|null Per-component state manager override */
    private ?StateManager $stateManager = null;

    /** @var bool Whether initialize() has been called */
    protected bool $initialized = false;

    /**
     * Register a variable as state. Call in initialize().
     */
    protected function useState(mixed &$ref, ?StateManager $stateManager = null): void
    {
        if ($stateManager !== null) {
            $this->stateManager = $stateManager;
        }
        $this->stateRefs[] = &$ref;
    }

    /**
     * Resolve which state manager to use: per-component override or the inherited one.
     */
    private function resolveStateManager(StateManager $default): StateManager
    {
        return $this->stateManager ?? $default;
    }

    /**
     * Override to register state variables via useState().
     */
    protected function initialize(): void
    {
    }

    /**
     * Ensure initialize() has been called exactly once.
     */
    protected function ensureInitialized(): void
    {
        if (!$this->initialized) {
            $this->initialize();
            $this->initialized = true;
        }
    }

    /**
     * Called after state has been restored.
     * Override to perform actions after state restoration.
     */
    protected function restored(): void
    {
    }

    /**
     * Called before build() to determine if rendering should proceed.
     * Return false to skip rendering and return a NoOpDomNode.
     */
    protected function shouldRender(): bool
    {
        return true;
    }

    /**
     * Build the component's virtual node tree.
     */
    abstract protected function build(): VNode;

    /**
     * Get the component's state as an indexed array.
     * @return array<int, mixed>
     */
    protected function getState(): array
    {
        $state = [];
        foreach ($this->stateRefs as $ref) {
            $state[] = $ref;
        }
        return $state;
    }

    /**
     * Set the component's state from an indexed array.
     * Aborts if the count doesn't match.
     * @param array<int, mixed> $state
     */
    protected function setState(array $state): void
    {
        $values = array_values($state);

        if (count($values) !== count($this->stateRefs)) {
            return;
        }

        for ($i = 0; $i < count($this->stateRefs); $i++) {
            $this->stateRefs[$i] = $values[$i];
        }
    }

    /**
     * Render this component to a DOM node.
     * @param StateManager $state The state manager
     * @param VNode|null $parent The parent VNode
     * @param RenderPhase $phase The render phase (Initial or Patch)
     */
    public function render(StateManager $state, ?VNode $parent = null, RenderPhase $phase = RenderPhase::Initial): DomNode
    {
        $this->parent = $parent;
        if (empty($this->path)) {
            $this->path = $parent?->getPath() ?? [];
        }

        // Initialize state references (guard against double-init from boot())
        $this->ensureInitialized();

        // Restore state (use per-component override if set)
        $resolved = $this->resolveStateManager($state);
        $pathKey = $this->getStateKey();
        $savedState = $resolved->getState($pathKey);
        if (!empty($savedState)) {
            $this->setState($savedState);
        }

        // Lifecycle: restored
        $this->restored();

        // Lifecycle: shouldRender (only in Patch phase)
        if ($phase === RenderPhase::Patch && !$this->shouldRender()) {
            return new NoOpDomNode();
        }

        $child = $this->build();

        return $child->render($state, $this, $phase);
    }

    /**
     * Get the state key for this component.
     */
    protected function getStateKey(): string
    {
        $className = static::class;
        $pathStr = implode('.', $this->path);
        return $pathStr === '' ? $className : "$pathStr:$className";
    }

    /**
     * Finalize this component, saving its state.
     * @param StateManager $state The state manager
     */
    public function finalize(StateManager $state): void
    {
        $resolved = $this->resolveStateManager($state);
        $pathKey = $this->getStateKey();
        $currentState = $this->getState();
        if (!empty($currentState)) {
            $resolved->saveState($pathKey, $currentState);
        }
    }
}
