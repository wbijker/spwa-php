<?php

namespace Spwa\UI;

/**
 * A positioned child within a Stack.
 * Wraps content in an absolutely positioned container.
 *
 * Usage:
 *   Stack::position(UI::text("overlay"))
 *       ->top(Unit::px(10))
 *       ->left(Unit::px(20))
 */
class Position extends UIElement
{
    /** @var UIElement[] */
    protected array $children = [];

    public function __construct(UIElement ...$children)
    {
        $this->children = $children;
        $this->addStyle('absolute', ['position' => 'absolute']);
    }

    public function top(Unit $value): static
    {
        $this->addStyle($value->withContext('top'), ['top' => $value->getCssValue()]);
        return $this;
    }

    public function bottom(Unit $value): static
    {
        $this->addStyle($value->withContext('bottom'), ['bottom' => $value->getCssValue()]);
        return $this;
    }

    public function left(Unit $value): static
    {
        $this->addStyle($value->withContext('left'), ['left' => $value->getCssValue()]);
        return $this;
    }

    public function right(Unit $value): static
    {
        $this->addStyle($value->withContext('right'), ['right' => $value->getCssValue()]);
        return $this;
    }

    /**
     * Fill the entire stack (inset: 0).
     */
    public function fill(): static
    {
        $this->addStyle('inset-0', [
            'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0',
        ]);
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('div');
        foreach ($this->children as $child) {
            $node->children($child->build());
        }
        return $node;
    }
}
