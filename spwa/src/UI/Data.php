<?php

namespace Spwa\UI;

/**
 * Data element with machine-readable value.
 */
class Data extends UIElement
{
    public function __construct(
        protected string $content,
        protected string $value
    ) {
    }

    public function render(): DomNode
    {
        return $this->node('data')
            ->attr('value', $this->value)
            ->children($this->content);
    }
}
