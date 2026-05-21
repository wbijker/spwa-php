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
class Position extends UIElementContent
{
    public function __construct(UIElement ...$children)
    {
        parent::__construct('div');
        $this->children = $children;
        $this->addStyle('absolute', ['position' => 'absolute']);
    }

    public function top(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('top'), ['top' => $value->getCssValue()]);
        return $this;
    }

    public function bottom(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('bottom'), ['bottom' => $value->getCssValue()]);
        return $this;
    }

    public function left(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('left'), ['left' => $value->getCssValue()]);
        return $this;
    }

    public function right(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('right'), ['right' => $value->getCssValue()]);
        return $this;
    }

    /**
     * Fill the entire stack (inset: 0). Renamed from fill() to avoid clashing
     * with UIElement::fill() (the SVG fill utility).
     */
    public function fillParent(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'inset-0', [
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
