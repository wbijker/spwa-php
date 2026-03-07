<?php

namespace Spwa\UI;

class SvgLine extends SvgElement
{
    public function __construct(
        protected float $x1,
        protected float $y1,
        protected float $x2,
        protected float $y2
    ) {
    }

    public function toNode(): Node
    {
        $node = Node::el('line')
            ->attr('x1', (string)$this->x1)
            ->attr('y1', (string)$this->y1)
            ->attr('x2', (string)$this->x2)
            ->attr('y2', (string)$this->y2);
        $this->applyCommonAttrs($node);
        return $node;
    }
}
