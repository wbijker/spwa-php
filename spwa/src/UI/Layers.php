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

    public function render(): string
    {
        $classAttr = $this->classAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        $html = "<div{$classHtml}>";

        if ($this->primary !== null) {
            $html .= $this->primary->render();
        }

        foreach ($this->layers as $layer) {
            // Wrap each layer in absolute positioning
            $layerClasses = array_merge(['absolute', 'inset-0'], $layer->getClasses());
            $layerClassStr = implode(' ', $layerClasses);
            $html .= "<div class=\"{$layerClassStr}\">";
            $html .= $layer->render();
            $html .= "</div>";
        }

        $html .= "</div>";

        return $html;
    }

    public function collectStyles(): array
    {
        $styles = parent::collectStyles();

        // Add layer positioning styles
        $styles['absolute'] = ['position' => 'absolute'];
        $styles['inset-0'] = ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0'];

        // Collect styles from children
        if ($this->primary !== null) {
            $styles = array_merge($styles, $this->primary->collectStyles());
        }

        foreach ($this->layers as $layer) {
            $styles = array_merge($styles, $layer->collectStyles());
        }

        return $styles;
    }
}
