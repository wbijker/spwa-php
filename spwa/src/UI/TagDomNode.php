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

    /**
     * Add child nodes or text.
     */
    public function content(DomNode|string ...$children): static
    {
        foreach ($children as $child) {
            $this->children[] = $child instanceof DomNode ? $child : new TextDomNode($child);
        }
        return $this;
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
                $attrHtml .= ' on' . $event . "=\"handleEvent(event, '" . $event . "', '" . $pathStr . "', this)\"";
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

    /**
     * Add border on top only.
     */
    public function borderTop(int $width = 1): static
    {
        $this->addStyle('border-t-' . $width, ['border-top-width' => $width . 'px', 'border-top-style' => 'solid']);
        return $this;
    }

    /**
     * Add border on bottom only.
     */
    public function borderBottom(int $width = 1): static
    {
        $this->addStyle('border-b-' . $width, ['border-bottom-width' => $width . 'px', 'border-bottom-style' => 'solid']);
        return $this;
    }

    /**
     * Remove all borders.
     */
    public function borderNone(): static
    {
        $this->addStyle('border-none', ['border' => 'none']);
        return $this;
    }

    /**
     * Remove outline.
     */
    public function outlineNone(): static
    {
        $this->addStyle('outline-none', ['outline' => 'none']);
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
    // Typography
    // ============================================================

    /**
     * Set font size.
     */
    public function fontSize(FontSize $size): static
    {
        $this->addStyle($size->toClass(), ['font-size' => $size->getCssValue()]);
        return $this;
    }

    /**
     * Set font weight.
     */
    public function weight(FontWeight $weight): static
    {
        $this->addStyle($weight->toClass(), ['font-weight' => $weight->getCssValue()]);
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
    // Flex item properties
    // ============================================================

    /**
     * Allow element to grow to fill available space.
     */
    public function grow(int $factor = 1): static
    {
        $this->addStyle('grow-' . $factor, ['flex-grow' => (string)$factor]);
        return $this;
    }

    /**
     * Prevent element from shrinking.
     */
    public function noShrink(): static
    {
        $this->addStyle('shrink-0', ['flex-shrink' => '0']);
        return $this;
    }

    // ============================================================
    // Position
    // ============================================================

    /**
     * Set position to relative.
     */
    public function relative(): static
    {
        $this->addStyle('relative', ['position' => 'relative']);
        return $this;
    }

    /**
     * Set position to absolute.
     */
    public function absolute(): static
    {
        $this->addStyle('absolute', ['position' => 'absolute']);
        return $this;
    }

    /**
     * Set top offset.
     */
    public function offsetTop(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('top'), ['top' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set left offset.
     */
    public function offsetLeft(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('left'), ['left' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set right offset.
     */
    public function offsetRight(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('right'), ['right' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set bottom offset.
     */
    public function offsetBottom(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('bottom'), ['bottom' => $value->getCssValue()]);
        }
        return $this;
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
