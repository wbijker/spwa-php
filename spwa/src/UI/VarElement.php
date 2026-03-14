<?php

namespace Spwa\UI;

/**
 * Variable element.
 */
class VarElement extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function build(): DomNode
    {
        return $this->dom()->setTag('var')->children($this->content);
    }
}
