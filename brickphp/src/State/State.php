<?php

namespace BrickPHP\State;

/**
 * Base class for objects stored in component state.
 *
 * When a state ref is registered via `useState($ref, class: MySubclass::class)`,
 * the framework calls `MySubclass::deserialize($raw)` on each restored value before
 * assigning it back to the ref. This lets the class re-establish its concrete
 * type from raw deserialized data (e.g. an associative array left over from a
 * previous code shape) instead of crashing later when typed code touches it.
 *
 * Subclasses must accept either a self-instance (pass-through) or a primitive/
 * array form they know how to reconstruct from, and throw if the input is
 * unrecoverable. A throw triggers the framework's state-reset recovery path.
 */
abstract class State
{
    abstract public static function deserialize(mixed $raw): static;
}
