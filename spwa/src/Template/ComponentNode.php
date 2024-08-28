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

    function render(NodePath $path, PathState $state): HtmlNode
    {
        $data = $state->set($path);
        $instance = $data->component ?: new $this->class();
        $data->component = $instance;

        $instance->setProps($this->props);
        return $instance->render($path, $state);
    }
}