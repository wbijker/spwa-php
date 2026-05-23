<?php

namespace Spwa\UI;

use PhpToken;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;

/**
 * Build-time CSS extractor — per-call, no cross-line cartesian.
 *
 * For every `UI::factory()->method(args)->method(args)…` chain in source:
 *
 *   1. Eval `$probe->method(args)` verbatim. If it works, the only emitted
 *      classes are the ones the user actually wrote — zero bloat.
 *
 *   2. If verbatim throws (dynamic args like `$this->categoryColor($cat)` or
 *      `$highlight ? Color::red() : Color::gray()`), parse the args text for
 *      literal value expressions (Color::xxx, Unit::xxx, Pseudo chains, enum
 *      cases). The harvest is scoped to THIS call's argument expression only.
 *      Then use reflection to type each parameter and generate permutations
 *      *just from this line's bag*. e.g. a ternary with two Colors and one
 *      Pseudo on the right yields exactly two classes, not "every Color × every
 *      Pseudo across the whole file".
 *
 *   3. If neither verbatim nor synthesis succeeds, the call site is recorded
 *      and surfaces in a trailing CSS comment block.
 *
 * One probe per UI factory found in source — calls accumulate on it.
 *
 * The output is a single CSS string. Not safe for untrusted input.
 */
class CssExtractor
{
    /** @var array<string, UIElement> per-factory shared probe */
    private array $probes = [];

    /** @var array<int, array{file:string, line:int, call:string, error:string}> */
    private array $failures = [];

    private int $siteAttempted   = 0;
    private int $siteSucceeded   = 0;
    private int $synthAttempted  = 0;
    private int $synthSucceeded  = 0;

    /** Cap synthesised combos per failed call so cartesian stays bounded. */
    private const SYNTH_COMBO_CAP = 50;

    /** Class basenames whose `Class::…` expressions we treat as harvestable values. */
    private const HARVESTABLE = [
        'Color', 'Unit', 'Pseudo',
        'FontSize', 'FontWeight', 'Shadow', 'Cursor',
        'Direction', 'Align', 'GridColumns', 'Breakpoint', 'ColorScheme',
    ];

    public function __construct()
    {
        self::installAliases();
    }

    public function scan(string $dir): string
    {
        foreach ($this->collectPhpFiles($dir) as $file) {
            $this->processFile($file);
        }

        $styles = [];
        foreach ($this->probes as $probe) {
            foreach ($probe->build()->collectStyles() as $cls => $css) {
                $styles[$cls] = $css;
            }
        }

        return $this->renderCss($styles);
    }

    // ============================================================
    // Setup
    // ============================================================

    private static function installAliases(): void
    {
        static $done = false;
        if ($done) return;
        $done = true;

        // PSR-4 only autoloads same-name-per-file; co-located helpers need a nudge.
        class_exists(Grid::class);

        $names = ['Color','Unit','Pseudo','Cursor','FontSize','FontWeight','Shadow',
                  'Align','Direction','GridColumns','Breakpoint','ColorScheme',
                  'Selector','BaseRoute','UI'];
        foreach ($names as $name) {
            if (class_exists($name, false) || enum_exists($name, false)) continue;
            $fqn = "Spwa\\UI\\$name";
            if (class_exists($fqn) || enum_exists($fqn)) {
                class_alias($fqn, $name);
            }
        }
    }

    private function collectPhpFiles(string $dir): array
    {
        $out = [];
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($it as $p) {
            $s = (string)$p;
            if (str_ends_with($s, '.php')) $out[] = $s;
        }
        return $out;
    }

    private function makeProbe(string $factoryName): ?UIElement
    {
        if (!method_exists(UI::class, $factoryName)) return null;
        try {
            $rm = new ReflectionMethod(UI::class, $factoryName);
            if (!$rm->isPublic() || !$rm->isStatic()) return null;
            $sampleArgs = match ($factoryName) {
                'text', 'badge', 'button', 'code' => [''],
                'image' => ['', ''],
                'link'  => [''],
                default => [],
            };
            $result = UI::$factoryName(...$sampleArgs);
            return $result instanceof UIElement ? $result : null;
        } catch (Throwable) {
            return null;
        }
    }

