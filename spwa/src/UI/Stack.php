<?php

namespace Spwa\UI;

use Spwa\VNode\VNode;

/**
 * Stack element — children are positioned on top of each other.
 * Only accepts Position elements as children.
 *
 * Usage:
 *   UI::stack()
 *       ->content(
 *           Stack::position(UI::image("bg.jpg"))->fill(),
 *           Stack::position(UI::text("Top-left"))->top(Unit::px(10))->left(Unit::px(10)),
 *           Stack::position(UI::text("Bottom-right"))->bottom(Unit::px(10))->right(Unit::px(10)),
 *       )
 */
class Stack extends UIElement
{
    /** @var Position[] */
    protected array $children = [];

    public function __construct()
    {
        $this->addStyle('relative', ['position' => 'relative']);
    }

    /**
     * Create a positioned child for this stack.
     */
    public static function position(UIElement ...$children): Position
    {
        return new Position(...$children);
    }

    /**
     * Add positioned children to the stack.
     */
    public function content(DomNode|VNode|string ...$children): static
    {
        foreach ($children as $child) {
            if ($child instanceof Position) {
                $this->children[] = $child;
            }
        }
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
