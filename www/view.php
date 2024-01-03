<?php

function compare($prev, $next)
{
    // $prev and $next should always be of the same type
    if (get_class($prev) != get_class($next)) {
        return;
    }


    if (get_class($prev) == TextNode::class) {
        echo "Comparing binding:: $next->value with $prev->value\n";
        return;
    }

    if (get_class($prev) == ConditionalNode::class) {

        if ($prev->condition != $next->condition) {

            if ($next->condition) {
                // remove $prev->else
                // insert $next->then
                echo "Need to replace next\n";
                print_r($next->then);
                return;
            }
            // remove $prev->then
            // insert $next->else
            echo "Need to replace else\n";
            return;
        }
        return;
    }

    if (get_class($prev) == ArrayNode::class) {

        echo "comparing array\n";
        // hash each value and compare: delete, insert, move
        $prevHash = array_map(fn($item) => md5($item), $prev->array);
        $nextHash = array_map(fn($item) => md5($item), $next->array);

        // prev hashes that are not in next
        $toDelete = array_diff($prevHash, $nextHash);
        // map back to prev array
        echo "To delete:\n";
        print_r(array_map(fn($hash) => $prev->array[array_search($hash, $prevHash)], $toDelete));

        // next hashes that are not in prev
        $toInsert = array_diff($nextHash, $prevHash);
        echo "To insert:\n";
        print_r(array_map(fn($hash) => $next->array[array_search($hash, $nextHash)], $toInsert));

        return;
    }

    if (get_class($prev) == HtmlNode::class) {
        echo "Comparing $prev->tag\n";

        for ($i = 0; $i < count($prev->children); $i++) {
            compare($prev->children[$i], $next->children[$i]);
        }
    }
}

abstract class Node
{
    abstract function render(): void;
}

class TextNode extends Node
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }


    function render(): void
    {
        echo htmlentities($this->value);
    }
}

class ConditionalNode extends Node
{
    public bool $condition;
    public ?Node $then;
    public ?Node $else;

    /**
     * @param bool $condition
     * @param Node|null $then
     * @param Node|null $else
     */
    public function __construct(bool $condition, ?Node $then, ?Node $else)
    {
        $this->condition = $condition;
        $this->then = $then;
        $this->else = $else;
    }


    function render(): void
    {
        $call = $this->condition ? $this->then : $this->else;
        if (is_callable($call))
            call_user_func($call);
    }
}

class ArrayNode extends Node
{

    public ?array $array;
    /**
     * @var $callback callback
     */
    public $callback;

    /**
     * @param array|null $array $array
     * @param callback $callback
     */
    public function __construct(?array $array, callable $callback)
    {
        $this->array = $array;
        $this->callback = $callback;
    }


    function render(): void
    {
        if ($this->array == null) {
            return;
        }

        foreach ($this->array as $item) {
            $item = call_user_func($this->callback, $item);
            if ($item instanceof Node) {
                $item->render();
            }
        }
    }
}

class HtmlNode extends Node
{
    public string $tag;
    public ?array $attributes;

    /**
     * @var (Node|string)[]
     */
    public ?array $children = [];

    /**
     * @param string $tag
     * @param array|null $attributes
     * @param null|Node[] $children
     */
    public function __construct(string $tag, ?array $attributes, ?array $children)
    {
        $this->tag = $tag;
        $this->attributes = $attributes;
        $this->children = $children;
    }

    static private array $selfClosingTags = [
        "area",
        "base",
        "br",
        "col",
        "embed",
        "hr",
        "img",
        "input",
        "link",
        "meta",
        "param",
        "source",
        "track",
        "wbr"
    ];


    function render(): void
    {
        echo "<$this->tag";
        if ($this->attributes != null) {
            foreach ($this->attributes as $key => $value) {
                echo " $key=\"$value\"";
            }
        }

        // self closing tags
        if (in_array($this->tag, self::$selfClosingTags)) {
            echo "/>";
            return;
        }

        echo ">";
        if ($this->children != null) {
            foreach ($this->children as $child) {
                $child->render();
            }
        }
        echo "</$this->tag>";
    }
}


function node(string $tag, ?array $attributes, ?array $children): HtmlNode
{
    return new HtmlNode($tag, $attributes, $children);
}

function text($value): TextNode
{
    return new TextNode($value);
}

function conditional($exp, $then, $else): ConditionalNode
{
    return new ConditionalNode($exp, $then, $else);
}

function multiple($array, $callback): ArrayNode
{
    return new ArrayNode($array, $callback);
}
