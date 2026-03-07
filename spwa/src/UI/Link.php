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

    public function __construct(string $label, string $href)
    {
        parent::__construct('a');
        $this->content($label);
        $this->attr('href', $href);
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

    /**
     * Render to HTML string.
     */
    public function toHtml(): string
    {
        if ($this->newTab) {
            $this->attr('target', '_blank');
            $this->attr('rel', 'noopener noreferrer');
        }
        return parent::toHtml();
    }
}
