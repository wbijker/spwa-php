<?php

namespace Spwa\UI;

/**
 * Stacked layers - items positioned on top of each other.
 * Inspired by QuestPDF Layers.
 *
 * Usage:
 *   UI::layers()
 *       ->primary(UI::image("background.jpg"))
 *       ->layer(UI::text("Overlay text"))
 */
class Layers extends UIElement
{
    protected ?UIElement $primary = null;
    /** @var UIElement[] */
    protected array $layers = [];

    public function __construct()
    {
        $this->addStyle('relative', ['position' => 'relative']);
    }

    /**
     * Set the primary/background layer.
     */
    public function primary(UIElement $element): static
    {
        $this->primary = $element;
        return $this;
    }

    /**
     * Add an overlay layer.
     */
    public function layer(UIElement $element): static
    {
        $this->layers[] = $element;
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('div');

        if ($this->primary !== null) {
            $node->children($this->primary->render());
        }

        foreach ($this->layers as $layer) {
            // Wrap each layer in absolute positioning
            $wrapper = Node::el('div')
                ->class('absolute', 'inset-0')
                ->style('absolute', ['position' => 'absolute'])
                ->style('inset-0', ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0'])
                ->children($layer->render());

            $node->children($wrapper);
        }

        return $node;
    }
}
