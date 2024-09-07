<?php

namespace Spwa\Template;

use ReflectionClass;
use ReflectionProperty;

// simulate default constructor behavior
// by enforcing the presence of a parameterless constructor
interface DefaultCtor
{
    public function __construct();
}

/**
 * Represents a base component class.
 *
 * @template TProps
 */
abstract class Component implements DefaultCtor
{
    public function __construct()
    {
    }

    /**
     * The properties associated with the component.
     *
     * @var TProps
     */
    protected $props;

    abstract function view(): ElementNode;

    function restore($props): void
    {
        foreach ($props as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
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
                $result[$propertyName] = $this->$propertyName;
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

    function render(NodePath $path, PathState $listeners): \Spwa\Dom\HtmlNode
    {
        $template = $this->view();
        return $template->render($path, $listeners);
    }

}

