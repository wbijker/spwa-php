<?php

namespace Spwa\UI;

/**
 * Link element.
 *
 * Usage:
 *   UI::link("Click here", "/page")
 *       ->color(Color::blue(500), Color::blue(700)->hover())
 *       ->underline()
 */
class LinkElement extends BaseStyledElement
{
    public function __construct(
        protected string $text,
        protected string $href = '#'
    ) {
    }

    /**
     * Set link href.
     */
    public function href(string $href): static
    {
        $this->href = $href;
        return $this;
    }

    /**
     * Open link in new tab.
     */
    public function newTab(): static
    {
        return $this;
    }

    /**
     * Add underline on hover.
     */
    public function underline(): static
    {
        $this->addClass('underline');
        return $this;
    }

    /**
     * Add underline on hover only.
     */
    public function hoverUnderline(): static
    {
        $this->addClass('hover:underline');
        return $this;
    }

    /**
     * No underline.
     */
    public function noUnderline(): static
    {
        $this->addClass('no-underline');
        return $this;
    }

    /**
     * Set font weight.
     */
    public function weight(FontWeight $weight): static
    {
        $this->addStateValue($weight);
        return $this;
    }

    /**
     * Set font size.
     */
    public function size(FontSize $size): static
    {
        $this->addStateValue($size);
        return $this;
    }

    protected bool $targetBlank = false;

    public function render(): void
    {
        $classAttr = $this->buildClassAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';
        $targetAttr = $this->targetBlank ? ' target="_blank" rel="noopener noreferrer"' : '';

        echo "<a href=\"" . htmlspecialchars($this->href, ENT_QUOTES, 'UTF-8') . "\"{$classHtml}{$targetAttr}>";
        echo htmlspecialchars($this->text, ENT_QUOTES, 'UTF-8');
        echo "</a>";
    }
}
