<?php

namespace Spwa\UI;

use Spwa\VNode\VNode;

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
    /** @var (DomNode|VNode|string)[] */
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
    public function content(DomNode|VNode|string ...$children): static
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

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('a');

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
            if ($child instanceof UIElement) {
                $node->children($child->build());
            } elseif ($child instanceof DomNode) {
                $node->children($child);
            } elseif (is_string($child)) {
                $node->children($child);
            }
        }

        return $node;
    }
}
