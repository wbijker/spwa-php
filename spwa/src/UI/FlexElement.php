<?php

namespace Spwa\UI;

/**
 * Flex container element for rows and columns layout.
 *
 * Usage:
 *   UI::rows()
 *       ->gap(Unit::md())
 *       ->alignX(AlignXValue::center())
 *       ->children(...)
 *
 *   UI::columns()
 *       ->gap(Unit::lg())
 *       ->children(...)
 *
 *   UI::flex(DirectionValue::row()->lg()->column())
 *       ->gap(Unit::sm())
 */
class FlexElement extends Element
{
    public function __construct(
        ?DirectionValue $direction = null,
        string $tag = 'div'
    ) {
        parent::__construct($tag);
        $this->addClass('flex');

        if ($direction !== null) {
            $this->addStateValue($direction);
        }
    }

    /**
     * Set flex direction.
     */
    public function direction(DirectionValue ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Set as row direction.
     */
    public function row(): static
    {
        $this->addClass('flex-row');
        return $this;
    }

    /**
     * Set as column direction.
     */
    public function column(): static
    {
        $this->addClass('flex-col');
        return $this;
    }

    public function col(): static
    {
        return $this->column();
    }

    /**
     * Set gap between flex items.
     */
    public function gap(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asGap());
        }
        return $this;
    }

    /**
     * Set column gap.
     */
    public function gapX(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asGapX());
        }
        return $this;
    }

    /**
     * Set row gap.
     */
    public function gapY(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asGapY());
        }
        return $this;
    }

    /**
     * Horizontal alignment (justify-content).
     */
    public function alignX(AlignXValue ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Justify content to start. Accepts variadic for responsive values.
     */
    public function justifyStart(AlignXValue ...$responsive): static
    {
        $this->addStateValue(AlignXValue::start());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Justify content to center. Accepts variadic for responsive values.
     */
    public function justifyCenter(AlignXValue ...$responsive): static
    {
        $this->addStateValue(AlignXValue::center());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Justify content to end. Accepts variadic for responsive values.
     */
    public function justifyEnd(AlignXValue ...$responsive): static
    {
        $this->addStateValue(AlignXValue::end());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Justify content with space between. Accepts variadic for responsive values.
     */
    public function justifyBetween(AlignXValue ...$responsive): static
    {
        $this->addStateValue(AlignXValue::between());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Justify content with space around. Accepts variadic for responsive values.
     */
    public function justifyAround(AlignXValue ...$responsive): static
    {
        $this->addStateValue(AlignXValue::around());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Justify content with even spacing. Accepts variadic for responsive values.
     */
    public function justifyEvenly(AlignXValue ...$responsive): static
    {
        $this->addStateValue(AlignXValue::evenly());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Vertical alignment (align-items).
     */
    public function alignY(AlignYValue ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Align items to start. Accepts variadic for responsive values.
     * Usage: itemsStart() or itemsStart(AlignYValue::center()->md())
     */
    public function itemsStart(AlignYValue ...$responsive): static
    {
        $this->addStateValue(AlignYValue::start());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Align items to center. Accepts variadic for responsive values.
     * Usage: itemsCenter() or itemsCenter(AlignYValue::start()->md())
     */
    public function itemsCenter(AlignYValue ...$responsive): static
    {
        $this->addStateValue(AlignYValue::center());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Align items to end. Accepts variadic for responsive values.
     * Usage: itemsEnd() or itemsEnd(AlignYValue::center()->md())
     */
    public function itemsEnd(AlignYValue ...$responsive): static
    {
        $this->addStateValue(AlignYValue::end());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Align items to baseline. Accepts variadic for responsive values.
     */
    public function itemsBaseline(AlignYValue ...$responsive): static
    {
        $this->addStateValue(AlignYValue::baseline());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Stretch items. Accepts variadic for responsive values.
     */
    public function itemsStretch(AlignYValue ...$responsive): static
    {
        $this->addStateValue(AlignYValue::stretch());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Center both axes.
     */
    public function center(): static
    {
        $this->addClass('justify-center');
        $this->addClass('items-center');
        return $this;
    }

    /**
     * Flex wrap.
     */
    public function wrap(): static
    {
        $this->addClass('flex-wrap');
        return $this;
    }

    public function wrapReverse(): static
    {
        $this->addClass('flex-wrap-reverse');
        return $this;
    }

    public function nowrap(): static
    {
        $this->addClass('flex-nowrap');
        return $this;
    }

    // ============================================================
    // QuestPDF-style Row Item methods
    // These create flex item wrappers with specific sizing
    // ============================================================

    /**
     * Add an auto-sized item (takes only needed space).
     * (QuestPDF: Row.AutoItem)
     */
    public function autoItem(BaseElement $child): static
    {
        $wrapper = (new Element('div'))->shrinkHorizontal()->children($child);
        $this->children[] = $wrapper;
        return $this;
    }

    /**
     * Add a relative/proportional item (grows to fill space).
     * (QuestPDF: Row.RelativeItem)
     */
    public function relativeItem(BaseElement $child, int $weight = 1): static
    {
        $wrapper = (new Element('div'))->grow()->basis('0')->children($child);
        if ($weight > 1) {
            $wrapper->addClass('flex-[' . $weight . ']');
        }
        $this->children[] = $wrapper;
        return $this;
    }

    /**
     * Add a constant-width item (fixed size).
     * (QuestPDF: Row.ConstantItem)
     */
    public function constantItem(BaseElement $child, int $width): static
    {
        $wrapper = (new Element('div'))
            ->shrinkNone()
            ->growNone()
            ->width(Unit::px($width))
            ->children($child);
        $this->children[] = $wrapper;
        return $this;
    }

    /**
     * Add spacing between items (creates empty space).
     * (QuestPDF: Row.Spacing alternative)
     */
    public function spacingItem(int $size): static
    {
        $spacer = (new Element('div'))->shrinkNone()->width(Unit::px($size));
        $this->children[] = $spacer;
        return $this;
    }

    // ============================================================
    // QuestPDF Column Item methods
    // ============================================================

    /**
     * Add an item to the column.
     * (QuestPDF: Column.Item)
     */
    public function item(BaseElement $child): static
    {
        $this->children[] = $child;
        return $this;
    }

    // ============================================================
    // Content distribution (align-content)
    // ============================================================

    public function contentStart(): static
    {
        $this->addClass('content-start');
        return $this;
    }

    public function contentCenter(): static
    {
        $this->addClass('content-center');
        return $this;
    }

    public function contentEnd(): static
    {
        $this->addClass('content-end');
        return $this;
    }

    public function contentBetween(): static
    {
        $this->addClass('content-between');
        return $this;
    }

    public function contentAround(): static
    {
        $this->addClass('content-around');
        return $this;
    }

    public function contentEvenly(): static
    {
        $this->addClass('content-evenly');
        return $this;
    }

    public function contentStretch(): static
    {
        $this->addClass('content-stretch');
        return $this;
    }

    // ============================================================
    // Flex direction variants
    // ============================================================

    public function rowReverse(): static
    {
        $this->addClass('flex-row-reverse');
        return $this;
    }

    public function columnReverse(): static
    {
        $this->addClass('flex-col-reverse');
        return $this;
    }

    public function colReverse(): static
    {
        return $this->columnReverse();
    }

    // ============================================================
    // Flex shorthand
    // ============================================================

    public function flex1(): static
    {
        $this->addClass('flex-1');
        return $this;
    }

    public function flexAuto(): static
    {
        $this->addClass('flex-auto');
        return $this;
    }

    public function flexInitial(): static
    {
        $this->addClass('flex-initial');
        return $this;
    }

    public function flexNone(): static
    {
        $this->addClass('flex-none');
        return $this;
    }
}
