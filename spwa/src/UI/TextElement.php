<?php

namespace Spwa\UI;

class TextElement extends BaseElement
{


    public function __construct(private string $text)
    {
    }

    function render(): void {
        echo "<span>" . htmlspecialchars($this->text) . "</span>\n";
    }

    function color(Color ...$colors): static
    {
        return $this;
    }

    function fontMedium(): static
    {
        return $this;
    }

    function textXl(): static
    {
        return $this;
    }
}