<?php

namespace Spwa\UI;

/**
 * Basic block element (div).
 *
 * Usage:
 *   UI::element()
 *       ->background(Color::blue(500))
 *       ->padding(Unit::md())
 *       ->children(UI::text("Hello"))
 */
class Element extends BaseStyledElement
{
    /** @var BaseElement[] */
    protected array $children = [];

    public function __construct(
        protected string $tag = 'div'
    ) {
    }

    /**
     * Set child elements.
     */
    public function children(BaseElement ...$children): static
    {
        $this->children = $children;
        return $this;
    }

    /**
     * Add child element(s).
     */
    public function child(BaseElement ...$children): static
    {
        foreach ($children as $child) {
            $this->children[] = $child;
        }
        return $this;
    }

    // Display
    public function block(): static
    {
        $this->addClass('block');
        return $this;
    }

    public function inline(): static
    {
        $this->addClass('inline');
        return $this;
    }

    public function inlineBlock(): static
    {
        $this->addClass('inline-block');
        return $this;
    }

    public function flex(): static
    {
        $this->addClass('flex');
        return $this;
    }

    public function inlineFlex(): static
    {
        $this->addClass('inline-flex');
        return $this;
    }

    public function grid(): static
    {
        $this->addClass('grid');
        return $this;
    }

    // Self alignment (when inside flex/grid)
    public function selfStart(): static
    {
        $this->addClass('self-start');
        return $this;
    }

    public function selfCenter(): static
    {
        $this->addClass('self-center');
        return $this;
    }

    public function selfEnd(): static
    {
        $this->addClass('self-end');
        return $this;
    }

    public function selfStretch(): static
    {
        $this->addClass('self-stretch');
        return $this;
    }

    public function selfAuto(): static
    {
        $this->addClass('self-auto');
        return $this;
    }

    // Flex grow/shrink
    public function grow(): static
    {
        $this->addClass('grow');
        return $this;
    }

    public function growNone(): static
    {
        $this->addClass('grow-0');
        return $this;
    }

    public function shrink(): static
    {
        $this->addClass('shrink');
        return $this;
    }

    public function shrinkNone(): static
    {
        $this->addClass('shrink-0');
        return $this;
    }

    public function render(): void
    {
        $classAttr = $this->buildClassAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        echo "<{$this->tag}{$classHtml}>";

        foreach ($this->children as $child) {
            $child->render();
        }

        echo "</{$this->tag}>";
    }
}
