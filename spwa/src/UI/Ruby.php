<?php

namespace Spwa\UI;

/**
 * Ruby annotation element.
 */
class Ruby extends UIElement
{
    protected string $base;
    protected string $annotation;

    public function __construct(string $base, string $annotation)
    {
        $this->base = $base;
        $this->annotation = $annotation;
    }

    public function build(): DomNode
    {
        return $this->dom()->setTag('ruby')
            ->children($this->base)
            ->children(DomNode::el('rp')->children('('))
            ->children(DomNode::el('rt')->children($this->annotation))
            ->children(DomNode::el('rp')->children(')'));
    }
}
