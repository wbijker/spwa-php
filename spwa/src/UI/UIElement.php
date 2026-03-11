<?php

namespace Spwa\UI;

use Spwa\State\StateManager;
use Spwa\VNode\Component;
use Spwa\VNode\Node;
use Spwa\VNode\RenderPhase;
use Spwa\VNode\VNode;

/**
 * Base class for all UI elements.
 * Extends Node with additional styling and layout capabilities.
 */
class UIElement extends Node
{
    /** @var TagDomNode */
    protected DomNode $domNode;

    /** @var (DomNode|VNode|string)[] Children to be rendered */
    protected array $children = [];

    /** @var Component|null The component that owns this element's events */
    protected ?Component $eventOwner = null;

    public function __construct(string $tag = 'div')
    {
        parent::__construct(new TagDomNode($tag));
    }

    /**
     * Render this UI element to a DOM node.
     * @param StateManager $state The state manager
     * @param VNode|null $parent The parent VNode
     * @param RenderPhase $phase The render phase (Initial or Patch)
     */
    public function render(StateManager $state, ?VNode $parent = null, RenderPhase $phase = RenderPhase::Initial): DomNode
    {
        $this->parent = $parent;
        // Only set path from parent if not already set (e.g., by setPath)
        if (empty($this->path)) {
            $this->path = $parent?->getPath() ?? [];
        }

        // Find the owning Component by traversing up the parent chain
        $this->eventOwner = $this->findOwningComponent($parent);

        // Set the event owner on the DOM node for all registered events
        if ($this->eventOwner !== null) {
            $this->domNode->setEventOwner($this->eventOwner);
        }

        // Render VNode children now that we have StateManager
        // Track child index for proper path assignment
        $domChildren = [];
        $index = 0;
        foreach ($this->children as $child) {
            if ($child instanceof VNode) {
                // Set the child's path before rendering
                $child->setPath([...$this->path, $index]);
                $domChildren[] = $child->render($state, $this, $phase);
            } else {
                $domChildren[] = $child;
            }
            $index++;
        }

        $this->domNode->content(...$domChildren);

        return $this->domNode->assignPaths($this->path);
    }

    /**
     * Find the nearest Component ancestor.
     */
    private function findOwningComponent(?VNode $node): ?Component
    {
        while ($node !== null) {
            if ($node instanceof Component) {
                return $node;
            }
            $node = $node->getParent();
        }
        return null;
    }

    // ============================================================
    // Delegated methods from TagDomNode
    // ============================================================

    protected function addStyle(string $class, array $css): void
    {
        $this->domNode->style($class, $css);
    }

    public function attr(string $name, string $value): static
    {
        $this->domNode->attr($name, $value);
        return $this;
    }

    public function attrs(array $attributes): static
    {
        $this->domNode->attrs($attributes);
        return $this;
    }

    public function class(string ...$classes): static
    {
        $this->domNode->class(...$classes);
        return $this;
    }

    public function style(string $className, array $css): static
    {
        $this->domNode->style($className, $css);
        return $this;
    }

    public function on(string $event, callable $callback): static
    {
        $this->domNode->on($event, $callback);
        return $this;
    }

