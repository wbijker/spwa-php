<?php

namespace Spwa\UI;

/**
 * Datalist element.
 */
class Datalist extends UIElement
{
    protected ?string $id = null;
    /** @var Option[] */
    protected array $options = [];

    public function id(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function options(Option ...$options): static
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('datalist');

        if ($this->id !== null) {
            $node->attr('id', $this->id);
        }

        foreach ($this->options as $option) {
            $node->children($option->toNode());
        }

        return $node;
    }
}
