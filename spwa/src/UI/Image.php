<?php

namespace Spwa\UI;

/**
 * Image element.
 *
 * Usage:
 *   UI::image("/photo.jpg", "Profile photo")
 *       ->size(Unit::size(48))
 *       ->rounded()
 */
class Image extends UIElement
{
    public function __construct(
        protected string $src,
        protected string $alt = ''
    ) {
    }

    // ============================================================
    // Object fit
    // ============================================================

    /**
     * Cover container (crop if needed).
     */
    public function cover(): static
    {
        $this->classes[] = 'object-cover';
        return $this;
    }

    /**
     * Fit inside container (letterbox if needed).
     */
    public function contain(): static
    {
        $this->classes[] = 'object-contain';
        return $this;
    }

    /**
     * Stretch to fill container.
     */
    public function fill(): static
    {
        $this->classes[] = 'object-fill';
        return $this;
    }

    /**
     * No resizing.
     */
    public function original(): static
    {
        $this->classes[] = 'object-none';
        return $this;
    }

    // ============================================================
    // Object position
    // ============================================================

    /**
     * Position at center.
     */
    public function positionCenter(): static
    {
        $this->classes[] = 'object-center';
        return $this;
    }

    /**
     * Position at top.
     */
    public function positionTop(): static
    {
        $this->classes[] = 'object-top';
        return $this;
    }

    /**
     * Position at bottom.
     */
    public function positionBottom(): static
    {
        $this->classes[] = 'object-bottom';
        return $this;
    }

    // ============================================================
    // Responsive behavior
    // ============================================================

    /**
     * Make responsive (max-width: 100%, height: auto).
     */
    public function responsive(): static
    {
        $this->classes[] = 'max-w-full';
        $this->classes[] = 'h-auto';
        return $this;
    }

    public function render(): string
    {
        $classAttr = $this->classAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';
        $altAttr = htmlspecialchars($this->alt);

        return "<img src=\"{$this->src}\" alt=\"{$altAttr}\"{$classHtml} />";
    }
}
