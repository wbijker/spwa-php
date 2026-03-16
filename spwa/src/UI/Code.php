<?php

namespace Spwa\UI;

/**
 * Code element for inline code.
 */
class Code extends UIElement
{
    public function __construct(protected string $content = '')
    {
        parent::__construct('code');
    }

    public function build(): DomNode
    {
        return $this->dom()->setTag('code')->children($this->content);
    }
}
