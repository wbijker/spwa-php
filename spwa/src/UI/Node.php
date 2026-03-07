<?php

namespace Spwa\UI;

/**
 * Represents a DOM node for rendering.
 * This is the low-level primitive that UI elements render to.
 */
class Node
{
    /** @var (Node|string)[] */
    protected array $children = [];

    /** @var array<string, string> */
    protected array $attributes = [];

    /** @var array<string, array<string, string>> */
    protected array $styles = [];

    protected string|int|null $key = null;

    /** @var array<string, callable> */
    protected array $events = [];

    public function __construct(
        protected string $tag,
    ) {
    }

    /**
     * Create an element node.
     */
    public static function el(string $tag): static
    {
        return new static($tag);
    }

    /**
     * Create a text node.
     */
    public static function text(string $content): static
    {
        $node = new static('');
        $node->children = [$content];
        return $node;
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
     * Add CSS classes.
     */
    public function class(string ...$classes): static
    {
        $existing = $this->attributes['class'] ?? '';
        $all = $existing ? explode(' ', $existing) : [];
        $all = array_merge($all, $classes);
        $this->attributes['class'] = implode(' ', array_unique($all));
        return $this;
    }

    /**
     * Add a style rule for CSS generation.
     * @param array<string, string> $css
     */
    public function style(string $className, array $css): static
    {
        $this->styles[$className] = $css;
        return $this;
    }

    /**
     * Add multiple style rules.
     * @param array<string, array<string, string>> $styles
     */
    public function styles(array $styles): static
    {
        $this->styles = array_merge($this->styles, $styles);
        return $this;
    }

    /**
     * Set a unique key for this node.
     */
    public function key(string|int $key): static
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get the node key.
     */
    public function getKey(): string|int|null
    {
        return $this->key;
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
    public function children(Node|string ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    /**
     * Get the tag name.
     */
    public function getTag(): string
    {
        return $this->tag;
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
     * Get all children.
     * @return (Node|string)[]
     */
    public function getChildren(): array
    {
        return $this->children;
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
        // Text node (no tag)
        if ($this->tag === '') {
            $html = '';
            foreach ($this->children as $child) {
                if ($child instanceof Node) {
                    $html .= $child->toHtml();
                } else {
                    $html .= htmlspecialchars($child);
                }
            }
            return $html;
        }

        // Build attributes
        $attrHtml = '';
        foreach ($this->attributes as $name => $value) {
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

    /**
     * Convert to string (alias for toHtml).
     */
    public function __toString(): string
    {
        return $this->toHtml();
    }
}
