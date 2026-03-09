<?php

namespace Spwa\UI;

/**
 * Optgroup element for select.
 */
class Optgroup
{
    protected bool $disabled = false;
    /** @var Option[] */
    protected array $options = [];

    public function __construct(protected string $label)
    {
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function options(Option ...$options): static
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function toNode(): DomNode
    {
        $node = DomNode::el('optgroup')->attr('label', $this->label);

        if ($this->disabled) {
            $node->attr('disabled', 'disabled');
        }

        foreach ($this->options as $option) {
            $node->children($option->toNode());
        }

        return $node;
    }
}
