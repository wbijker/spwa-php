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

    /** @var array<string, mixed> References to global state variables keyed by path key */
    private array $globalStateRefs = [];

    /** @var StateManager|null Per-component state manager override */
    private ?StateManager $stateManager = null;

    /** @var bool Whether initialize() has been called */
    protected bool $initialized = false;

    /** @var array<string, IProvideConsume> Provided values keyed by IProvideConsume::key() */
    private static array $provided = [];

    /** @var array<string, Component> Components rendered during the Initial (old) phase, keyed by state key */
    private static array $oldRegistry = [];

    /** @var array<string, Component> Components rendered during the Patch (new) phase, keyed by state key */
    private static array $newRegistry = [];

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
     * Register a variable as global state with a fixed path key.
     * Multiple components can share the same global state by using the same key.
     */
    protected function useGlobalState(mixed &$ref, string $key): void
    {
        $this->globalStateRefs[$key] = &$ref;
    }

    /**
     * Resolve which state manager to use: per-component override or the inherited one.
     */
    private function resolveStateManager(StateManager $default): StateManager
    {
        return $this->stateManager ?? $default;
    }

    /**
     * Inject a value to be available to all descendant components.
     */
    protected function inject(IProvideConsume $value): void
    {
        self::$provided[$value->key()] = $value;
    }

    /**
     * Consume a provided value by key. The variable's type must implement IProvideConsume.
     * Fills the reference with the injected instance matching its key.
     */
    protected function consume(IProvideConsume &$ref): void
    {
        $key = $ref->key();
        if (isset(self::$provided[$key])) {
            $ref = self::$provided[$key];
        }
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
     * Called when the component is rendered for the first time (no prior state).
     */
    protected function created(): void
    {
    }

    /**
     * Called when the component existed in the tree before and is being re-rendered.
     */
    protected function updated(): void
    {
    }

    /**
     * Called when the component was in the old tree but not in the new tree.
     */
    protected function deleted(): void
    {
    }

    /**
     * Compare this component instance against the old instance from the previous render.
     * Return false to skip rendering and return a NoOpDomNode.
     * Override to implement custom comparison logic (e.g. comparing state).
     *
     * @param static $old The old component instance (same class, same path)
     */
    protected function compare(self $old): bool
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
        $isNew = empty($savedState);
        if (!$isNew) {
            $this->setState($savedState);
        }

        // Track component by phase for lifecycle diffing
        if ($phase === RenderPhase::Initial) {
            self::$oldRegistry[$pathKey] = $this;
        } else {
            self::$newRegistry[$pathKey] = $this;
        }

        // Restore global state
        foreach ($this->globalStateRefs as $key => &$ref) {
            $globalState = $resolved->getState($key);
            if ($globalState !== null) {
                $ref = $globalState;
            }
        }

        // Lifecycle: restored
        $this->restored();

        // Lifecycle: created or updated
        if ($isNew) {
            $this->created();
        } else {
            $this->updated();
        }

        // Lifecycle: compare against old instance (only in Patch phase)
        if ($phase === RenderPhase::Patch) {
            $oldInstance = self::$oldRegistry[$pathKey] ?? null;
            if ($oldInstance !== null && !$this->compare($oldInstance)) {
                return new NoOpDomNode();
            }
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
     * Call deleted() on components that were in the old tree but not the new tree.
     * Clears both registries afterwards.
     */
    public static function processDeleted(): void
    {
        $deletedKeys = array_diff_key(self::$oldRegistry, self::$newRegistry);
        foreach ($deletedKeys as $component) {
            $component->deleted();
        }
        self::$oldRegistry = [];
        self::$newRegistry = [];
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

        // Save global state
        foreach ($this->globalStateRefs as $key => $ref) {
            $resolved->saveState($key, $ref);
        }
    }
}
