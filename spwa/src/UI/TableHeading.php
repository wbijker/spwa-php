<?php

namespace Spwa\UI;

/**
 * Table heading cell (th).
 */
class TableHeading extends UIElement
{
    public function __construct()
    {
        parent::__construct('th');
        $this->addStyle('px-4', ['padding-left' => '1rem', 'padding-right' => '1rem']);
        $this->addStyle('py-3', ['padding-top' => '0.75rem', 'padding-bottom' => '0.75rem']);
        $this->addStyle('text-left', ['text-align' => 'left']);
        $this->addStyle('font-semibold', ['font-weight' => '600']);
    }
}
