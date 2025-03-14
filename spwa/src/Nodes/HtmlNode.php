<?php

namespace Spwa\Nodes;


use Spwa\Js\JsFunction;
use Spwa\Js\JsLiteral;

abstract class HtmlNode extends Node
{

    protected bool $ignore = false;

    function initialize(?Node $parent, PathInfo $current, StateManager $manager): void
    {
        $this->path = $current->setKey($this->key);

        foreach ($this->children as $child) {
            $child->initialize($this, $this->path->addChild(), $manager);
        }
    }

    function initializeAndCompare(?Node $parent, PathInfo $current, StateManager $manager, Node $old, PatchBuilder $patch): void
    {
        if (!$old instanceof HtmlNode || count($this->children) != count($old->children)) {
            // initialize everything before rendering
            $this->initialize($this, $current, $manager);
            $patch->replace($this, $old);
            return;
        }

        // ignore patches
        if ($this->ignore) {
            return;
        }

        $this->path = $current->setKey($this->key);

        // compare attributes
        foreach ($this->attrs as $key => $value) {
            $oldAttr = $old->attrs[$key] ?? null;
            if ($oldAttr != $value) {
                $patch->updateAttr($this, $key, $value);
            }
        }
        // compare children
        foreach ($this->children as $i => $child) {
            $child->initializeAndCompare($this, $this->path->addChild(), $manager, $old->children[$i], $patch);
        }
    }

    public array $attrs = [];
    public array $events = [];
    public $bindings = null;
    /**
     * @var Node[] $children
     */
    public array $children = [];

    function addChild(HtmlNode $node)
    {
        $this->children[] = $node;
    }

    function find(array $path): ?Node
    {
        // remove one element from path
        $key = array_shift($path);
        if ($key === null)
            return $this;

        return $this->children[$key]?->find($path);
    }

    public function triggerEvent(string $event, array $args): void
    {
        $handler = $this->events[$event] ?? null;
        if (is_callable($handler)) {
            $handler(...$args);
        }
    }

    public function setEvents(array $events): void
    {
        $filtered = array_filter($events, fn($v) => $v !== null);
        foreach ($filtered as $key => $value) {
            $this->attrs[$key] = JsFunction::create("handleEvent", $key, new JsLiteral('event'));
            $this->events[$key] = $value;
        }
        $this->events = array_merge($this->events, $filtered);
    }

    protected function setStyle(array $style)
    {
        $this->attrs['style'] = implode('; ', array_map(
                fn($key, $value) => "{$key}: {$value}",
                array_keys($style),
                $style
            )) . ';';
    }

    protected function setAttrs(array $attrs)
    {
        // filter null values
        if ($attrs == null) {
            return;
        }

        $this->attrs = array_merge($this->attrs, array_filter($attrs, fn($v) => $v !== null));
    }

    abstract function tag(): string;

    function closed(): bool
    {
        return false;
    }


    function renderHtml(): string
    {
        $tag = $this->tag();
        $ret = "<$tag";

        $copy = $this->attrs;

        if ($this->path != null) {
            $copy['path'] = $this->path->pathStr();
            $copy['key'] = $this->path->keyStr();
        }

        foreach ($copy as $key => $value) {
            $v = htmlentities($value);
            $ret .= " $key=\"$v\"";
        }

        if ($this->closed()) {
            $ret .= "/>";
            return $ret;
        }

        $ret .= ">";
        foreach ($this->children as $child) {
            // $child->initialize($this, $this->path->addChild(), $manager);
//            $ret .= $child->renderHtml($context->next($this, $this->path->addChild()));
            $ret .= $child->renderHtml();
        }
        $ret .= "</$tag>";
        return $ret;
    }


    function finalize(StateManager $manager): void
    {
        foreach ($this->children as $child) {
            $child->finalize($manager);
        }
    }

}