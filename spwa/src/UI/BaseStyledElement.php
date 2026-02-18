<?php

namespace Spwa\UI;

/**
 * Base class providing common styling methods for all styled elements.
 * Contains methods for background, color, padding, margin, border, etc.
 */
abstract class BaseStyledElement extends BaseElement
{
    // Background
    public function background(Color ...$colors): static
    {
        foreach ($colors as $color) {
            $this->addStateValue($color);
        }
        return $this;
    }

    public function bg(Color ...$colors): static
    {
        return $this->background(...$colors);
    }

    // Text color
    public function color(Color ...$colors): static
    {
        foreach ($colors as $color) {
            $this->addStateValue($color->asText());
        }
        return $this;
    }

    // Border color
    public function borderColor(Color ...$colors): static
    {
        foreach ($colors as $color) {
            $this->addStateValue($color->asBorder());
        }
        return $this;
    }

    // Padding
    public function padding(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asPadding());
        }
        return $this;
    }

    public function p(Unit ...$values): static
    {
        return $this->padding(...$values);
    }

    public function paddingX(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asPaddingX());
        }
        return $this;
    }

    public function px(Unit ...$values): static
    {
        return $this->paddingX(...$values);
    }

    public function paddingY(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asPaddingY());
        }
        return $this;
    }

    public function py(Unit ...$values): static
    {
        return $this->paddingY(...$values);
    }

    // Margin
    public function margin(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asMargin());
        }
        return $this;
    }

    public function m(Unit ...$values): static
    {
        return $this->margin(...$values);
    }

    public function marginX(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asMarginX());
        }
        return $this;
    }

    public function mx(Unit ...$values): static
    {
        return $this->marginX(...$values);
    }

    public function marginY(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asMarginY());
        }
        return $this;
    }

    public function my(Unit ...$values): static
    {
        return $this->marginY(...$values);
    }

    // Width
    public function width(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asWidth());
        }
        return $this;
    }

    public function w(Unit ...$values): static
    {
        return $this->width(...$values);
    }

    public function minWidth(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asMinWidth());
        }
        return $this;
    }

    public function maxWidth(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asMaxWidth());
        }
        return $this;
    }

    // Height
    public function height(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asHeight());
        }
        return $this;
    }

    public function h(Unit ...$values): static
    {
        return $this->height(...$values);
    }

    public function minHeight(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asMinHeight());
        }
        return $this;
    }

    public function maxHeight(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asMaxHeight());
        }
        return $this;
    }

    // Size (width + height)
    public function size(Unit ...$values): static
    {
        $this->width(...$values);
        $this->height(...$values);
        return $this;
    }

    // Full screen
    public function fullScreen(): static
    {
        $this->addClass('w-screen');
        $this->addClass('h-screen');
        return $this;
    }

    public function fullWidth(): static
    {
        $this->addClass('w-full');
        return $this;
    }

    public function fullHeight(): static
    {
        $this->addClass('h-full');
        return $this;
    }

    // Border radius
    public function rounded(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asRadius());
        }
        return $this;
    }

    public function roundedFull(): static
    {
        $this->addClass('rounded-full');
        return $this;
    }

    // Border
    public function border(Unit ...$values): static
    {
        if (empty($values)) {
            $this->addClass('border');
        } else {
            foreach ($values as $value) {
                $this->addClass($value->buildStatePrefix() . 'border-' . $value->getValue());
            }
        }
        return $this;
    }

    // Shadow
    public function shadow(?string $size = null): static
    {
        $this->addClass($size ? 'shadow-' . $size : 'shadow');
        return $this;
    }

    public function shadowSm(): static
    {
        return $this->shadow('sm');
    }

    public function shadowMd(): static
    {
        return $this->shadow('md');
    }

    public function shadowLg(): static
    {
        return $this->shadow('lg');
    }

    public function shadowXl(): static
    {
        return $this->shadow('xl');
    }

    public function shadowNone(): static
    {
        return $this->shadow('none');
    }

    // Opacity
    public function opacity(int $value): static
    {
        $this->addClass('opacity-' . $value);
        return $this;
    }

    // Cursor
    public function cursor(string $type = 'pointer'): static
    {
        $this->addClass('cursor-' . $type);
        return $this;
    }

    public function cursorPointer(): static
    {
        return $this->cursor('pointer');
    }

    public function cursorNotAllowed(): static
    {
        return $this->cursor('not-allowed');
    }

    // Overflow
    public function overflow(string $value): static
    {
        $this->addClass('overflow-' . $value);
        return $this;
    }

    public function overflowHidden(): static
    {
        return $this->overflow('hidden');
    }

    public function overflowAuto(): static
    {
        return $this->overflow('auto');
    }

    public function overflowScroll(): static
    {
        return $this->overflow('scroll');
    }

    // Visibility
    public function hidden(): static
    {
        $this->addClass('hidden');
        return $this;
    }

    public function visible(): static
    {
        $this->addClass('visible');
        return $this;
    }

    public function invisible(): static
    {
        $this->addClass('invisible');
        return $this;
    }

    // Position
    public function relative(): static
    {
        $this->addClass('relative');
        return $this;
    }

    public function absolute(): static
    {
        $this->addClass('absolute');
        return $this;
    }

    public function fixed(): static
    {
        $this->addClass('fixed');
        return $this;
    }

    public function sticky(): static
    {
        $this->addClass('sticky');
        return $this;
    }

    // Inset
    public function inset(Unit ...$values): static
    {
        if (empty($values)) {
            $this->addClass('inset-0');
        } else {
            foreach ($values as $value) {
                $this->addClass($value->buildStatePrefix() . 'inset-' . $value->getValue());
            }
        }
        return $this;
    }

    // Z-index
    public function z(int $value): static
    {
        $this->addClass('z-' . $value);
        return $this;
    }

    // Transition
    public function transition(?string $property = null): static
    {
        $this->addClass($property ? 'transition-' . $property : 'transition');
        return $this;
    }

    public function transitionAll(): static
    {
        return $this->transition('all');
    }

    public function transitionColors(): static
    {
        return $this->transition('colors');
    }

    // Duration
    public function duration(int $ms): static
    {
        $this->addClass('duration-' . $ms);
        return $this;
    }

    // Ring (focus ring)
    public function ring(int $width = 2): static
    {
        $this->addClass('ring-' . $width);
        return $this;
    }

    public function ringColor(Color ...$colors): static
    {
        foreach ($colors as $color) {
            $this->addStateValue($color->asRing());
        }
        return $this;
    }

    // Outline
    public function outline(?string $style = null): static
    {
        $this->addClass($style ? 'outline-' . $style : 'outline');
        return $this;
    }

    public function outlineNone(): static
    {
        return $this->outline('none');
    }

    // ============================================================
    // Extend (QuestPDF: Extend, ExtendVertical, ExtendHorizontal)
    // ============================================================

    /**
     * Extend to fill all available space.
     */
    public function extend(): static
    {
        $this->addClass('w-full');
        $this->addClass('h-full');
        return $this;
    }

    /**
     * Extend to fill available vertical space.
     */
    public function extendVertical(): static
    {
        $this->addClass('h-full');
        return $this;
    }

    /**
     * Extend to fill available horizontal space.
     */
    public function extendHorizontal(): static
    {
        $this->addClass('w-full');
        return $this;
    }

    // ============================================================
    // Shrink (QuestPDF: Shrink, ShrinkVertical, ShrinkHorizontal)
    // ============================================================

    /**
     * Shrink to minimal size in both directions.
     */
    public function shrink(): static
    {
        $this->addClass('w-fit');
        $this->addClass('h-fit');
        return $this;
    }

    /**
     * Shrink to minimal vertical size.
     */
    public function shrinkVertical(): static
    {
        $this->addClass('h-fit');
        return $this;
    }

    /**
     * Shrink to minimal horizontal size.
     */
    public function shrinkHorizontal(): static
    {
        $this->addClass('w-fit');
        return $this;
    }

    // ============================================================
    // Alignment (QuestPDF: AlignLeft, AlignCenter, AlignRight, AlignTop, AlignMiddle, AlignBottom)
    // ============================================================

    /**
     * Align content to the left.
     */
    public function alignLeft(): static
    {
        $this->addClass('text-left');
        return $this;
    }

    /**
     * Align content to the center horizontally.
     */
    public function alignCenter(): static
    {
        $this->addClass('text-center');
        return $this;
    }

    /**
     * Align content to the right.
     */
    public function alignRight(): static
    {
        $this->addClass('text-right');
        return $this;
    }

    /**
     * Align content to the top.
     */
    public function alignTop(): static
    {
        $this->addClass('align-top');
        return $this;
    }

    /**
     * Align content to the middle vertically.
     */
    public function alignMiddle(): static
    {
        $this->addClass('align-middle');
        return $this;
    }

    /**
     * Align content to the bottom.
     */
    public function alignBottom(): static
    {
        $this->addClass('align-bottom');
        return $this;
    }

    // ============================================================
    // Transforms (QuestPDF: Rotate, Scale, Translate, Flip)
    // ============================================================

    /**
     * Rotate element by degrees.
     */
    public function rotate(int $degrees): static
    {
        $this->addClass('rotate-' . $degrees);
        return $this;
    }

    /**
     * Rotate 90 degrees clockwise.
     */
    public function rotateRight(): static
    {
        $this->addClass('rotate-90');
        return $this;
    }

    /**
     * Rotate 90 degrees counter-clockwise.
     */
    public function rotateLeft(): static
    {
        $this->addClass('-rotate-90');
        return $this;
    }

    /**
     * Rotate 180 degrees.
     */
    public function rotate180(): static
    {
        $this->addClass('rotate-180');
        return $this;
    }

    /**
     * Scale element (0-100 becomes 0-1, 100+ for larger).
     */
    public function scale(int $percent): static
    {
        $this->addClass('scale-' . $percent);
        return $this;
    }

    /**
     * Scale X axis only.
     */
    public function scaleX(int $percent): static
    {
        $this->addClass('scale-x-' . $percent);
        return $this;
    }

    /**
     * Scale Y axis only.
     */
    public function scaleY(int $percent): static
    {
        $this->addClass('scale-y-' . $percent);
        return $this;
    }

    /**
     * Translate X position.
     */
    public function translateX(string $value): static
    {
        $this->addClass('translate-x-' . $value);
        return $this;
    }

    /**
     * Translate Y position.
     */
    public function translateY(string $value): static
    {
        $this->addClass('translate-y-' . $value);
        return $this;
    }

    /**
     * Flip horizontally.
     */
    public function flipHorizontal(): static
    {
        $this->addClass('-scale-x-100');
        return $this;
    }

    /**
     * Flip vertically.
     */
    public function flipVertical(): static
    {
        $this->addClass('-scale-y-100');
        return $this;
    }

    /**
     * Flip both axes.
     */
    public function flipBoth(): static
    {
        $this->addClass('-scale-100');
        return $this;
    }

    /**
     * Enable transform (required for some transform utilities).
     */
    public function transform(): static
    {
        $this->addClass('transform');
        return $this;
    }

    /**
     * Set transform origin.
     */
    public function transformOrigin(string $origin): static
    {
        $this->addClass('origin-' . $origin);
        return $this;
    }

    // ============================================================
    // Additional sizing helpers (QuestPDF style)
    // ============================================================

    /**
     * Set padding on left side only.
     */
    public function paddingLeft(Unit $value): static
    {
        $this->addClass($value->buildStatePrefix() . 'pl-' . $value->getValue());
        return $this;
    }

    /**
     * Set padding on right side only.
     */
    public function paddingRight(Unit $value): static
    {
        $this->addClass($value->buildStatePrefix() . 'pr-' . $value->getValue());
        return $this;
    }

    /**
     * Set padding on top only.
     */
    public function paddingTop(Unit $value): static
    {
        $this->addClass($value->buildStatePrefix() . 'pt-' . $value->getValue());
        return $this;
    }

    /**
     * Set padding on bottom only.
     */
    public function paddingBottom(Unit $value): static
    {
        $this->addClass($value->buildStatePrefix() . 'pb-' . $value->getValue());
        return $this;
    }

    /**
     * Set margin on left side only.
     */
    public function marginLeft(Unit $value): static
    {
        $this->addClass($value->buildStatePrefix() . 'ml-' . $value->getValue());
        return $this;
    }

    /**
     * Set margin on right side only.
     */
    public function marginRight(Unit $value): static
    {
        $this->addClass($value->buildStatePrefix() . 'mr-' . $value->getValue());
        return $this;
    }

    /**
     * Set margin on top only.
     */
    public function marginTop(Unit $value): static
    {
        $this->addClass($value->buildStatePrefix() . 'mt-' . $value->getValue());
        return $this;
    }

    /**
     * Set margin on bottom only.
     */
    public function marginBottom(Unit $value): static
    {
        $this->addClass($value->buildStatePrefix() . 'mb-' . $value->getValue());
        return $this;
    }

    // ============================================================
    // Border sides (QuestPDF: BorderLeft, BorderRight, BorderTop, BorderBottom)
    // ============================================================

    public function borderLeft(int $width = 1): static
    {
        $this->addClass($width === 1 ? 'border-l' : 'border-l-' . $width);
        return $this;
    }

    public function borderRight(int $width = 1): static
    {
        $this->addClass($width === 1 ? 'border-r' : 'border-r-' . $width);
        return $this;
    }

    public function borderTop(int $width = 1): static
    {
        $this->addClass($width === 1 ? 'border-t' : 'border-t-' . $width);
        return $this;
    }

    public function borderBottom(int $width = 1): static
    {
        $this->addClass($width === 1 ? 'border-b' : 'border-b-' . $width);
        return $this;
    }

    public function borderX(int $width = 1): static
    {
        $this->addClass($width === 1 ? 'border-x' : 'border-x-' . $width);
        return $this;
    }

    public function borderY(int $width = 1): static
    {
        $this->addClass($width === 1 ? 'border-y' : 'border-y-' . $width);
        return $this;
    }

    // ============================================================
    // Corner radius per side
    // ============================================================

    public function roundedTop(string $size = 'md'): static
    {
        $this->addClass('rounded-t-' . $size);
        return $this;
    }

    public function roundedBottom(string $size = 'md'): static
    {
        $this->addClass('rounded-b-' . $size);
        return $this;
    }

    public function roundedLeft(string $size = 'md'): static
    {
        $this->addClass('rounded-l-' . $size);
        return $this;
    }

    public function roundedRight(string $size = 'md'): static
    {
        $this->addClass('rounded-r-' . $size);
        return $this;
    }

    public function roundedTopLeft(string $size = 'md'): static
    {
        $this->addClass('rounded-tl-' . $size);
        return $this;
    }

    public function roundedTopRight(string $size = 'md'): static
    {
        $this->addClass('rounded-tr-' . $size);
        return $this;
    }

    public function roundedBottomLeft(string $size = 'md'): static
    {
        $this->addClass('rounded-bl-' . $size);
        return $this;
    }

    public function roundedBottomRight(string $size = 'md'): static
    {
        $this->addClass('rounded-br-' . $size);
        return $this;
    }

    // ============================================================
    // Inset positions
    // ============================================================

    public function top(string $value): static
    {
        $this->addClass('top-' . $value);
        return $this;
    }

    public function bottom(string $value): static
    {
        $this->addClass('bottom-' . $value);
        return $this;
    }

    public function left(string $value): static
    {
        $this->addClass('left-' . $value);
        return $this;
    }

    public function right(string $value): static
    {
        $this->addClass('right-' . $value);
        return $this;
    }

    public function insetX(string $value): static
    {
        $this->addClass('inset-x-' . $value);
        return $this;
    }

    public function insetY(string $value): static
    {
        $this->addClass('inset-y-' . $value);
        return $this;
    }

    // ============================================================
    // Flex item properties
    // ============================================================

    /**
     * Allow item to grow.
     */
    public function grow(): static
    {
        $this->addClass('grow');
        return $this;
    }

    /**
     * Prevent item from growing.
     */
    public function growNone(): static
    {
        $this->addClass('grow-0');
        return $this;
    }

    /**
     * Allow item to shrink.
     */
    public function shrinkable(): static
    {
        $this->addClass('shrink');
        return $this;
    }

    /**
     * Prevent item from shrinking.
     */
    public function shrinkNone(): static
    {
        $this->addClass('shrink-0');
        return $this;
    }

    /**
     * Set flex basis.
     */
    public function basis(string $value): static
    {
        $this->addClass('basis-' . $value);
        return $this;
    }

    /**
     * Set flex order.
     */
    public function order(int $value): static
    {
        $this->addClass('order-' . $value);
        return $this;
    }

    public function orderFirst(): static
    {
        $this->addClass('order-first');
        return $this;
    }

    public function orderLast(): static
    {
        $this->addClass('order-last');
        return $this;
    }

    public function orderNone(): static
    {
        $this->addClass('order-none');
        return $this;
    }

    // ============================================================
    // Container and centering
    // ============================================================

    /**
     * Apply container max-width constraints.
     */
    public function container(): static
    {
        $this->addClass('container');
        return $this;
    }

    /**
     * Center horizontally using auto margins.
     */
    public function centerX(): static
    {
        $this->addClass('mx-auto');
        return $this;
    }

    /**
     * Center vertically using auto margins.
     */
    public function centerY(): static
    {
        $this->addClass('my-auto');
        return $this;
    }

    // ============================================================
    // Border styles
    // ============================================================

    /**
     * Solid border style.
     */
    public function borderSolid(): static
    {
        $this->addClass('border-solid');
        return $this;
    }

    /**
     * Dashed border style.
     */
    public function borderDashed(): static
    {
        $this->addClass('border-dashed');
        return $this;
    }

    /**
     * Dotted border style.
     */
    public function borderDotted(): static
    {
        $this->addClass('border-dotted');
        return $this;
    }

    /**
     * Double border style.
     */
    public function borderDouble(): static
    {
        $this->addClass('border-double');
        return $this;
    }

    /**
     * No border style.
     */
    public function borderNone(): static
    {
        $this->addClass('border-none');
        return $this;
    }

    // ============================================================
    // List styles
    // ============================================================

    /**
     * Disc list style (bullets).
     */
    public function listDisc(): static
    {
        $this->addClass('list-disc');
        return $this;
    }

    /**
     * Decimal list style (numbers).
     */
    public function listDecimal(): static
    {
        $this->addClass('list-decimal');
        return $this;
    }

    /**
     * No list style.
     */
    public function listNone(): static
    {
        $this->addClass('list-none');
        return $this;
    }

    /**
     * List marker inside.
     */
    public function listInside(): static
    {
        $this->addClass('list-inside');
        return $this;
    }

    /**
     * List marker outside.
     */
    public function listOutside(): static
    {
        $this->addClass('list-outside');
        return $this;
    }

    // ============================================================
    // Italic styling (for non-text elements)
    // ============================================================

    /**
     * Apply italic font style.
     */
    public function italic(): static
    {
        $this->addClass('italic');
        return $this;
    }

    /**
     * Remove italic font style.
     */
    public function notItalic(): static
    {
        $this->addClass('not-italic');
        return $this;
    }
}
