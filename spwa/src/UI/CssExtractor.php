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
                  'Selector','BaseRoute','UI','ValueMap'];
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
        $verbatimWorked = false;
        try {
            @eval("\$probe->$method($argText);");
            $this->siteSucceeded++;
            $verbatimWorked = true;
        } catch (Throwable $e) {
            $verbatimErr = $e;
        }

        // 2. ValueMap dereference — always runs if any ValueMap::create(…)
        //    occurs in the args, even when verbatim "succeeded". The verbatim
        //    eval only reaches one branch (the lookup returns the default
        //    because $key is undefined in this scope); synthesis covers the
        //    other branches by feeding every harvested value through the
        //    parent method.
        $valueMapValues = $this->extractValueMapValues($argText, $file);

        if ($verbatimWorked && empty($valueMapValues)) {
            return;
        }

        if (!method_exists($probe, $method)) {
            if (!$verbatimWorked) {
                $this->recordFailure($file, $line, $method, $argText, "method does not exist on " . $probe::class);
            }
            return;
        }

        $rm = new ReflectionMethod($probe, $method);
        $bag = $this->extractValues($argText);

        foreach ($valueMapValues as $cls => $exprs) {
            $bag[$cls] = array_values(array_unique([...($bag[$cls] ?? []), ...$exprs]));
        }

        $synthOk = $this->trySynthesis($probe, $rm, $bag);

        if (!$verbatimWorked && !$synthOk) {
            $msg = $verbatimErr ? trim(explode("\n", $verbatimErr->getMessage())[0]) : 'no synthesis';
            $this->recordFailure($file, $line, $method, $argText, $msg);
        }
    }

    // ============================================================
    // ValueMap dereferencing
    // ============================================================

    /** @var array<int, mixed>  Live PHP values pulled out of resolved ValueMaps. */
    private array $valueMapPool = [];

    /**
     * Find every `ValueMap::create(opts, default, key)` call inside an argument
     * expression, resolve the option/default pair (inline literal eval, or
     * Reflection for `$this->prop`), and return harvested values bucketed by
     * the short class name of each value.
     *
     * The returned arrays contain PHP source expressions (e.g. `Color::red(500)`)
     * for inline cases, or sentinels like `$this->valueMapPool[42]` for values
     * pulled out via Reflection — these are valid PHP that eval() inside this
     * class can resolve back to the live object.
     *
     * @return array<string, string[]>  shortClass → expressions
     */
    private function extractValueMapValues(string $argText, string $file): array
    {
        $bag = [];
        foreach ($this->extractValueMapCalls($argText) as $call) {
            $values = $this->resolveValueMap($call['opts'], $call['default'], $file);
            foreach ($values as $v) {
                $cls = is_object($v) ? (new \ReflectionClass($v))->getShortName() : gettype($v);
                $bag[$cls] ??= [];
                if ($this->isLiteralValueExpr($v)) {
                    // We have a source string from inline parsing — keep it
                    // readable in synthesized eval bodies.
                    $bag[$cls][] = $v['__expr'];
                } else {
                    $idx = count($this->valueMapPool);
                    $this->valueMapPool[] = $v;
                    $bag[$cls][] = "\$this->valueMapPool[$idx]";
                }
            }
        }
        foreach ($bag as $k => $v) {
            $bag[$k] = array_values(array_unique($v));
        }
        return $bag;
    }

    private function isLiteralValueExpr(mixed $v): bool
    {
        return is_array($v) && isset($v['__expr']);
    }

    /**
     * Scan an arg expression and return every ValueMap::create(...) call as
     * {opts, default, key} source-text triples.
     *
     * @return array<int, array{opts:string, default:string, key:string}>
     */
    private function extractValueMapCalls(string $argText): array
    {
        $tokens = PhpToken::tokenize('<?php ' . $argText . ';');
        $count = count($tokens);
        $out = [];
        for ($i = 0; $i < $count; $i++) {
            if ($tokens[$i]->id !== T_STRING || $tokens[$i]->text !== 'ValueMap') continue;
            $j = $this->skipTrivia($tokens, $i + 1);
            if ($j === null || $tokens[$j]->id !== T_DOUBLE_COLON) continue;
            $j = $this->skipTrivia($tokens, $j + 1);
            if ($j === null || $tokens[$j]->id !== T_STRING || $tokens[$j]->text !== 'create') continue;
            $k = $this->skipTrivia($tokens, $j + 1);
            if ($k === null || $tokens[$k]->text !== '(') continue;
            $args = $this->extractArgs($tokens, $k);
            $parts = $this->splitTopLevelArgs($args['text']);
            if (count($parts) < 2) continue;
            $out[] = [
                'opts'    => trim($parts[0]),
                'default' => trim($parts[1]),
                'key'     => isset($parts[2]) ? trim($parts[2]) : 'null',
            ];
        }
        return $out;
    }

    /**
     * Split arg text on top-level commas (commas outside any nested
     * paren/bracket/brace). Returns the raw substrings — caller can trim.
     *
     * @return string[]
     */
    private function splitTopLevelArgs(string $argText): array
    {
        $tokens = PhpToken::tokenize('<?php ' . $argText . ';');
        $count = count($tokens);
        $depth = 0;
        $parts = [''];
        // skip the opening <?php token; PhpToken includes it as element 0.
        foreach ($tokens as $idx => $t) {
            if ($idx === 0 && $t->id === T_OPEN_TAG) continue;
            $text = $t->text;
            if ($text === ';') continue;
            if ($text === '(' || $text === '[' || $text === '{') $depth++;
            elseif ($text === ')' || $text === ']' || $text === '}') $depth--;
            elseif ($text === ',' && $depth === 0) {
                $parts[] = '';
                continue;
            }
            $parts[count($parts) - 1] .= $text;
        }
        return $parts;
    }

    /**
     * Resolve a ValueMap::create's options + default into a flat array of
     * PHP values. Tries inline-literal eval first; for `$this->propName`
     * options falls back to reflecting the file's class and reading the
     * property default — that's where "the array may live somewhere else"
     * (parent class, trait) is handled by PHP itself.
     *
     * @return array<int, array{__expr:string}|mixed>
     */
    private function resolveValueMap(string $optsExpr, string $defaultExpr, string $file): array
    {
        $opts    = $this->resolveExpr($optsExpr, $file);
        $default = $this->resolveExpr($defaultExpr, $file);

        $values = [];
        if (is_array($opts)) {
            foreach ($opts as $v) {
                $values[] = $v;
            }
        }
        if ($default !== null || array_key_exists(0, [$default])) {
            $values[] = $default;
        }
        // Drop nulls (failed resolutions).
        return array_values(array_filter($values, static fn($v) => $v !== null));
    }

    /**
     * Best-effort PHP value resolution.
     *
     *   1. If the expression is `$this->propName` (no further chain),
     *      reflect the containing class and return the property's default.
     *      This is where the property can live in a parent class / trait —
     *      ReflectionClass::getDefaultProperties() walks the hierarchy.
     *
     *   2. Otherwise eval the expression directly. Works for any literal
     *      that doesn't need a runtime $this/$var context: inline arrays,
     *      Color::red(500), enum cases, etc.
     *
     * Each successful inline literal is wrapped as ['__expr' => '<source>']
     * so caller can keep readable expressions; reflected values are returned
     * as raw PHP values (they'll be looked up via $this->valueMapPool[N]).
     */
    private function resolveExpr(string $expr, string $file): mixed
    {
        $expr = trim($expr);

        if (preg_match('/^\$this->(\w+)$/', $expr, $m)) {
            $className = $this->findClassNameInFile($file);
            if ($className !== null && class_exists($className)) {
                try {
                    $defaults = (new \ReflectionClass($className))->getDefaultProperties();
                    return $defaults[$m[1]] ?? null;
                } catch (Throwable) {}
            }
            return null;
        }

        // Inline expression — eval it. If it's a literal array, we want to
        // preserve readable source expressions for each value so synthesized
        // eval'd code mentions e.g. Color::blue(600), not $pool[12].
        try {
            $value = null;
            @eval("\$value = $expr;");
            // For arrays whose elements are written as readable expressions,
            // re-parse the source and pair element values with element source.
            if (is_array($value) && str_starts_with($expr, '[')) {
                return $this->arrayWithSourceExprs($value, $expr);
            }
            if (is_array($value)) {
                // array(...) form or other — fall back to raw values.
                return $value;
            }
            return $value;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * For an inline literal `[k => v, k => v, …]` whose eval produced $value,
     * walk the source again and tag each element value with its source text.
     * The CssExtractor uses that source in the synthesized eval body so the
     * resulting CSS rules are emitted from the user's original expressions.
     *
     * @return array<int|string, array{__expr:string}>
     */
    private function arrayWithSourceExprs(array $value, string $arrayExpr): array
    {
        $tokens = PhpToken::tokenize('<?php ' . $arrayExpr . ';');
        $count = count($tokens);
        // Find the outermost [ ... ]
        $start = null;
        for ($i = 0; $i < $count; $i++) {
            if ($tokens[$i]->text === '[') { $start = $i; break; }
        }
        if ($start === null) return $value;

        $depth = 0;
        $entries = []; // raw source per top-level entry
        $cur = '';
        for ($i = $start; $i < $count; $i++) {
            $t = $tokens[$i]->text;
            if ($t === '[') {
                $depth++;
                if ($depth === 1) continue;
            } elseif ($t === ']') {
                $depth--;
                if ($depth === 0) {
                    if (trim($cur) !== '') $entries[] = trim($cur);
                    break;
                }
            }
            if ($depth === 1 && $t === ',') {
                if (trim($cur) !== '') $entries[] = trim($cur);
                $cur = '';
                continue;
            }
            if ($depth > 0) $cur .= $t;
        }

        // Extract just the value side of each `key => value`.
        $valueExprs = [];
        foreach ($entries as $entry) {
            // Find the top-level => (account for nested parens/brackets).
            $arrowPos = $this->findTopLevelArrow($entry);
            $valueExprs[] = $arrowPos === null ? $entry : trim(substr($entry, $arrowPos + 2));
        }

        // Pair each $value with its source expression. If counts don't match
        // (e.g. spread operators), fall back to raw values for the misaligned ones.
        $valueKeys = array_keys($value);
        $out = [];
        $i = 0;
        foreach ($valueKeys as $k) {
            if (isset($valueExprs[$i])) {
                $out[$k] = ['__expr' => $valueExprs[$i]];
            } else {
                $out[$k] = $value[$k];
            }
            $i++;
        }
        return $out;
    }

    private function findTopLevelArrow(string $s): ?int
    {
        $depth = 0;
        $len = strlen($s);
        for ($i = 0; $i < $len - 1; $i++) {
            $c = $s[$i];
            if ($c === '(' || $c === '[' || $c === '{') $depth++;
            elseif ($c === ')' || $c === ']' || $c === '}') $depth--;
            elseif ($depth === 0 && $c === '=' && ($s[$i + 1] ?? '') === '>') return $i;
        }
        return null;
    }

    /**
     * Walk the file tokens once to find `namespace Foo\Bar;` and the first
     * `class Name` declaration; return the FQCN or null. Cached per file.
     */
    private function findClassNameInFile(string $file): ?string
    {
        static $cache = [];
        if (array_key_exists($file, $cache)) return $cache[$file];

        $tokens = PhpToken::tokenize(@file_get_contents($file) ?: '');
        $count = count($tokens);
        $namespace = '';
        for ($i = 0; $i < $count; $i++) {
            $t = $tokens[$i];
            if ($t->id === T_NAMESPACE) {
                $j = $i + 1;
                $namespace = '';
                while ($j < $count && $tokens[$j]->text !== ';' && $tokens[$j]->text !== '{') {
                    if ($tokens[$j]->id === T_STRING || $tokens[$j]->id === T_NAME_QUALIFIED || $tokens[$j]->id === T_NS_SEPARATOR) {
                        $namespace .= $tokens[$j]->text;
                    }
                    $j++;
                }
                continue;
            }
            if ($t->id === T_CLASS) {
                $j = $this->skipTrivia($tokens, $i + 1);
                if ($j !== null && $tokens[$j]->id === T_STRING) {
                    $name = $tokens[$j]->text;
                    return $cache[$file] = $namespace !== '' ? "$namespace\\$name" : $name;
                }
            }
        }
        return $cache[$file] = null;
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
