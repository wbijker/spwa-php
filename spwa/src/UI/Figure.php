<?php

namespace Spwa\UI;

/**
 * Figure element with optional caption.
 */
class Figure extends UIElement
{
    protected ?UIElement $content = null;
    protected ?string $caption = null;
    protected bool $captionAbove = false;

    public function content(UIElement $content): static
    {
        $this->content = $content;
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

    public function render(): DomNode
    {
        $node = $this->node('figure');

        if ($this->captionAbove && $this->caption !== null) {
            $node->children(DomNode::el('figcaption')->children($this->caption));
        }

        if ($this->content !== null) {
            $node->children($this->content->render());
        }

        if (!$this->captionAbove && $this->caption !== null) {
            $node->children(DomNode::el('figcaption')->children($this->caption));
        }

        return $node;
    }
}
