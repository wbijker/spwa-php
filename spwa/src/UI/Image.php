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
        $this->addStyle('object-cover', ['object-fit' => 'cover']);
        return $this;
    }

    /**
     * Fit inside container (letterbox if needed).
     */
    public function contain(): static
    {
        $this->addStyle('object-contain', ['object-fit' => 'contain']);
        return $this;
    }

    /**
     * Stretch to fill container.
     */
    public function fill(): static
    {
        $this->addStyle('object-fill', ['object-fit' => 'fill']);
        return $this;
    }

    /**
     * No resizing.
     */
    public function original(): static
    {
        $this->addStyle('object-none', ['object-fit' => 'none']);
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
        $this->addStyle('object-center', ['object-position' => 'center']);
        return $this;
    }

    /**
     * Position at top.
     */
    public function positionTop(): static
    {
        $this->addStyle('object-top', ['object-position' => 'top']);
        return $this;
    }

    /**
     * Position at bottom.
     */
    public function positionBottom(): static
    {
        $this->addStyle('object-bottom', ['object-position' => 'bottom']);
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
        $this->addStyle('max-w-full', ['max-width' => '100%']);
        $this->addStyle('h-auto', ['height' => 'auto']);
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
