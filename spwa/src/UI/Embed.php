<?php

namespace Spwa\UI;

/**
 * Embed element for external content.
 */
class Embed extends UIElement
{
    protected ?string $type = null;

    public function __construct(protected string $src)
    {
    }

    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('embed')->attr('src', $this->src);

        if ($this->type !== null) {
            $node->attr('type', $this->type);
        }

        return $node;
    }
}
