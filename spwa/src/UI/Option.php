<?php

namespace Spwa\UI;

/**
 * Option element for select.
 */
class Option
{
    protected bool $selected = false;
    protected bool $disabled = false;

    public function __construct(
        protected string $label,
        protected ?string $value = null
    ) {
    }

    public function selected(bool $selected = true): static
    {
        $this->selected = $selected;
        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function toNode(): DomNode
    {
        $node = DomNode::el('option')->children($this->label);

        if ($this->value !== null) {
            $node->attr('value', $this->value);
        }

        if ($this->selected) {
            $node->attr('selected', 'selected');
        }

        if ($this->disabled) {
            $node->attr('disabled', 'disabled');
        }

        return $node;
    }
}
