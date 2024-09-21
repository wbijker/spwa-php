<?php

namespace Spwa\Template;

use ReflectionClass;
use ReflectionProperty;
use Spwa\Dom\HtmlNode;
use Spwa\Js\JS;

/**
 * Represents a base component class.
 *
 * @template TProps
 */
abstract class Component extends Node
{
    /**
     * The properties associated with the component.
     *
     * @var TProps
     */
    protected $props;

    abstract function view(): ElementNode;

    // mark all properties for serialization except 'props'
    public function __sleep()
    {
        // Get all object properties
        $properties = get_object_vars($this);
        // Remove the 'props' property
        unset($properties['props']);
        // Return the keys of the remaining properties
        return array_keys($properties);
    }

    public function __wakeup()
    {
        $this->restored();
    }

    function restored()
    {
    }

    /**
     * @param array<string, mixed> $properties
     * @phpstan-param TProps $properties
     * @return self
     */
    function setProps(array $properties): Component
    {
        $this->props = $properties;
        return $this;
    }

    function render(NodePath $path, PathState $listeners): HtmlNode
    {
        $template = $this->view();
        return $template->render($path, $listeners);
    }

}

