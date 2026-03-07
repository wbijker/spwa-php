<?php

namespace Spwa\UI;

/**
 * Time element.
 */
class Time extends UIElement
{
    protected ?string $datetime = null;

    public function __construct(protected string $content)
    {
    }

    public function datetime(string $datetime): static
    {
        $this->datetime = $datetime;
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('time')->children($this->content);

        if ($this->datetime !== null) {
            $node->attr('datetime', $this->datetime);
        }

        return $node;
    }
}
