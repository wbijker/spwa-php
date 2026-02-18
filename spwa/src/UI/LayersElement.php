<?php

namespace Spwa\UI;

/**
 * Layers element for stacking content on top of each other.
 * Similar to QuestPDF's Layers element.
 *
 * Usage:
 *   UI::layers()
 *       ->primaryLayer(UI::image("bg.jpg"))
 *       ->layer(UI::text("Overlay text")->color(Color::white()))
 */
class LayersElement extends BaseStyledElement
{
    /** @var BaseElement[] */
    protected array $layers = [];
    protected ?BaseElement $primary = null;

    public function __construct()
    {
        $this->addClass('relative');
    }

    /**
     * Add the primary/main layer (determines size).
     */
    public function primaryLayer(BaseElement $element): static
    {
        $this->primary = $element;
        return $this;
    }

    /**
     * Alias for primaryLayer.
     */
    public function main(BaseElement $element): static
    {
        return $this->primaryLayer($element);
    }

    /**
     * Add an overlay layer.
     */
    public function layer(BaseElement $element): static
    {
        $this->layers[] = $element;
        return $this;
    }

    public function render(): void
    {
        $classAttr = $this->buildClassAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        echo "<div{$classHtml}>";

        // Primary layer (normal flow)
        if ($this->primary !== null) {
            $this->primary->render();
        }

        // Overlay layers (absolute positioned)
        foreach ($this->layers as $layer) {
            echo '<div class="absolute inset-0">';
            $layer->render();
            echo '</div>';
        }

        echo "</div>";
    }
}
