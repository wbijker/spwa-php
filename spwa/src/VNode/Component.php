<?php

namespace Spwa\VNode;

use Spwa\State\StateManager;
use Spwa\UI\DomNode;

/**
 * A component virtual node that can have state and lifecycle.
 */
abstract class Component extends VNode
{
    /**
     * Build the component's virtual node tree.
     * Override this method to define the component's structure.
     */
    abstract protected function build(): VNode;

    /**
     * Get the component's state as a keyed array.
     * Override this method to define stateful properties.
     * @return array<string, mixed>
     */
    protected function getState(): array
    {
        return [];
    }

    /**
     * Set the component's state from a keyed array.
     * Override this method to restore stateful properties.
     * @param array<string, mixed> $state
     */
    protected function setState(array $state): void
    {
        // Default: do nothing
    }

    /**
     * Render this component to a DOM node.
     * @param StateManager $state The state manager
     * @param VNode|null $parent The parent VNode
     */
    public function render(StateManager $state, ?VNode $parent = null): DomNode
    {
        $this->parent = $parent;
        // Only set path from parent if not already set (e.g., by setPath)
        if (empty($this->path)) {
            $this->path = $parent?->getPath() ?? [];
        }

        // Restore state from state manager using component class name as key
        $pathKey = $this->getStateKey();
        $savedState = $state->getState($pathKey);
        if (!empty($savedState)) {
            $this->setState($savedState);
        }

        $child = $this->build();

        return $child->render($state, $this);
    }

    /**
     * Get the state key for this component.
     * Uses class name and path for uniqueness.
     */
    protected function getStateKey(): string
    {
        $className = static::class;
        $pathStr = implode('.', $this->path);
        return $pathStr === '' ? $className : "$pathStr:$className";
    }

    /**
     * Compare this component with another node and generate patches.
     * @param VNode $parent The parent VNode
     * @param StateManager $manager The state manager
     * @param VNode $other The other VNode to compare with
     * @param Patcher $patcher The patcher to record operations
     */
    public function compare(VNode $parent, StateManager $manager, VNode $other, Patcher $patcher): void
    {
        $this->parent = $parent;
        $this->path = $parent->getPath();

        // If the other node is not the same component type, replace entirely
        if (get_class($this) !== get_class($other)) {
            $patcher->replaceNode($this->path, $this->render($manager, $parent));
            return;
        }

        // Build both components and compare their children
        $thisChild = $this->build();
        $otherChild = $other->build();

        $thisChild->compare($this, $manager, $otherChild, $patcher);
    }

    /**
     * Finalize this component, saving its state.
     * @param StateManager $state The state manager
     */
    public function finalize(StateManager $state): void
    {
        $pathKey = $this->getStateKey();
        $currentState = $this->getState();
        if (!empty($currentState)) {
            $state->saveState($pathKey, $currentState);
        }
    }
}
