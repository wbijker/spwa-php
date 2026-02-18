<?php

namespace Spwa\UI;

/**
 * Decoration element with before, content, and after sections.
 * Similar to QuestPDF's Decoration element.
 * Before and after sections repeat on every page (in print context).
 *
 * Usage:
 *   UI::decoration()
 *       ->before(UI::text("Header"))
 *       ->content(UI::rows()->children(...))
 *       ->after(UI::text("Footer"))
 */
class DecorationElement extends BaseStyledElement
{
    protected ?BaseElement $beforeElement = null;
    protected ?BaseElement $contentElement = null;
    protected ?BaseElement $afterElement = null;

    public function __construct()
    {
        $this->addClass('flex');
        $this->addClass('flex-col');
    }

    /**
     * Set the before section (appears above content).
     */
    public function before(BaseElement $element): static
    {
        $this->beforeElement = $element;
        return $this;
    }

    /**
     * Set the main content section.
     */
    public function content(BaseElement $element): static
    {
        $this->contentElement = $element;
        return $this;
    }

    /**
     * Set the after section (appears below content).
     */
    public function after(BaseElement $element): static
    {
        $this->afterElement = $element;
        return $this;
    }

    /**
     * Alias for before.
     */
    public function header(BaseElement $element): static
    {
        return $this->before($element);
    }

    /**
     * Alias for after.
     */
    public function footer(BaseElement $element): static
    {
        return $this->after($element);
    }

    public function render(): void
    {
        $classAttr = $this->buildClassAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        echo "<div{$classHtml}>";

        if ($this->beforeElement !== null) {
            echo '<div class="shrink-0">';
            $this->beforeElement->render();
            echo '</div>';
        }

        if ($this->contentElement !== null) {
            echo '<div class="grow">';
            $this->contentElement->render();
            echo '</div>';
        }

        if ($this->afterElement !== null) {
            echo '<div class="shrink-0">';
            $this->afterElement->render();
            echo '</div>';
        }

        echo "</div>";
    }
}
