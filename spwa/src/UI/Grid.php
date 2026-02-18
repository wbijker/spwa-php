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
class Grid extends Container
{
    public function __construct(int $columns = 1)
    {
        $this->classes[] = 'grid';
        $this->classes[] = 'grid-cols-' . $columns;
    }

    /**
     * Set column count with responsive variants.
     */
    public function columns(int $base, GridColumns ...$responsive): static
    {
        $this->classes[] = 'grid-cols-' . $base;
        foreach ($responsive as $value) {
            $this->apply($value);
        }
        return $this;
    }

    /**
     * Set gap between items.
     */
    public function gap(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('gap');
        }
        return $this;
    }

    /**
     * Set horizontal gap.
     */
    public function gapHorizontal(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('gap-x');
        }
        return $this;
    }

    /**
     * Set vertical gap.
     */
    public function gapVertical(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('gap-y');
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

    public static function count(int $columns): static
    {
        return new static($columns);
    }
}
