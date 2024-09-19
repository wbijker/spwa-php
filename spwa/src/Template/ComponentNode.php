<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlNode;

/**
 * @template TProps
 */
class ComponentNode extends Node
{
    private $props;
    /**
     * @var Component|string
     */
    private $instance;

    /**
     * @param Component<TProps> $instance
     * @param TProps $props
     */
    public function __construct($instance, $props)
    {
        $this->instance = $instance;
        $this->props = $props;
    }

    function render(NodePath $path, PathState $state): HtmlNode
    {
        $this->instance->setProps($this->props);
        return $this->instance->render($path, $state);
    }
}