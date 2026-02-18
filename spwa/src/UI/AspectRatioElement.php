<?php

namespace Spwa\UI;

/**
 * AspectRatio element that maintains a specific aspect ratio.
 * Similar to QuestPDF's AspectRatio element.
 *
 * Usage:
 *   UI::aspectRatio(16/9)
 *       ->children(UI::image("video-thumb.jpg"))
 *
 *   UI::aspectRatio()->square()
 *       ->children(UI::element()->background(Color::blue(500)))
 */
class AspectRatioElement extends Element
{
    public function __construct(?float $ratio = null)
    {
        parent::__construct('div');

        if ($ratio !== null) {
            $this->ratio($ratio);
        }
    }

    /**
     * Set aspect ratio as width/height (e.g., 16/9).
     */
    public function ratio(float $ratio): static
    {
        // Tailwind supports aspect-[value] for arbitrary values
        $this->addClass('aspect-[' . $ratio . ']');
        return $this;
    }

    /**
     * Set 1:1 square aspect ratio.
     */
    public function square(): static
    {
        $this->addClass('aspect-square');
        return $this;
    }

    /**
     * Set 16:9 video aspect ratio.
     */
    public function video(): static
    {
        $this->addClass('aspect-video');
        return $this;
    }

    /**
     * Set 4:3 aspect ratio.
     */
    public function standard(): static
    {
        $this->addClass('aspect-[4/3]');
        return $this;
    }

    /**
     * Set 3:2 photo aspect ratio.
     */
    public function photo(): static
    {
        $this->addClass('aspect-[3/2]');
        return $this;
    }

    /**
     * Set 21:9 ultrawide aspect ratio.
     */
    public function ultrawide(): static
    {
        $this->addClass('aspect-[21/9]');
        return $this;
    }

    /**
     * Set portrait aspect ratio (3:4).
     */
    public function portrait(): static
    {
        $this->addClass('aspect-[3/4]');
        return $this;
    }

    /**
     * Set auto aspect ratio (natural).
     */
    public function auto(): static
    {
        $this->addClass('aspect-auto');
        return $this;
    }
}
