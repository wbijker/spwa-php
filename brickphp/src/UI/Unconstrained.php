<?php

namespace BrickPHP\UI;

/**
 * Removes the parent's size constraint — the child renders at its natural
 * (max-content) size and may overflow, instead of being shrunk to fit.
 * Modeled after QuestPDF's Unconstrained element.
 *
 * `flex: none` stops a flex parent from growing/shrinking it; `width:
 * max-content` + `max-width: none` let the content drive the width even past
 * the parent's bounds.
 *
 * Usage:
 *   UI::unconstrained()->content(
 *       UI::text('This line is laid out at its natural width and may overflow')
 *   )
 */
class Unconstrained extends UIElementContent
{
    public function __construct()
    {
        parent::__construct('div');
        $this->addStyle('unconstrained', [
            'width' => 'max-content',
            'max-width' => 'none',
            'flex' => 'none',
        ]);
    }
}
