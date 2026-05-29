<?php

namespace Spwa\UI;

/**
 * A cell within a Grid. Wraps its content and carries the grid placement
 * (col/row span, start/end lines, named area). Use it as a direct child of a
 * Grid — the GridItem element *is* the grid item, so the placement applies to
 * it. Grid placement deliberately lives here, not on UIElement, so ordinary
 * elements aren't burdened with grid-only methods.
 *
 * Usage:
 *   UI::grid(3)->content(
 *       UI::gridItem(UI::text('wide'))->colSpan(2),
 *       UI::gridItem(UI::text('tall'))->rowSpan(2),
 *       UI::gridItem()->gridArea('footer')->content(UI::text('footer')),
 *   )
 */
class GridItem extends UIElementContent
{
    public function __construct()
    {
        parent::__construct('div');
    }

    /** Span N columns (grid-column: span N / span N). */
    public function colSpan(int $span, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'col-span-' . $span, ['grid-column' => 'span ' . $span . ' / span ' . $span]);
        return $this;
    }

    /** Span N rows (grid-row: span N / span N). */
    public function rowSpan(int $span, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'row-span-' . $span, ['grid-row' => 'span ' . $span . ' / span ' . $span]);
        return $this;
    }

    /** Start column line (grid-column-start). */
    public function colStart(int $line, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'col-start-' . $line, ['grid-column-start' => (string) $line]);
        return $this;
    }

    /** End column line (grid-column-end). */
    public function colEnd(int $line, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'col-end-' . $line, ['grid-column-end' => (string) $line]);
        return $this;
    }

    /** Start row line (grid-row-start). */
    public function rowStart(int $line, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'row-start-' . $line, ['grid-row-start' => (string) $line]);
        return $this;
    }

    /** End row line (grid-row-end). */
    public function rowEnd(int $line, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'row-end-' . $line, ['grid-row-end' => (string) $line]);
        return $this;
    }

    /** Arbitrary grid-column placement, e.g. '1 / 3' or 'span 2'. */
    public function gridColumn(string $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'col-[' . str_replace(' ', '_', $value) . ']', ['grid-column' => $value]);
        return $this;
    }

    /** Arbitrary grid-row placement, e.g. '1 / 3' or 'span 2'. */
    public function gridRow(string $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'row-[' . str_replace(' ', '_', $value) . ']', ['grid-row' => $value]);
        return $this;
    }

    /** Place into a named template area (grid-area). */
    public function gridArea(string $value, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'grid-area-[' . str_replace(' ', '_', $value) . ']', ['grid-area' => $value]);
        return $this;
    }
}
