<?php

namespace Spwa\UI;

/**
 * Newspaper-style multi-column flow — content flows down one column then
 * continues at the top of the next, using CSS multi-column layout
 * (column-count / column-width / column-gap).
 *
 * Usage:
 *   UI::multiColumn(3)->gap(Unit::rem(2))->content(
 *       UI::text($longArticle)
 *   )
 *
 *   UI::multiColumn()->columnWidth(Unit::px(240))   // fluid column count
 */
class MultiColumn extends UIElementContent
{
    public function __construct(int $count = 2)
    {
        parent::__construct('div');
        $this->count($count);
    }

    /** Fixed number of columns (column-count). */
    public function count(int $count, ?Pseudo $pseudo = null): static
    {
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . 'columns-' . $count, ['column-count' => (string) $count]);
        return $this;
    }

    /**
     * Preferred column width (column-width) — the browser fits as many
     * columns of at least this width as will fit, so the count is fluid.
     */
    public function columnWidth(Unit|int $value, ?Pseudo $pseudo = null): static
    {
        $value = self::unit($value);
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('col-w'), ['column-width' => $value->getCssValue()]);
        return $this;
    }

    /** Gap between columns (column-gap). */
    public function gap(Unit|int $value, ?Pseudo $pseudo = null): static
    {
        $value = self::unit($value);
        $prefix = $pseudo?->prefix() ?? '';
        $this->addStyle($prefix . $value->withContext('col-gap'), ['column-gap' => $value->getCssValue()]);
        return $this;
    }
}
