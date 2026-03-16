<?php

namespace Spwa\UI;

/**
 * Hyperlink element — supports both text labels and rich content.
 *
 * Usage:
 *   UI::link("Visit site", "https://example.com")
 *   UI::link("https://example.com")->content(UI::image("icon.png"), UI::text("Click"))
 */
class Link extends UIElementContent
{
    protected ?string $href = null;
    protected bool $newTab = false;
    protected ?string $download = null;

    public function __construct(string $href, ?string $label = null)
    {
        parent::__construct('a');
        $this->href = $href;
        if ($label !== null) {
            $this->content($label);
        }
    }

    public function href(string $url): static
    {
        $this->href = $url;
        return $this;
    }

    public function newTab(): static
    {
        $this->newTab = true;
        return $this;
    }

    public function download(?string $filename = null): static
    {
        $this->download = $filename ?? '';
        return $this;
    }

    public function underline(): static
    {
        $this->addStyle('underline', ['text-decoration' => 'underline']);
        return $this;
    }

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
