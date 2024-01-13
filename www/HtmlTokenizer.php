<?php
/**
 * @param array $needles
 * @param string $string
 * @param int $offset
 * @return int|false
 */
function nextBoundary(string $string, bool $whitespaces, int $offset = 0)
{
    $base = '/[<=>"\']';

    $wpreg = $base . '|\s+/';
    $reg = $base . '/';

    if (preg_match($whitespaces ? $wpreg : $reg, $string, $matches, PREG_OFFSET_CAPTURE, $offset)) {
        return $matches[0];
    }

    return false;
}

class HtmlTokenizer
{

    static function tokenizeHtml(string $html): array
    {
        $ret = [];
        $offset = 0;
        $prev = 0;
        $whitespaces = true;

        while ($offset < strlen($html)) {
            $next = nextBoundary($html, $whitespaces, $offset);
            if ($next === false) {
                break;
            }
            [$match, $index] = $next;
            $content = substr($html, $prev, $index - $prev);
            if (trim($content) !== "") {
                $ret[] = $content;
            }

            switch ($match) {
                case "<":
                    $ret[] = 'open';
                    $whitespaces = true;
                    break;
                case ">":
                    $ret[] = 'close';
                    $whitespaces = false;
                    break;
                case "=":
                    $ret[] = 'attr';
                    break;
                case "/>":
                    $ret[] = 'selfclose';
                    break;
            }

            $offset = $index + strlen($match);
            $prev = $offset;
        }

        return $ret;
    }

}
