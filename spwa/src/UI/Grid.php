<?php

namespace Spwa\UI;

/**
 * CSS grid container. Mirrors the CSS grid properties: explicit track lists
 * (templateColumns / templateRows), template areas, implicit tracks
 * (autoColumns / autoRows / flow), gaps, and box alignment
 * (justify/align/place items + content). Free-form track values are passed
 * as CSS strings; fixed choices use the GridFlow / GridAlign enums.
 *
 * Usage:
 *   UI::grid(3)->gap(4)->content(...)                 // 3 equal columns
 *   UI::grid()->templateColumns('200px 1fr')->gap(2)  // explicit tracks
 *   UI::grid()
 *       ->areas('header header', 'nav main')
 *       ->templateColumns('150px 1fr')
 *       ->alignItems(GridAlign::Stretch)
 *
 * Children are placed by wrapping them in GridItem (UI::gridItem()), which
 * carries the placement (colSpan / rowSpan / colStart / gridArea / …) — so
 * ordinary elements aren't burdened with grid-only methods.
 */
class Grid extends UIElementContent
{
    public function __construct(?int $columns = null)
    {
        parent::__construct('div');
        $this->addStyle('grid', ['display' => 'grid']);
        if ($columns !== null) {
            $this->columns($columns);
        }
    }

    // ============================================================
    // Explicit tracks
    // ============================================================

    /** N equal columns: `repeat(N, minmax(0, 1fr))`. */
    public function columns(int $count, ?Pseudo $pseudo = null): static
    {
        return $this->track('grid-cols', 'grid-template-columns', 'repeat(' . $count . ', minmax(0, 1fr))', $pseudo);
    }

    /** N equal rows: `repeat(N, minmax(0, 1fr))`. */
    public function rows(int $count, ?Pseudo $pseudo = null): static
    {
        return $this->track('grid-rows', 'grid-template-rows', 'repeat(' . $count . ', minmax(0, 1fr))', $pseudo);
    }

    /** Equal columns via a GridColumns value — supports a Pseudo (breakpoint, :has(), …). */
    public function cols(GridColumns $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle(
            $prefix . $value->toClass(),
            ['grid-template-columns' => 'repeat(' . $value->getCount() . ', minmax(0, 1fr))']
        );
        return $this;
    }

    /** Explicit column track list, e.g. '200px 1fr' or 'repeat(3, 1fr)'. */
    public function templateColumns(string $value, ?Pseudo $pseudo = null): static
    {
        return $this->track('grid-cols', 'grid-template-columns', $value, $pseudo);
    }

    /** Explicit row track list, e.g. 'auto 1fr auto'. */
    public function templateRows(string $value, ?Pseudo $pseudo = null): static
    {
        return $this->track('grid-rows', 'grid-template-rows', $value, $pseudo);
    }

    /**
     * Named template areas — one string per row, e.g.
     *   ->areas('header header', 'nav main', 'footer footer')
     * becomes grid-template-areas: "header header" "nav main" "footer footer".
     */
    public function areas(string ...$rows): static
    {
        $css = implode(' ', array_map(fn(string $r) => '"' . trim($r) . '"', $rows));
        $slug = implode('-', array_map(fn(string $r) => str_replace(' ', '_', trim($r)), $rows));
        $this->addStyle('grid-areas-[' . $slug . ']', ['grid-template-areas' => $css]);
        return $this;
    }

    // ============================================================
    // Implicit tracks
    // ============================================================

    /** Size of implicitly-created columns (grid-auto-columns). */
    public function autoColumns(string $value, ?Pseudo $pseudo = null): static
    {
        return $this->track('auto-cols', 'grid-auto-columns', $value, $pseudo);
    }

    /** Size of implicitly-created rows (grid-auto-rows). */
    public function autoRows(string $value, ?Pseudo $pseudo = null): static
    {
        return $this->track('auto-rows', 'grid-auto-rows', $value, $pseudo);
    }

    /** Auto-placement direction (grid-auto-flow). */
    public function flow(GridFlow $flow, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'grid-flow-' . $flow->value, ['grid-auto-flow' => $flow->css()]);
        return $this;
    }

    // ============================================================
    // Gaps
    // ============================================================

    /** Gap between items (row + column). */
    public function gap(Unit|int $value, ?Pseudo $pseudo = null): static
    {
        $value = self::unit($value);
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('gap'), ['gap' => $value->getCssValue()]);
        return $this;
    }

    /** Horizontal (column) gap. */
    public function gapX(Unit|int $value, ?Pseudo $pseudo = null): static
    {
        $value = self::unit($value);
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('gap-x'), ['column-gap' => $value->getCssValue()]);
        return $this;
    }

    /** Vertical (row) gap. */
    public function gapY(Unit|int $value, ?Pseudo $pseudo = null): static
    {
        $value = self::unit($value);
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('gap-y'), ['row-gap' => $value->getCssValue()]);
        return $this;
    }

    // ============================================================
    // Box alignment
    // ============================================================

    /** Inline-axis alignment of items within their cells (justify-items). */
    public function justifyItems(GridAlign $value, ?Pseudo $pseudo = null): static
    {
        return $this->keyword('justify-items', 'justify-items', $value, $pseudo);
    }

    /** Block-axis alignment of items within their cells (align-items). */
    public function alignItems(GridAlign $value, ?Pseudo $pseudo = null): static
    {
        return $this->keyword('items', 'align-items', $value, $pseudo);
    }

    /** Shorthand for align-items + justify-items (place-items). */
    public function placeItems(GridAlign $value, ?Pseudo $pseudo = null): static
    {
        return $this->keyword('place-items', 'place-items', $value, $pseudo);
    }

    /** Inline-axis distribution of the grid within the container (justify-content). */
    public function justifyContent(GridAlign $value, ?Pseudo $pseudo = null): static
    {
        return $this->keyword('justify', 'justify-content', $value, $pseudo);
    }

    /** Block-axis distribution of the grid within the container (align-content). */
    public function alignContent(GridAlign $value, ?Pseudo $pseudo = null): static
    {
        return $this->keyword('content', 'align-content', $value, $pseudo);
    }

    /** Shorthand for align-content + justify-content (place-content). */
    public function placeContent(GridAlign $value, ?Pseudo $pseudo = null): static
    {
        return $this->keyword('place-content', 'place-content', $value, $pseudo);
    }

    // ============================================================
    // Internals
    // ============================================================

    /**
     * Add an arbitrary-value track style. The class encodes the value in
     * Tailwind bracket form with spaces slugged to underscores (so it's a
     * valid single class / selector), while the CSS keeps the real value.
     */
    private function track(string $cls, string $prop, string $value, ?Pseudo $pseudo): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $cls . '-[' . str_replace(' ', '_', $value) . ']', [$prop => $value]);
        return $this;
    }

    /** Add a keyword (enum) alignment style. */
    private function keyword(string $cls, string $prop, GridAlign $value, ?Pseudo $pseudo): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $cls . '-' . $value->value, [$prop => $value->css()]);
        return $this;
    }
}

/**
 * Grid columns property for responsive grids (kept for back-compat with
 * ->cols(GridColumns::count(n), $pseudo)).
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
