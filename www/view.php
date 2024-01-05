<?php
require_once 'levenshtein.php';

const UPDATE_TEXT = 0;
const UPDATE_ATTR = 1;
const DELETE_NODE = 2;
const INSERT_NODE = 3;


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

    abstract function serialize();


    function fillPath(?Node $parent, int $index): void
    {
        $this->parent = $parent;
        $this->index = $index;
    }

    function getPath(): array
    {
        $it = $this;
        while ($it->parent != null) {
            // do not count virtual nodes like conditional and array nodes
            if (get_class($it) != ConditionalNode::class && get_class($it) != ArrayNode::class) {
                $paths[] = $it->index;
            }
            $it = $it->parent;
        }
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
//            echo "The text are not the same!\n";
//            print_r($this->parent->parent->parent->parent->serialize());

            $list[] = ['type' => UPDATE_TEXT, 'value' => $other->value, 'path' => $this->getPath()];
        }
    }

    function serialize()
    {
        return $this->value;
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
            $this->then->fillPath($parent, 0);

        if ($this->else != null)
            $this->else->fillPath($parent, 0);
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
        // place holder
        else echo "<!--if-->";
    }

    function serialize()
    {
        return null;
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
            $child->fillPath($parent, $index);
        }
    }

    function compare(ArrayNode $other, &$list)
    {
        $path = $this->getPath();
        // hash each value and compare: delete, insert, move
        $prevHash = array_map($this->keyCallback, $this->array);
        $nextHash = array_map($other->keyCallback, $other->array);

        $diff = lavenshteinDiff($prevHash, $nextHash);
        foreach ($diff as $action) {

            if ($action->action == SKIP || $action->action == REPLACE) {
                // although the key is the same, there might still be differences
                // check for potential updates
                $this->children[$action->i]->compare($other->children[$action->j], $list);
                continue;
            }
            if ($action->action == DELETE) {
                // update index for all children greater than $index
                for ($i = $action->i + 1; $i < count($this->children); $i++) {
                    $this->children[$i]->index--;
                }
                $list[] = ['type' => DELETE_NODE, 'index' => $action->i, 'path' => $this->children[$action->i]->getPath()];
            }
            if ($action->action == INSERT) {
                for ($i = $action->i + 1; $i < count($this->children); $i++) {
                    $this->children[$i]->index++;
                }

                $list[] = ['type' => INSERT_NODE, 'index' => $action->j, 'value' => $other->children[$action->j]->serialize(), 'path' => $path];
            }
        }
    }

    function serialize()
    {
        return array_map(fn($child) => $child->serialize(), $this->children);
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

    function formatEvent($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            return [$value];
        }
        return [];
    }

    function render(): void
    {
        echo "<$this->tag";
        if ($this->attributes != null) {
            foreach ($this->attributes as $key => $value) {

                // check for event handlers
                if ($key == 'onClick') {
                    echo " onclick='eventHandler(event, " . json_encode($this->formatEvent($value)) . ")'";
                    continue;
                }

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

    function serialize()
    {
        return [
            'tag' => $this->tag,
            'index' => $this->index,
            'attributes' => $this->attributes,
            'children' => array_map(fn($child) => $child->serialize(), $this->children),
        ];
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

function buildAttr($attrs): string
{
    if (empty($attrs) || count($attrs) == 0)
        return "null";

    $attrs = array_map(fn($attr) => "\"" . $attr->name . "\" => \"" . $attr->value . "\"", iterator_to_array($attrs));
    return "[" . implode(", ", $attrs) . "]";
}


function buildNode(DOMNode $node, $index = 0): string
{
    $indent = str_repeat(" ", $index * 4);
    $children = array_map(fn($child) => buildTree($child, $index + 1), iterator_to_array($node->childNodes));
    $c = implode("," . PHP_EOL, $children);

    return $indent . "node(\"" . $node->tagName . "\", " . buildAttr($node->attributes) . ", [" . PHP_EOL . $c . PHP_EOL . $indent . "])";
}

function buildTree(DOMNode $node, $index = 0)
{
    $indent = str_repeat(" ", $index * 4);

    if ($node instanceof DOMElement) {
        // handle special conditional cases
        $if = $node->attributes->getNamedItem("if");
        if ($if != null) {
            $exp = $node->attributes->getNamedItem("if")->value;
            // remove if expression from final output
            $node->removeAttribute("if");

            $then = buildNode($node, $index);
            return $indent . "conditional($exp, " . $then . ", null)";
        }

        return buildNode($node, $index);
    }

    if ($node instanceof DOMText) {
        // replace newline with \n
        // and carriage return with \r
        $text = str_replace("\n", "\\n", $node->textContent);
        $text = str_replace("\r", "\\r", $text);
        return $indent . "text(\"" . $text . "\")";
    }

    return null;
}


