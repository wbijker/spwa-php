<?php

namespace Spwa\VNode;

use Spwa\State\State;
use Spwa\State\StateManager;
use Spwa\UI\DomNode;
use Spwa\UI\NoOpDomNode;

/**
 * A component virtual node that can have state and lifecycle.
 */
abstract class Component extends VNode
{
    /** @var StateRef[] Registered state refs and their per-ref metadata */
    private array $stateRefs = [];

    /** @var array<string, mixed> References to global state variables keyed by path key */
    private array $globalStateRefs = [];

    /** @var StateManager|null Per-component state manager override */
    private ?StateManager $stateManager = null;

    /** @var StateManager|null The resolved state manager used during render */
    private ?StateManager $resolvedManager = null;

    /** @var bool Whether any state variable is bound to the component lifecycle */
    private bool $hasBoundState = false;

    /** @var bool Whether initialize() has been called */
    protected bool $initialized = false;

    /** @var array<string, IProvideConsume> Provided values keyed by IProvideConsume::key() */
    private static array $provided = [];

    /** @var array<string, Component> Components rendered during the Initial (old) phase, keyed by state key */
    private static array $oldRegistry = [];

    /** @var array<string, Component> Components rendered during the Patch (new) phase, keyed by state key */
    private static array $newRegistry = [];

    /** @var App|null The current App instance, set during render */
    private static ?App $currentApp = null;

    /**
     * Register a variable as state. Call in initialize().
     *
     * @param class-string<State>|null $class Optional. Subclass of `Spwa\State\State`
     *   to coerce restored values through `Class::deserialize()`. If `$ref` is an
     *   array at registration time, each element is formatted; otherwise the
     *   value itself is formatted.
     */
    protected function useState(
        mixed &$ref,
        ?StateManager $stateManager = null,
        StateLifecycle $lifecycle = StateLifecycle::Bound,
        ?string $class = null,
    ): void
    {
        if ($stateManager !== null) {
            $this->stateManager = $stateManager;
        }
        if ($lifecycle === StateLifecycle::Bound) {
            $this->hasBoundState = true;
        }
        $this->stateRefs[] = new StateRef($ref, $class, is_array($ref));
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
     * Called during render to allow components to register custom JS/CSS with the App.
     * Override to call $app->addJs() or $app->addCss().
     */
    protected function register(App $app): void
    {
    }

    /**
     * Decide whether this component needs to be re-rendered in the current
     * diff cycle. Receives the matching instance from the OLD render at the
     * same path — typically you compare a few state fields and short-circuit
     * when nothing relevant has changed.
     *
     * Return `false` to skip both rendering AND diffing for this subtree:
     * the framework substitutes a NoOpDomNode whose compare() is a no-op, so
     * the frontend DOM keeps the existing subtree from the previous render.
     *
     * Default delegates to `hasChanged($old)` — render iff any subclass
     * state or prop value differs from the OLD instance. Override if your
     * component depends on inputs the framework can't see (wall clock,
     * RNG, external data).
     *
     * Only consulted in RenderPhase::Patch (NEW tree during a POST).
     *
     * @param static $old The OLD component instance at the same path —
     *   always the same concrete class as `$this`, safe to narrow in
     *   overrides via `@param MySubclass $old` in the docblock.
     */
    protected function shouldRender(Component $old): bool
    {
        return $this->hasChanged($old);
    }

    /** @var string|null Serialized snapshot of state/props at the moment render() entered build() */
    private ?string $stateSnapshot = null;

    /**
     * True when any state/prop value on a subclass of Component differs
     * from `$old`. Comparison is done against a SNAPSHOT taken at render
     * time (before any event handler runs) — so in-place mutation of
     * state objects is correctly detected.
     *
     * Walks the class hierarchy from this component's class up to (but
     * not including) Component itself, collecting every declared instance
     * property except closures (which are never reliably comparable) and
     * the snapshot field itself. The collected values are `serialize()`d
     * into a string snapshot; hasChanged compares the two snapshot
     * strings.
     */
    public function hasChanged(Component $old): bool
    {
        if ($old::class !== static::class) {
            return true;
        }
        // No snapshot on either side → can't decide; render to be safe.
        if ($this->stateSnapshot === null || $old->stateSnapshot === null) {
            return true;
        }
        return $this->stateSnapshot !== $old->stateSnapshot;
    }

    /**
     * Capture the values of every state/prop declared on subclasses of
     * Component as a single serialized string. Closures are excluded.
     */
    private function captureStateSnapshot(): string
    {
        $values = [];
        for ($rc = new \ReflectionClass(static::class); $rc !== false && $rc->getName() !== self::class; $rc = $rc->getParentClass()) {
            foreach ($rc->getProperties() as $prop) {
                if ($prop->isStatic()) continue;
                if ($prop->getDeclaringClass()->getName() !== $rc->getName()) continue;

                $v = $prop->getValue($this);
                if ($v instanceof \Closure) continue;

                $values[$rc->getName() . '::' . $prop->getName()] = $v;
            }
        }
        return serialize($values);
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
        foreach ($this->stateRefs as $entry) {
            $state[] = $entry->ref;
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

        foreach ($this->stateRefs as $i => $entry) {
            $value = $values[$i];

            if ($entry->class !== null && is_subclass_of($entry->class, State::class)) {
                $class = $entry->class;
                if ($entry->isArray) {
                    $value = is_array($value)
                        ? array_map(fn($v) => $class::deserialize($v), $value)
                        : [];
                } else {
                    $value = $value === null ? null : $class::deserialize($value);
                }
            }

            $entry->ref = $value;
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
        $this->resolvedManager = $resolved;
        $pathKey = $this->getStateKey();
        $savedState = $resolved->getState($pathKey);
        $isNew = empty($savedState);
        if (!$isNew) {
            $this->setState($savedState);
        }

        // Snapshot state right after restore — this captures the "input"
        // to render() before any event handler can mutate it. shouldRender
        // compares NEW's snapshot vs OLD's snapshot to detect real change.
        $this->stateSnapshot = $this->captureStateSnapshot();

        // Track component by phase for lifecycle diffing
        if ($phase === RenderPhase::Initial || $phase === RenderPhase::DiffOld) {
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

        // Track current App instance for register() calls
        if ($this instanceof App) {
            self::$currentApp = $this;
        }

        // Lifecycle: register (allow components to inject JS/CSS)
        if (self::$currentApp !== null) {
            $this->register(self::$currentApp);
        }

        // Lifecycle: restored
        $this->restored();

        // Lifecycle: created or updated
        if ($isNew) {
            $this->created();
        } else {
            $this->updated();
        }

        // Memo hook — ask the component whether anything below it needs to
        // be rebuilt this cycle. False here skips both build() and the diff.
        if ($phase === RenderPhase::Patch) {
            $oldInstance = self::$oldRegistry[$pathKey] ?? null;
            if ($oldInstance !== null && !$this->shouldRender($oldInstance)) {
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
        foreach ($deletedKeys as $key => $component) {
            $component->deleted();

            // Remove bound state from storage
            if ($component->hasBoundState && $component->resolvedManager !== null) {
                $component->resolvedManager->removeState($key);
            }
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
