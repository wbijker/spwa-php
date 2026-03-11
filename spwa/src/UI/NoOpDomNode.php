<?php

namespace Spwa\UI;

use Spwa\VNode\Patcher;

/**
 * A DOM node that skips the diffing algorithm.
 * When compared, it does nothing - no patches are generated.
 */
class NoOpDomNode extends DomNode
{
    public function collectStyles(): array
    {
        return [];
    }

    public function toHtml(): string
    {
        return '';
    }

    public function compare(DomNode $other, Patcher $patcher): void
    {
        // No-op: skip all diffing
    }
}
