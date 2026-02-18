<?php

namespace Spwa\UI;

/**
 * Table element with rows and cells.
 *
 * Usage:
 *   UI::table()
 *       ->header("Name", "Age", "Email")
 *       ->row("John", "25", "john@example.com")
 *       ->row("Jane", "30", "jane@example.com")
 */
class TableElement extends BaseStyledElement
{
    /** @var string[] */
    protected array $headers = [];

    /** @var array<string[]> */
    protected array $rows = [];

    /** @var BaseElement[] */
    protected array $headerElements = [];

    /** @var array<BaseElement[]> */
    protected array $rowElements = [];

    /**
     * Set table headers.
     */
    public function header(string ...$headers): static
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Add a row to the table.
     */
    public function row(string ...$cells): static
    {
        $this->rows[] = $cells;
        return $this;
    }

    /**
     * Add header elements.
     */
    public function headerElements(BaseElement ...$elements): static
    {
        $this->headerElements = $elements;
        return $this;
    }

    /**
     * Add row elements.
     */
    public function rowElements(BaseElement ...$elements): static
    {
        $this->rowElements[] = $elements;
        return $this;
    }

    /**
     * Apply bordered style.
     */
    public function bordered(): static
    {
        $this->addClass('border');
        $this->addClass('border-collapse');
        return $this;
    }

    /**
     * Apply striped style.
     */
    public function striped(): static
    {
        return $this;
    }

    /**
     * Make table full width.
     */
    public function fullWidth(): static
    {
        $this->addClass('w-full');
        return $this;
    }

    public function render(): void
    {
        $classAttr = $this->buildClassAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        echo "<table{$classHtml}>";

        // Render headers
        if (!empty($this->headers) || !empty($this->headerElements)) {
            echo "<thead><tr>";
            if (!empty($this->headerElements)) {
                foreach ($this->headerElements as $element) {
                    echo "<th>";
                    $element->render();
                    echo "</th>";
                }
            } else {
                foreach ($this->headers as $header) {
                    echo "<th>" . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . "</th>";
                }
            }
            echo "</tr></thead>";
        }

        // Render body
        echo "<tbody>";
        if (!empty($this->rowElements)) {
            foreach ($this->rowElements as $rowElements) {
                echo "<tr>";
                foreach ($rowElements as $element) {
                    echo "<td>";
                    $element->render();
                    echo "</td>";
                }
                echo "</tr>";
            }
        } else {
            foreach ($this->rows as $row) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') . "</td>";
                }
                echo "</tr>";
            }
        }
        echo "</tbody>";

        echo "</table>";
    }
}
