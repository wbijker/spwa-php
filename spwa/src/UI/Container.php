<?php

namespace Spwa\UI;

/**
 * Basic container element that can hold children.
 */
class Container extends UIElement
{
    /** @var UIElement[] */
    protected array $children = [];

    /**
     * Add child elements.
     */
    public function content(UIElement ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    /**
     * Get child elements.
     * @return UIElement[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function render(): string
    {
        $classAttr = $this->classAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        $childrenHtml = '';
        foreach ($this->children as $child) {
            $childrenHtml .= $child->render();
        }

        return "<div{$classHtml}>{$childrenHtml}</div>";
    }

    public function collectStyles(): array
    {
        $styles = parent::collectStyles();

        // Collect styles from children
        foreach ($this->children as $child) {
            $styles = array_merge($styles, $child->collectStyles());
        }

        return $styles;
    }
}
