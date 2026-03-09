<?php

namespace Spwa\UI;

/**
 * Description list element.
 */
class Dl extends UIElement
{
    /** @var (Dt|Dd)[] */
    protected array $items = [];

    public function items(Dt|Dd ...$items): static
    {
        $this->items = array_merge($this->items, $items);
        return $this;
    }

    /**
     * Add a term-description pair.
     */
    public function pair(string $term, string|UIElement $description): static
    {
        $this->items[] = new Dt($term);
        $this->items[] = new Dd($description);
        return $this;
    }

    public function render(): DomNode
    {
        $node = $this->node('dl');

        foreach ($this->items as $item) {
            $node->children($item->render());
        }

        return $node;
    }
}
