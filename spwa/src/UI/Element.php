<?php

namespace Spwa\UI;

class Element
{

    function alignCenter(): static
    {
        return $this;
    }

    function alignRight(): static
    {
        return $this;
    }

    function alignLeft(): static
    {
        return $this;
    }

    function alignTop(): static
    {
        return $this;

    }

    function alignMiddle(): static
    {
        return $this;

    }

    function alignBottom(): static
    {
        return $this;

    }

    function maxWidth(Unit ...$units): static
    {
        return $this;
    }

    function padding(Unit ...$unit): static
    {
        return $this;
    }

    function radius(Unit ...$units): static
    {
        return $this;
    }

    function shadow(Unit ...$units): static
    {
        return $this;
    }

    function background(Color ...$color): static
    {
        return $this;
    }

    function outline(Unit ...$units): static
    {
        return $this;
    }

    function outlineColor(Color ...$color): static
    {
        return $this;
    }

    function size(Unit ...$units): static
    {
        return $this;
    }

    function shrink(Unit ...$units): static
    {
        return $this;
    }

    function children(array $children): static
    {
        return $this;
    }

}