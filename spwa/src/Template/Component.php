<?php

namespace Spwa\Template;

use Serializable;
use Spwa\Dom\HtmlNode;
use Spwa\Js\JS;

function to($data)
{
    if ($data instanceof Component) {
        return $data->serialize();
    }

    if (is_object($data)) {
        $ret = [];
        $properties = get_object_vars($data);
        foreach ($properties as $key => $value) {
            $ret[$key] = to($value);
        }
        return $ret;
    }

    if (is_array($data)) {
        $ret = [];
        foreach ($data as $key => $value) {
            $ret[$key] = to($value);
        }
        return $ret;
    }
    // Scalar values (int, string, bool, null, etc.)
    return $data;
}

function from($data, $instance = null)
{
    if ($instance instanceof Component) {
        $instance->unserialize($data);
        return $instance;
    }

    if (is_array($data)) {

        if ($instance !== null) {
            // If an object instance is provided, set properties on it
            foreach ($data as $key => $value) {
                if (property_exists($instance, $key)) {
                    $instance->$key = from($value, $instance->$key);
                }
            }
            return $instance;
        }
        // If no instance is provided, treat as associative array or array of values
        $ret = [];
        foreach ($data as $key => $value) {
            $ret[$key] = from($value);
        }
        return $ret;
    }

    // Scalar values (int, string, bool, null, etc.)
    return $data;
}

abstract class Component extends Node implements Serializable
{
    protected $state = null;

    function serialize(): string
    {
        return serialize($this->state);
    }

    public function unserialize($data): void
    {
        $this->state = unserialize($data);
        $this->stateRestored();
    }

    abstract function view(): ElementNode;

    function render(NodePath $path, PathState $state): HtmlNode
    {
        $state->fillComponent($path, $this->state);
        return $this->view()->render($path, $state);
    }

    protected function stateRestored()
    {

    }
}