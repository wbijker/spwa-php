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

    public function saveState(): array
    {
        $state = [];
        $reflection = new \ReflectionClass($this);

        // Get properties declared in the current class only
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE) as $property) {
            if ($property->getDeclaringClass()->getName() === $reflection->getName()) {
                $property->setAccessible(true); // Allow access to protected/private properties
                $value = $property->getValue($this);

                // If the property is a Component, recursively save its state
                if ($value instanceof Component) {
                    $state[$property->getName()] = $value->saveState();
                } else {
                    $state[$property->getName()] = $value;
                }
            }
        }
        return $state;
    }

    public function restoreState(array $state): void
    {
        $reflection = new \ReflectionClass($this);

        // Restore properties declared in the current class only
        foreach ($state as $name => $value) {
            if ($reflection->hasProperty($name)) {
                $property = $reflection->getProperty($name);
                if ($property->getDeclaringClass()->getName() === $reflection->getName()) {
                    $property->setAccessible(true); // Allow access to protected/private properties

                    // If the property is a Component, recursively restore its state
                    if ($this->$name instanceof Component) {
                        $this->$name->restoreState($value);
                    } else {
                        $property->setValue($this, $value);
                    }
                }
            }
        }
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

