<?php

namespace Spwa\UI;

/**
 * Table element for displaying tabular data.
 *
 * Usage:
 *   UI::table()
 *       ->head(
 *           Table::row(
 *               Table::heading()->content("Column1"),
 *               Table::heading()->content("Column2"),
 *           )
 *       )
 *       ->body(
 *           Table::row(
 *               Table::cell()->content("Row 1, Cell 1"),
 *               Table::cell()->content("Row 1, Cell 2"),
 *           ),
 *       )
 */
class Table extends UIElementContent
{
    protected ?string $tableCaption = null;
    protected ?TableColgroup $tableColgroup = null;
    protected ?TableRow $headRow = null;
    /** @var TableRow[] */
    protected array $bodyRows = [];
    /** @var TableRow[] */
    protected array $footRows = [];

    public function __construct()
    {
        parent::__construct('table');
        $this->addStyle('w-full', ['width' => '100%']);
        $this->addStyle('border-collapse', ['border-collapse' => 'collapse']);
    }

    /**
     * Set table caption.
     */
    public function caption(string $caption): static
    {
        $this->tableCaption = $caption;
        return $this;
    }

    /**
     * Set column group.
     */
    public function colgroup(TableColgroup $colgroup): static
    {
        $this->tableColgroup = $colgroup;
        return $this;
    }

    /**
     * Set table header row.
     */
    public function head(TableRow $row): static
    {
        $this->headRow = $row;
        return $this;
    }

    /**
     * Set table body rows.
     */
    public function body(TableRow ...$rows): static
    {
        $this->bodyRows = $rows;
        return $this;
    }

    /**
     * Set table footer rows.
     */
    public function foot(TableRow ...$rows): static
    {
        $this->footRows = $rows;
        return $this;
    }

    /**
     * Create a heading cell.
     */
    public static function heading(): TableHeading
    {
        return new TableHeading();
    }

    /**
     * Create a data cell.
     */
    public static function cell(): TableCell
    {
        return new TableCell();
    }

    /**
     * Create a table row.
     */
    public static function row(TableCell|TableHeading ...$cells): TableRow
    {
        return new TableRow(...$cells);
    }

    /**
     * Create a column group.
     */
    public static function createColgroup(TableCol ...$cols): TableColgroup
    {
        return new TableColgroup(...$cols);
    }

    /**
     * Create a column definition.
     */
    public static function col(): TableCol
    {
        return new TableCol();
    }

    /**
     * Render to HTML string.
     */
    public function toHtml(): string
    {
        if ($this->tableCaption !== null) {
            $this->content(DomNode::el('caption')->content($this->tableCaption));
        }

        if ($this->tableColgroup !== null) {
            $this->content($this->tableColgroup->toNode());
        }

        if ($this->headRow !== null) {
            $thead = DomNode::el('thead')->content($this->headRow);
            $this->content($thead);
        }

        if (!empty($this->bodyRows)) {
            $tbody = DomNode::el('tbody');
            foreach ($this->bodyRows as $row) {
                $tbody->content($row);
            }
            $this->content($tbody);
        }

        if (!empty($this->footRows)) {
            $tfoot = DomNode::el('tfoot');
            foreach ($this->footRows as $row) {
                $tfoot->content($row);
            }
            $this->content($tfoot);
        }

        return parent::toHtml();
    }
}
