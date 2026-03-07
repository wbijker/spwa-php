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

    public function render(): Node
    {
        return $this->node('ruby')
            ->children($this->base)
            ->children(Node::el('rp')->children('('))
            ->children(Node::el('rt')->children($this->annotation))
            ->children(Node::el('rp')->children(')'));
    }
}
