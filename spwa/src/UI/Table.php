<?php

namespace Spwa\UI;

/**
 * Table element for displaying tabular data.
 *
 * Usage:
 *   UI::table()
 *       ->head(
 *           UI::row(
 *               Table::heading()->content(UI::text("Column1")),
 *               Table::heading()->content(UI::text("Column2")),
 *           )
 *       )
 *       ->body(
 *           UI::row([
 *               Table::cell()->content("Row 1, Cell 1"),
 *               Table::cell()->content("Row 1, Cell 2"),
 *           ]),
 *       )
 */
class Table extends UIElement
{
    protected ?string $caption = null;
    protected ?TableColgroup $colgroup = null;
    protected ?TableRow $headRow = null;
    /** @var TableRow[] */
    protected array $bodyRows = [];
    /** @var TableRow[] */
    protected array $footRows = [];

    public function __construct()
    {
        $this->addStyle('w-full', ['width' => '100%']);
        $this->addStyle('border-collapse', ['border-collapse' => 'collapse']);
    }

    /**
     * Set table caption.
     */
    public function caption(string $caption): static
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * Set column group.
     */
    public function colgroup(TableColgroup $colgroup): static
    {
        $this->colgroup = $colgroup;
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

    public function render(): Node
    {
        $node = $this->node('table');

        if ($this->caption !== null) {
            $node->children(Node::el('caption')->children($this->caption));
        }

        if ($this->colgroup !== null) {
            $node->children($this->colgroup->toNode());
        }

        if ($this->headRow !== null) {
            $thead = Node::el('thead')->children($this->headRow->render());
            $node->children($thead);
        }

        if (!empty($this->bodyRows)) {
            $tbody = Node::el('tbody');
            foreach ($this->bodyRows as $row) {
                $tbody->children($row->render());
            }
            $node->children($tbody);
        }

        if (!empty($this->footRows)) {
            $tfoot = Node::el('tfoot');
            foreach ($this->footRows as $row) {
                $tfoot->children($row->render());
            }
            $node->children($tfoot);
        }

        return $node;
    }
}
