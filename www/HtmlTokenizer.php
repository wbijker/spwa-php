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


    static function feed(int $type, string $value, int &$mode)
    {
//        [14] => slash
//        [15] => close

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
                    echo "Self close tag\n";
                }
                // <... />
                break;
            case self::TOKEN_CONTENT:
                switch ($mode) {
                    case self::MODE_CONTENT:
                        echo "Content " . $value . "\n";
                        break;
                    case self::NODE_OPEN: // tag name
                        $mode = self::NODE_ATTR_NAME;
                        echo "Open tag with " . $value . "\n";
                        break;
                    case self::NODE_CLOSE:
                        // <div>..</div>
                        echo "Close tag with " . $value . "\n";
                        break;
                    case self::NODE_ATTR_NAME: // attribute name
                        echo "Attribute name " . $value . "\n";
                        break;
                    case self::NODE_ATTR_VALUE:
                        echo "Attribute value " . $value . "\n";
                        $mode = self::NODE_ATTR_NAME;
                        break;
                }
                break;
        }
    }

    static function tokenizeHtml(string $html): array
    {
        $ret = [];
        $offset = 0;
        $prev = 0;
        $whitespaces = true;
        $mode = self::MODE_CONTENT;

        while ($offset < strlen($html)) {
            $next = nextBoundary($html, $whitespaces, $offset);
            if ($next === false) {
                break;
            }
            [$match, $index] = $next;
            $content = substr($html, $prev, $index - $prev);
            if (trim($content) !== "") {
                self::feed(self::TOKEN_CONTENT, $content, $mode);
                $ret[] = $content;
            }

            switch ($match) {
                case "<":
                    self::feed(self::TOKEN_OPEN, $content, $mode);
                    $ret[] = 'open';
                    $whitespaces = true;
                    break;
                case ">":
                    self::feed(self::TOKEN_CLOSE, $content, $mode);
                    $ret[] = 'close';
                    $whitespaces = false;
                    break;
                case "=":
                    self::feed(self::TOKEN_EQUAL, $content, $mode);
                    $ret[] = 'attr';
                    break;
                case "/":
                    self::feed(self::TOKEN_SLASH, $content, $mode);
                    $ret[] = 'slash';
                    break;
            }

            $offset = $index + strlen($match);
            $prev = $offset;
        }

        return $ret;
    }

}
