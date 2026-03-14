<?php

namespace Spwa\UI;

use Spwa\Events\AnimationEvent;
use Spwa\Events\ClipboardEvent;
use Spwa\Events\DragEvent;
use Spwa\Events\InputEvent;
use Spwa\Events\KeyboardEvent;
use Spwa\Events\MediaEvent;
use Spwa\Events\MouseEvent;
use Spwa\Events\PointerEvent;
use Spwa\Events\ResizeEvent;
use Spwa\Events\ScrollEvent;
use Spwa\Events\TouchEvent;
use Spwa\Events\TransitionEvent;
use Spwa\Events\WheelEvent;
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
     * Get the domNode, initializing lazily if the constructor was skipped.
     */
    protected function dom(): TagDomNode
    {
        if (!isset($this->domNode)) {
            $this->domNode = new TagDomNode('div');
        }
        return $this->domNode;
    }

    /**
     * Build the DomNode for this element (without VNode lifecycle).
     * Subclasses override to provide custom DOM building.
     * Base implementation builds from domNode and children.
     */
    public function build(): DomNode
    {
        $domChildren = [];
        foreach ($this->children as $child) {
            if ($child instanceof UIElement) {
                $domChildren[] = $child->build();
            } elseif ($child instanceof DomNode) {
                $domChildren[] = $child;
            } elseif (is_string($child)) {
                $domChildren[] = $child;
            }
        }

        $this->dom()->content(...$domChildren);
        return $this->domNode;
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
            $this->dom()->setEventOwner($this->eventOwner);
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
            } elseif ($child instanceof DomNode) {
                $domChildren[] = $child;
            } elseif (is_string($child)) {
                $domChildren[] = $child;
            }
            $index++;
        }

        $this->dom()->content(...$domChildren);

        return $this->dom()->assignPaths($this->path);
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
        $this->dom()->style($class, $css);
    }

    public function key(string $key): static
    {
        $this->dom()->key($key);
        return $this;
    }

    public function attr(string $name, string $value): static
    {
        $this->dom()->attr($name, $value);
        return $this;
    }

    public function attrs(array $attributes): static
    {
        $this->dom()->attrs($attributes);
        return $this;
    }

    public function class(string ...$classes): static
    {
        $this->dom()->class(...$classes);
        return $this;
    }

    public function style(string $className, array $css): static
    {
        $this->dom()->style($className, $css);
        return $this;
    }

    public function on(string $event, callable $callback): static
    {
        $this->dom()->on($event, $callback);
        return $this;
    }

    // ============================================================
    // Mouse Events
    // ============================================================

    /** @param callable(MouseEvent): void $callback */
    public function onClick(callable $callback): static { return $this->on('click', $callback); }
    /** @param callable(MouseEvent): void $callback */
    public function onDblClick(callable $callback): static { return $this->on('dblclick', $callback); }
    /** @param callable(MouseEvent): void $callback */
    public function onMouseDown(callable $callback): static { return $this->on('mousedown', $callback); }
    /** @param callable(MouseEvent): void $callback */
    public function onMouseUp(callable $callback): static { return $this->on('mouseup', $callback); }
    /** @param callable(MouseEvent): void $callback */
    public function onMouseOver(callable $callback): static { return $this->on('mouseover', $callback); }
    /** @param callable(MouseEvent): void $callback */
    public function onMouseOut(callable $callback): static { return $this->on('mouseout', $callback); }
    /** @param callable(MouseEvent): void $callback */
    public function onMouseEnter(callable $callback): static { return $this->on('mouseenter', $callback); }
    /** @param callable(MouseEvent): void $callback */
    public function onMouseLeave(callable $callback): static { return $this->on('mouseleave', $callback); }
    /** @param callable(MouseEvent): void $callback */
    public function onMouseMove(callable $callback): static { return $this->on('mousemove', $callback); }
    /** @param callable(MouseEvent): void $callback */
    public function onContextMenu(callable $callback): static { return $this->on('contextmenu', $callback); }

    // ============================================================
    // Keyboard Events
    // ============================================================

    /** @param callable(KeyboardEvent): void $callback */
    public function onKeyDown(callable $callback): static { return $this->on('keydown', $callback); }
    /** @param callable(KeyboardEvent): void $callback */
    public function onKeyUp(callable $callback): static { return $this->on('keyup', $callback); }

    // ============================================================
    // Form / Input Events
    // ============================================================

    /** @param callable(InputEvent): void $callback */
    public function onChange(callable $callback): static { return $this->on('change', $callback); }
    /** @param callable(InputEvent): void $callback */
    public function onInput(callable $callback): static { return $this->on('input', $callback); }
    /** @param callable(): void $callback */
    public function onSubmit(callable $callback): static { return $this->on('submit', $callback); }
    /** @param callable(): void $callback */
    public function onReset(callable $callback): static { return $this->on('reset', $callback); }
    /** @param callable(): void $callback */
    public function onFocus(callable $callback): static { return $this->on('focus', $callback); }
    /** @param callable(): void $callback */
    public function onBlur(callable $callback): static { return $this->on('blur', $callback); }
    /** @param callable(): void $callback */
    public function onFocusIn(callable $callback): static { return $this->on('focusin', $callback); }
    /** @param callable(): void $callback */
    public function onFocusOut(callable $callback): static { return $this->on('focusout', $callback); }
    /** @param callable(InputEvent): void $callback */
    public function onSelect(callable $callback): static { return $this->on('select', $callback); }
    /** @param callable(InputEvent): void $callback */
    public function onInvalid(callable $callback): static { return $this->on('invalid', $callback); }

    // ============================================================
    // Touch Events
    // ============================================================

    /** @param callable(TouchEvent): void $callback */
    public function onTouchStart(callable $callback): static { return $this->on('touchstart', $callback); }
    /** @param callable(TouchEvent): void $callback */
    public function onTouchEnd(callable $callback): static { return $this->on('touchend', $callback); }
    /** @param callable(TouchEvent): void $callback */
    public function onTouchMove(callable $callback): static { return $this->on('touchmove', $callback); }
    /** @param callable(TouchEvent): void $callback */
    public function onTouchCancel(callable $callback): static { return $this->on('touchcancel', $callback); }

    // ============================================================
    // Pointer Events
    // ============================================================

    /** @param callable(PointerEvent): void $callback */
    public function onPointerDown(callable $callback): static { return $this->on('pointerdown', $callback); }
    /** @param callable(PointerEvent): void $callback */
    public function onPointerUp(callable $callback): static { return $this->on('pointerup', $callback); }
    /** @param callable(PointerEvent): void $callback */
    public function onPointerMove(callable $callback): static { return $this->on('pointermove', $callback); }
    /** @param callable(PointerEvent): void $callback */
    public function onPointerOver(callable $callback): static { return $this->on('pointerover', $callback); }
    /** @param callable(PointerEvent): void $callback */
    public function onPointerOut(callable $callback): static { return $this->on('pointerout', $callback); }
    /** @param callable(PointerEvent): void $callback */
    public function onPointerEnter(callable $callback): static { return $this->on('pointerenter', $callback); }
    /** @param callable(PointerEvent): void $callback */
    public function onPointerLeave(callable $callback): static { return $this->on('pointerleave', $callback); }
    /** @param callable(PointerEvent): void $callback */
    public function onPointerCancel(callable $callback): static { return $this->on('pointercancel', $callback); }

    // ============================================================
    // Drag & Drop Events
    // ============================================================

    /** @param callable(DragEvent): void $callback */
    public function onDragStart(callable $callback): static { return $this->on('dragstart', $callback); }
    /** @param callable(DragEvent): void $callback */
    public function onDrag(callable $callback): static { return $this->on('drag', $callback); }
    /** @param callable(DragEvent): void $callback */
    public function onDragEnd(callable $callback): static { return $this->on('dragend', $callback); }
    /** @param callable(DragEvent): void $callback */
    public function onDragEnter(callable $callback): static { return $this->on('dragenter', $callback); }
    /** @param callable(DragEvent): void $callback */
    public function onDragLeave(callable $callback): static { return $this->on('dragleave', $callback); }
    /** @param callable(DragEvent): void $callback */
    public function onDragOver(callable $callback): static { return $this->on('dragover', $callback); }
    /** @param callable(DragEvent): void $callback */
    public function onDrop(callable $callback): static { return $this->on('drop', $callback); }

    // ============================================================
    // Clipboard Events
    // ============================================================

    /** @param callable(ClipboardEvent): void $callback */
    public function onCopy(callable $callback): static { return $this->on('copy', $callback); }
    /** @param callable(ClipboardEvent): void $callback */
    public function onCut(callable $callback): static { return $this->on('cut', $callback); }
    /** @param callable(ClipboardEvent): void $callback */
    public function onPaste(callable $callback): static { return $this->on('paste', $callback); }

    // ============================================================
    // Scroll & Wheel Events
    // ============================================================

    /** @param callable(ScrollEvent): void $callback */
    public function onScroll(callable $callback): static { return $this->on('scroll', $callback); }
    /** @param callable(WheelEvent): void $callback */
    public function onWheel(callable $callback): static { return $this->on('wheel', $callback); }

    // ============================================================
    // Transition & Animation Events
    // ============================================================

    /** @param callable(TransitionEvent): void $callback */
    public function onTransitionEnd(callable $callback): static { return $this->on('transitionend', $callback); }
    /** @param callable(TransitionEvent): void $callback */
    public function onTransitionStart(callable $callback): static { return $this->on('transitionstart', $callback); }
    /** @param callable(AnimationEvent): void $callback */
    public function onAnimationEnd(callable $callback): static { return $this->on('animationend', $callback); }
    /** @param callable(AnimationEvent): void $callback */
    public function onAnimationStart(callable $callback): static { return $this->on('animationstart', $callback); }
    /** @param callable(AnimationEvent): void $callback */
    public function onAnimationIteration(callable $callback): static { return $this->on('animationiteration', $callback); }

    // ============================================================
    // Media Events
    // ============================================================

    /** @param callable(MediaEvent): void $callback */
    public function onPlay(callable $callback): static { return $this->on('play', $callback); }
    /** @param callable(MediaEvent): void $callback */
    public function onPause(callable $callback): static { return $this->on('pause', $callback); }
    /** @param callable(MediaEvent): void $callback */
    public function onEnded(callable $callback): static { return $this->on('ended', $callback); }
    /** @param callable(MediaEvent): void $callback */
    public function onTimeUpdate(callable $callback): static { return $this->on('timeupdate', $callback); }
    /** @param callable(MediaEvent): void $callback */
    public function onVolumeChange(callable $callback): static { return $this->on('volumechange', $callback); }
    /** @param callable(MediaEvent): void $callback */
    public function onSeeking(callable $callback): static { return $this->on('seeking', $callback); }
    /** @param callable(MediaEvent): void $callback */
    public function onSeeked(callable $callback): static { return $this->on('seeked', $callback); }
    /** @param callable(MediaEvent): void $callback */
    public function onLoadedData(callable $callback): static { return $this->on('loadeddata', $callback); }
    /** @param callable(MediaEvent): void $callback */
    public function onLoadedMetadata(callable $callback): static { return $this->on('loadedmetadata', $callback); }
    /** @param callable(MediaEvent): void $callback */
    public function onCanPlay(callable $callback): static { return $this->on('canplay', $callback); }
    /** @param callable(MediaEvent): void $callback */
    public function onCanPlayThrough(callable $callback): static { return $this->on('canplaythrough', $callback); }
    /** @param callable(MediaEvent): void $callback */
    public function onWaiting(callable $callback): static { return $this->on('waiting', $callback); }
    /** @param callable(MediaEvent): void $callback */
    public function onPlaying(callable $callback): static { return $this->on('playing', $callback); }

    // ============================================================
    // Misc Events
    // ============================================================

    /** @param callable(): void $callback */
    public function onLoad(callable $callback): static { return $this->on('load', $callback); }
    /** @param callable(): void $callback */
    public function onError(callable $callback): static { return $this->on('error', $callback); }
    /** @param callable(ResizeEvent): void $callback */
    public function onResize(callable $callback): static { return $this->on('resize', $callback); }

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
        $this->dom()->background(...$colors);
        return $this;
    }

    // ============================================================
    // Text Color
    // ============================================================

    public function color(Color ...$colors): static
    {
        $this->dom()->color(...$colors);
        return $this;
    }

    // ============================================================
    // Border
    // ============================================================

    public function bordered(int $width = 1): static
    {
        $this->dom()->bordered($width);
        return $this;
    }

    public function borderColor(Color ...$colors): static
    {
        $this->dom()->borderColor(...$colors);
        return $this;
    }

    public function dashed(): static
    {
        $this->dom()->dashed();
        return $this;
    }

    public function dotted(): static
    {
        $this->dom()->dotted();
        return $this;
    }

    public function borderTop(int $width = 1): static
    {
        $this->dom()->borderTop($width);
        return $this;
    }

    public function borderBottom(int $width = 1): static
    {
        $this->dom()->borderBottom($width);
        return $this;
    }

    public function borderNone(): static
    {
        $this->dom()->borderNone();
        return $this;
    }

    public function outlineNone(): static
    {
        $this->dom()->outlineNone();
        return $this;
    }

    // ============================================================
    // Sizing
    // ============================================================

    public function width(Unit ...$values): static
    {
        $this->dom()->width(...$values);
        return $this;
    }

    public function height(Unit ...$values): static
    {
        $this->dom()->height(...$values);
        return $this;
    }

    public function size(Unit ...$values): static
    {
        $this->dom()->size(...$values);
        return $this;
    }

    public function minWidth(Unit ...$values): static
    {
        $this->dom()->minWidth(...$values);
        return $this;
    }

    public function maxWidth(Unit ...$values): static
    {
        $this->dom()->maxWidth(...$values);
        return $this;
    }

    public function minHeight(Unit ...$values): static
    {
        $this->dom()->minHeight(...$values);
        return $this;
    }

    public function maxHeight(Unit ...$values): static
    {
        $this->dom()->maxHeight(...$values);
        return $this;
    }

    public function extend(): static
    {
        $this->dom()->extend();
        return $this;
    }

    public function extendHorizontal(): static
    {
        $this->dom()->extendHorizontal();
        return $this;
    }

    public function extendVertical(): static
    {
        $this->dom()->extendVertical();
        return $this;
    }

    public function shrink(): static
    {
        $this->dom()->shrink();
        return $this;
    }

    // ============================================================
    // Spacing
    // ============================================================

    public function padding(Unit ...$values): static
    {
        $this->dom()->padding(...$values);
        return $this;
    }

    public function paddingHorizontal(Unit ...$values): static
    {
        $this->dom()->paddingHorizontal(...$values);
        return $this;
    }

    public function paddingVertical(Unit ...$values): static
    {
        $this->dom()->paddingVertical(...$values);
        return $this;
    }

    public function paddingTop(Unit ...$values): static
    {
        $this->dom()->paddingTop(...$values);
        return $this;
    }

    public function margin(Unit ...$values): static
    {
        $this->dom()->margin(...$values);
        return $this;
    }

    public function marginHorizontal(Unit ...$values): static
    {
        $this->dom()->marginHorizontal(...$values);
        return $this;
    }

    public function marginVertical(Unit ...$values): static
    {
        $this->dom()->marginVertical(...$values);
        return $this;
    }

    // ============================================================
    // Corners
    // ============================================================

    public function rounded(Unit ...$values): static
    {
        $this->dom()->rounded(...$values);
        return $this;
    }

    public function roundedFull(): static
    {
        $this->dom()->roundedFull();
        return $this;
    }

    // ============================================================
    // Shadow
    // ============================================================

    public function shadow(Shadow $size = Shadow::Medium): static
    {
        $this->dom()->shadow($size);
        return $this;
    }

    // ============================================================
    // Opacity
    // ============================================================

    public function opacity(int $value): static
    {
        $this->dom()->opacity($value);
        return $this;
    }

    // ============================================================
    // Visibility
    // ============================================================

    public function hidden(): static
    {
        $this->dom()->hidden();
        return $this;
    }

    public function visible(): static
    {
        $this->dom()->visible();
        return $this;
    }

    // ============================================================
    // Overflow
    // ============================================================

    public function clipContent(): static
    {
        $this->dom()->clipContent();
        return $this;
    }

    public function overflow(): static
    {
        $this->dom()->overflow();
        return $this;
    }

    public function scrollable(): static
    {
        $this->dom()->scrollable();
        return $this;
    }

    // ============================================================
    // Typography
    // ============================================================

    public function fontSize(FontSize $size): static
    {
        $this->dom()->fontSize($size);
        return $this;
    }

    public function weight(FontWeight $weight): static
    {
        $this->dom()->weight($weight);
        return $this;
    }

    // ============================================================
    // Cursor
    // ============================================================

    public function cursor(Cursor ...$cursors): static
    {
        $this->dom()->cursor(...$cursors);
        return $this;
    }

    public function clickable(): static
    {
        $this->dom()->clickable();
        return $this;
    }

    public function notAllowed(): static
    {
        $this->dom()->notAllowed();
        return $this;
    }

    // ============================================================
    // Transitions
    // ============================================================

    public function animated(int $durationMs = 200): static
    {
        $this->dom()->animated($durationMs);
        return $this;
    }

    // ============================================================
    // Transforms
    // ============================================================

    public function rotate(int $degrees): static
    {
        $this->dom()->rotate($degrees);
        return $this;
    }

    public function scale(int $percent): static
    {
        $this->dom()->scale($percent);
        return $this;
    }

    public function flipHorizontal(): static
    {
        $this->dom()->flipHorizontal();
        return $this;
    }

    public function flipVertical(): static
    {
        $this->dom()->flipVertical();
        return $this;
    }

    // ============================================================
    // Z-Index / Layering
    // ============================================================

    public function layer(int $index): static
    {
        $this->dom()->layer($index);
        return $this;
    }

    // ============================================================
    // Flex item properties
    // ============================================================

    public function grow(int $factor = 1): static
    {
        $this->dom()->grow($factor);
        return $this;
    }

    public function noShrink(): static
    {
        $this->dom()->noShrink();
        return $this;
    }

    // ============================================================
    // Position
    // ============================================================

    public function relative(): static
    {
        $this->dom()->relative();
        return $this;
    }

    public function absolute(): static
    {
        $this->dom()->absolute();
        return $this;
    }

    public function offsetTop(Unit ...$values): static
    {
        $this->dom()->offsetTop(...$values);
        return $this;
    }

    public function offsetLeft(Unit ...$values): static
    {
        $this->dom()->offsetLeft(...$values);
        return $this;
    }

    public function offsetRight(Unit ...$values): static
    {
        $this->dom()->offsetRight(...$values);
        return $this;
    }

    public function offsetBottom(Unit ...$values): static
    {
        $this->dom()->offsetBottom(...$values);
        return $this;
    }
}
