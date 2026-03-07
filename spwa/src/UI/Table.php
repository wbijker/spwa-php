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

    public function render(): string
    {
        $classAttr = $this->classAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        $html = "<table{$classHtml}>";

        if ($this->headRow !== null) {
            $html .= '<thead>';
            $html .= $this->headRow->render();
            $html .= '</thead>';
        }

        if (!empty($this->bodyRows)) {
            $html .= '<tbody>';
            foreach ($this->bodyRows as $row) {
                $html .= $row->render();
            }
            $html .= '</tbody>';
        }

        $html .= '</table>';

        return $html;
    }

    public function collectStyles(): array
    {
        $styles = parent::collectStyles();

        if ($this->headRow !== null) {
            $styles = array_merge($styles, $this->headRow->collectStyles());
        }

        foreach ($this->bodyRows as $row) {
            $styles = array_merge($styles, $row->collectStyles());
        }

        return $styles;
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

    public function render(): string
    {
        $classAttr = $this->classAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        $html = "<tr{$classHtml}>";
        foreach ($this->cells as $cell) {
            $html .= $cell->render();
        }
        $html .= '</tr>';

        return $html;
    }

    public function collectStyles(): array
    {
        $styles = parent::collectStyles();

        foreach ($this->cells as $cell) {
            $styles = array_merge($styles, $cell->collectStyles());
        }

        return $styles;
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

    public function render(): string
    {
        $classAttr = $this->classAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        $innerHtml = '';
        if ($this->child !== null) {
            $innerHtml = $this->child->render();
        } elseif ($this->textContent !== null) {
            $innerHtml = htmlspecialchars($this->textContent);
        }

        return "<th{$classHtml}>{$innerHtml}</th>";
    }

    public function collectStyles(): array
    {
        $styles = parent::collectStyles();

        if ($this->child !== null) {
            $styles = array_merge($styles, $this->child->collectStyles());
        }

        return $styles;
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

    public function render(): string
    {
        $classAttr = $this->classAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        $innerHtml = '';
        if ($this->child !== null) {
            $innerHtml = $this->child->render();
        } elseif ($this->textContent !== null) {
            $innerHtml = htmlspecialchars($this->textContent);
        }

        return "<td{$classHtml}>{$innerHtml}</td>";
    }

    public function collectStyles(): array
    {
        $styles = parent::collectStyles();

        if ($this->child !== null) {
            $styles = array_merge($styles, $this->child->collectStyles());
        }

        return $styles;
    }
}
