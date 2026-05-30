<?php

namespace BrickPHP\UI;

/**
 * Dialog element.
 */
class Dialog extends Container
{
    protected bool $open = false;

    public function __construct()
    {
        parent::__construct('dialog');
    }

    public function open(bool $open = true): static
    {
        $this->open = $open;
        return $this;
    }

    protected function applyAttributes(): void
    {
        if ($this->open) {
            $this->dom()->attr('open', 'open');
        }
    }
}
