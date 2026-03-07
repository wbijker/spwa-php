<?php

namespace Spwa\UI;

class SvgPolyline extends SvgElement
{
    public function __construct(protected string $points)
    {
    }

    public function toNode(): Node
    {
        $node = Node::el('polyline')->attr('points', $this->points);
        $this->applyCommonAttrs($node);
        return $node;
    }
}
