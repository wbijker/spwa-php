<?php

namespace Spwa\UI\Examples;

class ShowcaseState
{
    public int $a = 0;
    public int $b = 0;

    public function sum(): int {
        return $this->a + $this->b;
    }
}
