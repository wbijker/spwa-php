<?php

namespace Spwa\UI;

/**
 * Grid layout - items arranged in rows and columns.
 *
 * Usage:
 *   UI::grid(3)
 *       ->gap(Unit::base())
 *       ->content(...)
 */
class Grid extends UIElementContent
{
    public function __construct(int $columns = 1)
    {
        parent::__construct('div');
        $this->addStyle('grid', ['display' => 'grid']);
        $this->addStyle('grid-cols-' . $columns, ['grid-template-columns' => 'repeat(' . $columns . ', minmax(0, 1fr))']);
    }

    /**
     * Set column count with responsive variants.
     */
    public function columns(int $base, GridColumns ...$responsive): static
    {
        $this->addStyle('grid-cols-' . $base, ['grid-template-columns' => 'repeat(' . $base . ', minmax(0, 1fr))']);
        foreach ($responsive as $value) {
            $this->addStyle($value->toClass(), ['grid-template-columns' => 'repeat(' . $value->getCount() . ', minmax(0, 1fr))']);
        }
        return $this;
    }

    /**
     * Set gap between items.
     */
    public function gap(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('gap'), ['gap' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set horizontal gap.
     */
    public function gapHorizontal(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('gap-x'), ['column-gap' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set vertical gap.
     */
    public function gapVertical(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('gap-y'), ['row-gap' => $value->getCssValue()]);
        }
        return $this;
    }
}

/**
 * Grid columns property for responsive grids.
 */
class GridColumns extends Property
{
    public function __construct(
        protected int $count
    ) {
    }

    protected function base(): string
    {
        return 'grid-cols-' . $this->count;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public static function count(int $columns): static
    {
        return new static($columns);
    }
}
