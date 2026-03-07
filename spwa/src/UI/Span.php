<?php

namespace Spwa\UI;

/**
 * Generic span element.
 */
class Span extends UIElement
{
    public function __construct(Node|string|null $content = null)
    {
        parent::__construct('span');
        if ($content !== null) {
            $this->content($content);
        }
    }
}
