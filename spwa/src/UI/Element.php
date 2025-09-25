<?php

namespace Spwa\UI;

interface StyleResolver
{
    function resolve(BaseElement $parent): array;
}

class StaticResolver implements StyleResolver
{

    /**
     * @var string[]
     */
    private array $classes = [];

    public function __construct(string ...$classes)
    {
        $this->classes = $classes;
    }

    public function resolve(BaseElement $parent): array
    {
        return $this->classes;
    }
}

abstract class ParentResolver implements StyleResolver
{
    public function resolve(BaseElement $parent): array
    {
        if ($parent instanceof Element) {
            return $this->resolveForElement($parent);
        }
        if ($parent instanceof FlexElement) {
            return $this->resolveForFlex($parent);
        }
        if ($parent instanceof GridElement) {
            return $this->resolveForGrid($parent);
        }
        if ($parent instanceof TableCellElement) {
            return $this->resolveForTableCell($parent);
        }
    }

    abstract function resolveForElement(BaseElement $parent): array;

    abstract function resolveForFlex(BaseElement $parent): array;

    abstract function resolveForGrid(BaseElement $parent): array;

    abstract function resolveForTableCell(BaseElement $parent): array;
}

class Element extends BaseElement
{
    public function resolve(BaseElement $parent): array
    {
        return [];
    }

    public function __construct(private string $tag = "div")
    {
    }

    protected array $childElements = [];
    protected array $classes = [];

    function render(): void
    {
        echo "<" . $this->tag . " class=\"" . implode(" ", $this->classes) . "\">\n";
        foreach ($this->childElements as $child) {
            $child->render();
        }
        echo "</" . $this->tag . ">\n";
    }

    function alignCenter(): static
    {
        // if width or max-width is set then mx-auto
        // if parent is flex / grid then justify-center


        // parent: flex, grid, element, table-cell
        // new Props(flex: "mx-auto", grid: "mx-auto", element: $parent->add("flex"), "mx-auto", tableCell: "align-center");
        // inline-block with text-center
//        $this->attrs[] = new Align()

//        $this->classes[] = "mx-auto";
        return $this;
    }

    function alignRight(): static
    {
        return $this;
    }

    function alignLeft(): static
    {
        return $this;
    }

    function alignTop(): static
    {
        // if flex / grid
        $this->classes[] = "self-start";
        return $this;
    }

    function alignMiddle(): static
    {
        return $this;

    }

    function alignBottom(): static
    {
        return $this;

    }

    function maxWidth(Unit ...$units): static
    {
        return $this;
    }

    function padding(Unit ...$unit): static
    {
        return $this;
    }

    function radius(Unit ...$units): static
    {
        return $this;
    }

    function shadow(Unit ...$units): static
    {
        return $this;
    }

    function background(Color ...$color): static
    {
        // $color[0]->
        return $this;
    }

    function outline(Unit ...$units): static
    {
        return $this;
    }

    function outlineColor(Color ...$color): static
    {
        return $this;
    }

    function size(Unit ...$units): static
    {
        return $this;
    }

    function shrink(Unit ...$units): static
    {
        return $this;
    }

    function children(array $children): static
    {
        // set parent for each child
        // then run the resolver method

        $this->childElements = $children;
        return $this;
    }

    public function extendScreen(): static
    {
        $this->classes[] = "w-screen";
        $this->classes[] = "h-screen";
        return $this;
    }

}