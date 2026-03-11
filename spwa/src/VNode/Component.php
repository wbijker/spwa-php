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
     */
    abstract protected function build(): VNode;

    /**
     * Get the component's state as a keyed array.
     * By convention, uses the $state property if it exists.
     * @return array<string, mixed>
     */
    protected function getState(): array
    {
        if (property_exists($this, 'state') && is_object($this->state)) {
            return get_object_vars($this->state);
        }
        return [];
    }

    /**
     * Set the component's state from a keyed array.
     * By convention, populates the $state property if it exists.
     * @param array<string, mixed> $state
     */
    protected function setState(array $state): void
    {
        if (property_exists($this, 'state') && is_object($this->state)) {
            foreach ($state as $key => $value) {
                if (property_exists($this->state, $key)) {
                    $this->state->$key = $value;
                }
            }
        }
    }

    /**
     * Render this component to a DOM node.
     * @param StateManager $state The state manager
     * @param VNode|null $parent The parent VNode
     */
    public function render(StateManager $state, ?VNode $parent = null): DomNode
    {
        $this->parent = $parent;
        if (empty($this->path)) {
            $this->path = $parent?->getPath() ?? [];
        }

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
        $pathKey = $this->getStateKey();
        $currentState = $this->getState();
        if (!empty($currentState)) {
            $state->saveState($pathKey, $currentState);
        }
    }
}
