<?php

namespace Samples\Docs\Components;

use BrickPHP\UI\Color;
use BrickPHP\UI\FontSize;
use BrickPHP\UI\UI;
use BrickPHP\UI\UIElement;
use BrickPHP\UI\Unit;
use BrickPHP\VNode\Component;
use BrickPHP\VNode\VNode;
use PhpToken;

/**
 * Renders a PHP source string with syntax highlighting. Uses PHP's own
 * `PhpToken::tokenize()` so the highlight always matches the language —
 * no external dependency, no regex approximation.
 *
 * Usage:
 *   new PHPCode("<?php\necho 'hello';\n")
 *
 * The `<?php` opening tag is added automatically if missing so users can
 * paste plain snippets like `echo 'hi';`.
 *
 * Colour palette is loosely inspired by VS Code Dark+, mapped to the
 * framework's tonal `Color::*` palette so the CSS extractor picks every
 * style up at build time.
 */
class PHPCode extends Component
{
    public function __construct(private string $code) {}

    protected function build(): VNode
    {
        $spans = $this->tokenize($this->code);

        return UI::pre()
            ->padding(Unit::px(24))
            ->background(Color::slate(950))
            ->color(Color::slate(200))
            ->fontSize(FontSize::Small)
            ->overflow()
            ->content(...$spans);
    }

    // ============================================================
    // Tokenizer
    // ============================================================

    /**
     * @return array<int, UIElement>
     */
    private function tokenize(string $source): array
    {
        $prepended = false;
        $trimmedHead = ltrim($source);
        if (!str_starts_with($trimmedHead, '<?php') && !str_starts_with($trimmedHead, '<?=')) {
            $source = "<?php\n" . $source;
            $prepended = true;
        }

        $tokens = PhpToken::tokenize($source);

        // Strip the synthetic `<?php\n` we added so the rendered output
        // exactly matches what the caller passed in.
        if ($prepended && $tokens !== [] && $tokens[0]->id === T_OPEN_TAG) {
            array_shift($tokens);
        }

        $spans = [];
        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            $color = $this->colorFor($token, $tokens, $i);
            $text  = $token->text;

            // Hover-friendly: each visible run is one span. Whitespace
            // stays uncoloured (default text color) so selecting code
            // doesn't pick up phantom highlights.
            $span = UI::span($text);
            if ($color !== null) {
                $span = $span->color($color);
            }
            $spans[] = $span;
        }

