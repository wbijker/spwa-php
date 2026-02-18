<?php

namespace Spwa\UI;

/**
 * Base class for all UI elements.
 * Contains methods for all common styling properties.
 */
abstract class UIElement
{
    /** @var string[] Collected classes */
    protected array $classes = [];

    /**
     * Add a Property value to the element.
     */
    protected function apply(Property $property): void
    {
        $this->classes[] = $property->toClass();
    }

    /**
     * Get all collected classes.
     * @return string[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * Build class attribute string.
     */
    protected function classAttribute(): string
    {
        return implode(' ', $this->classes);
    }

    // ============================================================
    // Background
    // ============================================================

    /**
     * Set background color.
     */
    public function background(Color ...$colors): static
    {
        foreach ($colors as $color) {
            $this->classes[] = $color->withContext('bg');
        }
        return $this;
    }

    // ============================================================
    // Text Color
    // ============================================================

    /**
     * Set text color.
     */
    public function color(Color ...$colors): static
    {
        foreach ($colors as $color) {
            $this->classes[] = $color->withContext('text');
        }
        return $this;
    }

    // ============================================================
    // Border
    // ============================================================

    /**
     * Add border.
     */
    public function bordered(int $width = 1): static
    {
        $this->classes[] = $width === 1 ? 'border' : 'border-' . $width;
        return $this;
    }

    /**
     * Set border color.
     */
    public function borderColor(Color ...$colors): static
    {
        foreach ($colors as $color) {
            $this->classes[] = $color->withContext('border');
        }
        return $this;
    }

    /**
     * Dashed border style.
     */
    public function dashed(): static
    {
        $this->classes[] = 'border-dashed';
        return $this;
    }

    /**
     * Dotted border style.
     */
    public function dotted(): static
    {
        $this->classes[] = 'border-dotted';
        return $this;
    }

    // ============================================================
    // Sizing
    // ============================================================

    /**
     * Set width.
     */
    public function width(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('w');
        }
        return $this;
    }

    /**
     * Set height.
     */
    public function height(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('h');
        }
        return $this;
    }

    /**
     * Set both width and height.
     */
    public function size(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('w');
            $this->classes[] = $value->withContext('h');
        }
        return $this;
    }

    /**
     * Set min width.
     */
    public function minWidth(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('min-w');
        }
        return $this;
    }

    /**
     * Set max width.
     */
    public function maxWidth(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('max-w');
        }
        return $this;
    }

    /**
     * Set min height.
     */
    public function minHeight(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('min-h');
        }
        return $this;
    }

    /**
     * Set max height.
     */
    public function maxHeight(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('max-h');
        }
        return $this;
    }

    /**
     * Extend to fill available space.
     */
    public function extend(): static
    {
        $this->classes[] = 'w-full';
        $this->classes[] = 'h-full';
        return $this;
    }

    /**
     * Extend horizontally only.
     */
    public function extendHorizontal(): static
    {
        $this->classes[] = 'w-full';
        return $this;
    }

    /**
     * Extend vertically only.
     */
    public function extendVertical(): static
    {
        $this->classes[] = 'h-full';
        return $this;
    }

    /**
     * Shrink to content size.
     */
    public function shrink(): static
    {
        $this->classes[] = 'w-fit';
        $this->classes[] = 'h-fit';
        return $this;
    }

    // ============================================================
    // Spacing
    // ============================================================

    /**
     * Set padding on all sides.
     */
    public function padding(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('p');
        }
        return $this;
    }

    /**
     * Set horizontal padding.
     */
    public function paddingHorizontal(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('px');
        }
        return $this;
    }

    /**
     * Set vertical padding.
     */
    public function paddingVertical(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('py');
        }
        return $this;
    }

    /**
     * Set margin on all sides.
     */
    public function margin(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('m');
        }
        return $this;
    }

    /**
     * Set horizontal margin.
     */
    public function marginHorizontal(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('mx');
        }
        return $this;
    }

    /**
     * Set vertical margin.
     */
    public function marginVertical(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('my');
        }
        return $this;
    }

    // ============================================================
    // Corners
    // ============================================================

    /**
     * Round corners.
     */
    public function rounded(Unit ...$values): static
    {
        if (empty($values)) {
            $this->classes[] = 'rounded';
        } else {
            foreach ($values as $value) {
                $this->classes[] = $value->withContext('rounded');
            }
        }
        return $this;
    }

    /**
     * Fully round corners (circle/pill shape).
     */
    public function roundedFull(): static
    {
        $this->classes[] = 'rounded-full';
        return $this;
    }

    // ============================================================
    // Shadow
    // ============================================================

    /**
     * Add shadow.
     */
    public function shadow(Shadow $size = Shadow::Medium): static
    {
        $this->classes[] = $size->toClass();
        return $this;
    }

    // ============================================================
    // Opacity
    // ============================================================

    /**
     * Set opacity (0-100).
     */
    public function opacity(int $value): static
    {
        $this->classes[] = 'opacity-' . $value;
        return $this;
    }

    // ============================================================
    // Visibility
    // ============================================================

    /**
     * Hide element.
     */
    public function hidden(): static
    {
        $this->classes[] = 'hidden';
        return $this;
    }

    /**
     * Show element.
     */
    public function visible(): static
    {
        $this->classes[] = 'visible';
        return $this;
    }

    // ============================================================
    // Overflow
    // ============================================================

    /**
     * Hide overflow.
     */
    public function clipContent(): static
    {
        $this->classes[] = 'overflow-hidden';
        return $this;
    }

    /**
     * Allow scrolling.
     */
    public function scrollable(): static
    {
        $this->classes[] = 'overflow-auto';
        return $this;
    }

    // ============================================================
    // Cursor
    // ============================================================

    /**
     * Set pointer cursor.
     */
    public function clickable(): static
    {
        $this->classes[] = 'cursor-pointer';
        return $this;
    }

    /**
     * Set not-allowed cursor.
     */
    public function notAllowed(): static
    {
        $this->classes[] = 'cursor-not-allowed';
        return $this;
    }

    // ============================================================
    // Transitions
    // ============================================================

    /**
     * Enable transitions.
     */
    public function animated(int $durationMs = 200): static
    {
        $this->classes[] = 'transition';
        $this->classes[] = 'duration-' . $durationMs;
        return $this;
    }

    // ============================================================
    // Transforms
    // ============================================================

    /**
     * Rotate element.
     */
    public function rotate(int $degrees): static
    {
        $this->classes[] = 'rotate-' . $degrees;
        return $this;
    }

    /**
     * Scale element.
     */
    public function scale(int $percent): static
    {
        $this->classes[] = 'scale-' . $percent;
        return $this;
    }

    /**
     * Flip horizontally.
     */
    public function flipHorizontal(): static
    {
        $this->classes[] = '-scale-x-100';
        return $this;
    }

    /**
     * Flip vertically.
     */
    public function flipVertical(): static
    {
        $this->classes[] = '-scale-y-100';
        return $this;
    }

    // ============================================================
    // Z-Index / Layering
    // ============================================================

    /**
     * Set z-index.
     */
    public function layer(int $index): static
    {
        $this->classes[] = 'z-' . $index;
        return $this;
    }

    // ============================================================
    // Rendering
    // ============================================================

    /**
     * Render the element to HTML.
     */
    abstract public function render(): string;
}
