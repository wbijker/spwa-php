<?php

namespace Spwa\UI;

/**
 * Inserted text element.
 */
class Ins extends UIElement
{
    protected ?string $cite = null;
    protected ?string $datetime = null;

    public function __construct(protected string $content)
    {
    }

    public function cite(string $cite): static
    {
        $this->cite = $cite;
        return $this;
    }

    public function datetime(string $datetime): static
    {
        $this->datetime = $datetime;
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('ins')->children($this->content);

        if ($this->cite !== null) {
            $node->attr('cite', $this->cite);
        }

        if ($this->datetime !== null) {
            $node->attr('datetime', $this->datetime);
        }

        return $node;
    }
}
