<?php

namespace Spwa\UI;

/**
 * Table heading cell (th).
 */
class TableHeading extends UIElement
{
    protected ?UIElement $child = null;
    protected ?string $textContent = null;

    public function __construct()
    {
        $this->addStyle('px-4', ['padding-left' => '1rem', 'padding-right' => '1rem']);
        $this->addStyle('py-3', ['padding-top' => '0.75rem', 'padding-bottom' => '0.75rem']);
        $this->addStyle('text-left', ['text-align' => 'left']);
        $this->addStyle('font-semibold', ['font-weight' => '600']);
    }

    /**
     * Set cell content (UIElement or string).
     */
    public function content(UIElement|string $content): static
    {
        if ($content instanceof UIElement) {
            $this->child = $content;
        } else {
            $this->textContent = $content;
        }
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('th');

        if ($this->child !== null) {
            $node->children($this->child->render());
        } elseif ($this->textContent !== null) {
            $node->children($this->textContent);
        }

        return $node;
    }
}
