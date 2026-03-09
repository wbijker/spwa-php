<?php

namespace Spwa\UI;

class SvgPolygon extends SvgElement
{
    public function __construct(protected string $points)
    {
    }

    public function toNode(): DomNode
    {
        $node = DomNode::el('polygon')->attr('points', $this->points);
        $this->applyCommonAttrs($node);
        return $node;
    }
}
