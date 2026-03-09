<?php

namespace Spwa\UI;

/**
 * Picture element for responsive images.
 */
class Picture extends UIElement
{
    /** @var Source[] */
    protected array $sources = [];
    protected ?Image $fallback = null;

    public function sources(Source ...$sources): static
    {
        $this->sources = array_merge($this->sources, $sources);
        return $this;
    }

    public function fallback(Image $image): static
    {
        $this->fallback = $image;
        return $this;
    }

    public function render(): DomNode
    {
        $node = $this->node('picture');

        foreach ($this->sources as $source) {
            $node->children($source->toNode());
        }

        if ($this->fallback !== null) {
            $node->children($this->fallback->render());
        }

        return $node;
    }
}
