<?php

namespace Spwa\UI;

/**
 * Table data cell (td).
 */
class TableCell extends UIElement
{
    public function __construct()
    {
        parent::__construct('td');
        $this->addStyle('px-4', ['padding-left' => '1rem', 'padding-right' => '1rem']);
        $this->addStyle('py-3', ['padding-top' => '0.75rem', 'padding-bottom' => '0.75rem']);
    }
}
