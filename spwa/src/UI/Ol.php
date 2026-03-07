<?php

namespace Spwa\UI;

/**
 * Ordered list element.
 */
class Ol extends UIElement
{
    /** @var Li[] */
    protected array $items = [];
    protected ?int $start = null;
    protected bool $reversed = false;
    protected ?string $type = null;

    public function items(Li ...$items): static
    {
        $this->items = array_merge($this->items, $items);
        return $this;
    }

    public function start(int $start): static
    {
        $this->start = $start;
        return $this;
    }

    public function reversed(bool $reversed = true): static
    {
        $this->reversed = $reversed;
        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('ol');

        if ($this->start !== null) {
            $node->attr('start', (string)$this->start);
        }

        if ($this->reversed) {
            $node->attr('reversed', 'reversed');
        }

        if ($this->type !== null) {
            $node->attr('type', $this->type);
        }

        foreach ($this->items as $item) {
            $node->children($item->render());
        }

        return $node;
    }
}
