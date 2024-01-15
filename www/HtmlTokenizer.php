<?php
/**
 * @param array $needles
 * @param string $string
 * @param int $offset
 * @return int|false
 */
function nextBoundary(string $string, bool $whitespaces, int $offset = 0)
{
    $base = '/[<=>\/"\']';

    $wpreg = $base . '|\s+/';
    $reg = $base . '/';

    if (preg_match($whitespaces ? $wpreg : $reg, $string, $matches, PREG_OFFSET_CAPTURE, $offset)) {
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


}

;

class HtmlTextNode extends HtmlDomNode
{
    public string $text;

    public function __construct(?HtmlDomNode $parent, string $text)
    {
        parent::__construct($parent);
        $this->text = $text;
    }
}

;

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
//        $node->parent = $this;
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
                            $node->attributes[] = [$value, null];
                        }
                        break;
                    case self::NODE_ATTR_VALUE:
                        if ($node != null && count($node->attributes) > 0) {
                            // fill last attr
                            $node->attributes[count($node->attributes) - 1][1] = $value;
                        }
                        $mode = self::NODE_ATTR_NAME;
                        break;
                }
                break;
        }
    }

    static function tokenizeHtml(string $html): HtmlDomNode
    {
        $ret = [];
        $offset = 0;
        $prev = 0;
        $whitespaces = true;
        $mode = self::MODE_CONTENT;
        // define one root node
        $root = new HtmlTagNode(null, '', [], []);
        $node = $root;

        while ($offset < strlen($html)) {
            $next = nextBoundary($html, $whitespaces, $offset);
            if ($next === false) {
                break;
            }
            [$match, $index] = $next;
            $content = substr($html, $prev, $index - $prev);
            if (trim($content) !== "") {
                self::feed(self::TOKEN_CONTENT, $content, $mode, $node);
                $ret[] = $content;
            }

            switch ($match) {
                case "<":
                    self::feed(self::TOKEN_OPEN, $content, $mode, $node);
                    $ret[] = 'open';
                    $whitespaces = true;
                    break;
                case ">":
                    self::feed(self::TOKEN_CLOSE, $content, $mode, $node);
                    $ret[] = 'close';
                    $whitespaces = false;
                    break;
                case "=":
                    self::feed(self::TOKEN_EQUAL, $content, $mode, $node);
                    $ret[] = 'attr';
                    break;
                case "/":
                    self::feed(self::TOKEN_SLASH, $content, $mode, $node);
                    $ret[] = 'slash';
                    break;
            }

            $offset = $index + strlen($match);
            $prev = $offset;
        }

        return $root;
    }

}