    // ============================================================
    // Walk UI:: chains
    // ============================================================

    private function processFile(string $file): void
    {
        $tokens = PhpToken::tokenize(file_get_contents($file));
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            if ($tokens[$i]->id !== T_STRING || $tokens[$i]->text !== 'UI') continue;
            $j = $this->skipTrivia($tokens, $i + 1);
            if ($j === null || $tokens[$j]->id !== T_DOUBLE_COLON) continue;
            $j = $this->skipTrivia($tokens, $j + 1);
            if ($j === null || $tokens[$j]->id !== T_STRING) continue;
            $factory = $tokens[$j]->text;

            $probe = $this->probes[$factory] ??= ($this->makeProbe($factory) ?? UI::container());

            // Skip past UI::factory(...)
            $k = $this->skipTrivia($tokens, $j + 1);
            if ($k === null || $tokens[$k]->text !== '(') continue;
            $depth = 0;
            for (; $k < $count; $k++) {
                $t = $tokens[$k]->text;
                if ($t === '(') $depth++;
                elseif ($t === ')') {
                    $depth--;
                    if ($depth === 0) { $k++; break; }
                }
            }

            // Walk ->method(args) chain.
            while ($k < $count) {
                $j = $this->skipTrivia($tokens, $k);
                if ($j === null || $tokens[$j]->id !== T_OBJECT_OPERATOR) break;
                $k = $j + 1;

                $j = $this->skipTrivia($tokens, $k);
                if ($j === null || $tokens[$j]->id !== T_STRING) break;
                $methodName = $tokens[$j]->text;
                $line = $tokens[$j]->line;
                $k = $j + 1;

                $j = $this->skipTrivia($tokens, $k);
                if ($j === null || $tokens[$j]->text !== '(') break;

                $args = $this->extractArgs($tokens, $j);
                $k = $args['end'];

                $this->processCall($probe, $methodName, $args['text'], $file, $line);
            }
        }
    }

    // ============================================================
    // Per-call: verbatim → synthesis → failure
    // ============================================================

    private function processCall(UIElement $probe, string $method, string $argText, string $file, int $line): void
    {
        $this->siteAttempted++;

        // 1. Verbatim — happens to work for any call with all-literal args.
        $verbatimErr = null;
        try {
            @eval("\$probe->$method($argText);");
            $this->siteSucceeded++;
            return; // verbatim covered it — synthesis would be redundant
        } catch (Throwable $e) {
            $verbatimErr = $e;
        }

        // 2. Synthesis — only the values that THIS call's args text contains,
        //    plus one level of `$this->name(...)` dereference: when the args
        //    invoke an instance method on the same file's class (e.g.
        //    `->color($this->categoryColor($category))`), pull literal values
        //    out of that method's body too. Lets `match` expressions and other
        //    helper-returned values reach the bag.
        if (!method_exists($probe, $method)) {
            $this->recordFailure($file, $line, $method, $argText, "method does not exist on " . $probe::class);
            return;
        }
        $rm = new ReflectionMethod($probe, $method);
        $bag = $this->extractValues($argText);

        foreach ($this->extractInstanceMethodCalls($argText) as $calledMethod) {
            $body = $this->getMethodBody($file, $calledMethod);
            if ($body === null) continue;
            foreach ($this->extractValues($body) as $cls => $exprs) {
                $bag[$cls] = array_values(array_unique([...($bag[$cls] ?? []), ...$exprs]));
            }
        }

        $synthOk = $this->trySynthesis($probe, $rm, $bag);

        if (!$synthOk) {
            $msg = $verbatimErr ? trim(explode("\n", $verbatimErr->getMessage())[0]) : 'no synthesis';
            $this->recordFailure($file, $line, $method, $argText, $msg);
        }
    }

    /**
     * Find method names called as `$this->name(` in the given expression text.
     *
     * @return string[]
     */
    private function extractInstanceMethodCalls(string $argText): array
    {
        $tokens = PhpToken::tokenize('<?php ' . $argText . ';');
        $count = count($tokens);
        $methods = [];
        for ($i = 0; $i < $count; $i++) {
            if ($tokens[$i]->id !== T_VARIABLE || $tokens[$i]->text !== '$this') continue;
            $j = $this->skipTrivia($tokens, $i + 1);
            if ($j === null || $tokens[$j]->id !== T_OBJECT_OPERATOR) continue;
            $j = $this->skipTrivia($tokens, $j + 1);
            if ($j === null || $tokens[$j]->id !== T_STRING) continue;
            $name = $tokens[$j]->text;
            $k = $this->skipTrivia($tokens, $j + 1);
            if ($k === null || $tokens[$k]->text !== '(') continue;
            $methods[$name] = true;
        }
        return array_keys($methods);
    }

    /** @var array<string, array<string, string|null>>  file → method → body|null */
    private array $methodBodyCache = [];

    /**
     * Locate `function <name>(…) { body }` in a PHP file and return the body
     * source (between the outermost braces). Returns null if not found.
     * Cached per (file, method).
     */
    private function getMethodBody(string $file, string $methodName): ?string
    {
        if (array_key_exists($methodName, $this->methodBodyCache[$file] ?? [])) {
            return $this->methodBodyCache[$file][$methodName];
        }
        $body = $this->findMethodBody($file, $methodName);
        $this->methodBodyCache[$file][$methodName] = $body;
        return $body;
    }

    private function findMethodBody(string $file, string $methodName): ?string
    {
        $tokens = PhpToken::tokenize(@file_get_contents($file) ?: '');
        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            if ($tokens[$i]->id !== T_FUNCTION) continue;
            $j = $this->skipTrivia($tokens, $i + 1);
            if ($j === null || $tokens[$j]->id !== T_STRING) continue;
            if ($tokens[$j]->text !== $methodName) continue;
            // Walk past the parameter list ()
            $k = $this->skipTrivia($tokens, $j + 1);
            if ($k === null || $tokens[$k]->text !== '(') continue;
            $depth = 0;
            for (; $k < $count; $k++) {
                $t = $tokens[$k]->text;
                if ($t === '(') $depth++;
                elseif ($t === ')') {
                    $depth--;
                    if ($depth === 0) { $k++; break; }
                }
            }
            // Skip return-type (no braces), find the opening { of the body.
            while ($k < $count && $tokens[$k]->text !== '{') $k++;
            if ($k >= $count) return null;
            // Capture body text between matching braces.
            $depth = 0;
            $body = '';
            for (; $k < $count; $k++) {
                $t = $tokens[$k]->text;
                if ($t === '{') {
                    $depth++;
                    if ($depth === 1) continue; // skip outermost {
                } elseif ($t === '}') {
                    $depth--;
                    if ($depth === 0) return $body;
                }
                $body .= $t;
            }
            return null;
        }
        return null;
    }

    private function recordFailure(string $file, int $line, string $method, string $argText, string $reason): void
    {
        $this->failures[] = [
            'file'  => $file,
            'line'  => $line,
            'call'  => "$method($argText)",
            'error' => $reason,
        ];
    }

    /**
     * Pull literal Color/Unit/Pseudo/enum expressions out of an arg text.
     *
     * @return array<string, string[]>  shortClass → expressions (deduped)
     */
    private function extractValues(string $argText): array
    {
        // Tokenize as a snippet (PhpToken needs a php tag).
        $tokens = PhpToken::tokenize('<?php ' . $argText . ';');
        $count = count($tokens);
        $bag = [];
        for ($i = 0; $i < $count; $i++) {
            if ($tokens[$i]->id !== T_STRING) continue;
            $j = $this->skipTrivia($tokens, $i + 1);
            if ($j === null || $tokens[$j]->id !== T_DOUBLE_COLON) continue;
            $className = $tokens[$i]->text;
            if (!in_array($className, self::HARVESTABLE, true)) continue;

            $expr = $this->readChainExpr($tokens, $i);
            if ($expr !== null) {
                $bag[$className][$expr] = true;
            }
        }
        foreach ($bag as $k => $v) {
            $bag[$k] = array_keys($v);
        }
        return $bag;
    }

    /**
     * Type each parameter; pull candidate value strings out of $bag; eval
     * every cartesian combo. Returns true if at least one combo worked.
     *
     * @param array<string, string[]> $bag
     */
    private function trySynthesis(UIElement $probe, ReflectionMethod $rm, array $bag): bool
    {
        $params = $rm->getParameters();
        if (count($params) === 0) return false;

        $valueLists = [];
        foreach ($params as $p) {
            $values = $this->candidatesForParam($p, $bag);
            if ($values === null) return false; // can't fill this param from the bag
            $valueLists[] = $values;
        }

        $combos = $this->cartesian($valueLists, self::SYNTH_COMBO_CAP);
        if (empty($combos)) return false;

        $methodName = $rm->getName();
        $any = false;
        foreach ($combos as $combo) {
            $this->synthAttempted++;
            try {
                @eval("\$probe->$methodName(" . implode(', ', $combo) . ");");
                $this->synthSucceeded++;
                $any = true;
            } catch (Throwable) {
                // type mismatch in this combo — try the next
            }
        }
        return $any;
    }

    /**
     * @param array<string, string[]> $bag
     * @return string[]|null  value-source strings to feed this param, or null
     *                        if there's no usable candidate (required + missing)
     */
    private function candidatesForParam(ReflectionParameter $p, array $bag): ?array
    {
        $type = $p->getType();
        if (!$type instanceof ReflectionNamedType) {
            return $p->isOptional() ? ['null'] : null;
        }
        $name = $type->getName();
        $shortName = ($pos = strrpos($name, '\\')) !== false ? substr($name, $pos + 1) : $name;

        $candidates = $bag[$shortName] ?? null;
        if ($candidates === null) {
            return $p->isOptional() ? ['null'] : null;
        }
        return $candidates;
    }

    /**
     * Cartesian product, bounded — abort growth past $cap to keep memory sane.
     *
     * @param array<int, string[]> $lists
     * @return string[][]
     */
    private function cartesian(array $lists, int $cap): array
    {
        $result = [[]];
        foreach ($lists as $list) {
            $next = [];
            foreach ($result as $r) {
                foreach ($list as $v) {
                    $next[] = [...$r, $v];
                    if (count($next) >= $cap) break 2;
                }
            }
            $result = $next;
        }
        return $result;
    }

    // ============================================================
    // Token helpers
    // ============================================================

    private function skipTrivia(array $tokens, int $start): ?int
    {
        $count = count($tokens);
        for ($i = $start; $i < $count; $i++) {
            $id = $tokens[$i]->id;
            if ($id === T_WHITESPACE || $id === T_COMMENT || $id === T_DOC_COMMENT) continue;
            return $i;
        }
        return null;
    }

    /** @return array{text:string, end:int} */
    private function extractArgs(array $tokens, int $openIdx): array
    {
        $depth = 0;
        $out = '';
        $count = count($tokens);
        $i = $openIdx;
        for (; $i < $count; $i++) {
            $text = $tokens[$i]->text;
            if ($text === '(') {
                $depth++;
                if ($depth === 1) continue;
            } elseif ($text === ')') {
                $depth--;
                if ($depth === 0) { $i++; break; }
            }
            $out .= $text;
        }
        return ['text' => $out, 'end' => $i];
    }

    /**
     * Read a `Class::name[(args)][->name[(args)]]*` chain from tokens[$start].
     * Returns the verbatim source text or null if no method-or-case follows.
     */
    private function readChainExpr(array $tokens, int $start): ?string
    {
        $count = count($tokens);
        $i = $start;
        $out = '';

        // Class
        $out .= $tokens[$i++]->text;
        // ::
        $j = $this->skipTrivia($tokens, $i);
        if ($j === null || $tokens[$j]->id !== T_DOUBLE_COLON) return null;
        for (; $i <= $j; $i++) $out .= $tokens[$i]->text;
        $i = $j + 1;
        // method or case name
        $j = $this->skipTrivia($tokens, $i);
        if ($j === null || $tokens[$j]->id !== T_STRING) return null;
        for (; $i <= $j; $i++) $out .= $tokens[$i]->text;
        $i = $j + 1;
        // optional (args)
        $j = $this->skipTrivia($tokens, $i);
        if ($j !== null && $tokens[$j]->text === '(') {
            $depth = 0;
            for (; $i < $count; $i++) {
                $text = $tokens[$i]->text;
                $out .= $text;
                if ($text === '(') $depth++;
                elseif ($text === ')') {
                    $depth--;
                    if ($depth === 0) { $i++; break; }
                }
            }
        }
        // chained ->name(args)*
        while ($i < $count) {
            $j = $this->skipTrivia($tokens, $i);
            if ($j === null || $tokens[$j]->id !== T_OBJECT_OPERATOR) break;
            for (; $i <= $j; $i++) $out .= $tokens[$i]->text;
            $i = $j + 1;
            $j = $this->skipTrivia($tokens, $i);
            if ($j === null || $tokens[$j]->id !== T_STRING) break;
            for (; $i <= $j; $i++) $out .= $tokens[$i]->text;
            $i = $j + 1;
            $j = $this->skipTrivia($tokens, $i);
            if ($j === null || $tokens[$j]->text !== '(') break;
            $depth = 0;
            for (; $i < $count; $i++) {
                $text = $tokens[$i]->text;
                $out .= $text;
                if ($text === '(') $depth++;
                elseif ($text === ')') {
                    $depth--;
                    if ($depth === 0) { $i++; break; }
                }
            }
        }
        return $out;
    }

    // ============================================================
    // Render
    // ============================================================

    private function renderCss(array $styles): string
    {
        $header = sprintf(
            "/* CssExtractor — %d rules\n *   call-sites: %d/%d eval'd verbatim\n *   synthesis:  %d/%d permutations succeeded\n *   unresolved: %d call sites (see end of file)\n */\n\n",
            count($styles),
            $this->siteSucceeded, $this->siteAttempted,
            $this->synthSucceeded, $this->synthAttempted,
            count($this->failures),
        );

        $rules = StyleGenerator::from($styles)->toCSS();

        $failuresBlock = '';
        if (!empty($this->failures)) {
            $failuresBlock = "\n/*\n * Unresolved call sites — eval failed and synthesis couldn't reconstruct\n * the args from this line alone. Usually means: dynamic value coming from a\n * method/property, or a required parameter\n * whose type has no literal in the call text.\n";
            $byFile = [];
            foreach ($this->failures as $f) $byFile[$f['file']][] = $f;
            foreach ($byFile as $file => $entries) {
                $failuresBlock .= " *\n * $file\n";
                foreach ($entries as $e) {
                    $oneLine = preg_replace('/\s+/', ' ', $e['call']);
                    if (strlen($oneLine) > 160) $oneLine = substr($oneLine, 0, 157) . '...';
                    $failuresBlock .= " *   :{$e['line']}  ->$oneLine\n";
                    $failuresBlock .= " *      [{$e['error']}]\n";
                }
            }
            $failuresBlock .= " */\n";
        }

        return $header . $rules . $failuresBlock;
    }
}
