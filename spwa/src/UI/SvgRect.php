<?php

namespace Spwa\UI;

class SvgRect extends SvgElement
{
    protected ?float $rx = null;
    protected ?float $ry = null;

    public function __construct(
        protected float $x,
        protected float $y,
        protected float $width,
        protected float $height
    ) {
    }

    public function rounded(float $rx, ?float $ry = null): static
    {
        $this->rx = $rx;
        $this->ry = $ry;
        return $this;
    }

    public function toNode(): Node
    {
        $node = Node::el('rect')
            ->attr('x', (string)$this->x)
            ->attr('y', (string)$this->y)
            ->attr('width', (string)$this->width)
            ->attr('height', (string)$this->height);

        if ($this->rx !== null) {
            $node->attr('rx', (string)$this->rx);
        }
        if ($this->ry !== null) {
            $node->attr('ry', (string)$this->ry);
        }

        $this->applyCommonAttrs($node);
        return $node;
    }
}
