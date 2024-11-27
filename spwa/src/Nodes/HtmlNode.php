<?php

namespace Spwa\Nodes;

use Spwa\Js\JS;

abstract class HtmlNode extends Node
{
    function compare(Node $node, PatchBuilder $patch): void
    {
        if (!$node instanceof HtmlNode) {
            $patch->replace($this, $node);
            return;
        }

        if (count($this->children) != count($node->children)) {
            $patch->replace($this, $node);
            return;
        }

        // compare attributes
        foreach ($this->attrs as $key => $value) {
            $old = $node->attrs[$key] ?? null;
            if ($old != $value) {
                $patch->updateAttr($this, $key, $value);
            }
        }
        // compare children
        foreach ($this->children as $i => $child) {
            $child->compare($node->children[$i], $patch);
        }
    }

    public array $attrs = [];
    public array $events = [];
    /**
     * @var Node[] $children
     */
    public array $children = [];

    function addChild(HtmlNode $node)
    {
        $this->children[] = $node;
    }

    protected function setEvents(array $events)
    {
        $this->events = array_filter($events, fn($v) => $v !== null);
    }

    protected function setAttrs(array $attrs)
    {
        // filter null values
        if ($attrs == null) {
            return;
        }
        $this->attrs = array_filter($attrs, fn($v) => $v !== null);
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
        $copy['path'] = $this->pathStr();
        $copy['key'] = $this->keyStr();

        foreach ($copy as $key => $value) {
            $ret .= " $key=\"$value\"";
        }

        if ($this->closed()) {
            $ret .= "/>";
            return $ret;
        }

        $ret .= ">";
        foreach ($this->children as $child) {
            $ret .= $child->renderHtml();
        }
        $ret .= "</$tag>";
        return $ret;
    }

    function initialize(?Node $parent, PathInfo $path, StateManager $manager): void
    {
        $path->set($this, $parent, true);

        // bind events
        foreach ($this->events as $key => $value) {
            $manager->bindEvent($this, $key, $value);
        }

        foreach ($this->children as $i => $child) {
            $child->initialize($this, new PathInfo($i, $i), $manager);
        }

    }

    function finalize(StateManager $manager): void
    {
        foreach ($this->children as $child) {
            $child->finalize($manager);
        }
    }

}