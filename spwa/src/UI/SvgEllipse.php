<?php

namespace Spwa\UI;

class SvgEllipse extends SvgElement
{
    public function __construct(
        protected float $cx,
        protected float $cy,
        protected float $rx,
        protected float $ry
    ) {
    }

    public function toNode(): Node
    {
        $node = Node::el('ellipse')
            ->attr('cx', (string)$this->cx)
            ->attr('cy', (string)$this->cy)
            ->attr('rx', (string)$this->rx)
            ->attr('ry', (string)$this->ry);
        $this->applyCommonAttrs($node);
        return $node;
    }
}
