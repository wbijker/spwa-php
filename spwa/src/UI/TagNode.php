<?php

namespace Spwa\UI;

/**
 * Represents an element node in the DOM.
 * Base class for all UI elements.
 */
class TagNode extends Node
{
    /** @var (Node|string)[] */
    protected array $children = [];

    /** @var array<string, string> */
    protected array $attributes = [];

    /** @var array<string, array<string, string>> */
    protected array $styles = [];

    /** @var array<string, callable> */
    protected array $events = [];

    /** @var string[] Collected class names */
    protected array $classes = [];

    public function __construct(
        protected string $tag
    ) {
    }

    /**
     * Get the tag name.
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * Set an attribute.
     */
    public function attr(string $name, string $value): static
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Set multiple attributes.
     * @param array<string, string> $attributes
     */
    public function attrs(array $attributes): static
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * Get all attributes.
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Add CSS classes.
     */
    public function class(string ...$classes): static
    {
        $this->classes = array_merge($this->classes, $classes);
        return $this;
    }

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
     * Add a style rule for CSS generation.
     * @param array<string, string> $css
     */
    public function style(string $className, array $css): static
    {
        $this->addStyle($className, $css);
        return $this;
    }

    /**
     * Add an event listener.
     */
    public function on(string $event, callable $callback): static
    {
        $this->events[$event] = $callback;
        return $this;
    }

    /**
     * Get all event listeners.
     * @return array<string, callable>
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * Add child nodes or text.
     */
    public function content(Node|string ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    /**
     * Get all children.
     * @return (Node|string)[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Assign paths to child nodes.
     */
    protected function assignChildPaths(): void
    {
        $index = 0;
        foreach ($this->children as $child) {
            if ($child instanceof Node) {
                $child->assignPaths([...$this->path, $index]);
            }
            $index++;
        }
    }

    /**
     * Collect all styles from this node and descendants.
     * @return array<string, array<string, string>>
     */
    public function collectStyles(): array
    {
        $allStyles = $this->styles;

        foreach ($this->children as $child) {
            if ($child instanceof Node) {
                $allStyles = array_merge($allStyles, $child->collectStyles());
            }
        }

        return $allStyles;
    }

    /**
     * Render to HTML string.
     */
    public function toHtml(): string
    {
        // Build class attribute
        $allClasses = $this->classes;
        if (isset($this->attributes['class'])) {
            $allClasses = array_merge(explode(' ', $this->attributes['class']), $allClasses);
        }

        // Build attributes
        $attrHtml = '';

        // Add data-path attribute
        $attrHtml .= ' data-path="' . implode(',', $this->path) . '"';

        if (!empty($allClasses)) {
            $attrHtml .= ' class="' . htmlspecialchars(implode(' ', array_unique($allClasses))) . '"';
        }
        foreach ($this->attributes as $name => $value) {
            if ($name === 'class') continue;
            $attrHtml .= ' ' . $name . '="' . htmlspecialchars($value) . '"';
        }

        // Self-closing tags
        $selfClosing = ['img', 'br', 'hr', 'input', 'meta', 'link', 'area', 'base', 'col', 'embed', 'source', 'track', 'wbr'];
        if (in_array($this->tag, $selfClosing) && empty($this->children)) {
            return "<{$this->tag}{$attrHtml}>";
        }

        // Build children
        $childrenHtml = '';
        foreach ($this->children as $child) {
            if ($child instanceof Node) {
                $childrenHtml .= $child->toHtml();
            } else {
                $childrenHtml .= htmlspecialchars($child);
            }
        }

        return "<{$this->tag}{$attrHtml}>{$childrenHtml}</{$this->tag}>";
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
     * Set padding on top only.
     */
    public function paddingTop(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('pt'), ['padding-top' => $value->getCssValue()]);
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
}
