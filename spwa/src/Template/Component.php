<?php

namespace Spwa\Template;

use ReflectionClass;
use ReflectionProperty;
use Spwa\Dom\HtmlNode;

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

    function init()
    {

    }

//    public function serialize(): string
//    {
//        return serialize($this);
//    }
//
//    function unserialize($data)
//    {
////        if (empty($data) || !is_string($data)) {
////            return;
////        }
////        $restored = unserialize($data);
////        print_r($restored);
////        // Manually copy properties from the unserialized object
////        foreach (get_object_vars($restored) as $property => $value) {
////            $this->$property = $value;
////        }
//    }

    /**
     * @param TProps $props
     * @return void
     */
    function setProps($props): void
    {
        $this->props = $props;
    }

    function render(NodePath $path, PathState $listeners): HtmlNode
    {
        $template = $this->view();
        return $template->render($path, $listeners);
    }

}

