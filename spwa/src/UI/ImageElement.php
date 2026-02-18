<?php

namespace Spwa\UI;

/**
 * Image element.
 *
 * Usage:
 *   UI::image("photo.jpg", "Description")
 *       ->rounded(Unit::roundedLg())
 *       ->width(Unit::full())
 */
class ImageElement extends BaseStyledElement
{
    public function __construct(
        protected string $src,
        protected ?string $alt = null
    ) {
    }

    /**
     * Set image source.
     */
    public function src(string $src): static
    {
        $this->src = $src;
        return $this;
    }

    /**
     * Set alt text.
     */
    public function alt(string $alt): static
    {
        $this->alt = $alt;
        return $this;
    }

    /**
     * Make image responsive (max-width: 100%).
     */
    public function responsive(): static
    {
        $this->addClass('max-w-full');
        $this->addClass('h-auto');
        return $this;
    }

    /**
     * Cover the container.
     */
    public function cover(): static
    {
        $this->addClass('object-cover');
        return $this;
    }

    /**
     * Contain within the container.
     */
    public function contain(): static
    {
        $this->addClass('object-contain');
        return $this;
    }

    /**
     * Fill the container.
     */
    public function fill(): static
    {
        $this->addClass('object-fill');
        return $this;
    }

    /**
     * Set object position.
     */
    public function position(string $position): static
    {
        $this->addClass('object-' . $position);
        return $this;
    }

    public function render(): void
    {
        $classAttr = $this->buildClassAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';
        $altAttr = $this->alt !== null ? ' alt="' . htmlspecialchars($this->alt, ENT_QUOTES, 'UTF-8') . '"' : '';

        echo "<img src=\"" . htmlspecialchars($this->src, ENT_QUOTES, 'UTF-8') . "\"{$altAttr}{$classHtml} />";
    }
}
