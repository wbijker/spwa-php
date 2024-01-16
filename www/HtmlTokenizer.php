<?php
/**
 * @param array $needles
 * @param string $string
 * @param int $offset
 * @return array|false
 */
function nextBoundary(string $string, bool $whitespaces, ?string $quote, int $offset)
{
    if ($quote !== null) {
        $next = strpos($string, $quote, $offset);
        if ($next === false) {
            return false;
        }
        return [$quote, $next];
    }

    $whitespaceReg = '/[<=>\/"\']|\s+/';
    $reg = '/[<\/]/';

    if (preg_match($whitespaces ? $whitespaceReg : $reg, $string, $matches, PREG_OFFSET_CAPTURE, $offset)) {
        return $matches[0];
    }

    return false;
}


abstract class HtmlDomNode
{
    public ?HtmlDomNode $parent = null;

    /**
     * @param HtmlDomNode|null $parent
     */
    public function __construct(?HtmlDomNode $parent)
    {
        $this->parent = $parent;
    }

    abstract function render();

}


class HtmlTextNode extends HtmlDomNode
{
    public string $text;

    public function __construct(?HtmlDomNode $parent, string $text)
    {
        parent::__construct($parent);
        $this->text = $text;
    }

    function render()
    {
        echo htmlentities($this->text);
    }
}

class HtmlTagNode extends HtmlDomNode
{
    public string $name;
    public array $attributes;
    public array $children;

//    public DomNode $parent;

    public function __construct(?HtmlDomNode $parent, string $name, array $attributes = [], array $children = [])
    {
        parent::__construct($parent);
        $this->name = $name;
        $this->attributes = $attributes;
        $this->children = $children;
    }

    public function addChild(HtmlDomNode $node)
    {
        $this->children[] = $node;
    }

    public function setAttribute(string $name, ?string $value)
    {
        $this->attributes[$name] = $this->attributes[$name] ?? [];
        if ($value != null) {
            $this->attributes[$name][] = $value;
        }
    }

    public function setLastAttributeValue($value)
    {
        $this->attributes[array_key_last($this->attributes)][] = $value;
    }

    function getAndRemoveAttr(string $attr) {
        $value = $this->attributes[$attr];
        unset($this->attributes[$attr]);
        return $value;
    }

    function render()
    {
        echo "<$this->name";
        foreach ($this->attributes as $name => $values) {
            foreach ($values as $value) {
                echo " $name=\"$value\"";
            }
        }
        echo ">";
        foreach ($this->children as $child) {
            $child->render();
        }
        echo "</$this->name>";
    }
}

class HtmlTokenizer
{
    const MODE_CONTENT = 0;
    const NODE_OPEN = 1;
    const NODE_CLOSE = 2;
    const NODE_ATTR_NAME = 3;
    const NODE_ATTR_VALUE = 4;

    const TOKEN_OPEN = 0;
    const TOKEN_CLOSE = 1;
    const TOKEN_EQUAL = 2;
    const TOKEN_SLASH = 3;
    const TOKEN_CONTENT = 4;


    static function feed(int $type, string $value, int &$mode, &$node)
    {
        switch ($type) {
            case self::TOKEN_OPEN:
                // next content token is the tag name
                $mode = self::NODE_OPEN;
                break;
            case self::TOKEN_CLOSE:
                // >
                $mode = self::MODE_CONTENT;
                break;
            case self::TOKEN_EQUAL:
                $mode = self::NODE_ATTR_VALUE;
                break;
            case self::TOKEN_SLASH:
                // </..>
                if ($mode === self::NODE_OPEN) {
                    $mode = self::NODE_CLOSE;
                } else {
                    $node = $node->parent;
                }
                // <... />
                break;
            case self::TOKEN_CONTENT:
                switch ($mode) {
                    case self::MODE_CONTENT:
                        if ($node != null) {
                            $node->addChild(new HtmlTextNode($node, $value));
                        }
                        break;
                    case self::NODE_OPEN: // tag name
                        $mode = self::NODE_ATTR_NAME;
                        $child = new HtmlTagNode($node, $value, [], []);
                        if ($node != null) {
                            $node->addChild($child);
                        }
                        $node = $child;
                        break;
                    case self::NODE_CLOSE:
                        // need to check $value
                        // transverse up
                        $node = $node->parent;
                        break;
                    case self::NODE_ATTR_NAME: // attribute name
                        if ($node != null) {
                            $node->setAttribute($value, null);
                        }
                        break;
                    case self::NODE_ATTR_VALUE:
                        if ($node != null && count($node->attributes) > 0) {
                            // fill last attr
                            $node->setLastAttributeValue($value);
                        }
                        $mode = self::NODE_ATTR_NAME;
                        break;
                }
                break;
        }
    }


    static function parseTokens(string $html): array
    {
        $ret = [];
        $offset = 0;
        $prev = 0;
        $whitespaces = true;
        $quote = null;

        while ($offset < strlen($html)) {
            $next = nextBoundary($html, $whitespaces, $quote, $offset);
            if ($next === false) {
                break;
            }

            [$match, $index] = $next;

            $content = substr($html, $prev, $index - $prev);
            if (trim($content) !== "") {
//                echo "<span style='color: orange'>".htmlentities($content)."</span>";
                $ret[] = [self::TOKEN_CONTENT, $content];
            }
//            echo "<b>".htmlentities($match)."</b>";
            switch ($match) {
                case "<":
                    $ret[] = [self::TOKEN_OPEN, $match];
                    $whitespaces = true;
                    break;
                case ">":
                    $ret[] = [self::TOKEN_CLOSE, $match];
                    $whitespaces = false;
                    break;
                case "=":
                    $ret[] = [self::TOKEN_EQUAL, $match];
                    break;
                case "/":
                    $ret[] = [self::TOKEN_SLASH, $match];
                    break;
                case "'":
                case '"':
                    if ($quote === null) {
                        $quote = $match;
                    } else if ($quote === $match) {
                        $quote = null;
                    }
                    break;
            }

            $offset = $index + strlen($match);
            $prev = $offset;
        }

        return $ret;
    }

    static function parseHtml(string $html): HtmlTagNode
    {
        $mode = self::MODE_CONTENT;
        // define one root node
        $root = new HtmlTagNode(null, '', [], []);
        $node = $root;

        $tokens = self::parseTokens($html);
        foreach ($tokens as $token) {
            self::feed($token[0], $token[1], $mode, $node);
        }

        return $root;
    }

}
