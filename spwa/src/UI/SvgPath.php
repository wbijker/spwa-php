<?php

namespace Spwa\UI;

class SvgPath extends SvgElement
{
    public function __construct(protected string $d)
    {
    }

    public function toNode(): DomNode
    {
        $node = DomNode::el('path')->attr('d', $this->d);
        $this->applyCommonAttrs($node);
        return $node;
    }
}
