<?php

namespace Spwa\UI;

use Spwa\Events\AnimationEvent;
use Spwa\Events\ClipboardEvent;
use Spwa\Events\DragEvent;
use Spwa\Events\FileEvent;
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
    /** @var Component|null The component that owns this element's events */
    protected ?Component $eventOwner = null;

    public function __construct(string $tag = 'div')
    {
        parent::__construct(new TagDomNode($tag));
    }

    protected function dom(): TagDomNode
    {
        return $this->domNode;
    }

    /**
     * Apply element-specific attributes to the underlying DOM node.
     * Subclasses override to write HTML attributes (placeholder, type, etc.)
     * derived from setter-stored state. Called before render() and build().
     */
    protected function applyAttributes(): void
    {
    }

    /**
     * Build the DomNode for this element (without VNode lifecycle).
     * Subclasses override to provide custom DOM building.
     */
    public function build(): DomNode
    {
        $this->applyAttributes();
        $this->applyInvalidations();
        return $this->dom();
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
        if (empty($this->path)) {
            $this->path = $parent?->getPath() ?? [];
        }

        // Frozen during a diff cycle: skip the whole render. Both OLD (DiffOld)
        // and NEW (Patch) get an empty-but-frozen TagDomNode; compare() then
        // short-circuits and emits no patches. The frontend DOM keeps whatever
        // was rendered on the initial GET. First-page render still runs in
        // full because the HTML payload needs the real subtree.
        if ($this->dom()->isFrozen() && $phase !== RenderPhase::Initial) {
            return $this->dom()->assignPaths($this->path);
        }

        $this->applyAttributes();

        $this->eventOwner = $this->findOwningComponent($parent);
        if ($this->eventOwner !== null) {
            $this->dom()->setEventOwner($this->eventOwner);
        }

        $this->applyInvalidations();

        return $this->dom()->assignPaths($this->path);
    }

    /**
     * Find the nearest Component ancestor.
     */
    protected function findOwningComponent(?VNode $node): ?Component
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

    /**
     * Force this element — and recursively every attribute, class, text node,
     * and child below it — to be patched on every diff. Use for trees whose
     * server-side OLD/NEW renders happen in the same instant and agree on
     * values (e.g. clock hands) while the frontend DOM holds stale values
     * from earlier renders.
     */
    public function invalidate(bool $on = true): static
    {
        $this->dom()->setInvalidated($on);
        return $this;
    }

    /**
     * Force-patch a single attribute on this element, regardless of how it
     * was set (via attr(), via a styling helper like color() / background()
     * that ultimately writes the `class` attribute, etc.). The rest of the
     * element diffs normally.
     *
     *   $el->color(Color::red())->invalidateAttr('class')
     *   $el->attr('data-tick', $now)->invalidateAttr('data-tick')
     */
    public function invalidateAttr(string $name): static
    {
        $this->dom()->markInvalidatedAttr($name);
        return $this;
    }

    /**
     * Force-patch every text node directly inside this element on every
     * diff. Element children are untouched — for those use ->invalidate()
     * on the child itself, or ->invalidate() recursively on this element.
     *
     *   UI::text(date('H:i:s'))->invalidateText()
     *   $el->content('label: ' . $value)->invalidateText()
     *
     * String children added via content() are only materialised into
     * TextDomNodes during build()/render(), so the marking is deferred to
     * the applyInvalidations() hook called after that step.
     */
    public function invalidateText(): static
    {
        $this->invalidateTextOnRender = true;
        $this->applyInvalidations();
        return $this;
    }

    /** @var bool Whether direct text children should be force-patched on each render */
    protected bool $invalidateTextOnRender = false;

    /**
     * Idempotent post-children hook. Called once children are present on
     * the underlying dom node so flags set before children materialise
     * still take effect.
     */
    protected function applyInvalidations(): void
    {
        if (!$this->invalidateTextOnRender) {
            return;
        }
        foreach ($this->dom()->getChildren() as $child) {
            if ($child instanceof TextDomNode) {
                $child->setInvalidated(true);
            }
        }
    }

    /**
     * Opposite of invalidate(): the diff walks past this element entirely.
     * No attributes, classes, text, or children below it are compared, and
     * no patches are emitted. The frontend DOM keeps whatever it had — use
     * for static subtrees (headers, logos) to skip traversal cost.
     */
    public function frozen(bool $on = true): static
    {
        $this->dom()->setFrozen($on);
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
    public function onClick(callable $callback): static
    {
        return $this->on('click', $callback);
    }

    /** @param callable(MouseEvent): void $callback */
    public function onDblClick(callable $callback): static
    {
        return $this->on('dblclick', $callback);
    }

    /** @param callable(MouseEvent): void $callback */
    public function onMouseDown(callable $callback): static
    {
        return $this->on('mousedown', $callback);
    }

    /** @param callable(MouseEvent): void $callback */
    public function onMouseUp(callable $callback): static
    {
        return $this->on('mouseup', $callback);
    }

    /** @param callable(MouseEvent): void $callback */
    public function onMouseOver(callable $callback): static
    {
        return $this->on('mouseover', $callback);
    }

    /** @param callable(MouseEvent): void $callback */
    public function onMouseOut(callable $callback): static
    {
        return $this->on('mouseout', $callback);
    }

    /** @param callable(MouseEvent): void $callback */
    public function onMouseEnter(callable $callback): static
    {
        return $this->on('mouseenter', $callback);
    }

    /** @param callable(MouseEvent): void $callback */
    public function onMouseLeave(callable $callback): static
    {
        return $this->on('mouseleave', $callback);
    }

    /** @param callable(MouseEvent): void $callback */
    public function onMouseMove(callable $callback): static
    {
        return $this->on('mousemove', $callback);
    }

    /** @param callable(MouseEvent): void $callback */
    public function onContextMenu(callable $callback): static
    {
        return $this->on('contextmenu', $callback);
    }

    // ============================================================
    // Keyboard Events
    // ============================================================

    /** @param callable(KeyboardEvent): void $callback */
    public function onKeyDown(callable $callback): static
    {
        return $this->on('keydown', $callback);
    }

    /** @param callable(KeyboardEvent): void $callback */
    public function onKeyUp(callable $callback): static
    {
        return $this->on('keyup', $callback);
    }

    // ============================================================
    // Form / Input Events
    // ============================================================

    /** @param callable(InputEvent): void $callback */
    public function onChange(callable $callback): static
    {
        return $this->on('change', $callback);
    }

    /** @param callable(InputEvent): void $callback */
    public function onInput(callable $callback): static
    {
        return $this->on('input', $callback);
    }

    /** @param callable(): void $callback */
    public function onSubmit(callable $callback): static
    {
        return $this->on('submit', $callback);
    }

    /** @param callable(): void $callback */
    public function onReset(callable $callback): static
    {
        return $this->on('reset', $callback);
    }

    /** @param callable(): void $callback */
    public function onFocus(callable $callback): static
    {
        return $this->on('focus', $callback);
    }

    /** @param callable(): void $callback */
    public function onBlur(callable $callback): static
    {
        return $this->on('blur', $callback);
    }

    /** @param callable(): void $callback */
    public function onFocusIn(callable $callback): static
    {
        return $this->on('focusin', $callback);
    }

    /** @param callable(): void $callback */
    public function onFocusOut(callable $callback): static
    {
        return $this->on('focusout', $callback);
    }

    /** @param callable(InputEvent): void $callback */
    public function onSelect(callable $callback): static
    {
        return $this->on('select', $callback);
    }

    /** @param callable(InputEvent): void $callback */
    public function onInvalid(callable $callback): static
    {
        return $this->on('invalid', $callback);
    }

    /** @param callable(FileEvent): void $callback */
    public function onUpload(callable $callback): static
    {
        return $this->on('upload', $callback);
    }

    // ============================================================
    // Touch Events
    // ============================================================

    /** @param callable(TouchEvent): void $callback */
    public function onTouchStart(callable $callback): static
    {
        return $this->on('touchstart', $callback);
    }

    /** @param callable(TouchEvent): void $callback */
    public function onTouchEnd(callable $callback): static
    {
        return $this->on('touchend', $callback);
    }

    /** @param callable(TouchEvent): void $callback */
    public function onTouchMove(callable $callback): static
    {
        return $this->on('touchmove', $callback);
    }

    /** @param callable(TouchEvent): void $callback */
    public function onTouchCancel(callable $callback): static
    {
        return $this->on('touchcancel', $callback);
    }

    // ============================================================
    // Pointer Events
    // ============================================================

    /** @param callable(PointerEvent): void $callback */
    public function onPointerDown(callable $callback): static
    {
        return $this->on('pointerdown', $callback);
    }

    /** @param callable(PointerEvent): void $callback */
    public function onPointerUp(callable $callback): static
    {
        return $this->on('pointerup', $callback);
    }

    /** @param callable(PointerEvent): void $callback */
    public function onPointerMove(callable $callback): static
    {
        return $this->on('pointermove', $callback);
    }

    /** @param callable(PointerEvent): void $callback */
    public function onPointerOver(callable $callback): static
    {
        return $this->on('pointerover', $callback);
    }

    /** @param callable(PointerEvent): void $callback */
    public function onPointerOut(callable $callback): static
    {
        return $this->on('pointerout', $callback);
    }

    /** @param callable(PointerEvent): void $callback */
    public function onPointerEnter(callable $callback): static
    {
        return $this->on('pointerenter', $callback);
    }

    /** @param callable(PointerEvent): void $callback */
    public function onPointerLeave(callable $callback): static
    {
        return $this->on('pointerleave', $callback);
    }

    /** @param callable(PointerEvent): void $callback */
    public function onPointerCancel(callable $callback): static
    {
        return $this->on('pointercancel', $callback);
    }

    // ============================================================
    // Drag & Drop Events
    // ============================================================

    /** @param callable(DragEvent): void $callback */
    public function onDragStart(callable $callback): static
    {
        return $this->on('dragstart', $callback);
    }

    /** @param callable(DragEvent): void $callback */
    public function onDrag(callable $callback): static
    {
        return $this->on('drag', $callback);
    }

    /** @param callable(DragEvent): void $callback */
    public function onDragEnd(callable $callback): static
    {
        return $this->on('dragend', $callback);
    }

    /** @param callable(DragEvent): void $callback */
    public function onDragEnter(callable $callback): static
    {
        return $this->on('dragenter', $callback);
    }

    /** @param callable(DragEvent): void $callback */
    public function onDragLeave(callable $callback): static
    {
        return $this->on('dragleave', $callback);
    }

    /** @param callable(DragEvent): void $callback */
    public function onDragOver(callable $callback): static
    {
        return $this->on('dragover', $callback);
    }

    /** @param callable(DragEvent): void $callback */
    public function onDrop(callable $callback): static
    {
        return $this->on('drop', $callback);
    }

    // ============================================================
    // Clipboard Events
    // ============================================================

    /** @param callable(ClipboardEvent): void $callback */
    public function onCopy(callable $callback): static
    {
        return $this->on('copy', $callback);
    }

    /** @param callable(ClipboardEvent): void $callback */
    public function onCut(callable $callback): static
    {
        return $this->on('cut', $callback);
    }

    /** @param callable(ClipboardEvent): void $callback */
    public function onPaste(callable $callback): static
    {
        return $this->on('paste', $callback);
    }

    // ============================================================
    // Scroll & Wheel Events
    // ============================================================

    /** @param callable(ScrollEvent): void $callback */
    public function onScroll(callable $callback): static
    {
        return $this->on('scroll', $callback);
    }

    /** @param callable(WheelEvent): void $callback */
    public function onWheel(callable $callback): static
    {
        return $this->on('wheel', $callback);
    }

    // ============================================================
    // Transition & Animation Events
    // ============================================================

    /** @param callable(TransitionEvent): void $callback */
    public function onTransitionEnd(callable $callback): static
    {
        return $this->on('transitionend', $callback);
    }

    /** @param callable(TransitionEvent): void $callback */
    public function onTransitionStart(callable $callback): static
    {
        return $this->on('transitionstart', $callback);
    }

    /** @param callable(AnimationEvent): void $callback */
    public function onAnimationEnd(callable $callback): static
    {
        return $this->on('animationend', $callback);
    }

    /** @param callable(AnimationEvent): void $callback */
    public function onAnimationStart(callable $callback): static
    {
        return $this->on('animationstart', $callback);
    }

    /** @param callable(AnimationEvent): void $callback */
    public function onAnimationIteration(callable $callback): static
    {
        return $this->on('animationiteration', $callback);
    }

    // ============================================================
    // Media Events
    // ============================================================

    /** @param callable(MediaEvent): void $callback */
    public function onPlay(callable $callback): static
    {
        return $this->on('play', $callback);
    }

    /** @param callable(MediaEvent): void $callback */
    public function onPause(callable $callback): static
    {
        return $this->on('pause', $callback);
    }

    /** @param callable(MediaEvent): void $callback */
    public function onEnded(callable $callback): static
    {
        return $this->on('ended', $callback);
    }

    /** @param callable(MediaEvent): void $callback */
    public function onTimeUpdate(callable $callback): static
    {
        return $this->on('timeupdate', $callback);
    }

    /** @param callable(MediaEvent): void $callback */
    public function onVolumeChange(callable $callback): static
    {
        return $this->on('volumechange', $callback);
    }

    /** @param callable(MediaEvent): void $callback */
    public function onSeeking(callable $callback): static
    {
        return $this->on('seeking', $callback);
    }

    /** @param callable(MediaEvent): void $callback */
    public function onSeeked(callable $callback): static
    {
        return $this->on('seeked', $callback);
    }

    /** @param callable(MediaEvent): void $callback */
    public function onLoadedData(callable $callback): static
    {
        return $this->on('loadeddata', $callback);
    }

    /** @param callable(MediaEvent): void $callback */
    public function onLoadedMetadata(callable $callback): static
    {
        return $this->on('loadedmetadata', $callback);
    }

    /** @param callable(MediaEvent): void $callback */
    public function onCanPlay(callable $callback): static
    {
        return $this->on('canplay', $callback);
    }

    /** @param callable(MediaEvent): void $callback */
    public function onCanPlayThrough(callable $callback): static
    {
        return $this->on('canplaythrough', $callback);
    }

    /** @param callable(MediaEvent): void $callback */
    public function onWaiting(callable $callback): static
    {
        return $this->on('waiting', $callback);
    }

    /** @param callable(MediaEvent): void $callback */
    public function onPlaying(callable $callback): static
    {
        return $this->on('playing', $callback);
    }

    // ============================================================
    // Misc Events
    // ============================================================

    /** @param callable(): void $callback */
    public function onLoad(callable $callback): static
    {
        return $this->on('load', $callback);
    }

    /** @param callable(): void $callback */
    public function onError(callable $callback): static
    {
        return $this->on('error', $callback);
    }

    /** @param callable(ResizeEvent): void $callback */
    public function onResize(callable $callback): static
    {
        return $this->on('resize', $callback);
    }

    // ============================================================
    // Background
    // ============================================================

    public function background(Color $color, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $color->withContext('bg'), ['background-color' => $color->getValue()]);
        return $this;
    }

    // ============================================================
    // Text Color
    // ============================================================

    public function color(Color $color, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $color->withContext('text'), ['color' => $color->getValue()]);
        return $this;
    }

    // ============================================================
    // Border
    // ============================================================

    public function bordered(int $width = 1, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $class = $width === 1 ? 'border' : 'border-' . $width;
        $this->addStyle($prefix . $class, ['border-width' => $width . 'px', 'border-style' => 'solid']);
        return $this;
    }

    public function borderColor(Color $color, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $color->withContext('border'), ['border-color' => $color->getValue()]);
        return $this;
    }

    public function dashed(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'border-dashed', ['border-style' => 'dashed']);
        return $this;
    }

    public function dotted(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'border-dotted', ['border-style' => 'dotted']);
        return $this;
    }

    public function borderTop(int $width = 1, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'border-t-' . $width, ['border-top-width' => $width . 'px', 'border-top-style' => 'solid']);
        return $this;
    }

    public function borderBottom(int $width = 1, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'border-b-' . $width, ['border-bottom-width' => $width . 'px', 'border-bottom-style' => 'solid']);
        return $this;
    }

    public function borderNone(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'border-none', ['border' => 'none']);
        return $this;
    }

    public function outlineNone(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'outline-none', ['outline' => 'none']);
        return $this;
    }

    // ============================================================
    // Sizing
    // ============================================================

    public function width(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('w'), ['width' => $value->getCssValue()]);
        return $this;
    }

    public function height(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('h'), ['height' => $value->getCssValue()]);
        return $this;
    }

    public function size(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $css = $value->getCssValue();
        $this->addStyle($prefix . $value->withContext('w'), ['width' => $css]);
        $this->addStyle($prefix . $value->withContext('h'), ['height' => $css]);
        return $this;
    }

    public function minWidth(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('min-w'), ['min-width' => $value->getCssValue()]);
        return $this;
    }

    public function maxWidth(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('max-w'), ['max-width' => $value->getCssValue()]);
        return $this;
    }

    public function minHeight(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('min-h'), ['min-height' => $value->getCssValue()]);
        return $this;
    }

    public function maxHeight(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('max-h'), ['max-height' => $value->getCssValue()]);
        return $this;
    }

    public function extend(bool $screen = false, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'w-full', ['width' => $screen ? '100vw' : '100%']);
        $this->addStyle($prefix . 'h-full', ['height' => $screen ? '100vh' : '100%']);
        return $this;
    }

    public function extendHorizontal(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'w-full', ['width' => '100%']);
        return $this;
    }

    public function extendVertical(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'h-full', ['height' => '100%']);
        return $this;
    }

    public function shrink(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'w-fit', ['width' => 'fit-content']);
        $this->addStyle($prefix . 'h-fit', ['height' => 'fit-content']);
        return $this;
    }

    // ============================================================
    // Spacing
    // ============================================================

    public function padding(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('p'), ['padding' => $value->getCssValue()]);
        return $this;
    }

    public function paddingHorizontal(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $css = $value->getCssValue();
        $this->addStyle($prefix . $value->withContext('px'), ['padding-left' => $css, 'padding-right' => $css]);
        return $this;
    }

    public function paddingVertical(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $css = $value->getCssValue();
        $this->addStyle($prefix . $value->withContext('py'), ['padding-top' => $css, 'padding-bottom' => $css]);
        return $this;
    }

    public function paddingTop(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('pt'), ['padding-top' => $value->getCssValue()]);
        return $this;
    }

    public function margin(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('m'), ['margin' => $value->getCssValue()]);
        return $this;
    }

    public function marginHorizontal(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $css = $value->getCssValue();
        $this->addStyle($prefix . $value->withContext('mx'), ['margin-left' => $css, 'margin-right' => $css]);
        return $this;
    }

    public function marginVertical(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $css = $value->getCssValue();
        $this->addStyle($prefix . $value->withContext('my'), ['margin-top' => $css, 'margin-bottom' => $css]);
        return $this;
    }

    // ============================================================
    // Corners
    // ============================================================

    public function rounded(?Unit $value = null, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        if ($value === null) {
            $this->addStyle($prefix . 'rounded', ['border-radius' => '0.25rem']);
        } else {
            $this->addStyle($prefix . $value->withContext('rounded'), ['border-radius' => $value->getCssValue()]);
        }
        return $this;
    }

    public function roundedFull(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'rounded-full', ['border-radius' => '9999px']);
        return $this;
    }

    // ============================================================
    // Shadow
    // ============================================================

    public function shadow(Shadow $size = Shadow::Medium, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $size->toClass(), ['box-shadow' => $size->getCssValue()]);
        return $this;
    }

    // ============================================================
    // Opacity
    // ============================================================

    public function opacity(int $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'opacity-' . $value, ['opacity' => (string)($value / 100)]);
        return $this;
    }

    // ============================================================
    // Visibility
    // ============================================================

    public function hidden(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'hidden', ['display' => 'none']);
        return $this;
    }

    public function visible(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'visible', ['visibility' => 'visible']);
        return $this;
    }

    // ============================================================
    // Overflow
    // ============================================================

    public function clipContent(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'overflow-hidden', ['overflow' => 'hidden']);
        return $this;
    }

    public function overflow(?Pseudo $pseudo = null): static
    {
        return $this->clipContent($pseudo);
    }

    public function scrollable(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'overflow-auto', ['overflow' => 'auto']);
        return $this;
    }

    // ============================================================
    // Typography
    // ============================================================

    public function fontSize(FontSize $size, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $size->toClass(), ['font-size' => $size->getCssValue()]);
        return $this;
    }

    public function weight(FontWeight $weight, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $weight->toClass(), ['font-weight' => $weight->getCssValue()]);
        return $this;
    }

    // ============================================================
    // Cursor
    // ============================================================

    public function cursor(Cursor $cursor, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $cursor->toClass(), ['cursor' => $cursor->getCssValue()]);
        return $this;
    }

    public function clickable(?Pseudo $pseudo = null): static
    {
        return $this->cursor(Cursor::Pointer, $pseudo);
    }

    public function notAllowed(?Pseudo $pseudo = null): static
    {
        return $this->cursor(Cursor::NotAllowed, $pseudo);
    }

    // ============================================================
    // Transitions
    // ============================================================

    public function animated(int $durationMs = 200, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'transition', ['transition-property' => 'all', 'transition-timing-function' => 'cubic-bezier(0.4, 0, 0.2, 1)']);
        $this->addStyle($prefix . 'duration-' . $durationMs, ['transition-duration' => $durationMs . 'ms']);
        return $this;
    }

    // ============================================================
    // Transforms
    // ============================================================

    public function rotate(int $degrees, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'rotate-' . $degrees, ['transform' => 'rotate(' . $degrees . 'deg)']);
        return $this;
    }

    public function scale(int $percent, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'scale-' . $percent, ['transform' => 'scale(' . ($percent / 100) . ')']);
        return $this;
    }

    public function flipHorizontal(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . '-scale-x-100', ['transform' => 'scaleX(-1)']);
        return $this;
    }

    public function flipVertical(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . '-scale-y-100', ['transform' => 'scaleY(-1)']);
        return $this;
    }

    // ============================================================
    // Z-Index / Layering
    // ============================================================

    public function layer(int $index, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'z-' . $index, ['z-index' => (string)$index]);
        return $this;
    }

    // ============================================================
    // Flex item properties
    // ============================================================

    public function grow(int $factor = 1, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'grow-' . $factor, ['flex-grow' => (string)$factor]);
        return $this;
    }

    public function noShrink(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'shrink-0', ['flex-shrink' => '0']);
        return $this;
    }

    // ============================================================
    // Position
    // ============================================================

    public function relative(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'relative', ['position' => 'relative']);
        return $this;
    }

    public function absolute(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'absolute', ['position' => 'absolute']);
        return $this;
    }

    public function fixed(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'fixed', ['position' => 'fixed']);
        return $this;
    }

    public function sticky(?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'sticky', ['position' => 'sticky']);
        return $this;
    }

    /**
     * CSS `inset` shorthand applied uniformly to all four sides.
     */
    public function inset(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $css = $value->getCssValue();
        $class = 'inset-[' . str_replace(' ', '_', $css) . ']';
        $this->addStyle($prefix . $class, ['inset' => $css]);
        return $this;
    }

    public function offsetTop(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('top'), ['top' => $value->getCssValue()]);
        return $this;
    }

    public function offsetLeft(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('left'), ['left' => $value->getCssValue()]);
        return $this;
    }

    public function offsetRight(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('right'), ['right' => $value->getCssValue()]);
        return $this;
    }

    public function offsetBottom(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('bottom'), ['bottom' => $value->getCssValue()]);
        return $this;
    }
}