    public function content(DomNode|VNode|string ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    // ============================================================
    // Background
    // ============================================================

    public function background(Color ...$colors): static
    {
        $this->domNode->background(...$colors);
        return $this;
    }

    // ============================================================
    // Text Color
    // ============================================================

    public function color(Color ...$colors): static
    {
        $this->domNode->color(...$colors);
        return $this;
    }

    // ============================================================
    // Border
    // ============================================================

    public function bordered(int $width = 1): static
    {
        $this->domNode->bordered($width);
        return $this;
    }

    public function borderColor(Color ...$colors): static
    {
        $this->domNode->borderColor(...$colors);
        return $this;
    }

    public function dashed(): static
    {
        $this->domNode->dashed();
        return $this;
    }

    public function dotted(): static
    {
        $this->domNode->dotted();
        return $this;
    }

    // ============================================================
    // Sizing
    // ============================================================

    public function width(Unit ...$values): static
    {
        $this->domNode->width(...$values);
        return $this;
    }

    public function height(Unit ...$values): static
    {
        $this->domNode->height(...$values);
        return $this;
    }

    public function size(Unit ...$values): static
    {
        $this->domNode->size(...$values);
        return $this;
    }

    public function minWidth(Unit ...$values): static
    {
        $this->domNode->minWidth(...$values);
        return $this;
    }

    public function maxWidth(Unit ...$values): static
    {
        $this->domNode->maxWidth(...$values);
        return $this;
    }

    public function minHeight(Unit ...$values): static
    {
        $this->domNode->minHeight(...$values);
        return $this;
    }

    public function maxHeight(Unit ...$values): static
    {
        $this->domNode->maxHeight(...$values);
        return $this;
    }

    public function extend(): static
    {
        $this->domNode->extend();
        return $this;
    }

    public function extendHorizontal(): static
    {
        $this->domNode->extendHorizontal();
        return $this;
    }

    public function extendVertical(): static
    {
        $this->domNode->extendVertical();
        return $this;
    }

    public function shrink(): static
    {
        $this->domNode->shrink();
        return $this;
    }

    // ============================================================
    // Spacing
    // ============================================================

    public function padding(Unit ...$values): static
    {
        $this->domNode->padding(...$values);
        return $this;
    }

    public function paddingHorizontal(Unit ...$values): static
    {
        $this->domNode->paddingHorizontal(...$values);
        return $this;
    }

    public function paddingVertical(Unit ...$values): static
    {
        $this->domNode->paddingVertical(...$values);
        return $this;
    }

    public function paddingTop(Unit ...$values): static
    {
        $this->domNode->paddingTop(...$values);
        return $this;
    }

    public function margin(Unit ...$values): static
    {
        $this->domNode->margin(...$values);
        return $this;
    }

    public function marginHorizontal(Unit ...$values): static
    {
        $this->domNode->marginHorizontal(...$values);
        return $this;
    }

    public function marginVertical(Unit ...$values): static
    {
        $this->domNode->marginVertical(...$values);
        return $this;
    }

    // ============================================================
    // Corners
    // ============================================================

    public function rounded(Unit ...$values): static
    {
        $this->domNode->rounded(...$values);
        return $this;
    }

    public function roundedFull(): static
    {
        $this->domNode->roundedFull();
        return $this;
    }

    // ============================================================
    // Shadow
    // ============================================================

    public function shadow(Shadow $size = Shadow::Medium): static
    {
        $this->domNode->shadow($size);
        return $this;
    }

    // ============================================================
    // Opacity
    // ============================================================

    public function opacity(int $value): static
    {
        $this->domNode->opacity($value);
        return $this;
    }

    // ============================================================
    // Visibility
    // ============================================================

    public function hidden(): static
    {
        $this->domNode->hidden();
        return $this;
    }

    public function visible(): static
    {
        $this->domNode->visible();
        return $this;
    }

    // ============================================================
    // Overflow
    // ============================================================

    public function clipContent(): static
    {
        $this->domNode->clipContent();
        return $this;
    }

    public function overflow(): static
    {
        $this->domNode->overflow();
        return $this;
    }

    public function scrollable(): static
    {
        $this->domNode->scrollable();
        return $this;
    }

    // ============================================================
    // Cursor
    // ============================================================

    public function cursor(Cursor ...$cursors): static
    {
        $this->domNode->cursor(...$cursors);
        return $this;
    }

    public function clickable(): static
    {
        $this->domNode->clickable();
        return $this;
    }

    public function notAllowed(): static
    {
        $this->domNode->notAllowed();
        return $this;
    }

    // ============================================================
    // Transitions
    // ============================================================

    public function animated(int $durationMs = 200): static
    {
        $this->domNode->animated($durationMs);
        return $this;
    }

    // ============================================================
    // Transforms
    // ============================================================

    public function rotate(int $degrees): static
    {
        $this->domNode->rotate($degrees);
        return $this;
    }

    public function scale(int $percent): static
    {
        $this->domNode->scale($percent);
        return $this;
    }

    public function flipHorizontal(): static
    {
        $this->domNode->flipHorizontal();
        return $this;
    }

    public function flipVertical(): static
    {
        $this->domNode->flipVertical();
        return $this;
    }

    // ============================================================
    // Z-Index / Layering
    // ============================================================

    public function layer(int $index): static
    {
        $this->domNode->layer($index);
        return $this;
    }
}
