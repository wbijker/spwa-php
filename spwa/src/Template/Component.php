<?php

namespace Spwa\Template;

use ReflectionClass;

abstract class Component extends Node
{
    abstract function view(): ElementNode;

    function render(NodePath $path, EventListeners $listeners): \Spwa\Dom\HtmlNode
    {
        $template = $this->view();
        return $template->render($path, $listeners);
    }

    /**
     * Create an instance of the given class with the provided parameters.
     *
     * @template T of Component
     * @param class-string<T> $class
     * @param mixed ...$parameters
     * @return T
     */
    public static function make(string $class, ...$parameters)
    {
        $reflectionClass = new ReflectionClass($class);
        return $reflectionClass->newInstanceArgs($parameters);
    }

    function compare(Component $other) {
        $prev = $this->view();
        $next = $other->view();
        $prev->compare($next);
    }
}

