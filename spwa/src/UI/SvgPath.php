<?php

namespace Spwa\UI;

class SvgPath extends SvgElement
{
    public function __construct(protected string $d)
    {
    }

    public function toNode(): Node
    {
        $node = Node::el('path')->attr('d', $this->d);
        $this->applyCommonAttrs($node);
        return $node;
    }
}
