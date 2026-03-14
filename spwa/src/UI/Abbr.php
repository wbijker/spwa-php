<?php

namespace Spwa\UI;

/**
 * Abbreviation element.
 */
class Abbr extends UIElement
{
    protected ?string $title = null;

    public function __construct(protected string $content)
    {
        parent::__construct('abbr');
    }

    public function title(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('abbr')->children($this->content);

        if ($this->title !== null) {
            $node->attr('title', $this->title);
        }

        return $node;
    }
}
