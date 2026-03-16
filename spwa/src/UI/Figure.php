<?php

namespace Spwa\UI;

use Spwa\VNode\VNode;

/**
 * Figure element with optional caption.
 */
class Figure extends UIElement
{
    protected DomNode|VNode|string|null $content = null;

    public function __construct()
    {
        parent::__construct('figure');
    }
    protected ?string $caption = null;
    protected bool $captionAbove = false;

    public function content(DomNode|VNode|string|null ...$children): static
    {
        $this->content = $children[0] ?? null;
        return $this;
    }

    public function caption(string $caption): static
    {
        $this->caption = $caption;
        return $this;
    }

    public function captionAbove(): static
    {
        $this->captionAbove = true;
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('figure');

        if ($this->captionAbove && $this->caption !== null) {
            $node->children(DomNode::el('figcaption')->children($this->caption));
        }

        if ($this->content instanceof UIElement) {
            $node->children($this->content->build());
        } elseif ($this->content instanceof DomNode) {
            $node->children($this->content);
        } elseif (is_string($this->content)) {
            $node->children($this->content);
        }

        if (!$this->captionAbove && $this->caption !== null) {
            $node->children(DomNode::el('figcaption')->children($this->caption));
        }

        return $node;
    }
}
