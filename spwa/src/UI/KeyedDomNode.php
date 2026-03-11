<?php

namespace Spwa\UI;

/**
 * A DomNode wrapper that requires a key for list diffing.
 */
class KeyedDomNode
{
    public function __construct(
        public readonly string|int $key,
        public readonly DomNode $node
    ) {}
}
