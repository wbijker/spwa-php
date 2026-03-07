<?php

namespace Spwa\UI;

/**
 * Bidirectional override element.
 */
class Bdo extends UIElement
{
    protected string $dir = 'ltr';

    public function __construct(protected string $content)
    {
    }

    public function ltr(): static
    {
        $this->dir = 'ltr';
        return $this;
    }

    public function rtl(): static
    {
        $this->dir = 'rtl';
        return $this;
    }

    public function render(): Node
    {
        return $this->node('bdo')
            ->attr('dir', $this->dir)
            ->children($this->content);
    }
}
