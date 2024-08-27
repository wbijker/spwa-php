<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlNode;

/**
 * @template TProps
 */
class ComponentNode extends Node
{
    private string $class;
    private $props;

    /**
     * @param class-string<Component<TProps>> $class The name of the component
     * @param TProps $props
     */
    public function __construct(string $class, $props)
    {
        $this->class = $class;
        $this->props = $props;
    }

    function render(NodePath $path, EventListeners $listeners): HtmlNode
    {
        $component = new $this->class();
        $component->setProps($this->props);
        return $component->render($path, $listeners);
    }
}