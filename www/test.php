<?php

interface HtmlTagFacade
{
    public function execute(HtmlTag $tag): void;
}

class HtmlAttr implements HtmlTagFacade
{
    public string $name;
    public string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function execute(HtmlTag $tag): void
    {
        $tag->attrs[$this->name] = $this->value;
    }
}

abstract class HtmlNode
{
    abstract function render(): void;
}

class HtmlText extends HtmlNode implements HtmlTagFacade
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    function render(): void
    {
        echo htmlspecialchars($this->value);
    }

    public function execute(HtmlTag $tag): void
    {
        $tag->children[] = $this;
    }
}

class HtmlTag extends HtmlNode implements HtmlTagFacade
{
    public string $tag;
    public array $attrs;
    /**
     * @var HtmlNode[] $children
     */
    public array $children;

    /**
     * @param string $tag
     * @param HtmlTag[] $children
     */
    public function __construct(string $tag, array $attrs = [], array $children = [])
    {
        $this->tag = $tag;
        $this->attrs = $attrs;
        $this->children = $children;
    }

    public function execute(HtmlTag $tag): void
    {
        // add this to $tags children
        $tag->children[] = $this;
    }

    function render(): void
    {
        echo "<$this->tag";
        foreach ($this->attrs as $key => $value) {
            echo " $key=\"$value\"";
        }
        echo ">";
        foreach ($this->children as $child) {
            $child->render();
        }
        echo "</$this->tag>";
    }
}

/**
 * @param string $tag
 * @param HtmlTagFacade ...$args
 * @return HtmlTag
 */
function tag(string $tag, ...$args): HtmlTag
{
    $ret = new HtmlTag($tag);
    foreach ($args as $arg) {
        $arg->execute($ret);
    }
    return $ret;
}

/**
 * @param HtmlTagFacade ...$args
 * @return HtmlTag
 */
function div(...$args): HtmlTag
{
    return tag("div", ...$args);
}

function text(string $value): HtmlText
{
    return new HtmlText($value);
}

function _class(string ...$args): HtmlAttr
{
    // join $args to single string
    $joined = implode(" ", array_filter($args));
    return new HtmlAttr("class", $joined);
}


$node = div(_class("mx-10"),
    div(_class("text-blue-600 p-2"), text("some text")),
    div(_class("text-purple-600 p-2"), text("some text"))
);


$node->render();