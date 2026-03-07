<?php

namespace Spwa\UI;

/**
 * Code element for inline code.
 */
class Code extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function render(): Node
    {
        return $this->node('code')->children($this->content);
    }
}
