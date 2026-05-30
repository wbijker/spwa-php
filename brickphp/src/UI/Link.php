<?php

namespace BrickPHP\UI;

/**
 * Hyperlink element — supports both text labels and rich content.
 *
 * Construct with either a raw URL string (plain anchor) or a BaseRoute (SPA
 * navigation, handled server-side via Router unless `external: true`).
 *
 *   UI::link("https://example.com", "Visit site")           plain
 *   Router::link(new ArticleRoute('hi'))->content('Read')   SPA-handled
 *   Router::link($extRoute, external: true)                 browser-handled
 */
class Link extends UIElementContent
{
    protected ?string $href = null;
    protected bool $newTab = false;
    protected ?string $download = null;

    public function __construct(BaseRoute|string $target, ?string $label = null, bool $external = false)
    {
        parent::__construct('a');

        if ($target instanceof BaseRoute) {
            $this->href = $target->toUrl();
            if (!$external) {
                // SPA navigation — server-side route swap. brick.js
                // preventDefaults anchor clicks with an Brick handler attached.
                $route = $target;
                $this->onClick(fn() => Router::navigate($route));
            }
        } else {
            $this->href = $target;
        }

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

    protected function applyAttributes(): void
    {
        if ($this->href !== null) {
            $this->attr('href', $this->href);
        }
        if ($this->newTab) {
            $this->attr('target', '_blank')->attr('rel', 'noopener noreferrer');
        }
        if ($this->download !== null) {
            $this->attr('download', $this->download);
        }
    }
}
