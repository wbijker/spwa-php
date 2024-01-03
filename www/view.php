<?php

function moveElement(&$array, int $from, int $to): void
{
    // Remove the element from the array and keep the value
    $element = array_splice($array, $from, 1)[0];
    // Insert the element at the new position
    array_splice($array, $to, 0, [$element]);
}

function compare($prev, $next, &$list): void
{
    // $prev and $next should always be of the same type
    if (get_class($prev) != get_class($next)) {
        return;
    }

    $prev->compare($next, $list, $list);
}

abstract class Node
{
    public ?Node $parent = null;
    public int $index = 0;

    abstract function render(): void;


    function fillPath(?Node $parent, int $index): void
    {
        $this->parent = $parent;
        $this->index = $index;
    }

    function getPath(): array
    {
        $paths = [$this->index];
        $parent = $this->parent;
        while ($parent != null && $parent->parent != null) {
            $paths[] = $parent->index;
            $parent = $parent->parent;
        }
        // root level parent is always 0 - ignore
        return array_reverse($paths);
    }


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

    function compare(TextNode $other, &$list): void
    {
        if ($this->value != $other->value) {
            $list[] = ['type' => 0, 'value' => $other->value, 'path' => $this->getPath()];
        }
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

    function fillPath(?Node $parent, int $index): void
    {
        parent::fillPath($parent, $index);
        if ($this->then != null)
            $this->then->fillPath($this, 0);

        if ($this->else != null)
            $this->else->fillPath($this, 0);
    }

    public function compare(ConditionalNode $other, &$list): void
    {
//        if ($prev->condition != $next->condition) {
//
//            if ($next->condition) {
//                // remove $prev->else
//                // insert $next->then
////                echo "Need to replace next\n";
////                print_r($next->then);
//                return;
//            }
//            // remove $prev->then
//            // insert $next->else
////            echo "Need to replace else\n";
//            return;
//        }
//        return;
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
     * @var $keyCallback callback
     */
    public $keyCallback;

    /**
     * @var Node[] $children
     */
    public array $children = [];

    /**
     * @param array|null $array $array
     * @param callback $callback
     */
    public function __construct(?array $array, callable $callback, callable $keyCallback = null)
    {
        $this->array = $array;
        $this->callback = $callback;
        if ($this->array != null)
            $this->children = array_map($callback, $array);

        // use the provided key or use the default md5 on the serialized data
        $this->keyCallback = $keyCallback ?? fn($item) => md5(json_encode($item));
    }


    function render(): void
    {
        foreach ($this->children as $index => $child) {
            $key = call_user_func($this->keyCallback, $this->array[$index]);
//            echo "<!-- $key -->";
            $child->render();
        }
    }

    function fillPath(?Node $parent, int $index): void
    {
        parent::fillPath($parent, $index);
        foreach ($this->children as $index => $child) {
            $child->fillPath($this, $index);
        }
    }

    function compare(ArrayNode $other, &$list)
    {
        // hash each value and compare: delete, insert, move
        $prevHash = array_map($this->keyCallback, $this->array);
        $nextHash = array_map($other->keyCallback, $other->array);

        for ($i = 0; $i < max(count($prevHash), count($nextHash)); $i++) {
            $prevItem = $prevHash[$i];
            $nextItem = $nextHash[$i];

            if ($prevItem == null) {
                $list[] = ['type' => 1, 'value' => $nextItem, 'path' => $this->getPath()];
                continue;
            }
            if ($nextItem == null) {
                $list[] = ['type' => 2, 'path' => $this->getPath()];
                continue;
            }

            if ($prevItem == $nextItem) {
                continue;
            }

            // search for prev in next
            // DOM that already existed is present in the new list
            $found = array_search($prevItem, $other->array);
            if ($found !== false) {
                // move
                $list[] = ['type' => 3, 'from' => $i, 'to' => $found, 'path' => $this->getPath()];
                moveElement($prevHash, $i, $found);
            }

            $list[] = ['type' => 4, 'value' => $nextItem, 'path' => $this->getPath()];
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

    function fillPath(?Node $parent, int $index): void
    {
        parent::fillPath($parent, $index);
        foreach ($this->children as $index => $child) {
            $child->fillPath($this, $index);
        }
    }

    public function compare(HtmlNode $other, &$list)
    {
        for ($i = 0; $i < count($this->children); $i++) {
            compare($this->children[$i], $other->children[$i], $list);
        }
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

function multiple($array, $callback, $keyCallback = null): ArrayNode
{
    return new ArrayNode($array, $callback, $keyCallback);
}
