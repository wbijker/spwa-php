<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlNode;
use Spwa\Js\JS;

/**
 * @template TProps
 */
class ComponentNode extends Node
{
    private $props;
    private string $className;

    /**
     * @param string $className The name of the class to instantiate.
     * @param TProps $props
     */
    public function __construct(string $className, $props)
    {
        $this->props = $props;
        $this->className = $className;
    }
    
    function render(NodePath $path, PathState $state): HtmlNode
    {
        $data = $state->get($path);
        $instance = $data->component ?? new $this->className();
        $data->component = $instance;
        $instance->setProps($this->props);
        return $instance->render($path, $state);
    }
}