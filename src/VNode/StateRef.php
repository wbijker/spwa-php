<?php

namespace BrickPHP\VNode;

/**
 * One entry in a component's state registration list.
 *
 * `$ref` is bound by reference to the underlying property (e.g. `$this->todos`)
 * so reads see live values and writes assign back through to the property.
 * `$class` and `$isArray` are sampled at registration time and drive the
 * format-based coercion in Component::setState().
 */
final class StateRef
{
    public mixed $ref;

    public function __construct(
        mixed &$ref,
        public ?string $class,
        public bool $isArray,
    ) {
        $this->ref =& $ref;
    }
}
