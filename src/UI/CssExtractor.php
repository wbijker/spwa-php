<?php

namespace BrickPHP\UI;

use PhpToken;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
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
    /**
     * Per-factory shared probe. `null` means the factory exists but
     * returns a non-UIElement value object (Optgroup, Source, Track,
     * Option) — those chains can't generate CSS so we skip them
     * silently instead of recording false-positive failures.
     *
     * @var array<string, ?UIElement>
     */
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
        'Direction', 'Align', 'GridColumns', 'GridFlow', 'GridAlign', 'Breakpoint', 'ColorScheme',
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
            if ($probe === null) continue; // value-object factory; nothing to collect
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
                  'Align','Direction','GridColumns','GridFlow','GridAlign','Breakpoint','ColorScheme',
                  'Selector','BaseRoute','UI','Svg'];
        foreach ($names as $name) {
            if (class_exists($name, false) || enum_exists($name, false)) continue;
            $fqn = "BrickPHP\\UI\\$name";
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

            // Synthesize sample args by walking the required parameters
            // and providing a zero-value per scalar type. Stops at the
            // first optional parameter — those don't need to be passed.
            // This auto-covers factories like `optgroup(string $label)`,
            // `source(string $src)`, `track(string $src)`, `option(string
            // $label)`, `heading(string $content, int $level = 1)` so they
            // produce a real probe instead of falling back to Container.
            $sampleArgs = [];
            foreach ($rm->getParameters() as $p) {
                if ($p->isOptional()) break;
                $type = $p->getType();
                $name = $type instanceof ReflectionNamedType ? $type->getName() : null;
                $sampleArgs[] = match ($name) {
                    'string' => '',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => false,
                    default  => null,
                };
            }

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

            // Cache probe per factory. Null is a real outcome — it
            // means the factory returns a non-UIElement (Optgroup,
            // Source, Track, Option). We still advance past the
            // chain to keep the parser in sync, but skip processCall.
            if (!array_key_exists($factory, $this->probes)) {
                $this->probes[$factory] = $this->makeProbe($factory);
            }
            $probe = $this->probes[$factory];

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

                if ($probe !== null) {
                    $this->processCall($probe, $methodName, $args['text'], $file, $line);
                }
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

        // Static calls in the args — `self::categoryColor(...)`,
        // `News::categoryColor(...)`. Reconstruct the file's namespace + use
        // imports to resolve the class to its FQN, then harvest the literal
        // values out of the resolved method's body (reflection, so it works
        // across files). e.g. a `match` returning Color::blue(600)/red(600)/…
        // yields exactly those colors for the param below.
        foreach ($this->extractStaticMethodCalls($argText) as $call) {
            $short = ($p = strrpos($call['class'], '\\')) !== false ? substr($call['class'], $p + 1) : $call['class'];
            if (in_array($short, self::HARVESTABLE, true)) continue; // value class — already harvested literally
            $fqn = $this->resolveClassRef($call['class'], $file);
            if ($fqn === null) continue;
            $body = $this->reflectionMethodBody($fqn, $call['method']);
            if ($body === null) continue;
            foreach ($this->extractValues($body) as $cls => $exprs) {
                $bag[$cls] = array_values(array_unique([...($bag[$cls] ?? []), ...$exprs]));
            }
        }

        // Constructor calls in the args — `new AtomOneDark()`, `new
        // News\Article(...)`. Treated the same as a static method call,
        // but the reflected body is the class's `__construct`. Lets
        // chains like `->color((new AtomOneDark())->colorFor($kind))`
        // surface every Color literal that the typed ctor passes to its
        // parent.
        foreach ($this->extractConstructorCalls($argText) as $call) {
            $short = ($p = strrpos($call['class'], '\\')) !== false ? substr($call['class'], $p + 1) : $call['class'];
            if (in_array($short, self::HARVESTABLE, true)) continue;
            $fqn = $this->resolveClassRef($call['class'], $file);
            if ($fqn === null) continue;
            $body = $this->reflectionMethodBody($fqn, '__construct');
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

    /** @var array<string, array{namespace: string, class: ?string, uses: array<string, string>}>  file → reconstructed meta */
    private array $fileMetaCache = [];

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

    /**
     * Find static method calls in an expression — `self::m(...)`,
     * `static::m(...)`, `News::m(...)`, `Foo\Bar::m(...)`. Enum-case reads
     * (`Color::Red`) and `::class` are skipped (no following `(`).
     *
     * @return array<int, array{class: string, method: string}>
     */
    private function extractStaticMethodCalls(string $argText): array
    {
        $tokens = PhpToken::tokenize('<?php ' . $argText . ';');
        $count = count($tokens);
        $calls = [];
        for ($i = 0; $i < $count; $i++) {
            $id = $tokens[$i]->id;
            if ($id !== T_STRING && $id !== T_NAME_QUALIFIED && $id !== T_STATIC) continue;
            $class = $tokens[$i]->text;

            $j = $this->skipTrivia($tokens, $i + 1);
            if ($j === null || $tokens[$j]->id !== T_DOUBLE_COLON) continue;
            $k = $this->skipTrivia($tokens, $j + 1);
            if ($k === null || $tokens[$k]->id !== T_STRING) continue;
            $method = $tokens[$k]->text;
            $l = $this->skipTrivia($tokens, $k + 1);
            if ($l === null || $tokens[$l]->text !== '(') continue;

            $calls[] = ['class' => $class, 'method' => $method];
        }
        return $calls;
    }

    /**
     * Find `new Class(...)` constructor calls in an expression —
     * `new AtomOneDark()`, `new App\Service(...)`. Returns just the
     * class refs; the caller reflects on each class's `__construct`.
     *
     * @return array<int, array{class: string}>
     */
    private function extractConstructorCalls(string $argText): array
    {
        $tokens = PhpToken::tokenize('<?php ' . $argText . ';');
        $count = count($tokens);
        $calls = [];
        for ($i = 0; $i < $count; $i++) {
            if ($tokens[$i]->id !== T_NEW) continue;
            $j = $this->skipTrivia($tokens, $i + 1);
            if ($j === null) continue;
            $id = $tokens[$j]->id;
            if ($id !== T_STRING
                && $id !== T_NAME_QUALIFIED
                && $id !== T_NAME_FULLY_QUALIFIED
                && $id !== T_NAME_RELATIVE
                && $id !== T_STATIC
            ) {
                continue;
            }
            $calls[] = ['class' => $tokens[$j]->text];
        }
        return $calls;
    }

    /**
     * Resolve a class reference written in $file to a fully-qualified name,
     * using the file's reconstructed namespace + use imports. `self`/`static`/
     * `parent` map to the file's declared class (reflection then finds even
     * inherited methods); an unqualified name uses a matching import or falls
     * back to the file's namespace; an already-qualified name is returned as-is.
     */
    private function resolveClassRef(string $ref, string $file): ?string
    {
        if ($ref === 'self' || $ref === 'static' || $ref === 'parent') {
            return $this->fileMeta($file)['class'];
        }
        if (str_contains($ref, '\\')) {
            return ltrim($ref, '\\');
        }
        $meta = $this->fileMeta($file);
        if (isset($meta['uses'][$ref])) {
            return $meta['uses'][$ref];
        }
        return $meta['namespace'] !== '' ? $meta['namespace'] . '\\' . $ref : $ref;
    }

    /**
     * Source text of a method's body (signature line included — harmless for
     * value harvesting), located via reflection so it works across files and
     * resolves inherited methods. Null when the class/method can't be found.
     */
    private function reflectionMethodBody(string $fqn, string $method): ?string
    {
        if (!class_exists($fqn) || !method_exists($fqn, $method)) {
            return null;
        }
        try {
            $rm = new ReflectionMethod($fqn, $method);
            $srcFile = $rm->getFileName();
            $start = $rm->getStartLine();
            $end = $rm->getEndLine();
            if ($srcFile === false || $start === false || $end === false) {
                return null;
            }
            $lines = @file($srcFile);
            if ($lines === false) {
                return null;
            }
            return implode('', array_slice($lines, $start - 1, $end - $start + 1));
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Reconstruct a file's namespace, declared class FQN, and use-import map
     * (short/alias => FQN) by tokenizing its head. Cached per file.
     *
     * @return array{namespace: string, class: ?string, uses: array<string, string>}
     */
    private function fileMeta(string $file): array
    {
        if (isset($this->fileMetaCache[$file])) {
            return $this->fileMetaCache[$file];
        }
        $meta = ['namespace' => '', 'class' => null, 'uses' => []];
        $tokens = PhpToken::tokenize(@file_get_contents($file) ?: '');
        $count = count($tokens);

        $segment = function (int $from) use ($tokens, $count): string {
            $out = '';
            for ($j = $from; $j < $count; $j++) {
                $t = $tokens[$j];
                if ($t->text === ';' || $t->text === '{' || $t->id === T_AS) break;
                if ($t->id === T_STRING || $t->id === T_NAME_QUALIFIED || $t->id === T_NS_SEPARATOR) {
                    $out .= $t->text;
                }
            }
            return trim($out, '\\');
        };

        for ($i = 0; $i < $count; $i++) {
            $id = $tokens[$i]->id;
            if ($id === T_NAMESPACE) {
                $meta['namespace'] = $segment($i + 1);
            } elseif ($id === T_USE && $meta['class'] === null) {
                // Top-level use only (class not seen yet → not a trait `use`).
                $fqn = $segment($i + 1);
                if ($fqn === '') continue;
                $alias = null;
                for ($j = $i + 1; $j < $count; $j++) {
                    if ($tokens[$j]->text === ';' || $tokens[$j]->text === '{') break;
                    if ($tokens[$j]->id === T_AS) {
                        $a = $this->skipTrivia($tokens, $j + 1);
                        if ($a !== null && $tokens[$a]->id === T_STRING) $alias = $tokens[$a]->text;
                        break;
                    }
                }
                $short = $alias ?? (($p = strrpos($fqn, '\\')) !== false ? substr($fqn, $p + 1) : $fqn);
                $meta['uses'][$short] = $fqn;
            } elseif (($id === T_CLASS || $id === T_INTERFACE || $id === T_TRAIT || $id === T_ENUM) && $meta['class'] === null) {
                $j = $this->skipTrivia($tokens, $i + 1);
                if ($j !== null && $tokens[$j]->id === T_STRING) {
                    $name = $tokens[$j]->text;
                    $meta['class'] = $meta['namespace'] !== '' ? $meta['namespace'] . '\\' . $name : $name;
                }
            }
        }

        return $this->fileMetaCache[$file] = $meta;
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

        // Collect the named types. A union like `Unit|int` (now common since
        // style methods accept a bare-int spacing shorthand) yields both, so
        // we try each: the Unit half draws from the harvested bag, the int
        // half falls back to a scalar default.
        $names = [];
        if ($type instanceof ReflectionNamedType) {
            $names = [$type->getName()];
        } elseif ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $t) {
                if ($t instanceof ReflectionNamedType) {
                    $names[] = $t->getName();
                }
            }
        }
        if ($names === []) {
            return $p->isOptional() ? ['null'] : null;
        }

        $candidates = [];
        foreach ($names as $name) {
            $shortName = ($pos = strrpos($name, '\\')) !== false ? substr($name, $pos + 1) : $name;
            if (isset($bag[$shortName])) {
                $candidates = [...$candidates, ...$bag[$shortName]];
                continue;
            }
            // Scalar fallback so non-value params still eval (no CSS, no
            // failure) — e.g. attr('id', $this->key), or the int half of Unit|int.
            $scalar = match ($name) {
                'string' => "''",
                'int'    => '0',
                'float'  => '0.0',
                'bool'   => 'false',
                default  => null,
            };
            if ($scalar !== null) {
                $candidates[] = $scalar;
            }
        }

        if ($candidates !== []) {
            return array_values(array_unique($candidates));
        }

        return ($p->isOptional() || ($type !== null && $type->allowsNull())) ? ['null'] : null;
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
