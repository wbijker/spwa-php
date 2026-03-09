<?php

namespace Spwa\UI;

/**
 * Anchor element for links with child content.
 * Unlike Link which only accepts a text label, Anchor can contain any UI elements.
 *
 * Usage:
 *   UI::a("https://example.com")
 *       ->content(
 *           UI::row()->gap(Unit::small())->content(
 *               UI::image("/icon.png"),
 *               UI::text("Click here")
 *           )
 *       )
 */
class Anchor extends UIElement
{
    protected ?string $href = null;
    protected bool $newTab = false;
    protected ?string $download = null;
    /** @var UIElement[] */
    protected array $children = [];

    public function __construct(?string $href = null)
    {
        $this->href = $href;
    }

    /**
     * Set the href attribute.
     */
    public function href(string $url): static
    {
        $this->href = $url;
        return $this;
    }

    /**
     * Add child elements.
     */
    public function content(UIElement ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
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
     * Make link downloadable.
     */
    public function download(?string $filename = null): static
    {
        $this->download = $filename ?? '';
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

    public function render(): DomNode
    {
        $node = $this->node('a');

        if ($this->href !== null) {
            $node->attr('href', $this->href);
        }

        if ($this->newTab) {
            $node->attr('target', '_blank')
                ->attr('rel', 'noopener noreferrer');
        }

        if ($this->download !== null) {
            $node->attr('download', $this->download);
        }

        foreach ($this->children as $child) {
            $node->children($child->render());
        }

        return $node;
    }
}
