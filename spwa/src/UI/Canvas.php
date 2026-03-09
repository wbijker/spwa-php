<?php

namespace Spwa\UI;

/**
 * Canvas element.
 */
class Canvas extends UIElement
{
    protected ?int $canvasWidth = null;
    protected ?int $canvasHeight = null;

    public function canvasWidth(int $width): static
    {
        $this->canvasWidth = $width;
        return $this;
    }

    public function canvasHeight(int $height): static
    {
        $this->canvasHeight = $height;
        return $this;
    }

    public function canvasSize(int $width, int $height): static
    {
        $this->canvasWidth = $width;
        $this->canvasHeight = $height;
        return $this;
    }

    public function render(): DomNode
    {
        $node = $this->node('canvas');

        if ($this->canvasWidth !== null) {
            $node->attr('width', (string)$this->canvasWidth);
        }

        if ($this->canvasHeight !== null) {
            $node->attr('height', (string)$this->canvasHeight);
        }

        return $node;
    }
}
