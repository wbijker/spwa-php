<?php

namespace Spwa\UI;

class TextElement
{

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