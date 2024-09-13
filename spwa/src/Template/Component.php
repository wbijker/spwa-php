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

    function restore($props): void
    {
        foreach ($props as $key => $value) {
            if (property_exists($this, $key)) {
                if ($this->$key instanceof Component) {
                    $this->$key->restore($value);
                } else $this->$key = $value;
            }
        }
    }

    function save(): array
    {
        $reflect = new ReflectionClass($this);
        $publicProperties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

        $result = [];
        foreach ($publicProperties as $property) {
            if ($property->class === get_class($this)) { // Only include properties declared in this class
                $propertyName = $property->getName();
                $value = $this->$propertyName;

                $result[$propertyName] = $value instanceof Component
                    ? $value->save()
                    : $this->$propertyName;
            }
        }

        return $result;
    }

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

