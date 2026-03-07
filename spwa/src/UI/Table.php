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
    protected ?TableRow $headRow = null;
    /** @var TableRow[] */
    protected array $bodyRows = [];

    public function __construct()
    {
        $this->addStyle('w-full', ['width' => '100%']);
        $this->addStyle('border-collapse', ['border-collapse' => 'collapse']);
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

    public function render(): Node
    {
        $node = $this->node('table');

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

        return $node;
    }
}

/**
 * Table row element.
 */
class TableRow extends UIElement
{
    /** @var (TableCell|TableHeading)[] */
    protected array $cells = [];

    public function __construct(TableCell|TableHeading ...$cells)
    {
        $this->cells = $cells;
    }

    /**
     * Add cells to the row.
     */
    public function cells(TableCell|TableHeading ...$cells): static
    {
        $this->cells = array_merge($this->cells, $cells);
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('tr');

        foreach ($this->cells as $cell) {
            $node->children($cell->render());
        }

        return $node;
    }
}

/**
 * Table heading cell (th).
 */
class TableHeading extends UIElement
{
    protected ?UIElement $child = null;
    protected ?string $textContent = null;

    public function __construct()
    {
        $this->addStyle('px-4', ['padding-left' => '1rem', 'padding-right' => '1rem']);
        $this->addStyle('py-3', ['padding-top' => '0.75rem', 'padding-bottom' => '0.75rem']);
        $this->addStyle('text-left', ['text-align' => 'left']);
        $this->addStyle('font-semibold', ['font-weight' => '600']);
    }

    /**
     * Set cell content (UIElement or string).
     */
    public function content(UIElement|string $content): static
    {
        if ($content instanceof UIElement) {
            $this->child = $content;
        } else {
            $this->textContent = $content;
        }
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('th');

        if ($this->child !== null) {
            $node->children($this->child->render());
        } elseif ($this->textContent !== null) {
            $node->children($this->textContent);
        }

        return $node;
    }
}

/**
 * Table data cell (td).
 */
class TableCell extends UIElement
{
    protected ?UIElement $child = null;
    protected ?string $textContent = null;

    public function __construct()
    {
        $this->addStyle('px-4', ['padding-left' => '1rem', 'padding-right' => '1rem']);
        $this->addStyle('py-3', ['padding-top' => '0.75rem', 'padding-bottom' => '0.75rem']);
    }

    /**
     * Set cell content (UIElement or string).
     */
    public function content(UIElement|string $content): static
    {
        if ($content instanceof UIElement) {
            $this->child = $content;
        } else {
            $this->textContent = $content;
        }
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('td');

        if ($this->child !== null) {
            $node->children($this->child->render());
        } elseif ($this->textContent !== null) {
            $node->children($this->textContent);
        }

        return $node;
    }
}