        return $spans;
    }

    /**
     * Decide the highlight colour for one token. Some tokens (notably
     * `T_STRING`) need a peek at neighbours to disambiguate class names,
     * function calls, and bare identifiers.
     *
     * @param array<int, PhpToken> $tokens
     */
    private function colorFor(PhpToken $token, array $tokens, int $i): ?Color
    {
        $id   = $token->id;
        $text = $token->text;

        // Whitespace stays uncoloured so callers get clean copy-paste.
        if ($id === T_WHITESPACE) {
            return null;
        }

        // Comments — slate-400 (matches design's `code-syntax-comment`).
        if ($id === T_COMMENT || $id === T_DOC_COMMENT) {
            return Color::slate(400);
        }

        // Strings — emerald-500.
        if (
            $id === T_CONSTANT_ENCAPSED_STRING
            || $id === T_ENCAPSED_AND_WHITESPACE
            || $id === T_INLINE_HTML
        ) {
            return Color::emerald(500);
        }

        // Quote characters bracketing strings keep the string colour.
        if ($text === '"' || $text === "'" || $text === '`') {
            return Color::emerald(500);
        }

        // Numbers — emerald-400 (slightly lighter than strings to separate).
        if ($id === T_LNUMBER || $id === T_DNUMBER) {
            return Color::emerald(400);
        }

        // Variables — design uses default text for variables; tint slightly
        // so they read distinctly from punctuation.
        if ($id === T_VARIABLE) {
            return Color::slate(200);
        }

        // Open / close PHP tag — orange-500 (matches keyword colour, since
        // the design treats `<?php` as a language keyword).
        if ($id === T_OPEN_TAG || $id === T_OPEN_TAG_WITH_ECHO || $id === T_CLOSE_TAG) {
            return Color::orange(500);
        }

        // Keywords — orange-500 (design's `code-syntax-keyword`).
        if (self::isKeyword($id)) {
            return Color::orange(500);
        }

        // Operators `->`, `::`, `=>` — slate-400, faded.
        if (in_array($id, [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON, T_DOUBLE_ARROW], true)) {
            return Color::slate(400);
        }

        // Bare identifiers — peek at neighbours to disambiguate.
        if ($id === T_STRING) {
            $prev = $this->previousMeaningful($tokens, $i);
            if ($prev !== null && (
                $prev->id === T_NEW
                || $prev->id === T_USE
                || $prev->id === T_NAMESPACE
                || $prev->id === T_DOUBLE_COLON
                || $prev->id === T_EXTENDS
                || $prev->id === T_IMPLEMENTS
                || $prev->id === T_INSTANCEOF
                || $prev->text === '\\'
            )) {
                // Class names — orange-400, the design's `code-syntax-fn` tone.
                return Color::orange(400);
            }

            $next = $this->nextMeaningful($tokens, $i);
            if ($next !== null && $next->text === '(') {
                // Function calls — orange-400.
                return Color::orange(400);
            }

            // Pascal-cased bare word probably names a class.
            if (ctype_upper($text[0] ?? '')) {
                return Color::orange(400);
            }

            return null;
        }

        // Namespaced identifiers — class colour.
        if ($id === T_NAME_QUALIFIED || $id === T_NAME_FULLY_QUALIFIED || $id === T_NAME_RELATIVE) {
            return Color::orange(400);
        }

        return null;
    }

    private function previousMeaningful(array $tokens, int $i): ?PhpToken
    {
        for ($j = $i - 1; $j >= 0; $j--) {
            if ($tokens[$j]->id === T_WHITESPACE) {
                continue;
            }
            return $tokens[$j];
        }
        return null;
    }

    private function nextMeaningful(array $tokens, int $i): ?PhpToken
    {
        $count = count($tokens);
        for ($j = $i + 1; $j < $count; $j++) {
            if ($tokens[$j]->id === T_WHITESPACE) {
                continue;
            }
            return $tokens[$j];
        }
        return null;
    }

    /**
     * Common PHP language keywords whose colour is the "control flow" violet.
     * Listed by token id so we don't have to string-match the spelling.
     */
    private static function isKeyword(int $id): bool
    {
        static $set = null;
        if ($set === null) {
            $names = [
                'T_ABSTRACT', 'T_ARRAY', 'T_AS', 'T_BREAK', 'T_CALLABLE',
                'T_CASE', 'T_CATCH', 'T_CLASS', 'T_CLONE', 'T_CONST',
                'T_CONTINUE', 'T_DECLARE', 'T_DEFAULT', 'T_DO', 'T_ECHO',
                'T_ELSE', 'T_ELSEIF', 'T_EMPTY', 'T_ENDDECLARE', 'T_ENDFOR',
                'T_ENDFOREACH', 'T_ENDIF', 'T_ENDSWITCH', 'T_ENDWHILE',
                'T_ENUM', 'T_EXIT', 'T_EXTENDS', 'T_FINAL', 'T_FINALLY',
                'T_FN', 'T_FOR', 'T_FOREACH', 'T_FUNCTION', 'T_GLOBAL',
                'T_GOTO', 'T_IF', 'T_IMPLEMENTS', 'T_INCLUDE',
                'T_INCLUDE_ONCE', 'T_INSTANCEOF', 'T_INSTEADOF',
                'T_INTERFACE', 'T_ISSET', 'T_LIST', 'T_LOGICAL_AND',
                'T_LOGICAL_OR', 'T_LOGICAL_XOR', 'T_MATCH', 'T_NAMESPACE',
                'T_NEW', 'T_PRINT', 'T_PRIVATE', 'T_PROTECTED', 'T_PUBLIC',
                'T_READONLY', 'T_REQUIRE', 'T_REQUIRE_ONCE', 'T_RETURN',
                'T_STATIC', 'T_SWITCH', 'T_THROW', 'T_TRAIT', 'T_TRY',
                'T_UNSET', 'T_USE', 'T_VAR', 'T_WHILE', 'T_YIELD',
                'T_YIELD_FROM',
            ];
            $set = [];
            foreach ($names as $name) {
                if (defined($name)) {
                    $set[constant($name)] = true;
                }
            }
        }
        return isset($set[$id]);
    }
}
