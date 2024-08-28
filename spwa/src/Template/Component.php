<?php

namespace Spwa\Template;

use ReflectionClass;

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

