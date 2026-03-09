<?php

namespace Spwa\UI;

/**
 * Param element for object.
 */
class Param
{
    public function __construct(
        protected string $name,
        protected string $value
    ) {
    }

    public function toNode(): DomNode
    {
        return DomNode::el('param')
            ->attr('name', $this->name)
            ->attr('value', $this->value);
    }
}
