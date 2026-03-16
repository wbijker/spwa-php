<?php

namespace Spwa\UI;

use Spwa\State\StateManager;
use Spwa\VNode\RenderPhase;
use Spwa\VNode\VNode;

/**
 * A UIElement that can hold child content.
 * Provides the content() method and children storage.
 */
class UIElementContent extends UIElement
{
    /** @var (DomNode|VNode|string)[] */
    protected array $children = [];

    public function content(DomNode|VNode|string|null ...$children): static
    {
        foreach ($children as $child) {
            $this->children[] = $child ?? new CommentDomNode();
        }
        return $this;
    }

    public function build(): DomNode
    {
        $domChildren = [];
        foreach ($this->children as $child) {
            if ($child instanceof UIElement) {
                $domChildren[] = $child->build();
            } elseif ($child instanceof DomNode) {
                $domChildren[] = $child;
            } elseif (is_string($child)) {
                $domChildren[] = $child;
            }
        }

        $this->dom()->content(...$domChildren);
        return $this->domNode;
    }

    public function render(StateManager $state, ?VNode $parent = null, RenderPhase $phase = RenderPhase::Initial): DomNode
    {
        $this->parent = $parent;
        if (empty($this->path)) {
            $this->path = $parent?->getPath() ?? [];
        }

        $this->eventOwner = $this->findOwningComponent($parent);
        if ($this->eventOwner !== null) {
            $this->dom()->setEventOwner($this->eventOwner);
        }

        $domChildren = [];
        $index = 0;
        foreach ($this->children as $child) {
            if ($child instanceof VNode) {
                $child->setPath([...$this->path, $index]);
                $domChildren[] = $child->render($state, $this, $phase);
            } elseif ($child instanceof DomNode) {
                $domChildren[] = $child;
            } elseif (is_string($child)) {
                $domChildren[] = $child;
            }
            $index++;
        }

        $this->dom()->content(...$domChildren);

        return $this->dom()->assignPaths($this->path);
    }
}
