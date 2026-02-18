<?php

namespace Spwa\UI;

/**
 * CSS Grid container element.
 *
 * Usage:
 *   UI::grid(3)
 *       ->gap(Unit::md())
 *       ->children(...)
 */
class GridElement extends Element
{
    public function __construct(int $cols = 1)
    {
        parent::__construct('div');
        $this->addClass('grid');
        $this->cols($cols);
    }

    /**
     * Set number of columns with optional responsive variants.
     * Usage:
     *   ->cols(1, GridColsValue::cols(2)->sm(), GridColsValue::cols(3)->lg())
     */
    public function cols(int $base, GridColsValue ...$responsive): static
    {
        $this->addStateValue(GridColsValue::cols($base));
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    /**
     * Set columns for small breakpoint.
     */
    public function colsSm(int $count): static
    {
        $this->addStateValue(GridColsValue::cols($count)->sm());
        return $this;
    }

    /**
     * Set columns for medium breakpoint.
     */
    public function colsMd(int $count): static
    {
        $this->addStateValue(GridColsValue::cols($count)->md());
        return $this;
    }

    /**
     * Set columns for large breakpoint.
     */
    public function colsLg(int $count): static
    {
        $this->addStateValue(GridColsValue::cols($count)->lg());
        return $this;
    }

    /**
     * Set columns for extra large breakpoint.
     */
    public function colsXl(int $count): static
    {
        $this->addStateValue(GridColsValue::cols($count)->xl());
        return $this;
    }

    /**
     * Set number of rows.
     */
    public function rows(int $count): static
    {
        $this->addClass('grid-rows-' . $count);
        return $this;
    }

    /**
     * Set gap between grid items.
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
     * Set auto flow direction.
     */
    public function flowRow(): static
    {
        $this->addClass('grid-flow-row');
        return $this;
    }

    public function flowCol(): static
    {
        $this->addClass('grid-flow-col');
        return $this;
    }

    public function flowDense(): static
    {
        $this->addClass('grid-flow-dense');
        return $this;
    }

    /**
     * Set auto columns.
     */
    public function autoColsAuto(): static
    {
        $this->addClass('auto-cols-auto');
        return $this;
    }

    public function autoColsMin(): static
    {
        $this->addClass('auto-cols-min');
        return $this;
    }

    public function autoColsMax(): static
    {
        $this->addClass('auto-cols-max');
        return $this;
    }

    public function autoColsFr(): static
    {
        $this->addClass('auto-cols-fr');
        return $this;
    }

    /**
     * Set auto rows.
     */
    public function autoRowsAuto(): static
    {
        $this->addClass('auto-rows-auto');
        return $this;
    }

    public function autoRowsMin(): static
    {
        $this->addClass('auto-rows-min');
        return $this;
    }

    public function autoRowsMax(): static
    {
        $this->addClass('auto-rows-max');
        return $this;
    }

    public function autoRowsFr(): static
    {
        $this->addClass('auto-rows-fr');
        return $this;
    }

    /**
     * Place content.
     */
    public function placeContentCenter(): static
    {
        $this->addClass('place-content-center');
        return $this;
    }

    public function placeContentStart(): static
    {
        $this->addClass('place-content-start');
        return $this;
    }

    public function placeContentEnd(): static
    {
        $this->addClass('place-content-end');
        return $this;
    }

    public function placeContentBetween(): static
    {
        $this->addClass('place-content-between');
        return $this;
    }

    public function placeContentAround(): static
    {
        $this->addClass('place-content-around');
        return $this;
    }

    public function placeContentEvenly(): static
    {
        $this->addClass('place-content-evenly');
        return $this;
    }

    public function placeContentStretch(): static
    {
        $this->addClass('place-content-stretch');
        return $this;
    }

    /**
     * Place items.
     */
    public function placeItemsCenter(): static
    {
        $this->addClass('place-items-center');
        return $this;
    }

    public function placeItemsStart(): static
    {
        $this->addClass('place-items-start');
        return $this;
    }

    public function placeItemsEnd(): static
    {
        $this->addClass('place-items-end');
        return $this;
    }

    public function placeItemsStretch(): static
    {
        $this->addClass('place-items-stretch');
        return $this;
    }
}
