<?php

namespace Spwa\UI;

/**
 * Hyperlink element.
 *
 * Usage:
 *   UI::link("Visit site", "https://example.com")
 *       ->color(Color::blue(600))
 *       ->underline()
 */
class Link extends UIElement
{
    protected bool $newTab = false;

    public function __construct(
        protected string $label,
        protected string $href
    ) {
    }

    /**
     * Open in new tab.
     */
    public function newTab(): static
    {
        $this->newTab = true;
        return $this;
    }

    /**
     * Add underline.
     */
    public function underline(): static
    {
        $this->addStyle('underline', ['text-decoration' => 'underline']);
        return $this;
    }

    /**
     * Underline on hover only.
     */
    public function hoverUnderline(): static
    {
        $this->addStyle('hover:underline', ['text-decoration' => 'underline']);
        return $this;
    }

    /**
     * No underline.
     */
    public function noUnderline(): static
    {
        $this->addStyle('no-underline', ['text-decoration' => 'none']);
        return $this;
    }

    public function render(): string
    {
        $classAttr = $this->classAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';
        $targetAttr = $this->newTab ? ' target="_blank" rel="noopener noreferrer"' : '';

        return "<a href=\"{$this->href}\"{$targetAttr}{$classHtml}>" . htmlspecialchars($this->label) . "</a>";
    }
}
