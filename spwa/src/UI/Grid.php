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
     * Set base column count. For responsive variants, chain ->cols() with a
     * Pseudo modifier.
     */
    public function columns(int $base): static
    {
        $this->addStyle('grid-cols-' . $base, ['grid-template-columns' => 'repeat(' . $base . ', minmax(0, 1fr))']);
        return $this;
    }

    /**
     * Set columns with optional Pseudo modifier (breakpoint, :has(), …).
     *
     * Usage:
     *   ->cols(GridColumns::count(1))
     *   ->cols(GridColumns::count(2), Pseudo::sm())
     *   ->cols(GridColumns::count(4), Pseudo::lg()->has(
     *       Selector::child()->lastChild()->nthChild('4n-1')->not(Selector::nthChild('3n'))
     *   ))
     */
    public function cols(GridColumns $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle(
            $prefix . $value->toClass(),
            ['grid-template-columns' => 'repeat(' . $value->getCount() . ', minmax(0, 1fr))']
        );
        return $this;
    }

    /**
     * Set gap between items.
     */
    public function gap(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('gap'), ['gap' => $value->getCssValue()]);
        return $this;
    }

    /**
     * Set horizontal gap.
     */
    public function gapHorizontal(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('gap-x'), ['column-gap' => $value->getCssValue()]);
        return $this;
    }

    /**
     * Set vertical gap.
     */
    public function gapVertical(Unit $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('gap-y'), ['row-gap' => $value->getCssValue()]);
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
