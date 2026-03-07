<?php

namespace Spwa\UI;

/**
 * Base class for all UI elements.
 * Contains methods for all common styling properties.
 */
abstract class UIElement
{
    /** @var string[] Collected class names */
    protected array $classes = [];

    /** @var array<string, array<string, string>> Collected styles: className => [property => value] */
    protected array $styles = [];

    /**
     * Add a class with its CSS properties.
     */
    protected function addStyle(string $class, array $css): void
    {
        $this->classes[] = $class;
        if (!empty($css)) {
            $this->styles[$class] = $css;
        }
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
     * Get all collected styles.
     * @return array<string, array<string, string>>
     */
    public function getStyles(): array
    {
        return $this->styles;
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
            $class = $color->withContext('bg');
            $this->addStyle($class, ['background-color' => $color->getValue()]);
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
            $class = $color->withContext('text');
            $this->addStyle($class, ['color' => $color->getValue()]);
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
        $class = $width === 1 ? 'border' : 'border-' . $width;
        $this->addStyle($class, ['border-width' => $width . 'px', 'border-style' => 'solid']);
        return $this;
    }

    /**
     * Set border color.
     */
    public function borderColor(Color ...$colors): static
    {
        foreach ($colors as $color) {
            $class = $color->withContext('border');
            $this->addStyle($class, ['border-color' => $color->getValue()]);
        }
        return $this;
    }

    /**
     * Dashed border style.
     */
    public function dashed(): static
    {
        $this->addStyle('border-dashed', ['border-style' => 'dashed']);
        return $this;
    }

    /**
     * Dotted border style.
     */
    public function dotted(): static
    {
        $this->addStyle('border-dotted', ['border-style' => 'dotted']);
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
            $class = $value->withContext('w');
            $this->addStyle($class, ['width' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set height.
     */
    public function height(Unit ...$values): static
    {
        foreach ($values as $value) {
            $class = $value->withContext('h');
            $this->addStyle($class, ['height' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set both width and height.
     */
    public function size(Unit ...$values): static
    {
        foreach ($values as $value) {
            $css = $value->getCssValue();
            $this->addStyle($value->withContext('w'), ['width' => $css]);
            $this->addStyle($value->withContext('h'), ['height' => $css]);
        }
        return $this;
    }

    /**
     * Set min width.
     */
    public function minWidth(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('min-w'), ['min-width' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set max width.
     */
    public function maxWidth(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('max-w'), ['max-width' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set min height.
     */
    public function minHeight(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('min-h'), ['min-height' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set max height.
     */
    public function maxHeight(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('max-h'), ['max-height' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Extend to fill available space.
     */
    public function extend(): static
    {
        $this->addStyle('w-full', ['width' => '100%']);
        $this->addStyle('h-full', ['height' => '100%']);
        return $this;
    }

    /**
     * Extend horizontally only.
     */
    public function extendHorizontal(): static
    {
        $this->addStyle('w-full', ['width' => '100%']);
        return $this;
    }

    /**
     * Extend vertically only.
     */
    public function extendVertical(): static
    {
        $this->addStyle('h-full', ['height' => '100%']);
        return $this;
    }

    /**
     * Shrink to content size.
     */
    public function shrink(): static
    {
        $this->addStyle('w-fit', ['width' => 'fit-content']);
        $this->addStyle('h-fit', ['height' => 'fit-content']);
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
            $this->addStyle($value->withContext('p'), ['padding' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set horizontal padding.
     */
    public function paddingHorizontal(Unit ...$values): static
    {
        foreach ($values as $value) {
            $css = $value->getCssValue();
            $this->addStyle($value->withContext('px'), ['padding-left' => $css, 'padding-right' => $css]);
        }
        return $this;
    }

    /**
     * Set vertical padding.
     */
    public function paddingVertical(Unit ...$values): static
    {
        foreach ($values as $value) {
            $css = $value->getCssValue();
            $this->addStyle($value->withContext('py'), ['padding-top' => $css, 'padding-bottom' => $css]);
        }
        return $this;
    }

    /**
     * Set margin on all sides.
     */
    public function margin(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('m'), ['margin' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set horizontal margin.
     */
    public function marginHorizontal(Unit ...$values): static
    {
        foreach ($values as $value) {
            $css = $value->getCssValue();
            $this->addStyle($value->withContext('mx'), ['margin-left' => $css, 'margin-right' => $css]);
        }
        return $this;
    }

    /**
     * Set vertical margin.
     */
    public function marginVertical(Unit ...$values): static
    {
        foreach ($values as $value) {
            $css = $value->getCssValue();
            $this->addStyle($value->withContext('my'), ['margin-top' => $css, 'margin-bottom' => $css]);
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
            $this->addStyle('rounded', ['border-radius' => '0.25rem']);
        } else {
            foreach ($values as $value) {
                $this->addStyle($value->withContext('rounded'), ['border-radius' => $value->getCssValue()]);
            }
        }
        return $this;
    }

    /**
     * Fully round corners (circle/pill shape).
     */
    public function roundedFull(): static
    {
        $this->addStyle('rounded-full', ['border-radius' => '9999px']);
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
        $this->addStyle($size->toClass(), ['box-shadow' => $size->getCssValue()]);
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
        $this->addStyle('opacity-' . $value, ['opacity' => (string)($value / 100)]);
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
        $this->addStyle('hidden', ['display' => 'none']);
        return $this;
    }

    /**
     * Show element.
     */
    public function visible(): static
    {
        $this->addStyle('visible', ['visibility' => 'visible']);
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
        $this->addStyle('overflow-hidden', ['overflow' => 'hidden']);
        return $this;
    }

    /**
     * Hide overflow (alias).
     */
    public function overflow(): static
    {
        return $this->clipContent();
    }

    /**
     * Allow scrolling.
     */
    public function scrollable(): static
    {
        $this->addStyle('overflow-auto', ['overflow' => 'auto']);
        return $this;
    }

    /**
     * Set padding on top only.
     */
    public function paddingTop(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('pt'), ['padding-top' => $value->getCssValue()]);
        }
        return $this;
    }

    // ============================================================
    // Cursor
    // ============================================================

    /**
     * Set cursor style.
     */
    public function cursor(Cursor ...$cursors): static
    {
        foreach ($cursors as $cursor) {
            $this->addStyle($cursor->toClass(), ['cursor' => $cursor->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set pointer cursor (shorthand).
     */
    public function clickable(): static
    {
        return $this->cursor(Cursor::Pointer);
    }

    /**
     * Set not-allowed cursor (shorthand).
     */
    public function notAllowed(): static
    {
        return $this->cursor(Cursor::NotAllowed);
    }

    // ============================================================
    // Transitions
    // ============================================================

    /**
     * Enable transitions.
     */
    public function animated(int $durationMs = 200): static
    {
        $this->addStyle('transition', ['transition-property' => 'all', 'transition-timing-function' => 'cubic-bezier(0.4, 0, 0.2, 1)']);
        $this->addStyle('duration-' . $durationMs, ['transition-duration' => $durationMs . 'ms']);
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
        $this->addStyle('rotate-' . $degrees, ['transform' => 'rotate(' . $degrees . 'deg)']);
        return $this;
    }

    /**
     * Scale element.
     */
    public function scale(int $percent): static
    {
        $this->addStyle('scale-' . $percent, ['transform' => 'scale(' . ($percent / 100) . ')']);
        return $this;
    }

    /**
     * Flip horizontally.
     */
    public function flipHorizontal(): static
    {
        $this->addStyle('-scale-x-100', ['transform' => 'scaleX(-1)']);
        return $this;
    }

    /**
     * Flip vertically.
     */
    public function flipVertical(): static
    {
        $this->addStyle('-scale-y-100', ['transform' => 'scaleY(-1)']);
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
        $this->addStyle('z-' . $index, ['z-index' => (string)$index]);
        return $this;
    }

    // ============================================================
    // Rendering
    // ============================================================

    /**
     * Render the element to a Node.
     */
    abstract public function render(): Node;

    /**
     * Create a node with this element's classes and styles applied.
     */
    protected function node(string $tag): Node
    {
        $node = Node::el($tag);

        if (!empty($this->classes)) {
            $node->class(...$this->classes);
        }

        if (!empty($this->styles)) {
            $node->styles($this->styles);
        }

        return $node;
    }

    /**
     * Render to HTML string.
     */
    public function toHtml(): string
    {
        return $this->render()->toHtml();
    }

    /**
     * Convert to string.
     */
    public function __toString(): string
    {
        return $this->toHtml();
    }

    /**
     * Collect styles from this element and all children.
     * @return array<string, array<string, string>>
     */
    public function collectStyles(): array
    {
        return $this->render()->collectStyles();
    }
}
