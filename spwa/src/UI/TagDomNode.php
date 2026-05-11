<?php

namespace Spwa\UI;

use Spwa\Events\EventData;
use Spwa\State\StateManager;
use Spwa\UI\Css\CssStyle;
use Spwa\VNode\Component;
use Spwa\VNode\Patcher;

/**
 * Represents an element node in the DOM.
 * Base class for all UI elements.
 */
class TagDomNode extends DomNode
{
    /** Maps custom event names to their DOM event attribute equivalents */
    private const DOM_EVENT_MAP = [
        'upload' => 'change',
    ];

    /** @var (DomNode|string)[] */
    protected array $children = [];

    /** @var array<string, string> */
    protected array $attributes = [];

    /** @var array<string, array<string, string>> */
    protected array $styles = [];

    /** @var CssStyle[] */
    protected array $cssStyles = [];

    /** @var array<string, array{callback: callable, owner: ?Component}> */
    protected array $events = [];

    /** @var string[] Collected class names */
    protected array $classes = [];

    /** @var bool Whether this node has a bound value ref */
    private bool $hasBoundRef = false;

    /** @var mixed Reference to the bound component property */
    private mixed $boundRef = null;

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
     * Set the tag name.
     */
    public function setTag(string $tag): static
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * Set a key for efficient list diffing.
     */
    public function key(string $key): static
    {
        $this->key = $key;
        return $this;
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
     * Add a class with its CSS properties (legacy format).
     */
    protected function addStyle(string $class, array $css): void
    {
        $this->classes[] = $class;
        if (!empty($css)) {
            $this->styles[$class] = $css;
        }
    }

    /**
     * Add a CssStyle object.
     */
    protected function addCssStyle(CssStyle $style): void
    {
        $this->classes[] = $style->getClassName();
        $this->cssStyles[] = $style;
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
     * Add a CssStyle rule.
     */
    public function css(CssStyle $style): static
    {
        $this->addCssStyle($style);
        return $this;
    }

    /**
     * Add an event listener.
     */
    public function on(string $event, callable $callback, ?Component $owner = null): static
    {
        $this->events[$event] = ['callback' => $callback, 'owner' => $owner];
        return $this;
    }

    /**
     * Get all event listeners.
     * @return array<string, array{callback: callable, owner: ?Component}>
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * Set the owner component for all events.
     */
    public function setEventOwner(Component $owner): static
    {
        foreach ($this->events as $event => $data) {
            $this->events[$event]['owner'] = $owner;
        }
        return $this;
    }

    public function bindRef(mixed &$ref): void
    {
        $this->boundRef = &$ref;
        $this->hasBoundRef = true;
    }

    public function isBound(): bool
    {
        return $this->hasBoundRef;
    }

    public function hydrateBinding(string $value): void
    {
        if ($this->hasBoundRef) {
            $this->boundRef = $value;
        }
    }

    /**
     * Find all bound nodes in the tree and hydrate them from the bindings map.
     * @param array<string, string> $bindings Path string => value
     */
    public function hydrateBindings(array $bindings): void
    {
        $pathStr = implode(',', $this->path);
        if ($this->hasBoundRef && isset($bindings[$pathStr])) {
            $this->hydrateBinding($bindings[$pathStr]);
        }

        foreach ($this->children as $child) {
            if ($child instanceof TagDomNode) {
                $child->hydrateBindings($bindings);
            }
        }
    }

    /**
     * Add child nodes or text.
     */
    public function content(DomNode|string|null ...$children): static
    {
        foreach ($children as $child) {
            if ($child === null) {
                $this->children[] = new CommentDomNode();
            } elseif ($child instanceof DomNode) {
                $this->children[] = $child;
            } else {
                $this->children[] = new TextDomNode($child);
            }
        }
        return $this;
    }

    /**
     * Alias for content().
     */
    public function children(DomNode|string|null ...$children): static
    {
        return $this->content(...$children);
    }

    /**
     * Add raw HTML content without wrapping in TextDomNode.
     */
    public function rawContent(string $content): static
    {
        $this->children[] = new RawContent($content);
        return $this;
    }

    /**
     * Get all children.
     * @return DomNode[]
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
        foreach ($this->children as $index => $child) {
            $child->assignPaths([...$this->path, $index]);
        }
    }

    /**
     * Collect all styles from this node and descendants.
     * @return array<string, array<string, string>>
     */
    public function collectStyles(): array
    {
        $allStyles = $this->styles;

        // Include CssStyle objects in legacy format
        foreach ($this->cssStyles as $style) {
            $legacy = $style->toLegacy();
            $allStyles = array_merge($allStyles, $legacy);
        }

        foreach ($this->children as $child) {
            if ($child instanceof DomNode) {
                $allStyles = array_merge($allStyles, $child->collectStyles());
            }
        }

        return $allStyles;
    }

    /**
     * Collect CssStyle objects from this node and descendants.
     * @return CssStyle[]
     */
    public function collectCssStyles(): array
    {
        $allStyles = $this->cssStyles;

        foreach ($this->children as $child) {
            if ($child instanceof DomNode) {
                $allStyles = array_merge($allStyles, $child->collectCssStyles());
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

        if ($this->managed) {
            $attrHtml .= ' data-path="' . implode(',', $this->path) . '"';
        }

        if (!empty($allClasses)) {
            $attrHtml .= ' class="' . htmlspecialchars(implode(' ', array_unique($allClasses))) . '"';
        }
        foreach ($this->attributes as $name => $value) {
            if ($name === 'class') continue;
            $attrHtml .= ' ' . $name . '="' . htmlspecialchars($value) . '"';
        }

        if ($this->managed) {
            $pathStr = implode(',', $this->path);
            foreach ($this->events as $event => $callback) {
                $domEvent = self::DOM_EVENT_MAP[$event] ?? $event;
                $attrHtml .= ' on' . $domEvent . "=\"handleEvent(event, '" . $event . "', '" . $pathStr . "', this)\"";
            }
        }

        // Self-closing tags
        $selfClosing = ['img', 'br', 'hr', 'input', 'meta', 'link', 'area', 'base', 'col', 'embed', 'source', 'track', 'wbr'];
        if (in_array($this->tag, $selfClosing) && empty($this->children)) {
            return "<{$this->tag}{$attrHtml}>";
        }

        // Build children
        $childrenHtml = '';
        foreach ($this->children as $child) {
            $childrenHtml .= $child->toHtml();
        }

        return "<{$this->tag}{$attrHtml}>{$childrenHtml}</{$this->tag}>";
    }

    /**
     * Find a node by its path.
     * @param int[] $targetPath
     * @return DomNode|null
     */
    public function findByPath(array $targetPath): ?DomNode
    {
        if ($this->path === $targetPath) {
            return $this;
        }

        foreach ($this->children as $child) {
            if ($child instanceof DomNode) {
                $found = $child->findByPath($targetPath);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    public function countNodes(): int
    {
        $count = 1;
        foreach ($this->children as $child) {
            $count += $child->countNodes();
        }
        return $count;
    }

    /**
     * Execute an event handler if it exists.
     * @param string $event
     * @param StateManager|null $state The state manager for finalizing the owner component
     * @return bool Whether the event was handled
     */
    public function executeEvent(string $event, mixed $state = null, mixed $value = null): bool
    {
        if (isset($this->events[$event])) {
            $eventData = $this->events[$event];
            $typed = EventData::hydrate($event, $value);
            ($eventData['callback'])($typed);

            // Finalize the owner component if available
            if ($state !== null && $eventData['owner'] !== null) {
                $eventData['owner']->finalize($state);
            }

            return true;
        }
        return false;
    }

    /**
     * Compare this node with another and generate patches.
     */
    public function compare(DomNode $other, Patcher $patcher): void
    {
        // If other is not a TagDomNode or tag differs, replace entirely
        if (!$other instanceof TagDomNode || $this->tag !== $other->tag) {
            $patcher->replaceNode($this->path, $this);
            return;
        }

        // Compare attributes
        $thisAttrs = $this->attributes;
        $otherAttrs = $other->attributes;

        foreach ($thisAttrs as $name => $value) {
            if (!isset($otherAttrs[$name]) || $otherAttrs[$name] !== $value) {
                $patcher->setAttribute($this->path, $name, $value);
            }
        }

        foreach ($otherAttrs as $name => $value) {
            if (!isset($thisAttrs[$name])) {
                $patcher->removeAttribute($this->path, $name);
            }
        }

        // Compare classes
        $thisClasses = $this->classes;
        $otherClasses = $other->classes;
        if ($thisClasses !== $otherClasses) {
            $patcher->setAttribute($this->path, 'class', implode(' ', array_unique($thisClasses)));
        }

        // Compare children
        $this->compareChildren($other, $patcher);
    }

    /**
     * Check if children are keyed (all children have a non-null key).
     */
    private function childrenAreKeyed(): bool
    {
        if (empty($this->children)) {
            return false;
        }
        foreach ($this->children as $child) {
            if ($child->getKey() === null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Compare children using positional or keyed strategy.
     */
    private function compareChildren(TagDomNode $other, Patcher $patcher): void
    {
        $newKeyed = $this->childrenAreKeyed();
        $oldKeyed = $other->childrenAreKeyed();

        if ($newKeyed && $oldKeyed) {
            $this->compareChildrenKeyed($other, $patcher);
        } else {
            $this->compareChildrenPositional($other, $patcher);
        }
    }

    /**
     * Positional child comparison (no keys).
     */
    private function compareChildrenPositional(TagDomNode $other, Patcher $patcher): void
    {
        $thisCount = count($this->children);
        $otherCount = count($other->children);
        $commonCount = min($thisCount, $otherCount);

        // Delete removed children in reverse order
        for ($i = $otherCount - 1; $i >= $thisCount; $i--) {
            $patcher->deleteNode([...$this->path, $i]);
        }

        // Compare common children
        for ($i = 0; $i < $commonCount; $i++) {
            $this->children[$i]->compare($other->children[$i], $patcher);
        }

        // Insert new children
        for ($i = $otherCount; $i < $thisCount; $i++) {
            $patcher->insertNode([...$this->path, $i], $this->children[$i]);
        }
    }

    /**
     * Keyed child comparison using key matching.
     * Generates minimal insert/delete/update operations.
     */
    private function compareChildrenKeyed(TagDomNode $other, Patcher $patcher): void
    {
        // Build key→index maps
        $oldByKey = [];
        foreach ($other->children as $i => $child) {
            $oldByKey[$child->getKey()] = $i;
        }

        $newByKey = [];
        foreach ($this->children as $i => $child) {
            $newByKey[$child->getKey()] = $i;
        }

        // Delete old children not present in new (reverse order for stable indices)
        $toDelete = [];
        foreach ($other->children as $i => $child) {
            if (!isset($newByKey[$child->getKey()])) {
                $toDelete[] = $i;
            }
        }
        for ($i = count($toDelete) - 1; $i >= 0; $i--) {
            $patcher->removeAt($this->path, $toDelete[$i]);
        }

        // Build the old key order after deletions
        $oldKeysAfterDelete = [];
        foreach ($other->children as $i => $child) {
            $key = $child->getKey();
            if (isset($newByKey[$key])) {
                $oldKeysAfterDelete[] = $key;
            }
        }

        // Walk new children: insert new keys, update existing ones
        $oldPos = 0;
        foreach ($this->children as $newIdx => $newChild) {
            $key = $newChild->getKey();

            if (!isset($oldByKey[$key])) {
                // New key — insert at this position
                $patcher->insertAt($this->path, $newIdx, $newChild);
            } else {
                // Existing key — compare content in place
                $oldChild = $other->children[$oldByKey[$key]];
                // Re-assign path to the new position before comparing
                $newChild->assignPaths([...$this->path, $newIdx]);
                $oldChild->assignPaths([...$this->path, $newIdx]);
                $newChild->compare($oldChild, $patcher);
                $oldPos++;
            }
        }
    }
}
