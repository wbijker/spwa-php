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
 * Colour palette is modelled on PHPStorm's Darcula scheme — muted
 * brown-orange keywords, purple variables, olive-green strings, blue
 * numbers, yellow function names. Mapped to the framework's tonal
 * `Color::*` factories so the CSS extractor harvests every shade at
 * build time.
 */
class PHPCode extends Component
{
    public function __construct(private string $code) {}

    protected function build(): VNode
    {
        $spans = $this->tokenize($this->code);

        // Default foreground is PHPStorm Darcula's `#A9B7C6` — closest
        // framework match is slate-300. Background stays slate-950 so the
        // surrounding CodeWindow chrome flows in visually.
        return UI::pre()
            ->padding(Unit::px(24))
            ->background(Color::slate(950))
            ->color(Color::slate(300))
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
            // `->color($this->colorFor(...))` is the deliberate shape:
            // the CSS extractor follows `$this->name(...)` calls inside
            // chain args and harvests Color literals from the called
            // method's body, so writing it inline (rather than via a
            // local `$color` variable) is what registers every syntax
            // colour as a real CSS rule at build time.
            $spans[] = UI::span($tokens[$i]->text)
                ->color($this->colorFor($tokens[$i], $tokens, $i));
        }

        return $spans;
    }

    /**
     * Decide the highlight colour for one token. Some tokens (notably
     * `T_STRING`) need a peek at neighbours to disambiguate class names,
     * function calls, and bare identifiers. Always returns a Color so
     * the call site stays a literal `->color($this->colorFor(...))` —
     * the CSS extractor relies on that shape to walk this method's
     * body and register every Color literal it finds.
     *
     * @param array<int, PhpToken> $tokens
     */
    private function colorFor(PhpToken $token, array $tokens, int $i): Color
    {
        $id   = $token->id;
        $text = $token->text;

        // Whitespace + everything uncategorised falls through to the
        // Darcula default foreground (`#A9B7C6` ≈ slate-300).
        if ($id === T_WHITESPACE) {
            return Color::slate(300);
        }

        // Line/block comments — Darcula `#808080` ≈ slate-500.
        if ($id === T_COMMENT) {
            return Color::slate(500);
        }

        // PHPDoc comments — Darcula `#629755` (muted green, italic in IDE).
        if ($id === T_DOC_COMMENT) {
            return Color::green(700);
        }

        // Strings — Darcula `#6A8759` olive ≈ lime-600.
        if (
            $id === T_CONSTANT_ENCAPSED_STRING
            || $id === T_ENCAPSED_AND_WHITESPACE
            || $id === T_INLINE_HTML
        ) {
            return Color::lime(600);
        }

        // Quote characters bracketing strings keep the string colour.
        if ($text === '"' || $text === "'" || $text === '`') {
            return Color::lime(600);
        }

        // Numbers — Darcula `#6897BB` blue ≈ blue-400.
        if ($id === T_LNUMBER || $id === T_DNUMBER) {
            return Color::blue(400);
        }

        // Variables — Darcula `#9876AA` purple, the scheme's most
        // distinctive tone.
        if ($id === T_VARIABLE) {
            return Color::purple(400);
        }

        // Open / close PHP tag — treat like a keyword.
        if ($id === T_OPEN_TAG || $id === T_OPEN_TAG_WITH_ECHO || $id === T_CLOSE_TAG) {
            return Color::orange(700);
        }

        // Keywords — Darcula `#CC7832` muted brown-orange ≈ orange-700.
        if (self::isKeyword($id)) {
            return Color::orange(700);
        }

        // Operators `->`, `::`, `=>` — default foreground in Darcula; we
        // keep them a touch dimmer than body text for visual rhythm.
        if (in_array($id, [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON, T_DOUBLE_ARROW], true)) {
            return Color::slate(400);
        }

        // Bare identifiers — peek at neighbours.
        if ($id === T_STRING) {
            $upper = strtolower($text);
            // `true` / `false` / `null` — Darcula colours constants
            // purple like variables.
            if ($upper === 'true' || $upper === 'false' || $upper === 'null') {
                return Color::purple(400);
            }

            $prev = $this->previousMeaningful($tokens, $i);
            $prevId = $prev?->id;

            // Function declarations: `function foo(` — Darcula `#FFC66D`
            // yellow ≈ amber-300.
            if ($prevId === T_FUNCTION) {
                return Color::amber(300);
            }

            // Class references after `new`, `use`, `\`, `::`,
            // `extends`/`implements`/`instanceof` — default foreground
            // in Darcula (italic in the IDE, plain colour here).
            if ($prevId === T_NEW
                || $prevId === T_USE
                || $prevId === T_NAMESPACE
                || $prevId === T_DOUBLE_COLON
                || $prevId === T_EXTENDS
                || $prevId === T_IMPLEMENTS
                || $prevId === T_INSTANCEOF
                || ($prev !== null && $prev->text === '\\')
            ) {
                return Color::slate(300);
            }

            // Function or method call — yellow.
            $next = $this->nextMeaningful($tokens, $i);
            if ($next !== null && $next->text === '(') {
                return Color::amber(300);
            }

            // Pascal-cased bare word — likely a class name; default fg.
            return Color::slate(300);
        }

        // Namespaced identifiers — Darcula default foreground.
        if ($id === T_NAME_QUALIFIED || $id === T_NAME_FULLY_QUALIFIED || $id === T_NAME_RELATIVE) {
            return Color::slate(300);
        }

        return Color::slate(300);
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
