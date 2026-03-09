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

    public function render(): DomNode
    {
        return $this->node('var')->children($this->content);
    }
}
