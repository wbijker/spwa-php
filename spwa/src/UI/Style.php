<?php

namespace Spwa\UI;

abstract class Style
{
    function hover(): static
    {
        return $this;
    }

    function dark(): static
    {
        return $this;
    }

    function break(Unit ...$units): static
    {
        return $this;
    }


//    function md(): static
//    {
//        return $this;
//    }
//
//    function lg(): static
//    {
//        return $this;
//    }
//
//    function xl(): static
//    {
//        return $this;
//    }
}